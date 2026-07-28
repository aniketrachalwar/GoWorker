const db = require('../db-helper');

module.exports = async (req, res) => {
  if (req.method !== 'POST') {
    return res.status(455).json({ status: 'error', message: 'Method Not Allowed' });
  }

  const verifiedUser = db.getUserFromRequest(req);
  if (!verifiedUser) {
    return res.status(401).json({ status: 'error', message: 'Unauthorized. Login to place a booking request.' });
  }

  const {
    worker_profile_id,
    booking_date,
    time_slot,
    address,
    description,
    total_price
  } = req.body;

  if (!worker_profile_id || !booking_date) {
    return res.status(400).json({ status: 'error', message: 'Worker profile ID and date are required.' });
  }

  try {
    // 1. Fetch worker user_id from worker_profiles
    let workerUserId = null;
    const profiles = await db.query("SELECT user_id FROM worker_profiles WHERE id = ?", [worker_profile_id]);
    if (profiles.length > 0) {
      workerUserId = profiles[0].user_id;
    } else {
      // Fallback check: maybe worker_profile_id is actually user_id
      const profilesByUid = await db.query("SELECT user_id FROM worker_profiles WHERE user_id = ?", [worker_profile_id]);
      if (profilesByUid.length > 0) {
        workerUserId = profilesByUid[0].user_id;
      }
    }

    if (!workerUserId) {
      return res.status(404).json({ status: 'error', message: 'Worker professional profile not found.' });
    }

    // 2. Format booking_date safely into YYYY-MM-DD
    const rawTime = Date.parse(booking_date);
    const formattedDate = !isNaN(rawTime) ? new Date(rawTime).toISOString().slice(0, 10) : new Date().toISOString().slice(0, 10);

    // 3. Save booking requests
    const insResult = await db.query(`
      INSERT INTO bookings (customer_id, worker_id, booking_date, time_slot, description, address, total_price, status)
      VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    `, [
      verifiedUser.user_id,
      workerUserId,
      formattedDate,
      time_slot || 'General Time Slot',
      description || 'General Service Request',
      address || 'Contact customer for address details',
      parseFloat(total_price || '299.00')
    ]);

    return res.status(200).json({
      status: 'success',
      booking_id: insResult.insertId,
      message: 'Booking placed successfully!'
    });

  } catch (err) {
    console.error("Create booking API error:", err);
    return res.status(500).json({ status: 'error', message: 'Internal Server Error: ' + err.message });
  }
};
