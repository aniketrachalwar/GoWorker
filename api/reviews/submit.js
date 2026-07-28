const db = require('../db-helper');

module.exports = async (req, res) => {
  if (req.method !== 'POST') {
    return res.status(455).json({ status: 'error', message: 'Method Not Allowed' });
  }

  const verifiedUser = db.getUserFromRequest(req);
  if (!verifiedUser) {
    return res.status(401).json({ status: 'error', message: 'Unauthorized. Login to submit reviews.' });
  }

  const { booking_id, worker_id, rating, review_text } = req.body;

  if (!booking_id || !worker_id || !rating) {
    return res.status(400).json({ status: 'error', message: 'Booking ID, Worker ID, and Rating are required.' });
  }

  try {
    // Save review
    await db.query(`
      INSERT INTO reviews (booking_id, customer_id, worker_id, rating, review_text)
      VALUES (?, ?, ?, ?, ?)
    `, [
      booking_id,
      verifiedUser.user_id,
      worker_id,
      parseInt(rating),
      review_text || ''
    ]);

    return res.status(200).json({ status: 'success', message: 'Review submitted successfully!' });

  } catch (err) {
    console.error("Submit review API error:", err);
    return res.status(500).json({ status: 'error', message: 'Internal Server Error: ' + err.message });
  }
};
