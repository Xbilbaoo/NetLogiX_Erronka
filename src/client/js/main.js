var counter = 1
const cardTitles = document.querySelectorAll(".titulo2")
const cardTexts = document.querySelectorAll(".text")
const mediaQuery = window.matchMedia("(max-width: 660px)")

console.log(cardTitles)
setInterval(function () {
    document.getElementById('radio' + counter).checked = true
    counter++
    if (counter > 3) {
        counter = 1
    }
}, 7000)

checkMedia(mediaQuery)
mediaQuery.addListener(checkMedia)

document.querySelectorAll(".send-button").forEach(item => {

    item.addEventListener('click', () => {

        document.getElementById("session-popup").classList.add('load')
        document.getElementById("login-box").classList.add

    });
})
document.querySelectorAll(".saioa-hasi").forEach(item => {

    item.addEventListener('click', () => {

        document.getElementById("session-popup").classList.add('load')
        document.getElementById("login-box").classList.add

    });
})

document.getElementById("close").addEventListener('click', () => {

    document.getElementById("session-popup").classList.remove('load')

});

function checkMedia(media) {

    if (media.matches) {

        cardTitles.forEach((item, index) => {

            cardTexts[index].style.display = "none"

            item.addEventListener("click", (e) => {

                e.preventDefault()


                if (cardTexts[index].style.display === "none") {

                    cardTexts[index].style.display = "block"

                } else {

                    cardTexts[index].style.display = "none"

                }
            })
        })
    } else {

        cardTexts.forEach(item => {

            item.style.display = "block"
        })

    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Si el usuario ya tiene un token, lo mandamos al escritorio
    if (localStorage.getItem('authToken')) {
        window.location.href = 'desktop.html';
        return;
    }

    const loginForm = document.getElementById('login-form');
    const loginButton = document.getElementById('login-btn');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Mostramos un estado de carga
        loginButton.disabled = true;
        loginButton.textContent = 'Saioa hasten...';

        const username = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            // Llamamos a nuestra nueva API de login
            const response = await fetch('../server/controller/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Server returned non-JSON:', text);
                alert('Error del servidor. Por favor, intenta de nuevo.');
                return;
            }

            const data = await response.json();

            if (data.success) {
                // ¡Éxito! Guardamos el token y los datos del usuario en localStorage
                localStorage.setItem('authToken', data.token);
                localStorage.setItem('userInfo', JSON.stringify(data.user));

                // Redirigimos al escritorio
                window.location.href = 'pages/formulario.html';
            } else {
                console.error('Login failed:', data.message);
                alert('Error: ' + (data.message || 'Credenciales incorrectas'));
            }
        } catch (error) {
            console.error('Error en el fetch:', error);
            alert('Error de conexión. Por favor, intenta de nuevo.');
        } finally {
            // Restauramos el botón
            loginButton.disabled = false;
            loginButton.textContent = 'Saioa hasi';
        }
    });
});




