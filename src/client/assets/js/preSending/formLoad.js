const WAREHOUSES_URL = "/src/client/assets/json/preSending/loadForm/warehouses.json"
const DESTINATION_CHECK = document.getElementById("bidalketa")
const DESTINATION_TF = document.getElementById("helmuga")

kargatuBiltegiak(WAREHOUSES_URL, "biltegia")

DESTINATION_CHECK.addEventListener("click", () => {

/**
 * if(DESTINATION_TF.disabled) {
 *      
 *      DESTINATION_TF.disabled = false
 * 
 * } else {
 *      
 *      DESTINATION_TF.disabled = true
 * 
 * } 
 */

    DESTINATION_TF.disabled = !DESTINATION_TF.disabled

})

async function kargatuBiltegiak(url, selectID) {

    try {
         
        response = await fetch(url);
        
        if (!response.ok) {

            throw new Error("Errorea APIarekin konektatzean")

        }

        const options = await response.json()
        const select = document.getElementById(selectID)

        options.forEach(item => {
            
            const option = document.createElement("option")
            option.value = item.id
            option.textContent = item.izena
            select.appendChild(option)

        })

    } catch (e) {

        console.error("Ezin izan da biltegiak kargatu:", e);
    }
}
