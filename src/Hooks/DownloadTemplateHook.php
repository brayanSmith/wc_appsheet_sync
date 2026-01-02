<?php
// Hook para servir la plantilla Excel para descarga desde el área de administración
add_action('admin_init', function() {
    if (isset($_GET['wc_appsheet_download_template']) && current_user_can('manage_options')) {
        $file = plugin_dir_path(dirname(__DIR__, 1)) . 'assets/excelTemplates/PlantillaAppsheetWoocommerce.xlsx';
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="PlantillaAppsheetWoocommerce.xlsx"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        } else {
            wp_die('Archivo no encontrado.');
        }
    }
});
