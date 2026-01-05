<?php

namespace WcAppSheet\Hooks;

use WcAppSheet\Services\AppSheetClient;

class OrderHooks
{
    public function __construct()
    {
        add_action('woocommerce_new_order', [$this, 'scheduleSyncOrder']);
        add_action('woocommerce_update_order', [$this, 'scheduleSyncOrderEdit']);
        add_action('woocommerce_trash_order', [$this, 'scheduleSyncOrderEdit']);
        add_action('woocommerce_before_delete_order', [$this, 'syncOrderDelete']);
    }

    /**
     * Programa la sincronización de la edición de la orden como tarea asíncrona
     */
    public function scheduleSyncOrderEdit($orderId)
    {
        if (function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action('wc_appsheet_sync_order_edit', ['order_id' => $orderId]);
        } else {
            $this->syncOrderEdit($orderId);
        }
    }

    /**
     * Acción para procesar la sincronización de la edición de la orden
     */
    public static function actionSyncOrderEdit($orderId)
    {
        $instance = new self();
        $instance->syncOrderEdit($orderId);
    }

    /**
     * Programa la sincronización del pedido como tarea asíncrona
     */
    public function scheduleSyncOrder($orderId)
    {
        if (function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action('wc_appsheet_sync_order', ['order_id' => $orderId]);
        } else {
            $this->syncOrder($orderId);
        }
    }

    /**
     * Acción para procesar la sincronización del pedido
     */
    public static function actionSyncOrder($orderId)
    {
        $instance = new self();
        $instance->syncOrder($orderId);
    }

    /**
     * Acción para procesar la sincronización de la eliminación de la orden
     */
    public static function actionSyncOrderDelete($orderId)
    {
        $instance = new self();
        $instance->syncOrderDelete($orderId);
    }

    
    /**
     * Registrar los hooks de Action Scheduler
     */
    public static function registerActionSchedulerHooks()
    {
        add_action('wc_appsheet_sync_order', [self::class, 'actionSyncOrder'], 10, 1);
        add_action('wc_appsheet_sync_order_edit', [self::class, 'actionSyncOrderEdit'], 10, 1);
        add_action('wc_appsheet_sync_order_delete', [self::class, 'actionSyncOrderDelete'], 10, 1);
    }
    
    public function syncOrder($orderId)
    {
        $order = wc_get_order($orderId);
        error_log("[OrderHooks][syncOrder] Iniciando sincronización de orden: $orderId");
        if (!$order) {
            error_log("[OrderHooks][syncOrder] Orden no encontrada: $orderId");
            return;
        }
        $cliente = new AppSheetClient();
        $data = [
            'id' => $order->get_id(),
            'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'address_1' => $order->get_billing_address_1(),
            'address_2' => $order->get_billing_address_2(),
            'city' => $order->get_billing_city(),
            'postcode' => $order->get_billing_postcode(),
            'country' => $order->get_billing_country(),
            'email' => $order->get_billing_email(),
            'phone' => $order->get_billing_phone(),
            'currency' => $order->get_currency(),
            'payment_method' => $order->get_payment_method(),
            'payment_method_title' => $order->get_payment_method_title(),
            'discount_total' => $order->get_discount_total(),
            'discount_tax' => $order->get_discount_tax(),
            'shipping_total' => $order->get_shipping_total(),
            'shipping_tax' => $order->get_shipping_tax(),
            'cart_tax' => $order->get_cart_tax(),
            'total' => $order->get_total(),
            'total_tax' => $order->get_total_tax(),
            'status' => $order->get_status(),
            'date_created' => $order->get_date_created()->date('Y-m-d H:i:s'),
            'date_modified' => $order->get_date_modified()->date('Y-m-d H:i:s'),
        ];
        error_log('[OrderHooks][syncOrder] Datos enviados a AppSheet: ' . print_r($data, true));
        $cliente->sendData($data);

        $items = $order->get_items();
        $detalles = [];
        foreach ($items as $item) {
            if ($item instanceof \WC_Order_Item_Product) {
                $producto = $item->get_product();
                $detalles[] = [
                    'id' => $item->get_id(),
                    'order_id' => $order->get_id(),
                    'product_id' => $producto ? $producto->get_id() : 0,
                    'product_name' => $item->get_name(),
                    'quantity' => $item->get_quantity(),
                    'total' => $item->get_total(),
                ];
            }
        }
        if (!empty($detalles)) {
            $table_order_details = get_option('wc_appsheet_table_order_details', 'order_details');
            error_log('[OrderHooks][syncOrder] Detalles enviados a AppSheet: ' . print_r($detalles, true));
            $cliente->sendData($detalles, $table_order_details);
        }
    }

    /**
     * Sincroniza la edición de la orden con AppSheet
     */
    public function syncOrderEdit($orderId)
    {
        $order = wc_get_order($orderId);
        error_log("[OrderHooks][syncOrderEdit] Iniciando edición de orden: $orderId");
        if (!$order) {
            error_log("[OrderHooks][syncOrderEdit] Orden no encontrada: $orderId");
            return;
        }
        $cliente = new AppSheetClient();
        $data = [
            'id' => $order->get_id(),
            'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'address_1' => $order->get_billing_address_1(),
            'address_2' => $order->get_billing_address_2(),
            'city' => $order->get_billing_city(),
            'postcode' => $order->get_billing_postcode(),
            'country' => $order->get_billing_country(),
            'email' => $order->get_billing_email(),
            'phone' => $order->get_billing_phone(),
            'currency' => $order->get_currency(),
            'payment_method' => $order->get_payment_method(),
            'payment_method_title' => $order->get_payment_method_title(),
            'discount_total' => $order->get_discount_total(),
            'discount_tax' => $order->get_discount_tax(),
            'shipping_total' => $order->get_shipping_total(),
            'shipping_tax' => $order->get_shipping_tax(),
            'cart_tax' => $order->get_cart_tax(),
            'total' => $order->get_total(),
            'total_tax' => $order->get_total_tax(),
            'status' => $order->get_status(),
            'date_created' => $order->get_date_created()->date('Y-m-d H:i:s'),
            'date_modified' => $order->get_date_modified()->date('Y-m-d H:i:s'),
        ];
        error_log('[OrderHooks][syncOrderEdit] Datos enviados a AppSheet: ' . print_r($data, true));
        $cliente->editData($data);

        $items = $order->get_items();
        $detalles_nuevos = [];
        $detalles_existentes = [];
        foreach ($items as $item) {
            if ($item instanceof \WC_Order_Item_Product) {
                $producto = $item->get_product();
                $detalle = [
                    'id' => $item->get_id(),
                    'order_id' => $order->get_id(),
                    'product_id' => $producto ? $producto->get_id() : 0,
                    'product_name' => $item->get_name(),
                    'quantity' => $item->get_quantity(),
                    'total' => $item->get_total(),
                ];
                if ($item->get_id() < 1000000) {
                    $detalles_existentes[] = $detalle;
                } else {
                    $detalles_nuevos[] = $detalle;
                }
            }
        }
        $table_order_details = get_option('wc_appsheet_table_order_details', 'order_details');
        if (!empty($detalles_existentes)) {
            error_log('[OrderHooks][syncOrderEdit] Detalles existentes enviados a AppSheet: ' . print_r($detalles_existentes, true));
            $cliente->editData($detalles_existentes, $table_order_details);
        }
        if (!empty($detalles_nuevos)) {
            error_log('[OrderHooks][syncOrderEdit] Detalles nuevos enviados a AppSheet: ' . print_r($detalles_nuevos, true));
            $cliente->sendData($detalles_nuevos, $table_order_details);
        }

        $refunds = $order->get_refunds();
        $refunds_data = [];
        foreach ($refunds as $refund) {
            $refunds_data[] = [
                'id' => $refund->get_id(),
                'order_id' => $order->get_id(),
                'reason' => $refund->get_reason(),
                'total' => $refund->get_amount(),
            ];
        }
        if (!empty($refunds_data)) {
            $table_order_refunds = get_option('wc_appsheet_table_order_refunds', 'order_refunds');
            error_log('[OrderHooks][syncOrderEdit] Reembolsos enviados a AppSheet: ' . print_r($refunds_data, true));
            $cliente->sendData($refunds_data, $table_order_refunds);
        }
    }

    /**
     * Sincroniza la eliminación de la orden con AppSheet
     */
    public function syncOrderDelete($orderId)
    {
        $order = wc_get_order($orderId);
        error_log("[OrderHooks][syncOrderDelete] Eliminando orden: $orderId");
        if (!$order) {
            error_log("[OrderHooks][syncOrderDelete] Orden no encontrada: $orderId");
            return;
        }
        $cliente = new AppSheetClient();
        $data = [
            'id' => $order->get_id(),
        ];
        error_log('[OrderHooks][syncOrderDelete] Datos enviados a AppSheet: ' . print_r($data, true));
        $cliente->deleteData($data);
    }
    
}
