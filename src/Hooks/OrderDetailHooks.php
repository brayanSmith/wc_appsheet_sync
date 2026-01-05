<?php

namespace WcAppSheet\Hooks;

use WcAppSheet\Services\AppSheetClient;

class OrderDetailHooks    
   
{
    public function __construct()
    {
        // Ejecutar automáticamente la eliminación múltiple si WooCommerce elimina varios ítems
        add_action('woocommerce_before_delete_order_items', [$this, 'handleMultipleDelete'], 10, 1);
        // Ejecutar también para eliminación individual
        add_action('woocommerce_before_delete_order_item', [$this, 'handleSingleDelete'], 10, 1);
        // Ejecutar para adición individual
        add_action('woocommerce_new_order_item', [$this, 'handleSingleAdd'], 10, 1);
        // Ejecutar para adición múltiple
        add_action('woocommerce_new_order_items', [$this, 'handleMultipleAdd'], 10, 1);
    }    

    /**
     * Handler para el hook de eliminación individual de WooCommerce
     * @param int $itemId
     */
    public function handleSingleDelete($itemId)
    {
        if (!empty($itemId)) {
            $this->syncMultipleOrderDetails([$itemId], 'delete');
        }
    }

    /**
     * Handler para el hook de eliminación múltiple de WooCommerce
     * @param array $itemIds
     */
    public function handleMultipleDelete($itemIds)
    {
        if (is_array($itemIds) && !empty($itemIds)) {
            $this->syncMultipleOrderDetails($itemIds, 'delete');
        }
    }
    
    public function handleSingleAdd($itemId)
    {
        if (!empty($itemId)) {
            $this->syncMultipleOrderDetails([$itemId], 'add');
        }
    }

    public function handleMultipleAdd($itemIds)
    {
        if (is_array($itemIds) && !empty($itemIds)) {
            $this->syncMultipleOrderDetails($itemIds, 'add');
        }
    }

    /**
     * Sincroniza (agrega o elimina) varios detalles de orden y actualiza el total si corresponde
     * @param array $itemIds
     * @param string $action 'add' para agregar, 'delete' para eliminar
     */
    public function syncMultipleOrderDetails(array $itemIds, string $action = 'delete')
    {
        if (empty($itemIds)) return;
        $items = [];
        $orderId = null;
        foreach ($itemIds as $itemId) {
            $item = new \WC_Order_Item_Product($itemId);
            $orderId = $item->get_order_id();
            $producto = $item->get_product();
            $items[] = [
                'id' => $item->get_id(),
                'order_id' => $orderId,
                'product_id' => $producto ? $producto->get_id() : 0,
                'product_name' => $item->get_name(),
                'quantity' => $item->get_quantity(),
                'total' => $item->get_total(),
            ];
        }
        if (!empty($items)) {
            $cliente = new AppSheetClient();
            $table_order_details = get_option('wc_appsheet_table_order_details', 'order_details');
            if ($action === 'delete') {
                $cliente->deleteData($items, $table_order_details);
            } elseif ($action === 'add') {
                $cliente->sendData($items, $table_order_details);
            }
        }
        // Recalcular y sincronizar el total de la orden si se elimina o agrega detalle
        if ($orderId && in_array($action, ['delete'])) {
            $order = wc_get_order($orderId);
            if ($order) {
                $cliente = isset($cliente) ? $cliente : new AppSheetClient();
                $cliente->editData([
                    'id' => $order->get_id(),
                    'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    'total' => $order->get_total(),
                    'status' => $order->get_status(),
                    'date_created' => $order->get_date_created()->date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
    
}
