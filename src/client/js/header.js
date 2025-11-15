window.onclick = function (event) {
    if (!event.target.matches('.dropbtn') && !event.target.matches('.img_menu')) {
        const dropdownContent = document.getElementById("myDropdown");

        if (dropdownContent && dropdownContent.classList.contains('show')) {
            dropdownContent.classList.remove('show');
        }
    }
}
function toggleDropdown() {
    document.getElementById("myDropdown").classList.toggle("show");
}

window.onclick = function (event) {
    if (!event.target.matches('.dropbtn') && !event.target.matches('.img_menu')) {
        const dropdownContent = document.getElementById("myDropdown");
        if (dropdownContent && dropdownContent.classList.contains('show')) {
            dropdownContent.classList.remove('show');
        }
    }
}
/*Cerrar y abrir menu */
document.addEventListener('DOMContentLoaded', function () {

    // Seleccionar elementos
    const dropbtn = document.querySelector('.dropbtn');
    const dropdownContent = document.querySelector('.dropdown-content');
    const ekisBtn = document.querySelector('.ekis');


    // Abrir menú
    dropbtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropdownContent.style.display = 'block';
    });

    // Cerrar menú al hacer click fuera de él
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown')) {
            dropdownContent.style.display = 'none';
        }
        else if (window.innerWidth <= 530) {
            dropdownContent.style.display = 'none'; {

            }
        }
    });
    // Cerrar menú solo en pantallas pequeñas
    ekisBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (window.innerWidth <= 530) {
            dropdownContent.style.display = 'none';
        }
    });

    document.getElementById("tlf").addEventListener("keypress", (l) => {
        const caracter = String.fromCharCode(l.charCode); // Obtiene el carácter

        // Comprueba si NO es un número (si lo es, es distinto de una letra)
        if (isNaN(parseInt(caracter))) {
            l.preventDefault(); // Evita que se escriba la letra
        }


    })


});

function mostrarImagen() {

    const estado = document.getElementById("imagen").value;
    const imagenContainer = document.getElementById("imagenContainer");
    imagenContainer.innerHTML = "";


    let imagen = "<img src='img/descarga.jpg' class='imgjarra'>";

    switch (estado) {
        case "Pendiente":
            imagenContainer.innerHTML = imagen;
            break;
        case "Enviado":
            imagenContainer.innerHTML = "<div class='dots'>   </div>" + imagen + "<div class='dots'>   </div>";
            break;
        case "Recibido":
            imagenContainer.innerHTML = "<div class='dots'>   </div>" + imagen;
            break;
        default:
            imagenContainer.innerHTML = "";
    }
}