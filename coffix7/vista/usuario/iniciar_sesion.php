<?php
$roles_login = ['Cliente', 'Proveedor', 'Psicologo', 'Empleado', 'Administrador'];
?>
<section class="contenedor-formulario">
    <h2 class="titulo-formulario">🔑 Iniciar Sesión</h2>
    
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <p class="mensaje-error"><?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?></p>
    <?php endif; ?>

    <form action="index.php?ruta=procesar_login" method="POST" class="formulario-estilo-medieval">
        
        <div class="grupo-input">
            <label for="identificador">Nombre de Usuario o Correo:</label>
            <input type="text" id="identificador" name="usuario_o_correo" required> 
        </div>
        
        <div class="grupo-input">
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
        </div>

        <div class="grupo-input">
            <label for="rol">Rol (Selecciona tu rol):</label>
            <select id="rol" name="rol" required>
                <?php foreach ($roles_login as $rol): ?>
                    <option value="<?php echo $rol; ?>"><?php echo $rol; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit">Entrar</button>
    </form>
    <p class="enlace-registro">¿Aún no tienes cuenta? <a href="index.php?ruta=registro">Regístrate aquí</a>.</p>
</section>