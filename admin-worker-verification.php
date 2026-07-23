<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GoWorker - Worker Verification Center</title>

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
        <li><a href="admin-worker-verification.php" class="admin-link active"><i class="fa-solid fa-user-shield"></i> Verifications</a></li>
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
          <input type="text" placeholder="Search by applicant name...">
        </div>
        <button class="btn-icon" style="border: none;" onclick="location.href='index.php'"><i class="fa-solid fa-arrow-left-long"></i> View Site</button>
      </header>

      <!-- Content Area -->
      <main class="admin-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
          <div>
            <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 4px;">Worker Verification Pipeline</h2>
            <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">Review and verify worker credentials before they can offer services on GoWorker.</p>
          </div>
          <button class="btn-coupon" id="admin-export-btn"><i class="fa-solid fa-file-export"></i> Export List</button>
        </div>

        <!-- Stats row -->
        <div class="admin-stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 28px;">
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Pending Review</h4>
            <h3 style="font-size: 24px; font-weight: 700; color: var(--primary-text);">18</h3>
          </div>
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Approved Partners</h4>
            <h3 style="font-size: 24px; font-weight: 700; color: var(--success);">12,390</h3>
          </div>
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Rejections / Flagged</h4>
            <h3 style="font-size: 24px; font-weight: 700; color: var(--danger);">42</h3>
          </div>
        </div>

        <!-- Applicants Table -->
        <div class="admin-table-container">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Applicant</th>
                <th>Trade Profession</th>
                <th>Experience</th>
                <th>City</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <div class="admin-table-user">
                    <img class="row-avatar" src="https://images.unsplash.com/photo-1540569014015-19a7be504e3a?w=100" alt="Avatar">
                    <span class="row-name">Ramesh Kumar <span class="virtual-id-badge" style="font-size: 10px; background: var(--primary-light); color: var(--primary); padding: 1px 5px; border-radius: 4px; font-weight: 600; margin-left: 6px; vertical-align: middle;">GW-W-0001</span></span>
                  </div>
                </td>
                <td class="row-profession">Electrician</td>
                <td>5 Years</td>
                <td>Pune</td>
                <td><span class="status-badge" style="background: var(--primary-light); color: var(--primary);">Pending</span></td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <button class="btn-book btn-approve-action" style="background: var(--success); font-size: 11px; padding: 6px 12px;">Approve</button>
                    <button class="btn-book btn-reject-action" style="background: var(--danger); font-size: 11px; padding: 6px 12px;">Reject</button>
                  </div>
                </td>
              </tr>

              <tr>
                <td>
                  <div class="admin-table-user">
                    <img class="row-avatar" src="https://images.unsplash.com/photo-1628157582853-a796fa650a6a?w=100" alt="Avatar">
                    <span class="row-name">Pooja Sharma <span class="virtual-id-badge" style="font-size: 10px; background: var(--primary-light); color: var(--primary); padding: 1px 5px; border-radius: 4px; font-weight: 600; margin-left: 6px; vertical-align: middle;">GW-W-0003</span></span>
                  </div>
                </td>
                <td class="row-profession">House Cleaner</td>
                <td>3 Years</td>
                <td>Bangalore</td>
                <td><span class="status-badge" style="background: var(--primary-light); color: var(--primary);">Pending</span></td>
                <td>
                  <div style="display: flex; gap: 8px;">
                    <button class="btn-book btn-approve-action" style="background: var(--success); font-size: 11px; padding: 6px 12px;">Approve</button>
                    <button class="btn-book btn-reject-action" style="background: var(--danger); font-size: 11px; padding: 6px 12px;">Reject</button>
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
      <h4 id="drawer-name" style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">Applicant Name</h4>
      <p id="drawer-prof" style="font-size: 13px; color: var(--secondary-text);">Trade Profession</p>
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
