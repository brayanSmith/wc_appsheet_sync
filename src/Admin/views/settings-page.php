
<div class="wrap">
    <div class="wc-appsheet-card">
        <div style="text-align:center; margin-bottom:20px;">
            <img src="<?php echo plugins_url('assets/img/SmithCode.png', dirname(__DIR__, 2)); ?>" alt="Logo SmithCode" style="max-width:240px; height:auto;" />
        </div>
        <h1>Configuración AppSheet</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('wc_appsheet_sync_group');
                do_settings_sections('wc_appsheet_sync_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">App ID (Appsheet)</th>
                    <td><input type="text" name="wc_appsheet_app_id" value="<?php echo esc_attr($app_id); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Access Key (Appsheet)</th>
                    <td><input type="text" name="wc_appsheet_access_key" value="<?php echo esc_attr($access_key); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Tabla de Ordenes</th>
                    <td><input type="text" name="wc_appsheet_table" value="<?php echo esc_attr($table); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Tabla de Detalles de Orden</th>
                    <td><input type="text" name="wc_appsheet_table_order_details" value="<?php echo esc_attr($table_order_details); ?>" /></td>
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
</div>