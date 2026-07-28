const mysql = require('mysql2/promise');
const jwt = require('jsonwebtoken');
const cookie = require('cookie');
const fs = require('fs');
const path = require('path');

// Retrieve DB configurations from Environment Variables
const dbConfig = {
  host: process.env.DB_HOST || '127.0.0.1',
  port: parseInt(process.env.DB_PORT || '3306'),
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_NAME || 'goworker',
  connectionLimit: 10
};

// Auto-detect SSL configurations (required for Aiven MySQL)
const caPaths = [
  path.join(__dirname, 'ca.pem'),
  path.join(__dirname, '../ca.pem'),
  path.join(__dirname, '../config/ca.pem')
];
let sslConfig = null;
for (const caPath of caPaths) {
  if (fs.existsSync(caPath)) {
    sslConfig = { ca: fs.readFileSync(caPath) };
    break;
  }
}
if (!sslConfig && (dbConfig.host.includes('aivencloud.com') || process.env.DB_SSL === 'true')) {
  sslConfig = { rejectUnauthorized: false };
}
if (sslConfig) {
  dbConfig.ssl = sslConfig;
}

// Create MySQL connection pool
const pool = mysql.createPool(dbConfig);

// JWT Secret Key
const JWT_SECRET = process.env.JWT_SECRET || 'goworker_secret_key_2026';

/**
 * Execute SQL queries using pool connection
 */
async function query(sql, params) {
  const [results] = await pool.execute(sql, params);
  return results;
}

/**
 * Parse cookies from request headers
 */
function parseCookies(req) {
  const cookieHeader = req.headers.cookie || '';
  return cookie.parse(cookieHeader);
}

/**
 * Generate HttpOnly JWT cookie string
 */
function serializeAuthCookie(token) {
  return cookie.serialize('token', token, {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
    path: '/',
    maxAge: 60 * 60 * 24 * 7 // 1 week
  });
}

/**
 * Generate serialized cookie string to clear authentication
 */
function serializeLogoutCookie() {
  return cookie.serialize('token', '', {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
    path: '/',
    expires: new Date(0)
  });
}

/**
 * Verify JWT from request cookies and return payload
 */
function getUserFromRequest(req) {
  try {
    const cookies = parseCookies(req);
    const token = cookies.token;
    if (!token) return null;
    
    return jwt.verify(token, JWT_SECRET);
  } catch (err) {
    return null;
  }
}

/**
 * Sign payload to generate JWT
 */
function signToken(payload) {
  return jwt.sign(payload, JWT_SECRET, { expiresIn: '7d' });
}

module.exports = {
  query,
  parseCookies,
  serializeAuthCookie,
  serializeLogoutCookie,
  getUserFromRequest,
  signToken
};
