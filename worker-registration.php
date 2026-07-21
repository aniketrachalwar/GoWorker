<?php
/**
 * GoWorker - Become a Professional Partner (Worker Registration)
 */
require_once __DIR__ . '/includes/functions.php';
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
      <form id="onboarding-form" action="#">
        <!-- Step 1: Personal Info -->
        <div class="step-content-block" data-step="1">
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; color: var(--dark-navy);">Personal Information</h3>
          <div class="form-grid">
            <div class="form-group form-grid-full">
              <label>Full Name</label>
              <input type="text" class="form-input" style="padding-left: 16px;" placeholder="Full Name as per Government ID" required autocomplete="name">
            </div>
            <div class="form-group">
              <label>Mobile Number</label>
              <input type="tel" class="form-input" style="padding-left: 16px;" placeholder="9876543210" required autocomplete="tel">
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" class="form-input" style="padding-left: 16px;" placeholder="you@example.com" required autocomplete="email">
            </div>
          </div>
        </div>

        <!-- Step 2: Address Info -->
        <div class="step-content-block" data-step="2" style="display: none;">
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; color: var(--dark-navy);">Service Address</h3>
          <div class="form-grid">
            <div class="form-group form-grid-full">
              <label>Flat / House No. / Building Name</label>
              <input type="text" class="form-input" style="padding-left: 16px;" placeholder="Flat / House No. / Building Name" required>
            </div>
            <div class="form-group">
              <label>City / Location</label>
              <input type="text" class="form-input" style="padding-left: 16px;" placeholder="e.g. Pune" required>
            </div>
            <div class="form-group">
              <label>Pincode</label>
              <input type="text" class="form-input" style="padding-left: 16px;" placeholder="411016" required>
            </div>
          </div>
        </div>

        <!-- Step 3: Professional Info -->
        <div class="step-content-block" data-step="3" style="display: none;">
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; color: var(--dark-navy);">Professional Information</h3>
          <div class="form-grid">
            <div class="form-group">
              <label>Service Category</label>
              <select class="form-input" style="padding-left: 16px;" required>
                <option value="electrician"><?php echo e(__('cat_electrician')); ?></option>
                <option value="plumber"><?php echo e(__('cat_plumber')); ?></option>
                <option value="carpenter"><?php echo e(__('cat_carpenter')); ?></option>
                <option value="painter"><?php echo e(__('cat_painter')); ?></option>
                <option value="cleaner"><?php echo e(__('cat_cleaner')); ?></option>
                <option value="appliance"><?php echo e(__('cat_appliance')); ?></option>
                <option value="mechanic"><?php echo e(__('cat_mechanic')); ?></option>
                <option value="mason">Mason (Mistri)</option>
                <option value="construction_labour">Construction Labour</option>
                <option value="helper_labour">Helper Labour</option>
                <option value="tile_fitter">Tile Fitter</option>
                <option value="pop_worker">POP Worker</option>
                <option value="painter_labour">Painter Labour</option>
                <option value="concrete_worker">Concrete Worker</option>
                <option value="scaffolding_worker">Scaffolding Worker</option>
                <option value="wireman">Wireman</option>
                <option value="cctv_installer">CCTV Installer</option>
                <option value="inverter_technician">Inverter Technician</option>
                <option value="solar_panel_technician">Solar Panel Technician</option>
                <option value="borewell_technician">Borewell Technician</option>
                <option value="water_tank_cleaner">Water Tank Cleaner</option>
                <option value="furniture_assembler">Furniture Assembler</option>
                <option value="modular_furniture_installer">Modular Furniture Installer</option>
                <option value="house_cleaner">House Cleaner</option>
                <option value="deep_cleaning_service">Deep Cleaning Service</option>
                <option value="sofa_cleaner">Sofa Cleaner</option>
                <option value="carpet_cleaner">Carpet Cleaner</option>
                <option value="bathroom_cleaner">Bathroom Cleaner</option>
                <option value="ac_technician">AC Technician</option>
                <option value="refrigerator_repair">Refrigerator Repair</option>
                <option value="washing_machine_repair">Washing Machine Repair</option>
                <option value="tv_repair">TV Repair</option>
                <option value="microwave_repair">Microwave Repair</option>
                <option value="water_purifier_repair">Water Purifier Repair</option>
                <option value="gardener">Gardener</option>
                <option value="pest_control">Pest Control</option>
                <option value="security_guard">Security Guard</option>
                <option value="driver">Driver</option>
                <option value="cook">Cook</option>
                <option value="maid">Maid</option>
                <option value="babysitter">Babysitter</option>
                <option value="elder_care_assistant">Elder Care Assistant</option>
                <option value="welder">Welder</option>
                <option value="fabricator">Fabricator</option>
                <option value="steel_worker">Steel Worker</option>
                <option value="aluminium_worker">Aluminium Worker</option>
                <option value="loader">Loader</option>
                <option value="unloader">Unloader</option>
                <option value="packers_movers">Packers & Movers</option>
                <option value="tempo_service">Tempo Service</option>
                <option value="truck_helper">Truck Helper</option>
                <option value="bike_repair">Bike Repair</option>
                <option value="car_washing">Car Washing</option>
                <option value="puncture_repair">Puncture Repair</option>
                <option value="computer_repair">Computer Repair</option>
                <option value="laptop_repair">Laptop Repair</option>
                <option value="mobile_repair">Mobile Repair</option>
                <option value="network_technician">Network Technician</option>
                <option value="farm_labour">Farm Labour</option>
                <option value="tractor_driver">Tractor Driver</option>
                <option value="irrigation_worker">Irrigation Worker</option>
                <option value="dairy_worker">Dairy Worker</option>
                <option value="photographer">Photographer</option>
                <option value="videographer">Videographer</option>
                <option value="dj_service">DJ Service</option>
                <option value="event_decorator">Event Decorator</option>
                <option value="beautician">Beautician</option>
                <option value="hair_stylist">Hair Stylist</option>
                <option value="makeup_artist">Makeup Artist</option>
                <option value="mehendi_artist">Mehendi Artist</option>
              </select>
            </div>
            <div class="form-group">
              <label>Years of Experience</label>
              <input type="number" class="form-input" style="padding-left: 16px;" placeholder="e.g. 5" min="0" required>
            </div>
            <div class="form-group">
              <label>Hourly Service Charge (₹)</label>
              <input type="number" class="form-input" style="padding-left: 16px;" placeholder="e.g. 299" min="0" required>
            </div>
            <div class="form-group">
              <label>Brief Bio / Description</label>
              <textarea class="form-input" style="padding-left: 16px; padding-top: 12px; height: 46px; min-height: 46px; resize: vertical;" placeholder="Tell customers about your skills..." required></textarea>
            </div>
          </div>
        </div>

        <!-- Step 4: ID Verification -->
        <div class="step-content-block" data-step="4" style="display: none;">
          <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; color: var(--dark-navy);">Identity Verification Upload</h3>
          <div class="form-group" style="margin-bottom: 20px;">
            <label>Select Identity Document Type</label>
            <select class="form-input" style="padding-left: 16px;" required>
              <option value="aadhaar">Aadhaar Card</option>
              <option value="pan">PAN Card</option>
              <option value="dl">Driving License</option>
            </select>
          </div>
          
          <div class="upload-dropzone" style="border: 2px dashed var(--border-color); padding: 40px; border-radius: var(--radius-md); text-align: center; cursor: pointer; color: var(--secondary-text);">
            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--primary); margin-bottom: 12px;"></i>
            <p>Drag & Drop or Click to upload Aadhaar Front & Back PDF / Image</p>
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
