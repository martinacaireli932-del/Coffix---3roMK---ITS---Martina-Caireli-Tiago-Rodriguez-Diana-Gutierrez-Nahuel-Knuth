<h3>Servicios Publicados (Control Administrativo)</h3>

    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <p class="mensaje-exito"><?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <p class="mensaje-error"><?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?></p>
    <?php endif; ?>

    <table class="tabla-servicios-admin">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Fecha Pub.</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicios as $servicio): ?>
            <tr>
                <td><?php echo $servicio['id']; ?></td>
                <td><a href="index.php?ruta=detalle_servicio&id=<?php echo $servicio['id']; ?>" target="_blank"><?php echo htmlspecialchars($servicio['titulo']); ?></a></td>
                <?php echo htmlspecialchars($servicio['nombre_usuario'] ?? ''); ?>
                <?php echo htmlspecialchars($servicio['rol'] ?? ''); ?>
                <td><?php echo date('d/m/Y', strtotime($servicio['fecha_publicacion'])); ?></td>
                <td><span class="estado-<?php echo strtolower($servicio['estado']); ?>"><?php echo htmlspecialchars($servicio['estado']); ?></span></td>
                <td>
                    <?php if ($servicio['estado'] === 'Activo'): ?>
                        <button onclick="document.getElementById('form_admin_<?php echo $servicio['id']; ?>').style.display='block'">Eliminar</button>
                    <?php else: ?>
                        Eliminado
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td colspan="7" class="fila-eliminar-admin">
                    <form id="form_admin_<?php echo $servicio['id']; ?>" action="index.php?ruta=procesar_eliminacion_admin" method="POST" style="display:none; padding: 10px; border: 1px solid #ccc; margin-top: 5px;">
                        <input type="hidden" name="id_servicio" value="<?php echo $servicio['id']; ?>">
                        
                        <div class="grupo-input">
                            <label for="motivo_<?php echo $servicio['id']; ?>">Motivo de eliminación (Obligatorio):</label>
                            <textarea id="motivo_<?php echo $servicio['id']; ?>" name="motivo_eliminacion" rows="2" required></textarea>
                        </div>
                        
                        <div class="grupo-input">
                            <label>Fecha de eliminación:</label>
                            <input type="text" value="<?php echo date('Y-m-d H:i:s'); ?>" disabled>
                        </div>
                        
                        <button type="submit" class="boton-eliminar">Confirmar Eliminación</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>