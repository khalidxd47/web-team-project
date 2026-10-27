/* =====================================================================
   Store - Shared JavaScript (loaded on every page)
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

    // ---------- Quick-add modal (home, catalog, details) ----------
    const modal = document.getElementById('quickAddModal');
    if (!modal) return; // this page has no modal

    const $ = (id) => document.getElementById(id);
    let lastTrigger = null; // the button that opened the modal, to return focus to it

    function openModal(button) {
        // Read the product data stored in the button's data-* attributes
        const data = button.dataset;
        $('modalId').value = data.id;
        $('modalImage').src = data.image;
        $('modalImage').alt = data.title;
        $('modalTitle').textContent = data.title;
        $('modalPrice').textContent = data.price;
        $('modalDescription').textContent = data.description;
        $('modalLink').href = 'details.php?id=' + data.id;

        // Quantity can't exceed stock (or 10 per order)
        const stock = Number(data.stock);
        $('modalQty').value = 1;
        $('modalQty').max = Math.min(10, stock);
        $('modalStock').textContent = stock < 10 ? 'Only ' + stock + ' left' : '';

        // Build one radio button per available size
        $('modalSizes').innerHTML = '';
        data.sizes.split(',').forEach((size) => {
            const label = document.createElement('label');
            label.className = 'size-option';
            label.innerHTML = '<input type="radio" name="size" required><span></span>';
            label.querySelector('input').value = size;
            label.querySelector('span').textContent = size;
            $('modalSizes').appendChild(label);
        });

        lastTrigger = button;
        modal.hidden = false;
        requestAnimationFrame(() => modal.classList.add('open')); // next frame, so the transition runs
        document.body.style.overflow = 'hidden'; // stop the page scrolling behind the modal
        $('modalClose').focus();
    }

    function closeModal() {
        modal.classList.remove('open');
        modal.hidden = true;
        document.body.style.overflow = '';
        if (lastTrigger) lastTrigger.focus();
    }

    document.querySelectorAll('.js-quick-add').forEach((button) => {
        button.addEventListener('click', () => openModal(button));
    });

    $('modalClose').addEventListener('click', closeModal);

    // Close when clicking the dark background (but not the content box)
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // Close with the Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.hidden) closeModal();
    });
});
