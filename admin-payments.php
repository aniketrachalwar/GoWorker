<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GoWorker - Financial Management</title>

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
      <div class="admin-logo" style="display: flex; align-items: center; gap: 10px;">
        <div style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; background: rgba(255, 255, 255, 0.1); border-radius: 6px; color: #ffffff;">
            <i class="fa-solid fa-user-shield" style="font-size: 14px;"></i>
        </div>
        <span>Go<strong>Admin</strong></span>
      </div>

      <ul class="admin-menu">
        <li><a href="admin-dashboard.php" class="admin-link"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
        <li><a href="admin-users.php" class="admin-link"><i class="fa-solid fa-users"></i> Users</a></li>
        <li><a href="admin-worker-verification.php" class="admin-link"><i class="fa-solid fa-user-shield"></i> Verifications</a></li>
        <li><a href="admin-bookings.php" class="admin-link"><i class="fa-solid fa-calendar-check"></i> Bookings</a></li>
        <li><a href="admin-payments.php" class="admin-link active"><i class="fa-solid fa-wallet"></i> Payments</a></li>
      </ul>
    </aside>

    <!-- RIGHT MAIN WORKSPACE -->
    <div style="display: flex; flex-direction: column; overflow: hidden; width: 100%;">
      <!-- Top Navbar -->
      <header class="admin-header">
        <div class="admin-search">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" placeholder="Search transactions by ID...">
        </div>
        <button class="btn-icon" style="border: none;" onclick="location.href='index.php'"><i class="fa-solid fa-arrow-left-long"></i> View Site</button>
      </header>

      <!-- Content Area -->
      <main class="admin-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
          <div>
            <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 4px;">Payments & Payouts</h2>
            <p style="font-size: 13px; color: var(--secondary-text); margin-bottom: 0;">Monitor platform transactions, payouts, commission splits, and customer refund processing.</p>
          </div>
          <button class="btn-coupon" id="admin-export-btn"><i class="fa-solid fa-file-export"></i> Financial Statement</button>
        </div>

        <!-- Stats overview -->
        <div class="admin-stats-grid">
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Total Volume Processed</h4>
            <h3 style="font-size: 24px; font-weight: 700; color: var(--primary-text);">₹1,84,520</h3>
          </div>
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Platform Revenue (Commissions)</h4>
            <h3 style="font-size: 24px; font-weight: 700; color: var(--primary-text);">₹48,930</h3>
          </div>
          <div class="admin-stat-card">
            <h4 style="font-size: 13px; color: var(--secondary-text); margin-bottom: 8px;">Pending Partner Payouts</h4>
            <h3 style="font-size: 24px; font-weight: 700; color: var(--warning);">₹8,490</h3>
          </div>
        </div>

        <!-- Transactions Table -->
        <div class="admin-table-container">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Transaction ID</th>
                <th>Booking ID</th>
                <th>Customer</th>
                <th>Worker</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#TXN-8092138</td>
                <td>#GOW-902183</td>
                <td>Aniket Rachalwar</td>
                <td class="row-name">Ramesh Kumar</td>
                <td>₹334</td>
                <td>UPI (GPay)</td>
                <td><span class="status-badge" style="background: rgba(34, 197, 94, 0.08); color: var(--success);">Paid</span></td>
                <td>
                  <button class="btn-book" style="background: var(--white); border: 1px solid var(--border-color); color: var(--primary-text); font-size: 11px; padding: 6px 12px;" onclick="alert('Receipt Downloaded')">Receipt</button>
                </td>
              </tr>

              <tr>
                <td>#TXN-8047291</td>
                <td>#GOW-847291</td>
                <td>Sunita Patil</td>
                <td class="row-name">Sohan Singh</td>
                <td>₹499</td>
                <td>Credit Card</td>
                <td><span class="status-badge" style="background: rgba(34, 197, 94, 0.08); color: var(--success);">Paid</span></td>
                <td>
                  <button class="btn-book" style="background: var(--white); border: 1px solid var(--border-color); color: var(--primary-text); font-size: 11px; padding: 6px 12px;" onclick="alert('Receipt Downloaded')">Receipt</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <script src="admin.js"></script>

</body>
</html>
