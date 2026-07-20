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
    const dropzone = document.querySelector(".upload-dropzone");
    if (dropzone) {
        dropzone.addEventListener("click", () => {
            const fileInput = document.createElement("input");
            fileInput.type = "file";
            fileInput.accept = ".pdf,image/*";
            fileInput.click();

            fileInput.addEventListener("change", (e) => {
                const file = e.target.files[0];
                if (file) {
                    dropzone.querySelector("p").innerHTML = `📁 <strong>${escapeHTML(file.name)}</strong> selected successfully!`;
                }
            });
        });
    }

    // 4. SUBMIT APPLICATION
    const onboardingForm = document.getElementById("onboarding-form");
    if (onboardingForm && submitBtn) {
        onboardingForm.addEventListener("submit", (e) => {
            e.preventDefault();
            
            submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Submitting...`;
            submitBtn.disabled = true;

            setTimeout(() => {
                submitBtn.innerHTML = `Submit Application`;
                submitBtn.disabled = false;
                alert("Your GoWorker professional registration application has been submitted! Our admin team will review your details and documents within 48 hours.");
                window.location.href = "worker-dashboard.html";
            }, 2000);
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
});
