/*Cerrar y abrir menu */
document.addEventListener('DOMContentLoaded', function() {
    
    // Seleccionar elementos
    const dropbtn = document.querySelector('.dropbtn');
    const dropdownContent = document.querySelector('.dropdown-content');
    const ekisBtn = document.querySelector('.ekis');
    
    
    // Abrir menú
    dropbtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropdownContent.style.display = 'block';
    });
    
    // Cerrar menú al hacer click fuera de él
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            dropdownContent.style.display = 'none';
        }
        else if(window.innerWidth <= 530) {
        dropdownContent.style.display = 'none';{

        }
    }});
    // Cerrar menú solo en pantallas pequeñas
    ekisBtn.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    if (window.innerWidth <= 530) {
        dropdownContent.style.display = 'none';
    }
});
    
});
