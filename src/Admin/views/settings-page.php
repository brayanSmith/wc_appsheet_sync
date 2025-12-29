<div class="wrap">
    <h1>Configuración AppSheet</h1>
    <form method="post" action="options.php">
        <?php
            settings_fields('wc_appsheet_sync_group');
            do_settings_sections('wc_appsheet_sync_group');
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">App ID</th>
                <td><input type="text" name="wc_appsheet_app_id" value="<?php echo esc_attr($app_id); ?>" size="50"/></td>
            </tr>
            <tr valign="top">
                <th scope="row">Access Key</th>
                <td><input type="text" name="wc_appsheet_access_key" value="<?php echo esc_attr($access_key); ?>" size="50"/></td>
            </tr>
            <tr valign="top">
                <th scope="row">Table Orders Name</th>
                <td><input type="text" name="wc_appsheet_table" value="<?php echo esc_attr($table); ?>" size="50" /></td>
            </tr>
                           
        </table>
        <?php submit_button(); ?>
    </form>
</div>