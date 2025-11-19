document.addEventListener("DOMContentLoaded", () => {
    // MAPA
    const mapDiv = document.getElementById("restaurant-map");
    if (mapDiv) {
        const lat = parseFloat(mapDiv.dataset.lat || -33.4489);
        const lng = parseFloat(mapDiv.dataset.lng || -70.6693);
        const name = mapDiv.dataset.name || "Restaurante";

        const map = L.map("restaurant-map").setView([lat, lng], 15);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "© OpenStreetMap",
        }).addTo(map);

        L.marker([lat, lng]).addTo(map).bindPopup(name).openPopup();
    }

    /* FORMULARIO DE RESEÑAS (AJAX + ESTRELLAS) */
document.addEventListener('DOMContentLoaded', function() {
    const reviewForm = document.getElementById('reviewForm');

    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';
            
            fetch('/chilean_food_app/review/addRestaurant', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    clearReviewForm();
                    
                    setTimeout(() => {
                        const url = new URL(window.location.href);
                        url.searchParams.set('new_review', 'true');
                        window.location.href = url.toString();
                    }, 1500);
                } else {
                    showAlert('error', data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Reseña ⭐';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviar Reseña ⭐';
            });
        });

        function clearReviewForm() {
            const textarea = document.getElementById('commentTextarea');
            if (textarea) textarea.value = '';

            const starInputs = document.querySelectorAll('.star-rating input');
            starInputs.forEach(i => i.checked = false);

            const starLabels = document.querySelectorAll('.star-rating .star-label');
            starLabels.forEach(l => l.style.color = '#ccc');
        }

        // Estrellas interactivas
        const starInputs = document.querySelectorAll('.star-rating input');
        const starLabels = document.querySelectorAll('.star-rating .star-label');
        
        starInputs.forEach((input, index) => {
            input.addEventListener('change', function() {
                const rating = parseInt(this.value, 10);
                starLabels.forEach((label, i) => {
                    label.style.color = i < rating ? '#ffcc33' : '#ccc';
                });
            });
        });

        starLabels.forEach((label, index) => {
            label.addEventListener('mouseenter', function() {
                for (let i = 0; i <= index; i++) {
                    starLabels[i].style.color = '#ffcc33';
                }
            });
            label.addEventListener('mouseleave', function() {
                const checked = document.querySelector('.star-rating input:checked');
                const rating = checked ? parseInt(checked.value, 10) : 0;
                starLabels.forEach((l, i) => {
                    l.style.color = i < rating ? '#ffcc33' : '#ccc';
                });
            });
        });

        // Scroll suave si viene de nueva reseña
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('new_review') === 'true') {
            setTimeout(() => {
                const reseñasSection = document.getElementById('reseñas');
                if (reseñasSection) {
                    reseñasSection.scrollIntoView({ behavior: 'smooth' });
                }
                const newUrl = window.location.pathname + window.location.hash;
                window.history.replaceState({}, document.title, newUrl);
            }, 500);
        }
    }
});

/* ALERTAS */
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show mb-3`;
    alertDiv.innerHTML = `
        ${type === 'success' ? '✅' : '❌'} ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const form = document.getElementById('reviewForm');
    if (form) {
        form.parentNode.insertBefore(alertDiv, form);
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
}
/* MODAL RESEÑA DE PLATO */
function openReviewModal(id, name) {
    const dishIdInput = document.getElementById('dishId');
    const dishNameSpan = document.getElementById('dishName');
    const modalEl = document.getElementById('reviewModal');

    if (!dishIdInput || !dishNameSpan || !modalEl) return;

    dishIdInput.value = id;
    dishNameSpan.textContent = name;
    new bootstrap.Modal(modalEl).show();
}
});
