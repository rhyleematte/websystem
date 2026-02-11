document.addEventListener("DOMContentLoaded", () => {
    if (window.lucide) lucide.createIcons();

    const toggle = document.getElementById("togglePass");
    const pass = document.getElementById("password");

    if (!toggle || !pass || !window.lucide) return;

    toggle.addEventListener("click", () => {
        const isHidden = pass.type === "password";

        // Toggle password visibility
        pass.type = isHidden ? "text" : "password";

        // Replace icon markup then re-render lucide
        toggle.innerHTML = isHidden
            ? '<i data-lucide="eye-off"></i>'
            : '<i data-lucide="eye"></i>';

        toggle.setAttribute("aria-label", isHidden ? "Hide password" : "Show password");

        lucide.createIcons();
    });
});
