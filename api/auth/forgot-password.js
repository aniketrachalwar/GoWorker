const db = require('../db-helper');

module.exports = async (req, res) => {
  if (req.method !== 'POST') {
    return res.status(455).json({ status: 'error', message: 'Method Not Allowed' });
  }

  const { email } = req.body;

  if (!email) {
    return res.status(400).json({ status: 'error', message: 'Email address is required.' });
  }

  try {
    const users = await db.query("SELECT id FROM users WHERE email = ?", [email]);
    
    // Simulate email send
    return res.status(200).json({
      status: 'success',
      message: 'If the email address is registered on our platform, you will receive a password reset link shortly.'
    });

  } catch (err) {
    console.error("Forgot password API error:", err);
    return res.status(500).json({ status: 'error', message: 'Internal Server Error: ' + err.message });
  }
};
