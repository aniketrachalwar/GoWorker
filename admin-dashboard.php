<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GoWorker - Admin Dashboard</title>

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  
  <!-- Global Stylesheet -->
  <link rel="stylesheet" href="css/styles.css">
  
  <!-- Admin Panels stylesheet -->
  <link rel="stylesheet" href="admin.css">
</head>
<body>

  <div class="admin-layout">
    <!-- ADMIN SIDEBAR -->
    <aside class="admin-sidebar">
      <div class="admin-logo">
        <img src="images/logo_icon.png" alt="Logo" onerror="this.src='assets/logo.jfif';">
        <span>Go<strong>Admin</strong></span>
      </div>

      <ul class="admin-menu">
        <li><a href="admin-dashboard.php" class="admin-link active"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
        <li><a href="admin-users.php" class="admin-link"><i class="fa-solid fa-users"></i> Users</a></li>
        <li><a href="admin-worker-verification.php" class="admin-link"><i class="fa-solid fa-user-shield"></i> Verifications</a></li>
        <li><a href="admin-bookings.php" class="admin-link"><i class="fa-solid fa-calendar-check"></i> Bookings</a></li>
        <li><a href="admin-payments.php" class="admin-link"><i class="fa-solid fa-wallet"></i> Payments</a></li>
      </ul>
    </aside>

    <!-- RIGHT MAIN WORKSPACE -->
    <div style="display: flex; flex-direction: column; overflow: hidden; width: 100%;">
      <!-- Top Navbar -->
      <header class="admin-header">
        <div class="admin-search">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" placeholder="Search anything...">
        </div>
        <div style="display: flex; gap: 20px; align-items: center;">
          <button class="btn-icon" style="border: none;" onclick="location.href='index.php'"><i class="fa-solid fa-arrow-left-long"></i> View Site</button>
        </div>
      </header>

      <!-- Content Area -->
      <main class="admin-content">
        <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 24px;">Platform Overview</h2>

        <!-- Top summary Stats -->
        <div class="admin-stats-grid">
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Total Registrations</h4>
            <h3 style="font-size: 26px; font-weight: 700; color: var(--primary-text);">18,452</h3>
          </div>
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Verified Professionals</h4>
            <h3 style="font-size: 26px; font-weight: 700; color: var(--primary-text);">12,390</h3>
          </div>
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Active Bookings</h4>
            <h3 style="font-size: 26px; font-weight: 700; color: var(--primary-text);">429</h3>
          </div>
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Commission Revenue</h4>
            <h3 style="font-size: 26px; font-weight: 700; color: var(--primary-text);">₹48,930</h3>
          </div>
        </div>

        <!-- Verification Queue Table -->
        <div class="admin-table-container">
          <div class="admin-table-header">
            <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 0;">Verification Pipeline Queue</h3>
            <button class="btn-coupon" id="admin-export-btn"><i class="fa-solid fa-file-export"></i> Export CSV</button>
          </div>

          <table class="admin-table">
            <thead>
              <tr>
                <th>Applicant</th>
                <th>Trade Profession</th>
                <th>Submitted Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- Row 1 -->
              <tr>
                <td>
                  <div class="admin-table-user">
                    <img class="row-avatar" src="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=100" alt="Avatar">
                    <span class="row-name">Ramesh Kumar <span class="virtual-id-badge" style="font-size: 10px; background: var(--primary-light); color: var(--primary); padding: 1px 5px; border-radius: 4px; font-weight: 600; margin-left: 6px; vertical-align: middle;">GW-W-0001</span></span>
                  </div>
                </td>
                <td class="row-profession">Electrician</td>
                <td>Today, 08:30 AM</td>
                <td><span class="status-badge" style="background: var(--primary-light); color: var(--primary);">Pending</span></td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <button class="btn-book btn-approve-action" style="background: var(--success); font-size: 11px; padding: 6px 12px;">Approve</button>
                    <button class="btn-book btn-reject-action" style="background: var(--danger); font-size: 11px; padding: 6px 12px;">Reject</button>
                  </div>
                </td>
              </tr>

              <!-- Row 2 -->
              <tr>
                <td>
                  <div class="admin-table-user">
                    <img class="row-avatar" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100" alt="Avatar">
                    <span class="row-name">Sohan Singh <span class="virtual-id-badge" style="font-size: 10px; background: var(--primary-light); color: var(--primary); padding: 1px 5px; border-radius: 4px; font-weight: 600; margin-left: 6px; vertical-align: middle;">GW-W-0002</span></span>
                  </div>
                </td>
                <td class="row-profession">Plumber</td>
                <td>Yesterday</td>
                <td><span class="status-badge" style="background: rgba(34, 197, 94, 0.08); color: var(--success);">Approved</span></td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <button class="btn-book" style="background: var(--white); border: 1px solid var(--border-color); color: var(--primary-text); font-size: 11px; padding: 6px 12px;">Details</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <!-- DETAILS PANEL DRAWER SLIDE -->
  <div class="drawer-overlay" id="admin-drawer-overlay"></div>
  <aside class="drawer-panel" id="admin-detail-drawer">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 24px;">
      <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 0;">Application Details</h3>
      <span class="drawer-close-btn" style="cursor: pointer; font-size: 20px;"><i class="fa-solid fa-xmark"></i></span>
    </div>
    <div style="text-align: center; margin-bottom: 24px;">
      <img id="drawer-avatar" src="" alt="Avatar" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 12px; border: 3px solid var(--border-color);">
      <h4 id="drawer-name" style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">Ramesh Kumar</h4>
      <p id="drawer-prof" style="font-size: 13px; color: var(--secondary-text);">Electrician</p>
    </div>
    <div>
      <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px;">Verification Progress Checklist</h4>
      <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">✓ Phone Number Verified</p>
      <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">✓ Aadhaar ID Upload Matches Profile</p>
      <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 20px;">✓ Criminal Record Check Complete</p>
      <button class="btn-primary-auth btn-approve-action" style="width: 100%; height: 46px;">Approve Application</button>
    </div>
  </aside>

  <script src="admin.js"></script>

</body>
</html>
