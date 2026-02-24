// public/assets/js/dashboard.js
document.addEventListener("DOMContentLoaded", () => {
  if (window.lucide) lucide.createIcons();

  const root = document.documentElement;

  // Dropdown (chevron button)
  const toggle = document.getElementById("profileToggle");
  const menu = document.getElementById("profileDropdown");

  // Theme toggle button (inside dropdown)
  const themeBtn = document.getElementById("themeToggleBtn");

  // Load saved theme
  const savedTheme = localStorage.getItem("theme"); // "dark" | "light"
  if (savedTheme === "dark") root.classList.add("theme-dark");

  const updateThemeBtn = () => {
    if (!themeBtn) return;

    const isDark = root.classList.contains("theme-dark");
    const label = themeBtn.querySelector("span");
    const icon = themeBtn.querySelector("i");

    if (label) label.textContent = isDark ? "Light mode" : "Dark mode";
    if (icon) icon.setAttribute("data-lucide", isDark ? "sun" : "moon");

    if (window.lucide) lucide.createIcons();
  };

  updateThemeBtn();

  if (themeBtn) {
    themeBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      root.classList.toggle("theme-dark");
      localStorage.setItem("theme", root.classList.contains("theme-dark") ? "dark" : "light");
      updateThemeBtn();
    });
  }

  if (!toggle || !menu) return;

  const openMenu = () => {
    menu.classList.add("open");
    toggle.setAttribute("aria-expanded", "true");
  };

  const closeMenu = () => {
    menu.classList.remove("open");
    toggle.setAttribute("aria-expanded", "false");
  };

  toggle.addEventListener("click", (e) => {
    e.stopPropagation();
    menu.classList.contains("open") ? closeMenu() : openMenu();
    if (window.lucide) lucide.createIcons();
  });

  menu.addEventListener("click", (e) => e.stopPropagation());

  document.addEventListener("click", closeMenu);
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeMenu();
  });
});