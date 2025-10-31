<?php 
$alerta_exito = $_SESSION['mensaje_exito'] ?? null;
$alerta_error = $_SESSION['mensaje_error'] ?? null;
unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);
?>

<div class="contenido-admin">
    <h2>Gestión de Usuarios y Bloqueos</h2>

    <?php if ($alerta_exito): ?>
        <p style="color: green; background-color: #e6ffe6; padding: 10px; border: 1px solid green;"><?= htmlspecialchars($alerta_exito) ?></p>
    <?php endif; ?>
    <?php if ($alerta_error): ?>
        <p style="color: red; background-color: #ffe6e6; padding: 10px; border: 1px solid red;"><?= htmlspecialchars($alerta_error) ?></p>
    <?php endif; ?>

    <?php if (!empty($usuarios)): ?>
        <table class="tabla-gestion" border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>ID Usuario</th>
                    <th>Nombre/Usuario</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Motivo Bloqueo</th>
                    <th>Fecha Bloqueo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['id']) ?></td>
                    <td><?= htmlspecialchars($usuario['nombre_usuario']) ?></td>
                    <td><?= htmlspecialchars($usuario['correo']) ?></td>
                    <td><?= htmlspecialchars($usuario['rol']) ?></td>
                    <td>
                        <span style="color: <?= $usuario['estado'] === 'Activo' ? 'green' : 'red' ?>; font-weight: bold;">
                            <?= htmlspecialchars($usuario['estado']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($usuario['motivo_bloqueo'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($usuario['fecha_bloqueo'] ?? 'N/A') ?></td>
                    <td>
                        <?php if ($usuario['estado'] === 'Activo'): ?>
                            <button onclick="abrirModalBloqueo(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['nombre_usuario']) ?>')" 
                                    class="boton-accion-eliminar" style="background-color: darkred; color: white; border: none; padding: 5px 10px; cursor: pointer;">
                                BLOQUEAR
                            </button>
                        <?php else: ?>
                            <form method="POST" action="index.php?ruta=procesar_desbloqueo_usuario" style="display: inline;">
                                <input type="hidden" name="id_usuario" value="<?= $usuario['id'] ?>">
                                <button type="submit" class="boton-accion" style="background-color: darkgreen; color: white; border: none; padding: 5px 10px; cursor: pointer;">
                                    DESBLOQUEAR
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontraron usuarios en la base de datos.</p>
    <?php endif; ?>

</div>

<div id="modalBloqueo" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100;">
    <div style="background: white; margin: 10% auto; padding: 20px; width: 300px; border-radius: 5px;">
        <h3>Bloquear Usuario: <span id="nombreUsuarioBloqueo"></span></h3>
        <form id="formBloqueo" method="POST" action="index.php?ruta=procesar_bloqueo_usuario">
            <input type="hidden" name="id_usuario" id="idUsuarioBloqueo">
            <label for="motivo_bloqueo">Motivo del Bloqueo:</label>
            <textarea name="motivo_bloqueo" id="motivo_bloqueo" required rows="4" style="width: 95%;"></textarea>
            <div style="margin-top: 15px;">
                <button type="submit" style="background-color: darkred; color: white; padding: 8px;">Confirmar Bloqueo</button>
                <button type="button" onclick="cerrarModalBloqueo()" style="background-color: gray; color: white; padding: 8px;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalBloqueo(id, nombre) {
        document.getElementById('idUsuarioBloqueo').value = id;
        document.getElementById('nombreUsuarioBloqueo').innerText = nombre;
        document.getElementById('modalBloqueo').style.display = 'block';
    }

    function cerrarModalBloqueo() {
        document.getElementById('modalBloqueo').style.display = 'none';
        document.getElementById('motivo_bloqueo').value = '';
    }
</script>