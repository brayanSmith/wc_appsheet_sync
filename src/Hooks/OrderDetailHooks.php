<?php

namespace WcAppSheet\Hooks;

use WcAppSheet\Services\AppSheetClient;

class OrderDetailHooks
{
    public function __construct()
    {
        // Se ejecuta cuando se añade o actualiza un ítem en el pedido
        add_action('woocommerce_order_item_added', [$this, 'scheduleSyncOrderDetail'], 10, 3);
        add_action('woocommerce_update_order_item', [$this, 'scheduleSyncOrderDetail'], 10, 3);
        // Se ejecuta cuando se edita un ítem en el pedido
        add_action('woocommerce_before_save_order_item', [$this, 'scheduleSyncOrderDetailEdit'], 10, 1);
        // Se ejecuta cuando se elimina un ítem del pedido
        add_action('woocommerce_before_delete_order_item', [$this, 'syncOrderDetailDelete'], 10, 1);
    }

    /**
     * Programa la sincronización de la edición del detalle del pedido como tarea asíncrona
     */
    public function scheduleSyncOrderDetailEdit($item)
    {
        $itemId = $item->get_id();
        $orderId = $item->get_order_id();
        if (function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action('wc_appsheet_sync_order_detail_edit', [
                'item_id' => $itemId,
                'order_id' => $orderId
            ]);
        } else {
            $this->syncOrderDetailEdit($itemId, $orderId);
        }
    }

    /**
     * Acción para procesar la sincronización de la edición del detalle del pedido
     */
    public static function actionSyncOrderDetailEdit($itemId, $orderId)
    {
        $instance = new self();
        $instance->syncOrderDetailEdit($itemId, $orderId);
    }

    /**
     * Programa la sincronización del detalle del pedido como tarea asíncrona
     */
    public function scheduleSyncOrderDetail($itemId, $item, $orderId)
    {
        if (function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action('wc_appsheet_sync_order_detail', [
                'item_id' => $itemId,
                'order_id' => $orderId
            ]);
        } else {
            $this->syncOrderDetail($itemId, $orderId);
        }
    }

    /**
     * Acción para procesar la sincronización del detalle del pedido
     */
    public static function actionSyncOrderDetail($itemId, $orderId)
    {
        $instance = new self();
        $instance->syncOrderDetail($itemId, $orderId);
    }

    /**
     * Acción para procesar la sincronización de la eliminación del detalle del pedido
     */
    public static function actionSyncOrderDetailDelete($itemId, $orderId)
    {
        $instance = new self();
        $instance->syncOrderDetailDelete($itemId, $orderId);
    }

    /**
     * Registrar el hook de Action Scheduler
     */
    public static function registerActionSchedulerHooks()
    {
        add_action('wc_appsheet_sync_order_detail', [self::class, 'actionSyncOrderDetail'], 10, 2);
        add_action('wc_appsheet_sync_order_detail_edit', [self::class, 'actionSyncOrderDetailEdit'], 10, 2);
        add_action('wc_appsheet_sync_order_detail_delete', [self::class, 'actionSyncOrderDetailDelete'], 10, 2);
    }

    /**
     * Sincroniza la edición del detalle del pedido con AppSheet
     */
    public function syncOrderDetailEdit($itemId, $orderId)
    {
        $order = wc_get_order($orderId);
        if (!$order) return;
        $item = $order->get_item($itemId);
        if (!$item || !$item instanceof \WC_Order_Item_Product) return;
        $producto = $item->get_product();
        $cliente = new AppSheetClient();
        $table_order_details = get_option('wc_appsheet_table_order_details', 'order_details');
        $cliente->editData([
            'id' => $item->get_id(),
            'order_id' => $order->get_id(),
            'product_id' => $producto ? $producto->get_id() : 0,
            'product_name' => $item->get_name(),
            'quantity' => $item->get_quantity(),
            'total' => $item->get_total(),
        ], $table_order_details);
    }

    public function syncOrderDetail($itemId, $orderId)
    {
        $order = wc_get_order($orderId);
        if (!$order) return;
        $item = $order->get_item($itemId);
        if (!$item || !$item instanceof \WC_Order_Item_Product) return;
        $producto = $item->get_product();
        $cliente = new AppSheetClient();
        $table_order_details = get_option('wc_appsheet_table_order_details', 'order_details');
        $cliente->sendData([
            'id' => $item->get_id(),
            'order_id' => $order->get_id(),
            'product_id' => $producto ? $producto->get_id() : 0,
            'product_name' => $item->get_name(),
            'quantity' => $item->get_quantity(),
            'total' => $item->get_total(),
        ], $table_order_details);
    }

    public function syncOrderDetailDelete($itemId)
    {
        $item = new \WC_Order_Item_Product($itemId);
        $orderId = $item->get_order_id();
        $order = $orderId ? wc_get_order($orderId) : null;
        $item = ($order) ? $order->get_item($itemId) : null;
        if (!$item || !$item instanceof \WC_Order_Item_Product) return;
        $cliente = new AppSheetClient();
        $table_order_details = get_option('wc_appsheet_table_order_details', 'order_details');
        $cliente->deleteData([
            'id' => $item->get_id(),
        ], $table_order_details);
    }
    
}
