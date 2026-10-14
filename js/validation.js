// Form Validation - College Complaint System
document.addEventListener('DOMContentLoaded', function() {

    // === 1. LOGIN FORM VALIDATION ===
    const loginForm = document.querySelector('form[action=""]'); // login page
    if (loginForm && window.location.href.includes('login')) {
        loginForm.addEventListener('submit', function(e) {
            let email = loginForm.querySelector('input[type="email"]').value.trim();
            let password = loginForm.querySelector('input[type="password"]').value;

            if (!validateEmail(email)) {
                e.preventDefault();
                showAlert("Valid email enter pannu da! ex: student@college.edu", "error");
                return false;
            }
            if (password.length < 6) {
                e.preventDefault();
                showAlert("Password minimum 6 character irukanum!", "error");
                return false;
            }
        });
    }

    // === 2. REGISTER FORM ===
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let name = document.getElementById('name').value.trim();
            let email = document.getElementById('email').value.trim();
            let roll = document.getElementById('roll_no').value.trim();
            let pass = document.getElementById('password').value;
            let confirm = document.getElementById('confirm_password').value;

            if (name.length < 3) {
                e.preventDefault(); showAlert("Name la 3 letter-aachu irukanum da!", "error"); return;
            }
            if (!validateEmail(email)) {
                e.preventDefault(); showAlert("Correct email podu da!", "error"); return;
            }
            if (roll.length < 3) {
                e.preventDefault(); showAlert("Roll No correct-a podu!", "error"); return;
            }
            if (pass.length < 6) {
                e.preventDefault(); showAlert("Password 6+ characters!", "error"); return;
            }
            if (pass!== confirm) {
                e.preventDefault(); showAlert("Password match aagala da! Check pannu!", "error"); return;
            }
        });
    }

    // === 3. COMPLAINT FORM ===
    const complaintForm = document.querySelector('form[enctype="multipart/form-data"]');
    if (complaintForm) {
        complaintForm.addEventListener('submit', function(e) {
            let title = complaintForm.querySelector('input[name="title"]').value.trim();
            let desc = complaintForm.querySelector('textarea[name="description"]').value.trim();
            let category = complaintForm.querySelector('select[name="category"]').value;

            if (title.length < 10) {
                e.preventDefault(); showAlert("Title konjam detailed-a podu - 10+ characters!", "error"); return;
            }
            if (category === "") {
                e.preventDefault(); showAlert("Category select pannu da!", "error"); return;
            }
            if (desc.length < 20) {
                e.preventDefault(); showAlert("Description 20+ characters detailed-a sollu!", "error"); return;
            }
            // File check
            let fileInput = complaintForm.querySelector('input[type="file"]');
            if (fileInput && fileInput.files.length > 0) {
                let file = fileInput.files[0];
                let allowed = ['image/jpeg','image/png','image/jpg','application/pdf'];
                if (!allowed.includes(file.type)) {
                    e.preventDefault(); showAlert("Only JPG, PNG, PDF allowed!", "error"); return;
                }
                if (file.size > 5*1024*1024) {
                    e.preventDefault(); showAlert("File size 5MB kulla irukanum!", "error"); return;
                }
            }
        });

        // Live character count
        let descBox = complaintForm.querySelector('textarea[name="description"]');
        if (descBox) {
            let counter = document.createElement('div');
            counter.style.fontSize = "0.75rem"; counter.style.color = "#94A3B8"; counter.style.marginTop = "5px";
            descBox.parentNode.appendChild(counter);
            descBox.addEventListener('input', () => {
                counter.textContent = `${descBox.value.length} characters - ${descBox.value.length < 20? 'Need more details' : 'Good!'}`;
                counter.style.color = descBox.value.length < 20? "#EF4444" : "#10B981";
            });
        }
    }

    // === HELPER FUNCTIONS ===
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function showAlert(msg, type) {
        // Already irukra alert-a remove pannu
        let oldAlert = document.querySelector('.js-alert');
        if (oldAlert) oldAlert.remove();

        let div = document.createElement('div');
        div.className = `alert js-alert ${type=='error'? 'alert-error' : 'alert-success'}`;
        div.innerHTML = (type=='error'? '⚠️ ' : '✅ ') + msg;
        div.style.cssText = "padding:12px; border-radius:8px; margin-bottom:15px; animation: slideDown 0.3s ease;";

        if (type=='error') div.style.cssText += "background:#FEE2E2; color:#DC2626; border:1px solid #FECACA;";
        else div.style.cssText += "background:#D1FAE5; color:#059669; border:1px solid #A7F3D0;";

        let form = document.querySelector('form');
        form.parentNode.insertBefore(div, form);

        setTimeout(() => { div.remove(); }, 4000);
    }

    // Real-time border color
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === "" && this.hasAttribute('required')) {
                this.style.borderColor = "#EF4444";
            } else {
                this.style.borderColor = "#10B981";
            }
        });
        input.addEventListener('focus', function() {
            this.style.borderColor = "#4F46E5";
        });
    });

});

// Animation
const style = document.createElement('style');
style.textContent = `@keyframes slideDown { from { opacity:0; transform: translateY(-10px); } to { opacity:1; transform: translateY(0); } }`;
document.head.appendChild(style);