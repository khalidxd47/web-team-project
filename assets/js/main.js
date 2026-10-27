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

    // ---------- Event modal (events.php) ----------
    const modal = document.getElementById('eventModal');
    if (!modal) return; // this page has no modal

    const $ = (id) => document.getElementById(id);

    function openModal(card) {
        // Read the event data stored in the card's data-* attributes
        const data = card.dataset;
        $('modalImage').src = data.image;
        $('modalImage').alt = data.name;
        $('modalCategory').textContent = data.category;
        $('modalTitle').textContent = data.name;
        $('modalDate').textContent = data.date;
        $('modalLocation').textContent = data.location;
        $('modalDescription').textContent = data.description;
        $('modalLink').href = 'details.php?id=' + data.id;

        modal.classList.add('open');
        document.body.style.overflow = 'hidden'; // stop the page scrolling behind the modal
    }

    function closeModal() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.js-event-card').forEach((card) => {
        card.addEventListener('click', () => openModal(card));
    });

    $('modalClose').addEventListener('click', closeModal);

    // Close when clicking the dark background (but not the content box)
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // Close with the Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
    });
});
