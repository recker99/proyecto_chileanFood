const categoryFilters = ["Todos", "Desayuno", "Almuerzo", "Cena", "Postres", "Bebidas"];
const regionFilters = ["Todo Chile", "Norte", "Centro", "Sur"];
const categoryContainer = document.getElementById("category-filter-container");
const regionContainer = document.getElementById("region-filter-container");

function renderFilters(container, filters, activeClass) {
    filters.forEach(f => {
        const btn = document.createElement("button");
        btn.textContent = f;
        btn.className = "border px-3 py-2 rounded-lg text-sm hover:bg-primary hover:text-white transition";
        btn.onclick = () => setActive(container, btn, activeClass);
        container.appendChild(btn);
    });
}

function setActive(container, btn, activeClass) {
    container.querySelectorAll("button").forEach(b => b.classList.remove(activeClass));
    btn.classList.add(activeClass);
}

renderFilters(categoryContainer, categoryFilters, "bg-primary text-white");
renderFilters(regionContainer, regionFilters, "bg-tertiary-color text-white");

function openModal(nombre, restaurante) {
    alert(`Plato: ${nombre}\nRestaurante: ${restaurante}`);
}
