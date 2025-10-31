<?php
$roles_registro = ['Cliente', 'Proveedor', 'Psicologo', 'Empleado'];
?>
<section class="contenedor-formulario">
    <h2 class="titulo-formulario">📜 Registrarse en Coffix</h2>
    
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <p class="mensaje-error"><?php echo htmlspecialchars($_SESSION['mensaje_error']); unset($_SESSION['mensaje_error']); ?></p>
    <?php endif; ?>

    <form action="index.php?ruta=procesar_registro" method="POST" enctype="multipart/form-data" class="formulario-estilo-medieval">
        
        <div class="grupo-input">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>
        <div class="grupo-input">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" required>
        </div>
        <div class="grupo-input">
            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>
        </div>
        
        <div class="grupo-input">
            <label for="contrasena">Contraseña (Mín. 8 caracteres, Mayús, Minús, Número y Símbolo):</label>
            <input 
                type="password" 
                id="contrasena" 
                name="contrasena" 
                required 
                minlength="8" 
                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9\s]).{8,}"
                title="Debe contener al menos 8 caracteres, incluyendo una mayúscula, una minúscula, un número y un símbolo (ej: !@#$).">
        </div>
        <div class="grupo-input">
            <label for="contrasena_confirmar">Confirmar Contraseña:</label>
            <input type="password" id="contrasena_confirmar" name="contrasena_confirmar" required> 
        </div>
        <div class="grupo-input">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" required> 
        </div>
        
        <div class="grupo-input">
            <label for="numero_contacto">Número de Contacto (Ej: 091234567):</label>
            <input 
                type="tel" 
                id="numero_contacto" 
                name="numero_contacto" 
                required
                minlength="9" 
                maxlength="9"
                pattern="^09\d{7}$" 
                title="Debe ser un número de 9 dígitos que comience con '09' (Ej: 091234567).">
        </div>
        <div class="grupo-input">
            <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
        </div>

        <div class="grupo-input">
            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" required>
                <option value="Masculino">Masculino (M)</option> 
                <option value="Femenino">Femenino (F)</option>
                <option value="Otro">Otro</option>
            </select>
        </div>

        <div class="grupo-input">
            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <?php foreach ($roles_registro as $rol): ?>
                    <option value="<?php echo $rol; ?>"><?php echo $rol; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="grupo-input">
            <label for="foto_perfil">Foto de Perfil (Opcional):</label>
            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
        </div>

        <div class="grupo-input">
            <label for="biografia">Biografía / Habilidades (Opcional):</label>
            <textarea id="biografia" name="biografia" rows="4" placeholder="Habilidades, experiencia, etc."></textarea>
        </div>

        <button type="submit">Crear Cuenta</button>
    </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.formulario-estilo-medieval');
        const password = document.getElementById('contrasena');
        const confirmPassword = document.getElementById('contrasena_confirmar');

        form.addEventListener('submit', function(e) {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, ingrésalas de nuevo.');
                confirmPassword.focus();
            }
        });
    });
</script>