
<div class="wrap">
    <div class="wc-appsheet-card">
        <div style="text-align:center; margin-bottom:20px;">
            <img src="<?php echo plugins_url('assets/img/Emprende-Tech.png', dirname(__DIR__, 2)); ?>" alt="Logo Emprende-Tech" style="max-width:180px; height:auto;" />
        </div>
        <h1>Configuración AppSheet</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('wc_appsheet_sync_group');
                do_settings_sections('wc_appsheet_sync_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">App ID</th>
                    <td><input type="text" name="wc_appsheet_app_id" value="<?php echo esc_attr($app_id); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Access Key</th>
                    <td><input type="text" name="wc_appsheet_access_key" value="<?php echo esc_attr($access_key); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Table Orders Name</th>
                    <td><input type="text" name="wc_appsheet_table" value="<?php echo esc_attr($table); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Table OrderDetails Name</th>
                    <td><input type="text" name="wc_appsheet_table_order_details" value="<?php echo esc_attr($table_order_details); ?>" /></td>
                </tr>            
            </table>
            <?php submit_button('Guardar cambios', 'primary'); ?>
        </form>
    </div>
</div>