const db = require('./db-helper');

module.exports = async (req, res) => {
  const verifiedUser = db.getUserFromRequest(req);
  const action = req.query.action || req.body.action || '';

  if (action === 'list') {
    if (!verifiedUser) {
      return res.status(200).json({ status: 'success', favorites: [] });
    }

    try {
      const favs = await db.query("SELECT worker_id FROM favorites WHERE customer_id = ?", [verifiedUser.user_id]);
      const list = favs.map(f => f.worker_id);
      return res.status(200).json({ status: 'success', favorites: list });
    } catch (err) {
      return res.status(200).json({ status: 'success', favorites: [] });
    }
  }

  if (action === 'toggle' && req.method === 'POST') {
    const worker_id = parseInt(req.body.worker_id || '0');
    if (!worker_id) {
      return res.status(400).json({ status: 'error', message: 'Worker ID required' });
    }

    if (!verifiedUser) {
      return res.status(200).json({ status: 'success', is_favorite: true, guest: true });
    }

    try {
      const exists = await db.query("SELECT id FROM favorites WHERE customer_id = ? AND worker_id = ?", [verifiedUser.user_id, worker_id]);
      if (exists.length > 0) {
        await db.query("DELETE FROM favorites WHERE customer_id = ? AND worker_id = ?", [verifiedUser.user_id, worker_id]);
        return res.status(200).json({ status: 'success', is_favorite: false, message: 'Removed from favorites' });
      } else {
        await db.query("INSERT INTO favorites (customer_id, worker_id) VALUES (?, ?)", [verifiedUser.user_id, worker_id]);
        return res.status(200).json({ status: 'success', is_favorite: true, message: 'Saved to favorites' });
      }
    } catch (err) {
      return res.status(500).json({ status: 'error', message: 'Database error: ' + err.message });
    }
  }

  return res.status(400).json({ status: 'error', message: 'Invalid action or request' });
};
