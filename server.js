const http = require('http');
const fs = require('fs');
const path = require('path');
const url = require('url');

const PORT = process.env.PORT || 3000;

// Helper to resolve request to static files (mimics Vercel Clean URLs routing)
const cleanUrlMappings = {
  '/': '/Index.html',
  '/login': '/login.html',
  '/signup': '/signup.html',
  '/profile': '/profile.html',
  '/customer-dashboard': '/customer-dashboard.html',
  '/worker-dashboard': '/worker-dashboard.html',
  '/find-workers': '/find-workers.html',
  '/booking-history': '/booking-history.html',
  '/booking': '/booking.html',
  '/chat': '/chat.html',
  '/messages': '/chat.html',
  '/notifications': '/notifications.html',
  '/forgot-password': '/forgot-password.html',
  '/reset-password': '/reset-password.html',
  '/google-oauth': '/google-oauth.html'
};

const mimeTypes = {
  '.html': 'text/html',
  '.css': 'text/css',
  '.js': 'text/javascript',
  '.json': 'application/json',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.gif': 'image/gif',
  '.svg': 'image/svg+xml',
  '.ico': 'image/x-icon',
  '.woff': 'font/woff',
  '.woff2': 'font/woff2',
  '.ttf': 'font/ttf'
};

const server = http.createServer(async (req, res) => {
  const parsedUrl = url.parse(req.url, true);
  let pathname = parsedUrl.pathname;

  console.log(`[${req.method}] ${pathname}`);

  // Route API requests to serverless handlers
  if (pathname.startsWith('/api/')) {
    const apiPath = pathname.substring(5); // e.g. "auth/me" or "workers/list"
    const handlerFilePath = path.join(__dirname, 'api', `${apiPath}.js`);

    if (fs.existsSync(handlerFilePath)) {
      try {
        // Clear require cache for development hot-reloading
        delete require.cache[require.resolve(handlerFilePath)];
        const handler = require(handlerFilePath);

        // Decorate req and res to mimic Vercel/Express environment
        req.query = parsedUrl.query;
        req.body = {};

        if (req.method === 'POST' || req.method === 'PUT') {
          const bodyBuffer = [];
          for await (const chunk of req) {
            bodyBuffer.push(chunk);
          }
          const bodyString = Buffer.concat(bodyBuffer).toString();
          if (req.headers['content-type'] === 'application/json') {
            try {
              req.body = JSON.parse(bodyString);
            } catch (e) {
              req.body = bodyString;
            }
          } else {
            req.body = bodyString;
          }
        }

        res.status = (code) => {
          res.statusCode = code;
          return res;
        };

        res.json = (data) => {
          res.setHeader('Content-Type', 'application/json');
          res.end(JSON.stringify(data));
          return res;
        };

        await handler(req, res);
        return;
      } catch (err) {
        console.error(`API Error in ${pathname}:`, err);
        res.statusCode = 500;
        res.setHeader('Content-Type', 'application/json');
        res.end(JSON.stringify({ status: 'error', message: 'Internal Server Error: ' + err.message }));
        return;
      }
    } else {
      res.statusCode = 404;
      res.setHeader('Content-Type', 'application/json');
      res.end(JSON.stringify({ status: 'error', message: `API Endpoint /api/${apiPath} not found.` }));
      return;
    }
  }

  // Handle static file routing with Clean URL mappings
  let fileRelativePath = cleanUrlMappings[pathname] || pathname;
  let filePath = path.join(__dirname, fileRelativePath);

  // If path doesn't exist, check inside child GoWorker folder as fallback
  if (!fs.existsSync(filePath)) {
    filePath = path.join(__dirname, 'GoWorker', fileRelativePath);
  }

  // Fallback check if it's a directory
  if (fs.existsSync(filePath) && fs.statSync(filePath).isDirectory()) {
    filePath = path.join(filePath, 'Index.html');
  }

  if (fs.existsSync(filePath)) {
    const ext = path.extname(filePath);
    res.statusCode = 200;
    res.setHeader('Content-Type', mimeTypes[ext] || 'application/octet-stream');
    fs.createReadStream(filePath).pipe(res);
  } else {
    // Return 404
    res.statusCode = 404;
    res.setHeader('Content-Type', 'text/html');
    res.end('<h1>404 Not Found</h1><p>The requested URL was not found on this server.</p>');
  }
});

server.listen(PORT, () => {
  console.log(`\n==================================================`);
  console.log(`GoWorker Local Vercel Dev Server running at:`);
  console.log(`http://localhost:${PORT}`);
  console.log(`==================================================\n`);
});
