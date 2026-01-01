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

        if (!$order) return;

        $cliente = new AppSheetClient();

        $cliente->sendData([
            'id' => $order->get_id(),
            'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'total' => $order->get_total(),
            'status' => $order->get_status(),
            'date_created' => $order->get_date_created()->date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Sincroniza la edición de la orden con AppSheet
     */
    public function syncOrderEdit($orderId)
    {
        $order = wc_get_order($orderId);
        if (!$order) return;
        $cliente = new AppSheetClient();
        $cliente->editData([
            'id' => $order->get_id(),
            'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'total' => $order->get_total(),
            'status' => $order->get_status(),
            'date_created' => $order->get_date_created()->date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Sincroniza la eliminación de la orden con AppSheet
     */
    public function syncOrderDelete($orderId)
    {
        $order = wc_get_order($orderId);
        if (!$order) return;
        $cliente = new AppSheetClient();
        $cliente->deleteData([
            'id' => $order->get_id(),
        ]);
    }
    
}
