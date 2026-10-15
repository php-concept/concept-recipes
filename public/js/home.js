(() => {
    const items = document.querySelectorAll('.trinity-item');
    if (!items.length) {
        return;
    }

    if (!('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('is-in'));
        return;
    }

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-in');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.2 });

    items.forEach((el, i) => {
        el.style.transitionDelay = `${i * 0.12}s`;
        io.observe(el);
    });
})();
