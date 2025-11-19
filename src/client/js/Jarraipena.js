function projectBase(){
  const p = location.pathname;
  const i = p.indexOf('/src/');
  return i >= 0 ? p.slice(0, i) : '';
}

const IMG_DESCARGA = projectBase() + '../../client/assets/img/descarga.jpg';

function mostrarImagen(estado){
  const box = document.getElementById('imagenContainer');
  if (!box) { console.warn('No existe #imagenContainer'); return; }

  console.log('ESTADO:', estado, 'IMG URL:', IMG_DESCARGA);
  // prueba directa de carga
  const test = new Image();
  test.onload  = () => console.log('OK carga', test.width+'x'+test.height);
  test.onerror = (e) => console.error('ERROR carga', IMG_DESCARGA, e);
  test.src = IMG_DESCARGA;

  const imgTag = `<img src="${IMG_DESCARGA}" alt="descarga" class="imgjarra">`;

  switch (estado) {
    case 'Pendiente':
      box.innerHTML = imgTag;
      break;
    case 'Enviado':
      box.innerHTML = `<div class="dots"></div>` + imgTag;
      break;
    case 'Recibido':
      box.innerHTML = `<div class="dots"></div>` + imgTag;
      break;
    default:
      box.innerHTML = '';
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


