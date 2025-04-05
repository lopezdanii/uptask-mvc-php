<div class="contenedor pOlvidada">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php';?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina"> Recupera tu contraseña</p>

        <?php include_once __DIR__ . '/../templates/alertas.php';?>


        <form class="formulario" method="POST" action="/passwordOlvidada">
            <div class="campo"> 
                <label for="email">Email</label>
                <input type="email" id="email" placeholder="Tu email" name="email" />
            </div>

            <input type="submit" class="boton" value="Enviar instrucciones">
        </form>
        
        <div class="acciones">
            <a href="/crear">¿No tienes cuenta? Crear una</a>
            <a href="/">Iniciar sesión</a>
        </div>
    </div><!-- .contenedor-sm-->
</div>