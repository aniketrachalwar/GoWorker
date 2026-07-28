const bcrypt = require('bcryptjs');
const db = require('../db-helper');

module.exports = async (req, res) => {
  if (req.method !== 'POST') {
    return res.status(455).json({ status: 'error', message: 'Method Not Allowed' });
  }

  const {
    full_name,
    email,
    phone,
    password,
    location,
    user_type,
    profession,
    category,
    experience,
    avatar,        // Base64 Data URL or path
    id_document,   // Base64 Data URL or path
    id_type
  } = req.body;

  if (!full_name || !email || !password || !user_type) {
    return res.status(400).json({ status: 'error', message: 'Missing required signup fields.' });
  }

  try {
    // Check if email already exists
    const existing = await db.query("SELECT id FROM users WHERE email = ?", [email]);
    if (existing.length > 0) {
      return res.status(400).json({ status: 'error', message: 'Email address is already registered.' });
    }

    // Hash password (compatible with PHP's default bcrypt)
    const salt = bcrypt.genSaltSync(10);
    const hashedPassword = bcrypt.hashSync(password, salt);

    // Insert user into users table
    const result = await db.query(
      "INSERT INTO users (full_name, email, phone, password, location, user_type) VALUES (?, ?, ?, ?, ?, ?)",
      [full_name, email, phone, hashedPassword, location, user_type]
    );

    const userId = result.insertId;

    let workerProfileId = null;
    if (user_type === 'worker') {
      // Lookup category ID if name is provided, default to 1
      let categoryId = 1; // Default electrician
      if (category) {
        const catResult = await db.query("SELECT id FROM categories WHERE name = ?", [category]);
        if (catResult.length > 0) {
          categoryId = catResult[0].id;
        }
      }

      // Generate verification details
      const experienceYears = parseInt(experience || '0');
      
      // Save worker profile
      const wpResult = await db.query(
        "INSERT INTO worker_profiles (user_id, category_id, title, bio, hourly_rate, location, availability, skills, experience_years, profile_picture, id_document, id_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [
          userId,
          categoryId,
          profession || 'Certified Specialist',
          `Experienced professional in ${profession || category || 'services'}.`,
          299.00,
          location,
          'Mon-Sat 9AM-6PM',
          profession || category || 'General Support',
          experienceYears,
          avatar || null,
          id_document || null,
          id_type || null
        ]
      );
      workerProfileId = wpResult.insertId;

      // Add to availability table
      await db.query("INSERT INTO worker_availability (worker_id, is_online, status_text) VALUES (?, ?, ?)", [userId, 1, 'Available Now']);
    }

    // Create session token (JWT)
    const tokenPayload = {
      user_id: userId,
      full_name,
      email,
      user_type
    };
    const token = db.signToken(tokenPayload);

    // Set cookie header
    res.setHeader('Set-Cookie', db.serializeAuthCookie(token));

    return res.status(200).json({
      status: 'success',
      message: 'Registration successful.',
      user: {
        uid: userId,
        full_name,
        email,
        phone,
        location,
        user_type,
        worker_id: workerProfileId ? `GW-2026-${String(workerProfileId).padStart(6, '0')}` : null,
        avatar
      }
    });

  } catch (err) {
    console.error("Signup API error:", err);
    return res.status(500).json({ status: 'error', message: 'Internal Server Error: ' + err.message });
  }
};
