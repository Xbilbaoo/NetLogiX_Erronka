const WAREHOUSES_URL = "/src/client/assets/json/preSending/loadForm/warehouses.json"
kargatuBiltegiak(WAREHOUSES_URL, "biltegia")

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
            console.log("a")
        })
    } catch (e) {
        console.error("Ezin izan da biltegiak kargatu:", e);
    }
}