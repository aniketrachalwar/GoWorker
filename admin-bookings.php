<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GoWorker - Booking Management</title>

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  
  <!-- Global Stylesheet -->
  <link rel="stylesheet" href="css/styles.css">
  
  <!-- Page Specific Stylesheet -->
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
        <li><a href="admin-dashboard.php" class="admin-link"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
        <li><a href="admin-users.php" class="admin-link"><i class="fa-solid fa-users"></i> Users</a></li>
        <li><a href="admin-worker-verification.php" class="admin-link"><i class="fa-solid fa-user-shield"></i> Verifications</a></li>
        <li><a href="admin-bookings.php" class="admin-link active"><i class="fa-solid fa-calendar-check"></i> Bookings</a></li>
        <li><a href="admin-payments.php" class="admin-link"><i class="fa-solid fa-wallet"></i> Payments</a></li>
      </ul>
    </aside>

    <!-- RIGHT MAIN WORKSPACE -->
    <div style="display: flex; flex-direction: column; overflow: hidden; width: 100%;">
      <!-- Top Navbar -->
      <header class="admin-header">
        <div class="admin-search">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" placeholder="Search bookings by ID...">
        </div>
        <button class="btn-icon" style="border: none;" onclick="location.href='index.php'"><i class="fa-solid fa-arrow-left-long"></i> View Site</button>
      </header>

      <!-- Content Area -->
      <main class="admin-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
          <div>
            <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 4px;">Booking Management</h2>
            <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">Monitor, assign, reassign, cancel, and refund customer bookings on GoWorker.</p>
          </div>
          <button class="btn-coupon" id="admin-export-btn"><i class="fa-solid fa-file-export"></i> Export Bookings</button>
        </div>

        <!-- Bookings stats grid -->
        <div class="admin-stats-grid">
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Total Bookings</h4>
            <h3 style="font-size: 24px; font-weight: 700; color: var(--primary-text);">25,482</h3>
          </div>
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Active Bookings</h4>
            <h3 style="font-size: 24px; font-weight: 700; color: var(--primary-text);">429</h3>
          </div>
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Completed</h4>
            <h3 style="font-size: 24px; font-weight: 700; color: var(--success);">24,912</h3>
          </div>
        </div>

        <!-- Bookings Table -->
        <div class="admin-table-container">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Customer</th>
                <th>Worker</th>
                <th>Service Required</th>
                <th>Booking Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#GOW-902183</td>
                <td>Aniket Rachalwar</td>
                <td class="row-name">Ramesh Kumar <span class="virtual-id-badge" style="font-size: 10px; background: var(--primary-light); color: var(--primary); padding: 1px 5px; border-radius: 4px; font-weight: 600; margin-left: 6px; vertical-align: middle;">GW-W-0001</span></td>
                <td class="row-profession">Emergency Troubleshooting</td>
                <td>Today, 09:00 AM</td>
                <td><span class="status-badge status-ongoing">Ongoing</span></td>
                <td>
                  <button class="btn-book" style="background: var(--white); border: 1.5px solid var(--danger); color: var(--danger); font-size: 11px; padding: 6px 12px;" onclick="alert('Booking Cancelled by Admin')">Cancel</button>
                </td>
              </tr>

              <tr>
                <td>#GOW-847291</td>
                <td>Sunita Patil</td>
                <td class="row-name">Sohan Singh <span class="virtual-id-badge" style="font-size: 10px; background: var(--primary-light); color: var(--primary); padding: 1px 5px; border-radius: 4px; font-weight: 600; margin-left: 6px; vertical-align: middle;">GW-W-0002</span></td>
                <td class="row-profession">Plumbing Leak Repair</td>
                <td>14 July, 02:30 PM</td>
                <td><span class="status-badge status-completed">Completed</span></td>
                <td>
                  <span style="color: var(--secondary-text); font-size: 12px;">No actions</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <!-- DETAILS PANEL DRAWER -->
  <div class="drawer-overlay" id="admin-drawer-overlay"></div>
  <aside class="drawer-panel" id="admin-detail-drawer">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 24px;">
      <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 0;">Booking Details</h3>
      <span class="drawer-close-btn" style="cursor: pointer; font-size: 20px;"><i class="fa-solid fa-xmark"></i></span>
    </div>
    <div style="margin-bottom: 24px;">
      <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 6px;">Booking Status Timeline</h4>
      <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 6px;">✓ Booking Created - 08:30 AM</p>
      <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 6px;">✓ Worker Assigned - 08:45 AM</p>
      <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">✓ On The Way - 09:12 AM</p>
    </div>
    <button class="btn-primary-auth" style="width: 100%; height: 46px; background: var(--danger);" onclick="alert('Booking Cancelled')">Cancel Booking</button>
  </aside>

  <script src="admin.js"></script>

</body>
</html>
