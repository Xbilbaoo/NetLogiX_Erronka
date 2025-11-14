// ========== CARDLOAD.JS ==========

// Enpresa datuak
const enpresak = [
    {
        id: 1,
        izena: 'Express Logistics',
        logoUrl: 'https://via.placeholder.com/80x80/FF6B35/FFFFFF?text=EL',
        denbora: '24h',
        prezioa: 12.50,
        rating: 4.8,
        iruzkinak: 234,
        deskribapena: 'Bidalketa azkarrak eta fidagarriak',
        zerbitzuak: ['pakete_fragila', 'bidalketa', 'aseguru', 'segurtasuna']
    },
    {
        id: 2,
        izena: 'FastShip Pro',
        logoUrl: 'https://via.placeholder.com/80x80/004E89/FFFFFF?text=FS',
        denbora: '48h',
        prezioa: 8.75,
        rating: 4.5,
        iruzkinak: 189,
        deskribapena: 'Prezio merkeak eta kalitate handia',
        zerbitzuak: ['pakete_fragila', 'biltegiratze', 'bidalketa']
    },
    {
        id: 3,
        izena: 'QuickTransport',
        logoUrl: 'https://via.placeholder.com/80x80/1A936F/FFFFFF?text=QT',
        denbora: '72h',
        prezioa: 6.20,
        rating: 4.6,
        iruzkinak: 312,
        deskribapena: 'Ingurumenarekiko kontzientea',
        zerbitzuak: ['pakete_fragila', 'eko', 'aseguru']
    },
    {
        id: 4,
        izena: 'Global Cargo',
        logoUrl: 'https://via.placeholder.com/80x80/C1666B/FFFFFF?text=GC',
        denbora: '24h',
        prezioa: 15.00,
        rating: 4.9,
        iruzkinak: 567,
        deskribapena: 'Nazioarteko bidalketak',
        zerbitzuak: ['pakete_fragila', 'biltegiratze', 'bidalketa', 'aseguru', 'segurtasuna', 'eko']
    },
    {
        id: 5,
        izena: 'EcoTransit',
        logoUrl: 'https://via.placeholder.com/80x80/48A14D/FFFFFF?text=ET',
        denbora: '96h',
        prezioa: 5.50,
        rating: 4.3,
        iruzkinak: 145,
        deskribapena: 'Ingurumenarekiko errespetuzkoa',
        zerbitzuak: ['eko', 'biltegiratze']
    }
];

let currentResults = [];
let currentSort = 'zerbitzuak';

document.addEventListener('searchSubmitted', (event) => {
    const formData = event.detail;
    console.log('Processing search with:', formData);
    showLoadingState();

    // Use a shorter timeout for better UX
    setTimeout(() => {
        try {
            const results = getFilteredResults(formData);
            currentResults = results;
            renderCards(results, formData);
            updateFilters(results.length);
        } catch (error) {
            console.error('Error processing search:', error);
            showToast('Errorea gertatu da emaitzak prozesatzean', 'error');
            renderCards([], formData);
        }
    }, 800);
});

function showLoadingState() {
    const cardsSection = document.getElementById('cards');
    cardsSection.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    <p>Emaitzak bilatzen...</p>
                </div>
            `;
}

function getFilteredResults(formData) {
    let results = [...enpresak];

    const tamainaMultiplier = {
        'txikia': 1.0,
        'ertaina': 1.3,
        'handia': 1.6
    };

    const multiplier = tamainaMultiplier[formData.tamaina] || 1.0;
    const pisuaPrice = formData.pisua * 0.5;
    const zerbitzuakTotal = formData.zerbitzuak.reduce((total, z) => total + z.price, 0);

    results = results.map(enpresa => ({
        ...enpresa,
        prezioFinala: (enpresa.prezioa * multiplier + pisuaPrice + zerbitzuakTotal).toFixed(2),
        selectedServicesCount: calculateSelectedServicesCount(enpresa, formData.zerbitzuak)
    }));

    // Sort by current sort option
    sortResults(results, currentSort);

    return results;
}

function calculateSelectedServicesCount(enpresa, selectedServices) {
    if (!selectedServices || selectedServices.length === 0) return 0;

    const selectedServiceIds = selectedServices.map(service => service.id);
    return enpresa.zerbitzuak.filter(service => selectedServiceIds.includes(service)).length;
}

function sortResults(results, sortBy) {
    switch (sortBy) {
        case 'zerbitzuak':
            results.sort((a, b) => b.selectedServicesCount - a.selectedServicesCount || parseFloat(a.prezioFinala) - parseFloat(b.prezioFinala));
            break;
        case 'prezio':
            results.sort((a, b) => parseFloat(a.prezioFinala) - parseFloat(b.prezioFinala));
            break;
        case 'denbora':
            results.sort((a, b) => {
                const aTime = parseInt(a.denbora);
                const bTime = parseInt(b.denbora);
                return aTime - bTime;
            });
            break;
        case 'rating':
            results.sort((a, b) => b.rating - a.rating);
            break;
    }
}

function renderCards(results, formData) {
    const cardsSection = document.getElementById('cards');

    if (results.length === 0) {
        cardsSection.innerHTML = `
                    <div class="no-results">
                        <p>Ez da emaitzarik aurkitu zure bilaketarako.</p>
                    </div>
                `;
        return;
    }

    cardsSection.innerHTML = results.map((enpresa, index) =>
        createCardHTML(enpresa, index === 0, formData.zerbitzuak)
    ).join('');

    addCardEventListeners();
}

function createCardHTML(enpresa, isBest, selectedServices) {
    const selectedServiceIds = selectedServices ? selectedServices.map(service => service.id) : [];

    return `
                <div class="card ${isBest ? 'best-option' : ''}" data-enpresa-id="${enpresa.id}">
                    ${isBest ? '<div class="best-badge">Aukera onena</div>' : ''}
                    <div class="card-header">
                        <img src="${enpresa.logoUrl}" alt="${enpresa.izena}" class="company-logo">
                        <div class="company-info">
                            <h3 class="company-name">${enpresa.izena}</h3>
                            <div class="rating">
                                <span class="stars">${getStarsHTML(enpresa.rating)}</span>
                                <span class="rating-text">${enpresa.rating} (${enpresa.iruzkinak})</span>
                            </div>
                            <div class="service-badges">
                                ${getServiceBadgesHTML(enpresa, selectedServiceIds)}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="delivery-info">
                            <div class="info-item">
                                <span class="info-label">⏱️ Denbora:</span>
                                <span class="info-value">${enpresa.denbora}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">📦 Oinarrizko prezioa:</span>
                                <span class="info-value">${enpresa.prezioa}€</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">✅ Zerbitzu hautatuak:</span>
                                <span class="info-value">${enpresa.selectedServicesCount}/${selectedServiceIds.length}</span>
                            </div>
                        </div>
                        <div class="price-section">
                            <div class="price-label">Prezio totala:</div>
                            <div class="price-value">${enpresa.prezioFinala}€</div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="select-button" data-enpresa-id="${enpresa.id}">
                            ✅ Hautatu
                        </button>
                    </div>
                </div>
            `;
}

function getServiceBadgesHTML(enpresa, selectedServiceIds) {
    const serviceLabels = {
        pakete_fragila: 'Auzkorra',
        biltegiratze: 'Biltegiratzea',
        bidalketa: 'Bidalketa Azkarra',
        aseguru: 'Asegurua',
        segurtasuna: 'Segurtasun Armatua',
        eko: 'Eko Friendly'
    };

    let badgesHTML = '';

    enpresa.zerbitzuak.forEach(serviceId => {
        const isSelected = selectedServiceIds.includes(serviceId);
        badgesHTML += `<span class="service-badge ${isSelected ? 'available' : ''}">${serviceLabels[serviceId]}</span>`;
    });

    return badgesHTML;
}

function getStarsHTML(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    let starsHTML = '';

    for (let i = 0; i < fullStars; i++) {
        starsHTML += '⭐';
    }

    if (hasHalfStar) {
        starsHTML += '✨';
    }

    return starsHTML;
}

function addCardEventListeners() {
    const buttons = document.querySelectorAll('.select-button');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const empresaId = parseInt(button.dataset.empresaId);
            selectEmpresa(empresaId);
        });
    });
}

function selectEmpresa(empresaId) {
    const enpresa = enpresak.find(e => e.id === empresaId);

    if (!enpresa) return;

    const card = document.querySelector(`[data-enpresa-id="${empresaId}"]`);
    const prezioFinala = card.querySelector('.price-value').textContent;

    const jatorria = document.getElementById('jatorria').value;
    const helmuga = document.getElementById('helmuga').value;
    const pisua = document.getElementById('pisua').value;
    const tamaina = document.getElementById('tamaina').value;

    showToast(`"${enpresa.izena}" enpresarekin eskaera egin da`, 'success');

    console.log('Enpresa hautatuta:', {
        enpresa: enpresa,
        prezioFinala: prezioFinala,
        jatorria: jatorria,
        helmuga: helmuga,
        pisua: pisua,
        tamaina: tamaina
    });
}

function updateFilters(resultsCount) {
    const filters = document.getElementById('filters');
    const resultsCountElement = document.getElementById('results-count');

    if (resultsCount > 0) {
        filters.style.display = 'flex';
        resultsCountElement.textContent = `${resultsCount} emaitza aurkitu dira`;
    } else {
        filters.style.display = 'none';
    }
}

function setupSorting() {
    const sortSelect = document.getElementById('sort');

    sortSelect.addEventListener('change', () => {
        currentSort = sortSelect.value;

        if (currentResults.length > 0) {
            sortResults(currentResults, currentSort);
            renderCards(currentResults);
        }
    });
}

function showToast(message, type) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast';
    toast.classList.add(type, 'show');

    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Auto-submit form on page load for testing
window.addEventListener('load', () => {
    setTimeout(() => {
        document.getElementById('searchForm').dispatchEvent(new Event('submit'));
    }, 500);
});