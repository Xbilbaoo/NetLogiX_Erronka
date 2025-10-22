const WAREHOUSES_URL = "/src/client/json/preSending/loadForm/warehouses.json"

const DESTINATION_CHECK = document.getElementById("bidalketa")
const WAREHOUSE_CHECK = document.getElementById("biltegiratze")
const DESTINATION_TF = document.getElementById("helmuga")
const WAREHOUSE_SELECT = document.getElementById("biltegia")


kargatuBiltegiak(WAREHOUSES_URL, WAREHOUSE_SELECT)

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

WAREHOUSE_CHECK.addEventListener("click", () => {

    if(WAREHOUSE_SELECT.style.display == "inline-block") {

        WAREHOUSE_SELECT.style.display = "none"

    } else {

        WAREHOUSE_SELECT.style.display = "inline-block"

    }
})


/**
 * Funtion to load the names of the warehouses via JSON file.
 * 
 * @param {URL of the JSON file} url 
 * @param {Identifier of the select of the form} selectID 
 */

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
