const db = require('../db-helper');

module.exports = async (req, res) => {
  const verifiedUser = db.getUserFromRequest(req);
  if (!verifiedUser) {
    return res.status(401).json({ status: 'error', message: 'Unauthorized. Login to view bookings.' });
  }

  try {
    let bookings = [];

    if (verifiedUser.user_type === 'worker') {
      // Query bookings for this worker
      bookings = await db.query(`
        SELECT b.*, u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone
        FROM bookings b
        JOIN users u ON b.customer_id = u.id
        WHERE b.worker_id = ?
        ORDER BY b.booking_date DESC, b.created_at DESC
      `, [verifiedUser.user_id]);
    } else {
      // Query bookings for this customer
      bookings = await db.query(`
        SELECT b.*, u.full_name as worker_name, c.name as category_name, wp.profile_picture as worker_avatar, wp.id as worker_profile_id
        FROM bookings b
        JOIN users u ON b.worker_id = u.id
        LEFT JOIN worker_profiles wp ON wp.user_id = u.id
        LEFT JOIN categories c ON wp.category_id = c.id
        WHERE b.customer_id = ?
        ORDER BY b.booking_date DESC, b.created_at DESC
      `, [verifiedUser.user_id]);
    }

    return res.status(200).json({
      status: 'success',
      bookings
    });

  } catch (err) {
    console.error("List bookings API error:", err);
    return res.status(500).json({ status: 'error', message: 'Internal Server Error: ' + err.message });
  }
};
