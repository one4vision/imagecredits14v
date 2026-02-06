(function () {
    const lightbox = document.getElementById('lightbox');
    const closeBtn = document.getElementById('lbClose');
    const img = document.getElementById('lbImage');

    function showLightbox(fullSrc, alt) {
        img.src = fullSrc || this.src;
        img.alt = alt || this.alt;
        lightbox.hidden = false;
        lightbox.style.display = 'flex';
        closeBtn.focus();
        document.body.style.overflow = 'hidden';
    }

    function hideLightbox() {
        lightbox.hidden = true;
        lightbox.style.display = '';
        img.src = '';
        document.body.style.overflow = '';
    }

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('lightbox-enabled')) {
            e.preventDefault();
            const fullSrc = e.target.dataset.fullsrc;
            const alt = e.target.alt;
            showLightbox(fullSrc, alt);
        }
    });
    if(closeBtn) {
        closeBtn.addEventListener('click', hideLightbox);
    }
    if(lightbox) {
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) hideLightbox();
        });
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !lightbox.hidden) {
            hideLightbox();
        }
    });
})();
