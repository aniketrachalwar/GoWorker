<?php
/**
 * GoWorker - Become a Professional Partner (Worker Registration)
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect to profile if already logged in as a worker
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'worker') {
    redirect('profile.php');
}

$categories = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database query failed: " . $e->getMessage());
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        $errors[] = "Security validation failed. Please try again.";
    } else {
        $user_id = $_SESSION['user_id'] ?? null;
        
        try {
            $pdo->beginTransaction();
            
            if (!$user_id) {
                $full_name = trim($_POST['full_name'] ?? '');
                $mobile = trim($_POST['mobile'] ?? '');
                $email = trim($_POST['email'] ?? '');
                
                if (empty($full_name) || empty($mobile) || empty($email)) {
                    throw new Exception("Please provide all personal details.");
                }
                
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
                $stmt->execute([$email, $mobile]);
                if ($stmt->fetch()) {
                    throw new Exception("An account with this email or mobile number already exists. Please login first.");
                }
                
                $random_pass = bin2hex(random_bytes(6));
                $hashed_pass = password_hash($random_pass, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, user_type) VALUES (?, ?, ?, ?, 'worker')");
                $stmt->execute([$full_name, $email, $mobile, $hashed_pass]);
                $user_id = $pdo->lastInsertId();
                
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_type'] = 'worker';
                $_SESSION['name'] = $full_name;
            } else {
                $stmt = $pdo->prepare("UPDATE users SET user_type = 'worker' WHERE id = ?");
                $stmt->execute([$user_id]);
                $_SESSION['user_type'] = 'worker';
            }
            
            $id_type = $_POST['id_type'] ?? '';
            $id_document_path = null;
            
            if (isset($_FILES['id_document']) && $_FILES['id_document']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/uploads/identity/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $file_name = time() . '_' . basename($_FILES['id_document']['name']);
                $target_path = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES['id_document']['tmp_name'], $target_path)) {
                    $id_document_path = 'uploads/identity/' . $file_name;
                } else {
                    throw new Exception("Failed to upload identity document.");
                }
            } else {
                throw new Exception("Identity document is required.");
            }
            
            $address_line1 = $_POST['address_line1'] ?? '';
            $location = $_POST['location'] ?? '';
            $pincode = $_POST['pincode'] ?? '';
            $category_id = $_POST['category_id'] ?? 1;
            $experience_years = $_POST['experience_years'] ?? 0;
            $hourly_rate = $_POST['hourly_rate'] ?? 0;
            $bio = $_POST['bio'] ?? '';
            
            $stmt = $pdo->prepare("
                INSERT INTO worker_profiles (user_id, category_id, bio, hourly_rate, location, experience_years, id_document, id_type) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id, $category_id, $bio, $hourly_rate, $location, $experience_years, $id_document_path, $id_type
            ]);
            
            $pdo->commit();
            set_flash('success', "Your professional profile has been successfully created!");
            redirect("worker-dashboard.php");
            
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="worker-registration.css">

<!-- ================= ONBOARDING MAIN BANNER ================= -->
<section style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); color: var(--white); padding: 48px 0; text-align: center;">
  <div class="container">
    <h1 style="color: var(--white); font-size: 32px; margin-bottom: 8px;">Become a GoWorker Professional</h1>
    <p style="color: rgba(255,255,255,0.85); font-size: 15px; max-width: 600px; margin: 0 auto;">Join thousands of trusted local partners and build your business today.</p>
  </div>
</section>

<!-- ================= ONBOARDING LAYOUT ================= -->
<main class="container" style="min-height: 80vh;">
  <div class="onboarding-layout">
    <!-- LEFT STEPPERS COLUMN -->
    <aside class="onboarding-steppers">
      <ul class="onboarding-steps-list">
        <li class="onboarding-step-link active">
          <span class="step-badge">1</span>
          <span>Personal Info</span>
        </li>
        <li class="onboarding-step-link">
          <span class="step-badge">2</span>
          <span>Service Address</span>
        </li>
        <li class="onboarding-step-link">
          <span class="step-badge">3</span>
          <span>Professional info</span>
        </li>
        <li class="onboarding-step-link">
          <span class="step-badge">4</span>
          <span>ID Verification</span>
        </li>
        <li class="onboarding-step-link">
          <span class="step-badge">5</span>
          <span>Review & Submit</span>
        </li>
      </ul>
    </aside>

    <!-- CENTER FORM COLUMN (70%) -->
    <section class="onboarding-form-area" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm);">
      
      <?php if (!empty($errors)): ?>
          <div class="alert alert-danger" style="margin-bottom: 24px;">
              <ul style="margin: 0; padding-left: 20px;">
                  <?php foreach ($errors as $err): ?>
                      <li><?php echo e($err); ?></li>
                  <?php endforeach; ?>
              </ul>
          </div>
      <?php endif; ?>

      <form id="onboarding-form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <!-- Step 1: Personal Info -->
        <div class="step-content-block" data-step="1">
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; color: var(--dark-navy);">Personal Information</h3>
          <div class="form-grid">
            <div class="form-group form-grid-full">
              <label>Full Name</label>
              <input type="text" name="full_name" class="form-input" style="padding-left: 16px;" placeholder="Full Name as per Government ID" value="<?php echo e($_SESSION['name'] ?? ''); ?>" required autocomplete="name">
            </div>
            <div class="form-group">
              <label>Mobile Number</label>
              <input type="tel" name="mobile" class="form-input" style="padding-left: 16px;" placeholder="9876543210" required autocomplete="tel">
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" class="form-input" style="padding-left: 16px;" placeholder="you@example.com" required autocomplete="email">
            </div>
          </div>
        </div>

        <!-- Step 2: Address Info -->
        <div class="step-content-block" data-step="2" style="display: none;">
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; color: var(--dark-navy);">Service Address</h3>
          <div class="form-grid">
            <div class="form-group form-grid-full">
              <label>Flat / House No. / Building Name</label>
              <input type="text" name="address_line1" class="form-input" style="padding-left: 16px;" placeholder="Flat / House No. / Building Name" required>
            </div>
            <div class="form-group">
              <label>City / Location</label>
              <input type="text" name="location" class="form-input" style="padding-left: 16px;" placeholder="e.g. Pune" required>
            </div>
            <div class="form-group">
              <label>Pincode</label>
              <input type="text" name="pincode" class="form-input" style="padding-left: 16px;" placeholder="411016" required>
            </div>
          </div>
        </div>

        <!-- Step 3: Professional Info -->
        <div class="step-content-block" data-step="3" style="display: none;">
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; color: var(--dark-navy);">Professional Information</h3>
          <div class="form-grid">
            <div class="form-group">
              <label>Service Category</label>
              <select name="category_id" class="form-input" style="padding-left: 16px;" required>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo e($cat['id']); ?>"><?php echo e($cat['name']); ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="1">Electrician</option>
                    <option value="2">Plumber</option>
                    <option value="3">Carpenter</option>
                    <option value="4">Painter</option>
                    <option value="5">Cleaner</option>
                    <option value="6">Appliance Repair</option>
                    <option value="7">Mechanic</option>
                <?php endif; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Years of Experience</label>
              <input type="number" name="experience_years" class="form-input" style="padding-left: 16px;" placeholder="e.g. 5" min="0" required>
            </div>
            <div class="form-group">
              <label>Hourly Service Charge (₹)</label>
              <input type="number" name="hourly_rate" class="form-input" style="padding-left: 16px;" placeholder="e.g. 299" min="0" required>
            </div>
            <div class="form-group form-grid-full">
              <label>Brief Bio / Description</label>
              <textarea name="bio" class="form-input" style="padding-left: 16px; padding-top: 12px; height: 46px; min-height: 46px; resize: vertical;" placeholder="Tell customers about your skills..." required></textarea>
            </div>
          </div>
        </div>

        <!-- Step 4: ID Verification -->
        <div class="step-content-block" data-step="4" style="display: none;">
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; color: var(--dark-navy);">Identity Verification Upload</h3>
          <div class="form-group" style="margin-bottom: 20px;">
            <label>Select Identity Document Type</label>
            <select name="id_type" class="form-input" style="padding-left: 16px;" required>
              <option value="aadhaar">Aadhaar Card</option>
              <option value="pan">PAN Card</option>
              <option value="dl">Driving License</option>
            </select>
          </div>
          
          <input type="file" name="id_document" id="id_document_input" accept=".pdf,image/*" style="display: none;" required>
          <div class="upload-dropzone" id="upload-dropzone" style="border: 2px dashed var(--border-color); padding: 40px; border-radius: var(--radius-md); text-align: center; cursor: pointer; color: var(--secondary-text); transition: all 0.3s ease;">
            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--primary); margin-bottom: 12px;"></i>
            <p>Drag & Drop or Click to upload Front & Back PDF / Image</p>
          </div>
        </div>

        <!-- Step 5: Review & Submit -->
        <div class="step-content-block" data-step="5" style="display: none;">
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; color: var(--dark-navy);">Review & Submit Details</h3>
          <p style="font-size: 14px; color: var(--secondary-text); margin-bottom: 20px;">Please check all the uploaded parameters before confirming. Once submitted, your profile will go into verification pipeline.</p>
          <div style="background: var(--light-bg); padding: 20px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 13px;">
            <p style="margin-bottom: 6px;">✓ Government Identity Document Attached</p>
            <p style="margin-bottom: 6px;">✓ Service coverage matches primary location</p>
            <p style="margin-bottom: 0;">✓ Certification portfolio details loaded</p>
          </div>
          <label class="checkbox-container">
            <input type="checkbox" required>
            I agree to the Terms & Conditions and safety policies.
          </label>
        </div>

        <!-- Action buttons stepper -->
        <div style="display: flex; justify-content: space-between; margin-top: 32px; border-top: 1px solid var(--border-color); padding-top: 20px;">
          <button type="button" id="step-prev-btn" class="btn-book" style="background: var(--white); border: 1.5px solid var(--border-color); color: var(--primary-text); display: none;">Back</button>
          <span style="flex: 1;"></span>
          <button type="button" id="step-next-btn" class="btn-book">Continue Step <i class="fa-solid fa-arrow-right"></i></button>
          <button type="submit" id="step-submit-btn" class="btn-book" style="background: var(--success); display: none;">Submit Application</button>
        </div>
      </form>
    </section>

    <!-- RIGHT SIDE COLUMN -->
    <aside class="onboarding-right-panel">
      <div class="benefits-card" style="background: var(--white); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
        <h4 style="color: var(--dark-navy); margin-bottom: 16px;">Why join GoWorker?</h4>
        <ul class="benefits-list" style="list-style: none; padding-left: 0; display: flex; flex-direction: column; gap: 12px; font-size: 14px; color: var(--secondary-text);">
          <li><i class="fa-solid fa-circle-check" style="color: var(--success); margin-right: 8px;"></i> More local jobs</li>
          <li><i class="fa-solid fa-circle-check" style="color: var(--success); margin-right: 8px;"></i> Flexible timings</li>
          <li><i class="fa-solid fa-circle-check" style="color: var(--success); margin-right: 8px;"></i> Direct digital payments</li>
          <li><i class="fa-solid fa-circle-check" style="color: var(--success); margin-right: 8px;"></i> Partner insurance benefits</li>
        </ul>
      </div>
    </aside>
  </div>
</main>

<script src="worker-registration.js"></script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
