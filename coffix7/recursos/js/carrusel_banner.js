document.addEventListener('DOMContentLoaded', () => {
    const carruselBanner = document.getElementById('carrusel-banner');
    if (!carruselBanner) return;

    let indiceBanner = 0;
    const items = carruselBanner.querySelectorAll('.item-banner');
    const totalItems = items.length;
    const prevBtn = document.querySelector('.prev-banner');
    const nextBtn = document.querySelector('.next-banner');

    function mostrarItem(indice) {
        items.forEach((item, i) => {
            item.classList.remove('activo');
        });
        if (items[indice]) {
            items[indice].classList.add('activo');
        }
    }

    function siguienteBanner() {
        indiceBanner = (indiceBanner + 1) % totalItems;
        mostrarItem(indiceBanner);
    }

    mostrarItem(indiceBanner);

    setInterval(siguienteBanner, 5000);

    if (nextBtn) {
        nextBtn.addEventListener('click', siguienteBanner);
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            indiceBanner = (indiceBanner - 1 + totalItems) % totalItems;
            mostrarItem(indiceBanner);
        });
    }
});