const bcrypt = require('bcryptjs');
const db = require('../db-helper');

module.exports = async (req, res) => {
  if (req.method !== 'POST') {
    return res.status(455).json({ status: 'error', message: 'Method Not Allowed' });
  }

  const { email, password, role } = req.body;

  if (!email || !password) {
    return res.status(400).json({ status: 'error', message: 'Email and password are required.' });
  }

  try {
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

  } catch (err) {
    console.error("Login API error:", err);
    return res.status(500).json({ status: 'error', message: 'Internal Server Error: ' + err.message });
  }
};
