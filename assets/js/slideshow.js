/* =====================================================================
   Gallery slideshow: Next/Prev buttons, dot navigation, auto-play,
   pause on hover, and keyboard arrows.
   ===================================================================== */

(function () {
    const slideshow = document.getElementById('slideshow');
    if (!slideshow) return;

    const slides = slideshow.querySelectorAll('.slide');
    const dotsBox = document.getElementById('slideDots');
    const DELAY = 4000; // auto-play interval in milliseconds

    let current = 0;
    let timer = null;

    // Create one dot per slide
    slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'dot' + (i === 0 ? ' active' : '');
        dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
        dot.addEventListener('click', () => {
            showSlide(i);
            restart();
        });
        dotsBox.appendChild(dot);
    });
    const dots = dotsBox.querySelectorAll('.dot');

    function showSlide(index) {
        // Modulo wraps around: after the last slide comes the first, and vice versa
        current = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => slide.classList.toggle('active', i === current));
        dots.forEach((dot, i) => dot.classList.toggle('active', i === current));
    }

    const next = () => showSlide(current + 1);
    const prev = () => showSlide(current - 1);

    function start() {
        timer = setInterval(next, DELAY);
    }

    function stop() {
        clearInterval(timer);
    }

    // Restart the timer after a manual click so the slide doesn't change immediately
    function restart() {
        stop();
        start();
    }

    document.getElementById('nextBtn').addEventListener('click', () => { next(); restart(); });
    document.getElementById('prevBtn').addEventListener('click', () => { prev(); restart(); });

    // Pause while the mouse is over the slideshow
    slideshow.addEventListener('mouseenter', stop);
    slideshow.addEventListener('mouseleave', start);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') { next(); restart(); }
        if (e.key === 'ArrowLeft')  { prev(); restart(); }
    });

    start();
})();
