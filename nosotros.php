<?php
    require 'includes/app.php';
    incluirTemplate('header');
?>

<main>
    <section class="nosotros-info">
        <div>
            <h1>Sobre Nosotros</h1>
            <p>Conoce quiénes somos y qué nos motiva a ofrecerte la mejor experiencia de compra online.</p>
        </div>
    </section>

    <section>
        <div class="n-historia">
            <div class="n-historia-contenido">
                <h2>Nuestra Historia</h2>
                <p>
                    Somos una tienda online dedicada a ofrecer productos de alta calidad con precios accesibles. 
                    Nuestro proyecto nace como una iniciativa universitaria con el objetivo de brindar una experiencia 
                    de compra sencilla, rápida y confiable para todos nuestros usuarios.
                </p>
            </div>
        </div>
    </section>

    <section>
        <div class="n-secciones">
            <div class="seccion">
                <h3>Misión</h3>
                <p>Ofrecer productos innovadores y accesibles con una experiencia de usuario excepcional.</p>
            </div>
            <div class="seccion">
                <h3>Visión</h3>
                <p>Ser una tienda online reconocida por su calidad, confianza y atención al cliente.</p>
            </div>
            <div class="seccion">
                <h3>Valores</h3>
                <p>Compromiso, innovación, honestidad y trabajo en equipo.</p>
            </div>
        </div>
    </section>

    <section class="n-equipo">
        <h2>Nuestro Equipo</h2>
        <div class="equipo-contenido">

            <div class="e-integrante">
                <h5>Joshua Benjamin Cabriales Aguilar</h5>
                <p>Full-Stack Developer</p>
            </div>

            <div class="e-integrante">
                <h5>Alberto Gonzales Ruiz</h5>
                <p>FrontEnd Developer</p>
            </div>

            <div class="e-integrante">
                <h5>Fernanda Reyes Hernandez</h5>
                <p>Diseñador UI/UX</p>
            </div>

            <div class="e-integrante">
                <h5>Irving Yareth Guzman Jimenez</h5>
                <p>BackEnd Developer</p>
            </div>

        </div>
    </section>

    <section class="contenedor">
        <div class="n-comprar">
            <div>
                <h4>¿Listo para comprar?</h4>
                <p>Explora nuestros productos y descubre lo que tenemos para ti.</p>
            </div>
            <a class="btn btn-verde" href="index.php">Ir a la tienda</a>
        </div>
    </section>
</main>

<?php
    incluirTemplate('footer');
?>
