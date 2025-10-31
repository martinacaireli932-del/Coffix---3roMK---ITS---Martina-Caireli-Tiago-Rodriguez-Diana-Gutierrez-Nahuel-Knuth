document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.controles-carrusel button').forEach(button => {
        button.addEventListener('click', () => {
            const carruselId = button.getAttribute('data-carrusel-id');
            const carrusel = document.getElementById(carruselId);

            if (!carrusel) return;

            const direccion = button.classList.contains('next-btn') ? 1 : -1;
            
            const desplazamiento = 275 * direccion; 
            
            carrusel.scrollBy({ left: desplazamiento, behavior: 'smooth' });
        });
    });
});