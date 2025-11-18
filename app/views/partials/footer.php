<?php
/**
 * partials/footer.php
 * Estructura de pie de página para cerrar la aplicación.
 * Incluye la sección de navegación de apoyo, enlaces legales, redes sociales y scripts finales.
 */
?>

    <!-- Sección Footer -->
    <footer class="bg-gray-800 text-white pt-10 pb-6 border-t border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                
                <!-- Columna 1: Navegación Rápida -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-secondary-color uppercase">Navegación Rápida</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/chilean_food_app/" class="hover:text-red-400 transition duration-150">Inicio</a></li>
                        <li><a href="/chilean_food_app/menu" class="hover:text-red-400 transition duration-150">La Carta (Filtros)</a></li>
                        <li><a href="/chilean_food_app/zones" class="hover:text-red-400 transition duration-150">Zonas Gastronómicas</a></li>
                        <li><a href="/chilean_food_app/restaurants" class="hover:text-red-400 transition duration-150">Restaurantes Destacados</a></li>
                    </ul>
                </div>

                <!-- Columna 2: Recursos y Cultura -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-secondary-color uppercase">Cultura</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/chilean_food_app/history" class="hover:text-red-400 transition duration-150">Historia de la Cocina</a></li>
                        <li><a href="/chilean_food_app/ingredients" class="hover:text-red-400 transition duration-150">Ingredientes Chilenos</a></li>
                        <li><a href="/chilean_food_app/blog" class="hover:text-red-400 transition duration-150">Blog de Recetas</a></li>
                        <li><a href="/chilean_food_app/faq" class="hover:text-red-400 transition duration-150">Preguntas Frecuentes</a></li>
                    </ul>
                </div>

                <!-- Columna 3: Legal y Soporte -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-secondary-color uppercase">Legal</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/chilean_food_app/terms" class="hover:text-red-400 transition duration-150">Términos de Uso</a></li>
                        <li><a href="/chilean_food_app/privacy" class="hover:text-red-400 transition duration-150">Política de Privacidad</a></li>
                        <li><a href="/chilean_food_app/sitemap" class="hover:text-red-400 transition duration-150">Mapa del Sitio</a></li>
                    </ul>
                </div>

                <!-- Columna 4: Contacto y Redes Sociales -->
                <div>
                    <h3 class="text-lg font-bold mb-4 text-secondary-color uppercase">Contacto</h3>
                    <p class="text-sm mb-3">Email: info@saborchileno.cl</p>
                    <p class="text-sm mb-5">Teléfono: +56 9 1234 5678</p>
                    
                    <h4 class="font-bold mb-2">Síguenos:</h4>
                    <div class="flex space-x-4">
                        <!-- Iconos simulados de redes sociales -->
                        <a href="#" class="text-xl hover:text-red-400 transition duration-150" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i> <!-- Placeholder for Font Awesome -->
                        </a>
                        <a href="#" class="text-xl hover:text-red-400 transition duration-150" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-xl hover:text-red-400 transition duration-150" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>

            </div>
            
            <!-- Derechos de Autor y Logo -->
            <div class="mt-8 pt-6 border-t border-gray-700 text-center">
                <p class="text-gray-500 text-xs">
                    &copy; 2025 SABORESCHILE. Todos los derechos reservados. 
                </p>
            </div>

        </div>
    </footer>

    <!-- Scripts Finales (incluye los que ya tenías) -->
    <script src="/chilean_food_app/assets/js/navbar.js"></script>
    <script src="/chilean_food_app/assets/js/slider.js"></script>
    <!-- Asumo que tienes Font Awesome para los íconos de redes -->
    <script src="https://kit.fontawesome.com/v_a_key.js" crossorigin="anonymous"></script> 

</body>
</html>