
<div class="wrap">
    <div class="wc-appsheet-card">
        <div style="text-align:center; margin-bottom:20px;">
            <img src="<?php echo plugins_url('assets/img/SmithCode.png', dirname(__DIR__, 2)); ?>" alt="Logo SmithCode" style="max-width:240px; height:auto;" />
        </div>
        <h1 style="text-align:center;">AppSheet WooCommerce</h1>
        <nav style="margin-bottom:20px;">
            <button type="button" class="nav-tab nav-tab-active" id="tab-configuracion-btn" onclick="mostrarTab('configuracion')">Configuración</button>
            <button type="button" class="nav-tab" id="tab-contacto-btn" onclick="mostrarTab('contacto')">Contacto</button>
        </nav>
        <div id="tab-configuracion" class="tab-content" style="display:block;">
            <form method="post" action="options.php">
                <?php
                    settings_fields('wc_appsheet_sync_group');
                    do_settings_sections('wc_appsheet_sync_group');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">App ID (Appsheet)</th>
                        <td><input type="text" name="wc_appsheet_app_id" value="<?php echo esc_attr($app_id); ?>" required/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Access Key (Appsheet)</th>
                        <td><input type="text" name="wc_appsheet_access_key" value="<?php echo esc_attr($access_key); ?>" required/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Tabla de Ordenes</th>
                        <td><input type="text" name="wc_appsheet_table" value="<?php echo esc_attr($table); ?>" required/></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Tabla de Detalles de Orden</th>
                        <td><input type="text" name="wc_appsheet_table_order_details" value="<?php echo esc_attr($table_order_details); ?>" required/></td>
                    </tr>      
                    <tr valign="top">
                        <th scope="row">Descargar plantilla Excel</th>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=wc-appsheet-sync&wc_appsheet_download_template=1'); ?>" class="button button-secondary">
                                Descargar Plantilla
                            </a>
                        </td>
                    </tr>      
                </table>            
                <?php submit_button('Guardar cambios', 'primary'); ?>            
            </form>
        </div>
        <div id="tab-contacto" class="tab-content" style="display:none;">
            <br>
            <br>
            <div style="max-width:400px; margin:0; padding:10px 0 0 0;">
                <p style="margin-bottom: 16px;">¿Necesitas ayuda, soporte o quieres agregar nuevas funcionalidades a tu tienda? También desarrollamos plugins, integraciones y mejoras a medida para WooCommerce y AppSheet.</p>
                <ul style="list-style:none; padding:0; margin:0 0 16px 0;">                    
                    <li><strong>WhatsApp:</strong> <a href="https://wa.me/573203867042" target="_blank">+57 320 3867042</a></li>
                </ul>
                <p style="margin-top:16px; color:#888;">Desarrollado por <strong>Smith Code</strong>.</p>
            </div>
        </div>
    </div>
</div>
<script>
function mostrarTab(tab) {
    document.getElementById('tab-configuracion').style.display = (tab === 'configuracion') ? 'block' : 'none';
    document.getElementById('tab-contacto').style.display = (tab === 'contacto') ? 'block' : 'none';
    document.getElementById('tab-configuracion-btn').classList.toggle('nav-tab-active', tab === 'configuracion');
    document.getElementById('tab-contacto-btn').classList.toggle('nav-tab-active', tab === 'contacto');
}
</script>