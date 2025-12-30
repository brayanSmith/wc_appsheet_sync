<?php

/**
 * Plugin Name: WooCommerce AppSheet Sync
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once __DIR__ . '/vendor/autoload.php';

use WcAppSheet\Hooks\OrderHooks;
use WcAppSheet\Hooks\OrderDetailHooks;

new OrderHooks();
OrderHooks::registerActionSchedulerHooks();

new OrderDetailHooks();
OrderDetailHooks::registerActionSchedulerHooks();


// Cargar la clase de la página de configuración si no existe
if (is_admin()) {
	require_once __DIR__ . '/src/Admin/SettingsPage.php';
	new \WcAppSheet\Admin\SettingsPage();

	// Encolar el CSS solo para la página de ajustes del plugin
	add_action('admin_enqueue_scripts', function($hook) {
		// Cambia 'wc-appsheet-sync' por el slug real de tu página si es diferente
		if (isset($_GET['page']) && $_GET['page'] === 'wc-appsheet-sync') {
			wp_enqueue_style(
				'wc-appsheet-sync-settings',
				plugins_url('assets/css/settings-page.css', __FILE__),
				[],
				null
			);
		}
	});
}