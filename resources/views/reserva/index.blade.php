<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apartamento de Descanso - Tu Refugio Perfecto</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        header {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('img/9.jpeg') }}') no-repeat center;
            background-size: cover;
            color: white;
            text-align: center;
            padding: 150px 20px;
            position: relative;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            background-color: rgba(255, 255, 255, 0.9);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        nav .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .logo {
            display: flex;
            align-items: center;
            order: 1; /* Para escritorio, logo primero */
        }

        nav .logo img {
            width: 160px;
        }

        nav ul {
            display: flex;
            list-style: none;
            transition: all 0.3s ease;
        }

        nav ul li {
            margin-left: 20px;
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav ul li a:hover {
            color: #3a7bd5;
        }

        .hamburger {
            display: none;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 30px;
            color: #3a7bd5;
            order: 0; /* Para escritorio, hamburguesa no visible */
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-content h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-content p {
            font-size: 20px;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .btn {
            display: inline-block;
            background-color: #3a7bd5;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #2a5ca3;
        }

        section {
            padding: 80px 0;
        }

        section h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 36px;
            color: #3a7bd5;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .feature:hover {
            transform: translateY(-10px);
        }

        .feature i {
            font-size: 50px;
            color: #3a7bd5;
            margin-bottom: 20px;
        }

        .feature h3 {
            margin-bottom: 15px;
            font-size: 22px;
        }

        .carousel-container {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .carousel {
            display: flex;
            transition: transform 0.5s ease;
            height: 450px;
        }

        .carousel-slide {
            min-width: 100%;
            position: relative;
        }

        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 15px;
            text-align: center;
        }

        .carousel-nav {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .carousel-nav button {
            background: none;
            border: none;
            font-size: 24px;
            margin: 0 10px;
            cursor: pointer;
            color: #3a7bd5;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .carousel-nav button:hover {
            background-color: #3a7bd5;
            color: white;
        }

        .carousel-dots {
            display: flex;
            justify-content: center;
            margin-top: 15px;
        }

        .carousel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #ccc;
            margin: 0 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .carousel-dot.active {
            background-color: #3a7bd5;
        }

        .testimonials {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .testimonial {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 350px;
        }

        .testimonial p {
            font-style: italic;
            margin-bottom: 20px;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .contact-form {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group textarea {
            height: 150px;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 30px 0;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-links a {
            display: inline-block;
            color: white;
            margin: 0 10px;
            font-size: 24px;
        }

        /* Estilos para la sección de condiciones */
        .conditions {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin: 0 auto;
            max-width: 900px;
        }

        .conditions h3 {
            color: #3a7bd5;
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }

        .conditions-list {
            list-style-type: none;
        }

        .conditions-list li {
            margin-bottom: 15px;
            padding-left: 30px;
            position: relative;
        }

        .conditions-list li:before {
            content: "•";
            color: #3a7bd5;
            font-size: 20px;
            position: absolute;
            left: 0;
        }

        .conditions-note {
            margin-top: 20px;
            padding: 15px;
            background-color: #e9f2ff;
            border-left: 4px solid #3a7bd5;
            border-radius: 5px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 36px;
            }

            .hero-content p {
                font-size: 18px;
            }

            .hamburger {
                display: block;
                order: 0; /* En móvil, hamburguesa primero (izquierda) */
            }

            nav .logo {
                order: 1; /* En móvil, logo después (derecha) */
                margin-left: auto; /* Empuja el logo hacia la derecha */
            }

            nav .container {
                position: relative;
                width: 100%;
                padding: 0 20px;
                display: flex;
                justify-content: flex-start; /* Alinea los elementos al inicio */
            }

            nav ul {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: white;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
                padding: 20px;
                opacity: 0;
                visibility: hidden;
                transform: translateY(-10px);
                z-index: 1;
            }

            nav ul.active {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }

            nav ul li {
                margin: 10px 0;
            }
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav>
    <div class="container">
        <button class="hamburger" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="logo"><img src="{{ asset('img/logo.png') }}" alt="SueñoRelax Logo"></div>
        <ul id="mobile-menu">
            <li><a href="#inicio">Inicio</a></li>
            <li><a href="#caracteristicas">Características</a></li>
            <li><a href="#galeria">Galería</a></li>
            <li><a href="#condiciones">Condiciones</a></li>
        </ul>
    </div>
</nav>

<header id="inicio">
    <div class="hero-content">
        <h1>Su Refugio Perfecto para Descansar</h1>
        <p>Querido asociado disfrute de un apartamento completamente amoblado con todas las comodidades para una estadía inolvidable</p>

    </div>
</header>

<section id="caracteristicas">
    <div class="container">
        <h2>Características Exclusivas</h2>
        <div class="features">
            <div class="feature">
                <i class="fas fa-bed"></i>
                <h3>Comodidad Total</h3>
                <p>Dormitorios con camas king size y colchones premium para un descanso perfecto.</p>
            </div>
            <div class="feature">
                <i class="fas fa-wifi"></i>
                <h3>Internet de Alta Velocidad</h3>
                <p>Conexión Wi-Fi gratuita en todo el apartamento para mantenerse conectado.</p>
            </div>
            <div class="feature">
                <i class="fas fa-utensils"></i>
                <h3>Cocina Equipada</h3>
                <p>Cocina completamente equipada con electrodomésticos modernos y utensilios.</p>
            </div>
            <div class="feature">
                <i class="fas fa-tv"></i>
                <h3>Entretenimiento</h3>
                <p>Smart TV y sistema de sonido.</p>
            </div>
            <div class="feature">
                <i class="fas fa-lock"></i>
                <h3>Seguridad 24/7</h3>
                <p>Sistema de seguridad con vigilancia las 24 horas para su tranquilidad.</p>
            </div>
            <div class="feature">
                <i class="fas fa-swimming-pool"></i>
                <h3>Áreas Comunes</h3>
                <p>Acceso a piscina, gimnasio y zonas de recreación dentro del complejo.</p>
            </div>
        </div>
    </div>
</section>

<section id="galeria" style="background-color: #eef2f7;">
    <div class="container">
        <h2>Galería</h2>
        <div class="carousel-container">
            <div class="carousel" id="apartment-carousel">
                <div class="carousel-slide">
                    <img src="{{ asset('img/9.jpeg')}}" alt="Área de Comedor">
                    <div class="carousel-caption">Terraza</div>
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('img/12.jpeg')}}" alt="Área de Comedor">
                    <div class="carousel-caption">Dormitorio principal</div>
                </div>

                <div class="carousel-slide">
                    <img src="{{ asset('img/2.jpeg')}}" alt="Dormitorio Principal">
                    <div class="carousel-caption">Dormitorio</div>
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('img/3.jpeg')}}" alt="Cocina Equipada">
                    <div class="carousel-caption">Comedor</div>
                </div>

                <div class="carousel-slide">
                    <img src="{{ asset('img/5.jpeg')}}" alt="Vista desde el Balcón">
                    <div class="carousel-caption">Sala TV</div>
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('img/6.jpeg')}}" alt="Área de Comedor">
                    <div class="carousel-caption">Sala TV</div>
                </div>
                <div class="carousel-slide">
                    <img src="./img/7.jpeg')}}" alt="Área de Comedor">
                    <div class="carousel-caption">Balcón</div>
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('img/8.jpeg')}}" alt="Área de Comedor">
                    <div class="carousel-caption">Zona de labores</div>
                </div>

                <div class="carousel-slide">
                    <img src="{{ asset('img/10.jpeg')}}" alt="Área de Comedor">
                    <div class="carousel-caption">Vista desde el Balcón</div>
                </div>
                <div class="carousel-slide">
                    <img src="{{ asset('img/11.jpeg')}}" alt="Área de Comedor">
                    <div class="carousel-caption">Closet</div>
                </div>

                <div class="carousel-slide">
                    <img src="{{ asset('img/13.jpeg')}}" alt="Área de Comedor">
                    <div class="carousel-caption">Dormitorio</div>
                </div>
            </div>
        </div>
        <div class="carousel-nav">
            <button id="prev-slide"><i class="fas fa-chevron-left"></i></button>
            <button id="next-slide"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="carousel-dots" id="carousel-dots"></div>
    </div>
</section>

<!-- Nueva sección de condiciones -->
<section id="condiciones" style="background-color: #eef2f7;">
    <div class="container">
        <h2>CONDICIONES</h2>
        <div class="conditions">
            <ul class="conditions-list">
                <li>Bienvenido asociado de CORPENTUNIDA. El apartamento es para su descanso y el de su familia.</li>
                <li>Está ubicado en un edificio, cuyas políticas deben ser acatadas, con respeto.</li>
                <li>Haga su reservación con anticipación en link que se publicará en los grupos de pastores del país.</li>
                <li>El asociado -pastor- debe estar entre las personas, que asisten al lugar. No se permite alquilarlo para, familiares, amigos, o terceros.</li>
                <li>El asociado debe hacer el pago, de manera oportuna. Una vez hecho el pago, y subido el comprobante, podrá seleccionar, los días de su estadía. La aplicación bloqueará los días que usted ha elegido, para que nadie más los tome.</li>
                <li>El pago, no es el costo de alquiler, pues el apto., es gratis. Con el dinero, se suple el aseo y la administración del lugar.</li>
                <li>El ingreso al lugar -check in- es a las 3 de la tarde, del día de ingreso y la salida-check out- es a la 1 de la tarde, el día de la salida.</li>
                <li>Si por alguna eventualidad el asociado, no puede asistir en la fecha determinada, puede agendarse para otra, pues no se hará devolución del dinero. Se aceptará la prórroga, de la fecha, sólo si la situación que impidió su asistencia al lugar, previo estudio de la junta, es considerada, como fuerza mayor.</li>
                <li>Recuerde que puede estar en el apartamento, hasta cinco días cuatro noches máximo.</li>
                <li>Pueden asistir al lugar, máximo 6 personas.</li>
                <li>El cuidado de menores de edad y adultos mayores que acompañen al asociado, es de vital importancia y responsabilidad exclusiva del mismo.</li>
                <li>No se permite el ingreso de mascotas al edificio. Déjelas al cuidado de alguien en su lugar de origen.</li>
                <li>Las instalaciones se entregan en óptimas condiciones. Así mismo esperamos recibirlas. Cualquier daño o deterioro, que se cause al lugar o sus enseres, será responsabilidad de quien esté en su uso, cuando sucedió el daño. Para estos efectos, el asociado, firmará un acta que dé cuenta del estado en el que se le hace entrega del inmueble y demás.</li>
                <li>En la ciudad de Santa Marta, hay una hermana encargada de entregar las llaves. Ella se pondrá en contacto con el asociado, una vez se acerque su fecha elegida.</li>
            </ul>
            <div class="conditions-note">
                <p>Deseamos que su estadía sea grata y reparadora para que continúe sus labores pastorales con eficiencia.</p>
            </div>
        </div>
    </div>
</section>

<!--     <section id="testimonios" style="background-color: #eef2f7;">
    <div class="container">
        <h2>Lo que Dicen Nuestros Huéspedes</h2>
        <div class="testimonials">
            <div class="testimonial">
                <p>"Una experiencia increíble. El apartamento estaba impecable y tenía todo lo que necesitábamos. Las vistas son espectaculares y la ubicación perfecta. ¡Definitivamente volveremos!"</p>
                <div class="testimonial-author">
                    <img src="/api/placeholder/50/50" alt="María G.">
                    <div>
                        <strong>María G.</strong>
                        <div>⭐⭐⭐⭐⭐</div>
                    </div>
                </div>
            </div>
            <div class="testimonial">
                <p>"Nos encantó cada detalle del apartamento. La decoración es hermosa, la limpieza impecable y la atención del anfitrión excepcional. Recomendado al 100% para unas vacaciones relajantes."</p>
                <div class="testimonial-author">
                    <img src="/api/placeholder/50/50" alt="Carlos R.">
                    <div>
                        <strong>Carlos R.</strong>
                        <div>⭐⭐⭐⭐⭐</div>
                    </div>
                </div>
            </div>
            <div class="testimonial">
                <p>"Un oasis de tranquilidad. Encontramos todo lo que necesitábamos y más. La cocina está perfectamente equipada y las camas son muy cómodas. El wifi funciona excelente. Volveríamos sin dudarlo."</p>
                <div class="testimonial-author">
                    <img src="/api/placeholder/50/50" alt="Ana P.">
                    <div>
                        <strong>Ana P.</strong>
                        <div>⭐⭐⭐⭐⭐</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
-->


<footer>
    <div class="container">
        <p>© 2025 Corpentunida - Apartamento de Descanso. Todos los derechos reservados.</p>
        <div class="social-links">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-whatsapp"></i></a>
        </div>
    </div>
</footer>

<script>
    // Navegación suave
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            // Si estamos en móvil, cerrar el menú al hacer clic en un enlace
            const mobileMenu = document.getElementById('mobile-menu');
            if (window.innerWidth <= 768) {
                mobileMenu.classList.remove('active');
            }

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Menú móvil
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    menuToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('active');
    });

    // Cerrar menú móvil cuando se hace clic fuera de él
    document.addEventListener('click', function(event) {
        const isClickInsideMenu = mobileMenu.contains(event.target);
        const isClickOnToggle = menuToggle.contains(event.target);

        if (!isClickInsideMenu && !isClickOnToggle && mobileMenu.classList.contains('active')) {
            mobileMenu.classList.remove('active');
        }
    });

    // Ajustar menú al cambiar el tamaño de la pantalla
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
            mobileMenu.classList.remove('active');
        }
    });

    // Carrusel de imágenes
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('apartment-carousel');
        const slides = carousel.querySelectorAll('.carousel-slide');
        const prevBtn = document.getElementById('prev-slide');
        const nextBtn = document.getElementById('next-slide');
        const dotsContainer = document.getElementById('carousel-dots');

        let currentIndex = 0;

        // Crear los puntos de navegación
        slides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('carousel-dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                goToSlide(index);
            });
            dotsContainer.appendChild(dot);
        });

        const dots = dotsContainer.querySelectorAll('.carousel-dot');

        // Funciones para el carrusel
        function updateCarousel() {
            carousel.style.transform = `translateX(-${currentIndex * 100}%)`;

            // Actualizar puntos activos
            dots.forEach((dot, index) => {
                if (index === currentIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        function goToSlide(index) {
            currentIndex = index;
            updateCarousel();
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            updateCarousel();
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            updateCarousel();
        }

        // Event listeners
        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);

        // Auto avance (opcional)
        let intervalId = setInterval(nextSlide, 5000);

        // Pausar auto avance al pasar el ratón
        carousel.addEventListener('mouseenter', () => {
            clearInterval(intervalId);
        });

        carousel.addEventListener('mouseleave', () => {
            intervalId = setInterval(nextSlide, 5000);
        });

        // Soporte para swipe en móviles
        let touchStartX = 0;
        let touchEndX = 0;

        carousel.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        carousel.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            if (touchEndX < touchStartX) {
                nextSlide(); // Swipe izquierda
            } else if (touchEndX > touchStartX) {
                prevSlide(); // Swipe derecha
            }
        }
    });


</script>
</body>
</html>
