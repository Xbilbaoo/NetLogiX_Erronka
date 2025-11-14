/* Contador carrousel */
var counter = 1
/* Cards en pantalla pequeña */
const cardTitles = document.querySelectorAll(".titulo2")
const cardTexts = document.querySelectorAll(".text")
const mediaQuery = window.matchMedia("(max-width: 660px)")

/* Funcionamiento carrousel */
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
<<<<<<< HEAD
}
=======
}




>>>>>>> 22b6941758a2d8884430c8619782ee79341946a2
