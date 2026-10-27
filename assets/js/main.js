/* =====================================================================
   CS Club Website - Shared JavaScript (loaded on every page)
   ===================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // ---------- Mobile navigation toggle ----------
    const navToggle = document.getElementById('navToggle');
    const navLinks = document.getElementById('navLinks');

    if (navToggle && navLinks) {
        navToggle.addEventListener('click', () => {
            // toggle() adds the class if missing, removes it if present, and returns the new state
            const isOpen = navLinks.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', isOpen);
        });
    }
});
