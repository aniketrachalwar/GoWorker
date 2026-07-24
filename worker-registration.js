// GoWorker Worker Registration Engine
// worker-registration.js

document.addEventListener("DOMContentLoaded", () => {
    let currentStep = 1;
    const totalSteps = 5;

    const steps = document.querySelectorAll(".onboarding-step-link");
    const stepContents = document.querySelectorAll(".step-content-block");
    const nextBtn = document.getElementById("step-next-btn");
    const prevBtn = document.getElementById("step-prev-btn");
    const submitBtn = document.getElementById("step-submit-btn");

    // 1. NAVIGATION ENGINE
    const updateStepUI = () => {
        // Update vertical step list highlights
        steps.forEach((step, idx) => {
            step.classList.remove("active", "completed");
            const stepNum = idx + 1;
            
            if (stepNum === currentStep) {
                step.classList.add("active");
            } else if (stepNum < currentStep) {
                step.classList.add("completed");
            }
        });

        // Hide/Show step panels
        stepContents.forEach((panel, idx) => {
            const panelStep = idx + 1;
            panel.style.display = (panelStep === currentStep) ? "block" : "none";
        });

        // Toggle button states
        if (currentStep === 1) {
            prevBtn.style.display = "none";
        } else {
            prevBtn.style.display = "inline-flex";
        }

        if (currentStep === totalSteps) {
            nextBtn.style.display = "none";
            submitBtn.style.display = "inline-flex";
        } else {
            nextBtn.style.display = "inline-flex";
            submitBtn.style.display = "none";
        }
    };

    if (nextBtn) {
        nextBtn.addEventListener("click", () => {
            if (validateStep(currentStep)) {
                currentStep = Math.min(totalSteps, currentStep + 1);
                updateStepUI();
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener("click", () => {
            currentStep = Math.max(1, currentStep - 1);
            updateStepUI();
        });
    }

    // Initialize UI on start
    updateStepUI();

    // 2. REQUIRED STEP INPUTS VALIDATOR
    function validateStep(stepNum) {
        const activeBlock = document.querySelector(`.step-content-block[data-step="${stepNum}"]`);
        if (!activeBlock) return true;

        const inputs = activeBlock.querySelectorAll("input[required], select[required], textarea[required]");
        let valid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.style.borderColor = "var(--danger)";
                valid = false;
            } else {
                input.style.borderColor = "var(--border-color)";
            }
        });

        if (!valid) {
            alert("Please fill out all the required fields before proceeding to the next step!");
        }

        return valid;
    }

    // 3. FILE UPLOAD INTERACTION
    const dropzone = document.getElementById("upload-dropzone");
    const fileInput = document.getElementById("id_document_input");
    if (dropzone && fileInput) {
        dropzone.addEventListener("click", () => {
            fileInput.click();
        });
        
        fileInput.addEventListener("change", (e) => {
            if(e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                dropzone.innerHTML = `<i class="fa-solid fa-file-circle-check" style="font-size: 32px; color: var(--success); margin-bottom: 12px;"></i><p style="color:var(--success);font-weight:600;">Uploaded: ${fileName}</p>`;
                dropzone.style.borderColor = "var(--success)";
            }
        });
    }

    // 4. FORM SUBMISSION INTERCEPT
    const onboardingForm = document.getElementById("onboarding-form");
    if (onboardingForm) {
        onboardingForm.addEventListener("submit", (e) => {
            // We allow normal submission to PHP!
            // Just some visual feedback before it navigates away
            if (!validateStep(totalSteps)) {
                e.preventDefault();
                return;
            }
            
            const btn = document.getElementById("step-submit-btn");
            if (btn) {
                btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Submitting...`;
                btn.style.opacity = "0.7";
                btn.style.cursor = "not-allowed";
            }
        });
    }

    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }

    // Restrict all phone number inputs to 10 digits only
    const telInputs = document.querySelectorAll('input[type="tel"]');
    telInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });
    });
});
