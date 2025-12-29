<?php

namespace WcAppSheet\Hooks;

use WcAppSheet\Services\AppSheetClient;

class OrderHooks
{
    public function __construct()
    {
        add_action('woocommerce_order_status_completed', [$this, 'syncOrder']);
        add_action('woocommerce_order_status_completed', [$this, 'syncOrderDetails']);
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
