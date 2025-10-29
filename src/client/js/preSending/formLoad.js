const WAREHOUSES_URL = "/src/client/json/preSending/warehouses.json"

const WAREHOUSE_CHECK = document.getElementById("biltegiratze")
const DESTINATION_TF = document.getElementById("helmuga")
const WAREHOUSE_SELECT = document.getElementById("biltegia")
const WAREHOUSE_DIV = document.getElementById("warehouse-div")


kargatuBiltegiak(WAREHOUSES_URL, WAREHOUSE_SELECT)

WAREHOUSE_CHECK.addEventListener("click", () => {

    if(WAREHOUSE_DIV.style.display == "inline-block") {

        WAREHOUSE_DIV.style.display = "none"

    } else {

        WAREHOUSE_DIV.style.display = "inline-block"

    }

    DESTINATION_TF.disabled = !DESTINATION_TF.disabled

})


/**
 * Funtion to load the names of the warehouses via JSON file.
 * 
 * @param {URL of the JSON file} url 
 * @param {Identifier of the select of the form} selectID 
 */

async function kargatuBiltegiak(url, element) {

    try {
         
        response = await fetch(url);
        
        if (!response.ok) {

            throw new Error("Errorea APIarekin konektatzean")

        }

        const options = await response.json()

        

        options.forEach(item => {
            
            const option = document.createElement("option")
            option.value = item.id
            option.textContent = item.izena
            element.appendChild(option)

        })

    } catch (e) {

        console.error("Ezin izan da biltegiak kargatu:", e);
    }
}
