<?php

namespace WcAppSheet\Hooks;

use WcAppSheet\Services\AppSheetClient;

class OrderHooks
{
    public function __construct()
    {
        add_action('woocommerce_new_order', [$this, 'scheduleSyncOrder']);
        add_action('woocommerce_new_order', [$this, 'scheduleSyncOrderDetails']);
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
     * Programa la sincronización de los detalles del pedido como tarea asíncrona
     */
    public function scheduleSyncOrderDetails($orderId)
    {
        if (function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action('wc_appsheet_sync_order_details', ['order_id' => $orderId]);
        } else {
            $this->syncOrderDetails($orderId);
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
     * Acción para procesar la sincronización de los detalles del pedido
     */
    public static function actionSyncOrderDetails($orderId)
    {
        $instance = new self();
        $instance->syncOrderDetails($orderId);
    }

    /**
     * Registrar los hooks de Action Scheduler
     */
    public static function registerActionSchedulerHooks()
    {
        add_action('wc_appsheet_sync_order', [self::class, 'actionSyncOrder'], 10, 1);
        add_action('wc_appsheet_sync_order_details', [self::class, 'actionSyncOrderDetails'], 10, 1);
    }

    public function syncOrder($orderId)
    {
        $order = wc_get_order($orderId);

        if (!$order) return;

        $cliente = new AppSheetClient();

        $cliente->sendOrder([
            'OrderID' => $order->get_id(),
            'CustomerName' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'Total' => $order->get_total(),
            'Status' => $order->get_status(),
            'DateCreated' => $order->get_date_created()->date('Y-m-d H:i:s'),
        ]);
    }

    public function syncOrderDetails($orderId)
    {
        $order = wc_get_order($orderId);

        if (!$order) return;

        $cliente = new AppSheetClient();
        $table_order_details = get_option('wc_appsheet_table_order_details', 'order_details');

        foreach ($order->get_items() as $item) {
            if ($item instanceof \WC_Order_Item_Product) {
                $producto = $item->get_product();
                $cliente->sendOrder([
                    'id' => $item->get_id(),
                    'OrderID' => $order->get_id(),
                    'ProductID' => $producto ? $producto->get_id() : 0,
                    'ProductName' => $item->get_name(),
                    'Quantity' => $item->get_quantity(),
                    'Total' => $item->get_total(),
                ], $table_order_details);
            }
        }
    }
}
