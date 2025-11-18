function mostrarImagen() {

  const estado = document.getElementById("imagen").value; 
  const imagenContainer = document.getElementById("imagenContainer");
  imagenContainer.innerHTML = "";


  let imagen = "<img src='./assets/img/descarga.jpg' class='imgjarra'>"; 

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


