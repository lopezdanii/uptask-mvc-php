<div class="contenedor restablecer">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php';?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina"> Introduce tu nueva contraseña</p>

        <?php include_once __DIR__ . '/../templates/alertas.php';?>

        <?php if($mostrar) { ?>

        <form class="formulario" method="POST">

            <div class="campo"> 
                <label for="password">Password</label>
                <input type="password" id="password" placeholder="Tu contraseña" name="password" />
            </div>

            <input type="submit" class="boton" value="Guardar contraseña">
        </form>
        
        <?php } ?>
        <div class="acciones">
            <a href="/crear">¿No tienes cuenta? Crear una</a>
            <a href="/passwordOlvidada">¿Olvidaste tu contraseña?</a>
        </div>
    </div><!-- .contenedor-sm-->
</div>