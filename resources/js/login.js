document.addEventListener("DOMContentLoaded", () => {
    // 0) Remove any social login UI if present
    document.querySelectorAll(".social-btn, .divider").forEach(el => el.remove());

    // ===== Sections =====
    const loginSection = document.getElementById("login-section");
    const aboutSection = document.getElementById("about-section");

    // ===== Header Links (smooth scroll) =====
    const aboutLink = document.getElementById("aboutLink");
    if (aboutLink && aboutSection) {
        aboutLink.addEventListener("click", (e) => {
            e.preventDefault();
            aboutSection.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    }

    // Logo scrolls to login section (not top)
    const topLink = document.getElementById("topLink");
    if (topLink && loginSection) {
        topLink.style.cursor = "pointer";
        topLink.addEventListener("click", (e) => {
            // prevent default anchor jump so smooth scroll always happens
            e.preventDefault();
            loginSection.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    }

    // CTA button scroll to login section (if present)
    const ctaBtn = document.querySelector(".cta-btn");
    if (ctaBtn && loginSection) {
        ctaBtn.addEventListener("click", (e) => {
            e.preventDefault();
            loginSection.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    }

    // ===== Toggle Login <-> Register (one blade) =====
    const loginForm = document.getElementById("loginForm");
    const registerForm = document.getElementById("registerForm");

    // Links/buttons you must add in your Blade:
    // <a href="#" id="showRegisterLink">Create an account</a>
    // <a href="#" id="showLoginLink">Sign in</a>
    const showRegisterLink = document.getElementById("showRegisterLink");
    const showLoginLink = document.getElementById("showLoginLink");

    function showRegister() {
        if (!loginForm || !registerForm) return;
        loginForm.style.display = "none";
        registerForm.style.display = "block";

        // keep user centered on the form area
        if (loginSection) loginSection.scrollIntoView({ behavior: "smooth", block: "start" });

        // focus first register input if exists
        const first = registerForm.querySelector("input, select, textarea");
        if (first) first.focus();
    }

    function showLogin() {
        if (!loginForm || !registerForm) return;
        registerForm.style.display = "none";
        loginForm.style.display = "block";

        if (loginSection) loginSection.scrollIntoView({ behavior: "smooth", block: "start" });

        const first = loginForm.querySelector("input, select, textarea");
        if (first) first.focus();
    }

    if (showRegisterLink) {
        showRegisterLink.addEventListener("click", (e) => {
            e.preventDefault();
            showRegister();
        });
    }

    if (showLoginLink) {
        showLoginLink.addEventListener("click", (e) => {
            e.preventDefault();
            showLogin();
        });
    }

    // Optional: if you want the header "Sign Up" button to toggle register
    // add id="signupNav" to the Sign Up link in header, then this will work:
    const signupNav = document.getElementById("signupNav");
    if (signupNav && loginSection) {
        signupNav.addEventListener("click", (e) => {
            e.preventDefault();
            showRegister();
        });
    }

    // ===== Password toggle for LOGIN =====
    const passInput = document.getElementById("password");
    const toggleBtn = document.getElementById("togglePassBtn");
    if (passInput && toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            passInput.type = (passInput.type === "password") ? "text" : "password";
        });
    }

    // ===== Basic validation + disable submit button (LOGIN) =====
    const email = document.getElementById("email");
    const signInBtn = document.getElementById("signInBtn");

    if (loginForm && email && passInput && signInBtn) {
        loginForm.addEventListener("submit", (e) => {
            const emailVal = email.value.trim();
            const passVal = passInput.value.trim();

            if (!emailVal || !passVal) {
                e.preventDefault();
                alert("Please enter both email and password.");
                return;
            }

            signInBtn.disabled = true;
            signInBtn.textContent = "Signing in...";
        });
    }
});
