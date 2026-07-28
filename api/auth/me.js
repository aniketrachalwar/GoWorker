const db = require('../db-helper');

module.exports = async (req, res) => {
  const verifiedUser = db.getUserFromRequest(req);
  
  if (!verifiedUser) {
    return res.status(401).json({ status: 'error', message: 'Unauthorized. No valid session.' });
  }

  try {
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

  } catch (err) {
    console.error("Auth Me API error:", err);
    return res.status(500).json({ status: 'error', message: 'Internal Server Error: ' + err.message });
  }
};
