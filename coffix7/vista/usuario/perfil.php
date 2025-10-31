<?php
$usuario = $usuario ?? [];
$fechaNacimiento = $usuario['fecha_nacimiento'] ?? null;
$edad = $fechaNacimiento ? date_diff(date_create($fechaNacimiento), date_create('today'))->y : 'N/A';
$ruta_foto_guardada = $usuario['foto_perfil'] ?? '';
$ruta_default = 'recursos/img/perfiles/default.png';
$ruta_foto_final = (empty($ruta_foto_guardada) || $ruta_foto_guardada == 'default.png')
                   ? $ruta_default
                   : $ruta_foto_guardada;
?>

<section class="contenedor-perfil">
    <h2>👤 Mi Perfil (Rol: <?php echo htmlspecialchars($usuario['rol'] ?? 'N/A'); ?>)</h2>

    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <p class="mensaje-exito"><?php echo htmlspecialchars($_SESSION['mensaje_exito']); unset($_SESSION['mensaje_exito']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <p class="mensaje-error"><?php echo htmlspecialchars($_SESSION['mensaje_error']); unset($_SESSION['mensaje_error']); ?></p>
    <?php endif; ?>

    <div class="perfil-contenido">
        <form action="index.php?ruta=procesar_edicion_perfil" method="POST" enctype="multipart/form-data" class="formulario-perfil">
            <div class="perfil-columna-izquierda">
                <img src="<?php echo htmlspecialchars($ruta_foto_final); ?>" alt="Foto de perfil" class="foto-perfil-grande">

                <h3>Información personal</h3>

                <div class="grupo-input">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido'] ?? ''); ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="nombre_usuario">Nombre de usuario:</label>
                    <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario['nombre_usuario'] ?? ''); ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="contrasena">Nueva Contraseña (Dejar vacío para no cambiar):</label>
                    <input type="password" id="contrasena" name="contrasena">
                </div>

                <div class="grupo-input">
                    <label for="contrasena_confirmar">Confirmar Nueva Contraseña:</label>
                    <input type="password" id="contrasena_confirmar" name="contrasena_confirmar">
                </div>

                <div class="grupo-input">
                    <label for="correo">Correo electrónico:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo'] ?? ''); ?>" required>
                </div>

                <div class="grupo-input">
                    <label for="numero_contacto">Número de contacto:</label>
                    <input type="text" id="numero_contacto" name="numero_contacto" value="<?php echo htmlspecialchars($usuario['numero_contacto'] ?? ''); ?>">
                </div>

                <div class="grupo-input">
                    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($usuario['fecha_nacimiento'] ?? ''); ?>">
                </div>

                <div class="grupo-input">
                    <label for="sexo">Sexo:</label>
                    <select id="sexo" name="sexo">
                        <option value="Masculino" <?php echo (($usuario['sexo'] ?? '') == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Femenino" <?php echo (($usuario['sexo'] ?? '') == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                        <option value="Otro" <?php echo (($usuario['sexo'] ?? '') == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>

                <div class="grupo-input">
                    <label for="foto_perfil">Cambiar Foto de Perfil:</label>
                    <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                </div>

                <div class="grupo-input">
                    <label for="biografia">Biografía / Habilidades (Opcional):</label>
                    <textarea id="biografia" name="biografia" rows="4"><?php echo htmlspecialchars($usuario['biografia'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="boton-primario">Guardar Cambios</button>
            </div>
        </form>

        <div class="perfil-columna-derecha">
            <h3>Detalles Adicionales</h3>
            <p><strong>Edad:</strong> <?php echo htmlspecialchars($edad); ?></p>
            <p><strong>Miembro desde:</strong> <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'] ?? 'N/A')); ?></p>

            <?php if (in_array($usuario['rol'] ?? '', ['Proveedor', 'Psicologo'])): ?>
                <h3>Mis Servicios Publicados</h3>
                <p>Ver Servicios (Implementación futura)</p>
            <?php endif; ?>

            <form action="index.php?ruta=procesar_eliminacion" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar tu cuenta? Esta acción es irreversible.');">
                <button type="submit" class="boton-eliminar">Eliminar Cuenta</button>
            </form>
        </div>
    </div>
</section>