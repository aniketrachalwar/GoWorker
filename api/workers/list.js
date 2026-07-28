const db = require('../db-helper');

module.exports = async (req, res) => {
  const category_id = parseInt(req.query.category || '0');
  const location = (req.query.location || '').trim();
  const search_query = (req.query.q || '').trim();

  try {
    // 1. Get all categories
    const categories = await db.query("SELECT * FROM categories ORDER BY name ASC");

    // 2. Build workers query
    let sql = `
      SELECT w.*, w.location as service_area, u.full_name as worker_name, u.email, u.phone, c.name as category_name 
      FROM worker_profiles w 
      JOIN users u ON w.user_id = u.id 
      JOIN categories c ON w.category_id = c.id
      WHERE 1=1
    `;
    const params = [];

    if (category_id > 0) {
      sql += " AND w.category_id = ?";
      params.push(category_id);
    }
    if (location) {
      sql += " AND w.location LIKE ?";
      params.push(`%${location}%`);
    }
    if (search_query) {
      sql += " AND (u.full_name LIKE ? OR w.skills LIKE ? OR w.title LIKE ?)";
      params.push(`%${search_query}%`, `%${search_query}%`, `%${search_query}%`);
    }

    let workers = await db.query(sql, params);

    // Dynamic rating aggregation helper
    for (let worker of workers) {
      const ratingInfo = await db.query(
        "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM reviews WHERE worker_id = ?",
        [worker.user_id]
      );
      worker.rating_avg = parseFloat(ratingInfo[0].avg_rating || '5.0').toFixed(1);
      worker.rating_count = parseInt(ratingInfo[0].review_count || '0');
      
      // Determine online availability
      const avail = await db.query("SELECT is_online, status_text FROM worker_availability WHERE worker_id = ?", [worker.user_id]);
      worker.is_online = avail.length > 0 ? avail[0].is_online : 1;
      worker.status_text = avail.length > 0 ? avail[0].status_text : 'Available Now';
    }

    return res.status(200).json({
      status: 'success',
      categories,
      workers
    });

  } catch (err) {
    console.error("Workers list API error:", err);
    return res.status(500).json({ status: 'error', message: 'Internal Server Error: ' + err.message });
  }
};
