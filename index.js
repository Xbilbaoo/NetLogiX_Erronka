window.onclick = function(event) {
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

window.onclick = function(event) {
    if (!event.target.matches('.dropbtn') && !event.target.matches('.img_menu')) {
        const dropdownContent = document.getElementById("myDropdown");
        if (dropdownContent && dropdownContent.classList.contains('show')) {
            dropdownContent.classList.remove('show');
        }
    }
}

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
