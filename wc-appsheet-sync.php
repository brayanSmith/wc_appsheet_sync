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
}