const form = document.getElementById('register-form');
    const msg = document.getElementById('msg');

    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      msg.textContent = 'Enviando...';
      msg.className = 'msg';

      const body = Object.fromEntries(new FormData(form).entries());
      try {
        const res = await fetch('../../server/controller/register.php', {
          method: 'POST',
          headers: { 'Content-Type':'application/json' },
          credentials: 'include',
          body: JSON.stringify(body)
        });
        const text = await res.text();
        const data = JSON.parse(text);

        if (res.ok && data.success) {
          msg.textContent = 'Registro correcto. Redirigiendo...';
          msg.className = 'msg ok';
          // Si tienes el formulario en esta ruta, ajusta si es distinto:
          setTimeout(()=>location.href='formulario.html', 600);
        } else {
          msg.textContent = data.message || 'Registro fallido';
          msg.className = 'msg error';
        }
      } catch (err) {
        msg.textContent = 'No se pudo conectar con el servidor';
        msg.className = 'msg error';
      }
    });