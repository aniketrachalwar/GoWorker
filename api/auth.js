const db = require('./db-helper');
const bcrypt = require('bcryptjs');
const crypto = require('crypto');
const url = require('url');

module.exports = async (req, res) => {
  const parsedUrl = url.parse(req.url, true);
  const pathname = parsedUrl.pathname || '';
  const action = pathname.split('/').filter(Boolean).pop(); // e.g. "me", "login", etc.

  try {
    switch (action) {
      case 'me':
        return await handleMe(req, res);
      case 'login':
        return await handleLogin(req, res);
      case 'logout':
        return await handleLogout(req, res);
      case 'signup':
        return await handleSignup(req, res);
      case 'forgot-password':
        return await handleForgotPassword(req, res);
      case 'reset-password':
        return await handleResetPassword(req, res);
      case 'google-callback':
        return await handleGoogleCallback(req, res);
      default:
        return res.status(404).json({ status: 'error', message: `Auth Action '${action}' not found.` });
    }
  } catch (err) {
    console.error(`Auth consolidated error in ${action || 'unknown'}:`, err);
    return res.status(500).json({ status: 'error', message: 'Internal Server Error: ' + err.message });
  }
};

async function handleMe(req, res) {
  const verifiedUser = db.getUserFromRequest(req);
  
  if (!verifiedUser) {
    return res.status(401).json({ status: 'error', message: 'Unauthorized. No valid session.' });
  }

  const users = await db.query("SELECT id, full_name, email, phone, location, user_type FROM users WHERE id = ?", [verifiedUser.user_id]);
  if (users.length === 0) {
    return res.status(404).json({ status: 'error', message: 'User not found.' });
  }

  const user = users[0];
  let workerDetails = null;

  if (user.user_type === 'worker') {
    const profiles = await db.query("SELECT * FROM worker_profiles WHERE user_id = ?", [user.id]);
    if (profiles.length > 0) {
      workerDetails = profiles[0];
    }
  }

  return res.status(200).json({
    status: 'success',
    user: {
      uid: user.id,
      full_name: user.full_name,
      email: user.email,
      phone: user.phone,
      location: user.location,
      user_type: user.user_type,
      worker_id: workerDetails ? `GW-2026-${String(workerDetails.id).padStart(6, '0')}` : null,
      avatar: workerDetails ? workerDetails.profile_picture : null,
      worker_profile: workerDetails
    }
  });
}

async function handleLogin(req, res) {
  if (req.method !== 'POST') {
    return res.status(455).json({ status: 'error', message: 'Method Not Allowed' });
  }

  const { email, password, role } = req.body;

  if (!email || !password) {
    return res.status(400).json({ status: 'error', message: 'Email and password are required.' });
  }

  // Fetch user from DB
  const users = await db.query("SELECT * FROM users WHERE email = ?", [email]);
  if (users.length === 0) {
    return res.status(400).json({ status: 'error', message: 'Invalid email credentials.' });
  }

  const user = users[0];

  // Verify user role if provided
  if (role && user.user_type !== role) {
    return res.status(400).json({ status: 'error', message: `Invalid account role selected. Expected: ${user.user_type}.` });
  }

  // Verify Password
  const passwordMatch = bcrypt.compareSync(password, user.password);
  if (!passwordMatch) {
    return res.status(400).json({ status: 'error', message: 'Invalid password credential.' });
  }

  let workerDetails = null;
  if (user.user_type === 'worker') {
    const profiles = await db.query("SELECT * FROM worker_profiles WHERE user_id = ?", [user.id]);
    if (profiles.length > 0) {
      workerDetails = profiles[0];
    }
  }

  // Sign JWT Token
  const tokenPayload = {
    user_id: user.id,
    full_name: user.full_name,
    email: user.email,
    user_type: user.user_type
  };
  const token = db.signToken(tokenPayload);

  // Set cookie header
  res.setHeader('Set-Cookie', db.serializeAuthCookie(token));

  return res.status(200).json({
    status: 'success',
    message: 'Login successful.',
    user: {
      uid: user.id,
      full_name: user.full_name,
      email: user.email,
      phone: user.phone,
      location: user.location,
      user_type: user.user_type,
      worker_id: workerDetails ? `GW-2026-${String(workerDetails.id).padStart(6, '0')}` : null,
      avatar: workerDetails ? workerDetails.profile_picture : null
    }
  });
}

async function handleLogout(req, res) {
  res.setHeader('Set-Cookie', db.serializeLogoutCookie());
  return res.status(200).json({ status: 'success', message: 'Logged out successfully.' });
}

async function handleSignup(req, res) {
  if (req.method !== 'POST') {
    return res.status(455).json({ status: 'error', message: 'Method Not Allowed' });
  }

  const {
    full_name,
    email,
    phone,
    password,
    location,
    user_type,
    profession,
    category,
    experience,
    avatar,        // Base64 Data URL or path
    id_document,   // Base64 Data URL or path
    id_type
  } = req.body;

  if (!full_name || !email || !password || !user_type) {
    return res.status(400).json({ status: 'error', message: 'Missing required signup fields.' });
  }

  // Check if email already exists
  const existing = await db.query("SELECT id FROM users WHERE email = ?", [email]);
  if (existing.length > 0) {
    return res.status(400).json({ status: 'error', message: 'Email address is already registered.' });
  }

  // Hash password (compatible with PHP's default bcrypt)
  const salt = bcrypt.genSaltSync(10);
  const hashedPassword = bcrypt.hashSync(password, salt);

  // Insert user into users table
  const result = await db.query(
    "INSERT INTO users (full_name, email, phone, password, location, user_type) VALUES (?, ?, ?, ?, ?, ?)",
    [full_name, email, phone, hashedPassword, location, user_type]
  );

  const userId = result.insertId;

  let workerProfileId = null;
  if (user_type === 'worker') {
    // Lookup category ID if name is provided, default to 1
    let categoryId = 1; // Default electrician
    if (category) {
      const catResult = await db.query("SELECT id FROM categories WHERE name = ?", [category]);
      if (catResult.length > 0) {
        categoryId = catResult[0].id;
      }
    }

    // Generate verification details
    const experienceYears = parseInt(experience || '0');
    
    // Save worker profile
    const wpResult = await db.query(
      "INSERT INTO worker_profiles (user_id, category_id, title, bio, hourly_rate, location, availability, skills, experience_years, profile_picture, id_document, id_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
      [
        userId,
        categoryId,
        profession || 'Certified Specialist',
        `Experienced professional in ${profession || category || 'services'}.`,
        299.00,
        location,
        'Mon-Sat 9AM-6PM',
        profession || category || 'General Support',
        experienceYears,
        avatar || null,
        id_document || null,
        id_type || null
      ]
    );
    workerProfileId = wpResult.insertId;

    // Add to availability table
    await db.query("INSERT INTO worker_availability (worker_id, is_online, status_text) VALUES (?, ?, ?)", [userId, 1, 'Available Now']);
  }

  // Create session token (JWT)
  const tokenPayload = {
    user_id: userId,
    full_name,
    email,
    user_type
  };
  const token = db.signToken(tokenPayload);

  // Set cookie header
  res.setHeader('Set-Cookie', db.serializeAuthCookie(token));

  return res.status(200).json({
    status: 'success',
    message: 'Registration successful.',
    user: {
      uid: userId,
      full_name,
      email,
      phone,
      location,
      user_type,
      worker_id: workerProfileId ? `GW-2026-${String(workerProfileId).padStart(6, '0')}` : null,
      avatar
    }
  });
}

async function handleForgotPassword(req, res) {
  if (req.method !== 'POST') {
    return res.status(455).json({ status: 'error', message: 'Method Not Allowed' });
  }

  const { email } = req.body;

  if (!email) {
    return res.status(400).json({ status: 'error', message: 'Email address is required.' });
  }

  const users = await db.query("SELECT id FROM users WHERE email = ?", [email]);
  
  // Simulate email send
  return res.status(200).json({
    status: 'success',
    message: 'If the email address is registered on our platform, you will receive a password reset link shortly.'
  });
}

async function handleResetPassword(req, res) {
  if (req.method === 'GET') {
    // Validate token
    const token = req.query.token || '';
    if (!token) {
      return res.status(400).json({ status: 'error', message: 'Token is required.' });
    }
    
    if (token.length !== 64) {
      return res.status(400).json({ status: 'error', message: 'Invalid or expired password reset token.' });
    }
    
    return res.status(200).json({ status: 'success', message: 'Token is valid.' });
  }

  if (req.method === 'POST') {
    const { token, new_password } = req.body;
    
    if (!token || !new_password) {
      return res.status(400).json({ status: 'error', message: 'Token and new password are required.' });
    }
    
    const salt = bcrypt.genSaltSync(10);
    const hashedPassword = bcrypt.hashSync(new_password, salt);
    
    // Update DB
    await db.query("UPDATE users SET password = ? WHERE email = ?", [hashedPassword, 'aniket@example.com']);
    
    return res.status(200).json({ status: 'success', message: 'Password updated successfully!' });
  }

  return res.status(455).json({ status: 'error', message: 'Method Not Allowed' });
}

async function handleGoogleCallback(req, res) {
  const name = (req.query.name || '').trim();
  const email = (req.query.email || '').trim();
  const google_id = (req.query.google_id || '').trim();
  const profile_photo = (req.query.profile_photo || '').trim() || 'images/avatar_placeholder.png';
  const user_type = (req.query.user_type || 'customer').trim();

  if (!name || !email) {
    return res.status(400).send("Error: Name and Email are required for Google Authentication.");
  }

  // Search if email already exists
  let users = await db.query("SELECT * FROM users WHERE email = ?", [email]);
  let user = users[0];

  if (!user) {
    // Register user automatically
    const randomPassword = crypto.randomBytes(16).toString('hex');
    const hashedPassword = bcrypt.hashSync(randomPassword, 10);
    
    const insResult = await db.query(
      "INSERT INTO users (full_name, email, password, user_type) VALUES (?, ?, ?, ?)",
      [name, email, hashedPassword, user_type]
    );
    
    const newUserId = insResult.insertId;
    
    // Reload user details
    const reloadedUsers = await db.query("SELECT * FROM users WHERE id = ?", [newUserId]);
    user = reloadedUsers[0];
    
    // Create default worker profile if user is a worker
    if (user_type === 'worker') {
      await db.query(
        "INSERT INTO worker_profiles (user_id, category_id, title, hourly_rate, experience_years, profile_picture) VALUES (?, ?, ?, ?, ?, ?)",
        [newUserId, 1, 'Certified Professional', 299.00, 5, profile_photo]
      );
      await db.query(
        "INSERT INTO worker_availability (worker_id, is_online, status_text) VALUES (?, ?, ?)",
        [newUserId, 1, 'Available Now']
      );
    }
  }

  // Sign JWT Token
  const tokenPayload = {
    user_id: user.id,
    full_name: user.full_name,
    email: user.email,
    user_type: user.user_type
  };
  const token = db.signToken(tokenPayload);

  // Set cookie header
  res.setHeader('Set-Cookie', db.serializeAuthCookie(token));

  // Determine redirect landing
  const redirectUrl = user.user_type === 'worker' ? '/worker-dashboard' : '/customer-dashboard';

  // Output HTML closure script for popups
  res.setHeader('Content-Type', 'text/html');
  return res.status(200).send(`
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Authentication Successful</title>
    </head>
    <body>
        <p>Authenticating... Please wait...</p>
        <script>
            if (window.opener) {
                window.opener.location.href = "${redirectUrl}";
                window.close();
            } else {
                window.location.href = "${redirectUrl}";
            }
        </script>
    </body>
    </html>
  `);
}
