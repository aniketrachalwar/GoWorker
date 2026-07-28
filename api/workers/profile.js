const db = require('../db-helper');

module.exports = async (req, res) => {
  let rawId = req.query.id || req.query.worker_id || '';
  let workerUserId = null;
  let workerProfileId = null;

  // Extract ID from different formats: GW-2026-000001, GW-000001, 1, etc.
  if (rawId) {
    if (typeof rawId === 'string' && rawId.includes('GW-')) {
      const match = rawId.match(/GW-(?:\d+-)?(\d+)/i);
      if (match) {
        workerProfileId = parseInt(match[1]);
      }
    } else {
      workerProfileId = parseInt(rawId);
    }
  }

  // Fallback to logged-in worker session
  const verifiedUser = db.getUserFromRequest(req);
  if (!workerProfileId && !req.query.user_id) {
    if (verifiedUser && verifiedUser.user_type === 'worker') {
      workerUserId = verifiedUser.user_id;
    } else {
      // Default to Ramesh Kumar (profile ID 1)
      workerProfileId = 1;
    }
  }

  if (req.query.user_id) {
    workerUserId = parseInt(req.query.user_id);
  }

  try {
    let worker = null;

    if (workerProfileId) {
      const workers = await db.query(`
        SELECT w.*, u.id as user_id, u.full_name as worker_name, u.email, u.phone, u.created_at as user_created_at, u.location as user_location, c.name as category_name 
        FROM worker_profiles w
        JOIN users u ON w.user_id = u.id
        JOIN categories c ON w.category_id = c.id
        WHERE w.id = ?
      `, [workerProfileId]);
      if (workers.length > 0) {
        worker = workers[0];
      }
    } else if (workerUserId) {
      const workers = await db.query(`
        SELECT w.*, u.id as user_id, u.full_name as worker_name, u.email, u.phone, u.created_at as user_created_at, u.location as user_location, c.name as category_name 
        FROM users u
        LEFT JOIN worker_profiles w ON w.user_id = u.id
        LEFT JOIN categories c ON w.category_id = c.id
        WHERE u.id = ? AND u.user_type = 'worker'
      `, [workerUserId]);
      if (workers.length > 0) {
        worker = workers[0];
      }
    }

    // Default Fallback
    if (!worker) {
      return res.status(404).json({ status: 'error', message: 'Worker profile not found.' });
    }

    // Coalesce values
    worker.location = worker.location || worker.user_location || 'Pune';
    worker.profile_picture = worker.profile_picture || 'images/avatar_placeholder.png';
    worker.user_id = worker.user_id || worker.id;

    // Fetch reviews
    const reviews = await db.query(`
      SELECT r.*, u.full_name as customer_name 
      FROM reviews r
      JOIN users u ON r.customer_id = u.id
      WHERE r.worker_id = ?
      ORDER BY r.created_at DESC
    `, [worker.user_id]);

    let rating_avg = 5.0;
    let rating_count = 0;
    const rating_breakdown = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };

    if (reviews.length > 0) {
      let total_rating = 0;
      reviews.forEach(rev => {
        total_rating += rev.rating;
        rating_breakdown[rev.rating] = (rating_breakdown[rev.rating] || 0) + 1;
      });
      rating_count = reviews.length;
      rating_avg = parseFloat((total_rating / rating_count).toFixed(1));
    } else if (worker.id === 1) {
      // Ramesh Kumar default reviews fallback
      rating_avg = 4.9;
      rating_count = 128;
      rating_breakdown[5] = 115;
      rating_breakdown[4] = 10;
      rating_breakdown[3] = 3;
    }

    return res.status(200).json({
      status: 'success',
      worker,
      reviews,
      rating_avg,
      rating_count,
      rating_breakdown
    });

  } catch (err) {
    console.error("Worker profile API error:", err);
    return res.status(500).json({ status: 'error', message: 'Internal Server Error: ' + err.message });
  }
};
