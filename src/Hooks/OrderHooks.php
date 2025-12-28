<?php

namespace WcAppSheet\Hooks;

use WcAppSheet\Services\AppSheetClient;

class OrderHooks 
{
    public function __construct()
    {
        add_action('woocommerce_order_status_completed', [$this, 'syncOrder']);
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
}