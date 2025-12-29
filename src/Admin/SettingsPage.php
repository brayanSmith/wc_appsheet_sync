<?php

namespace WcAppSheet\Admin;

class SettingsPage 
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
    }
    public function addAdminMenu()
    {
        add_menu_page(
            'WooCommerce AppSheet Sync Settings',
            'AppSheet Sync',
            'manage_options',
            'wc-appsheet-sync',
            [$this, 'renderSettingsPage'],
            'dashicons-admin-generic'
        );
    }

    public function registerSettings()
    {
        register_setting('wc_appsheet_sync_group', 'wc_appsheet_app_id');
        register_setting('wc_appsheet_sync_group', 'wc_appsheet_access_key');
        register_setting('wc_appsheet_sync_group', 'wc_appsheet_table');
        register_setting('wc_appsheet_sync_group', 'wc_appsheet_table_order_details');
    }

    public function renderSettingsPage()
    {
        $view = __DIR__ . '/views/settings-page.php';
        if (file_exists($view)) {
            $options = [
                'app_id' => get_option('wc_appsheet_app_id', ''),
                'access_key' => get_option('wc_appsheet_access_key', ''),
                'table' => get_option('wc_appsheet_table', ''),
                'table_order_details' => get_option('wc_appsheet_table_order_details', ''),
            ];
            extract($options);
            include $view;  
        }
    }
}