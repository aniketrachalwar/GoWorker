const db = require('../db-helper');
const crypto = require('crypto');

module.exports = async (req, res) => {
  const name = (req.query.name || '').trim();
  const email = (req.query.email || '').trim();
  const google_id = (req.query.google_id || '').trim();
  const profile_photo = (req.query.profile_photo || '').trim() || 'images/avatar_placeholder.png';
  const user_type = (req.query.user_type || 'customer').trim();

  if (!name || !email) {
    return res.status(400).send("Error: Name and Email are required for Google Authentication.");
  }

  try {
    // Search if email already exists
    let users = await db.query("SELECT * FROM users WHERE email = ?", [email]);
    let user = users[0];

    if (!user) {
      // Register user automatically
      const randomPassword = crypto.randomBytes(16).toString('hex');
      const bcrypt = require('bcryptjs');
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

  } catch (err) {
    console.error("Google Callback error:", err);
    return res.status(500).send("Database error in Google Callback: " + err.message);
  }
};
