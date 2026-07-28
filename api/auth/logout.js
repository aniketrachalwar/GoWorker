const db = require('../db-helper');

module.exports = async (req, res) => {
  res.setHeader('Set-Cookie', db.serializeLogoutCookie());
  return res.status(200).json({ status: 'success', message: 'Logged out successfully.' });
};
