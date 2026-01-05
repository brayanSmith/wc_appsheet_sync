<?php

namespace WcAppSheet\Hooks;

use WcAppSheet\Services\AppSheetClient;

class ProductHooks
{
    public function __construct()
    {
        add_action('woocommerce_new_product', [$this, 'scheduleSyncProduct']);
        //add_action('woocommerce_publish_product', [$this, 'scheduleSyncProduct']);
        add_action('woocommerce_update_product', [$this, 'scheduleSyncProductEdit']);
        add_action('woocommerce_trash_product', [$this, 'scheduleSyncProductEdit']);
        add_action('woocommerce_before_delete_product', [$this, 'syncProductDelete']);
    }

    /**
     * Programa la sincronización del producto como tarea asíncrona
     */
    public function scheduleSyncProduct($productId)
    {
        if (function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action('wc_appsheet_sync_product', ['product_id' => $productId]);
        } else {
            $this->syncProduct($productId, 'add');
        }
    }
    /**
     * Acción para procesar la sincronización del producto
     */
    public static function actionSyncProduct($productId)
    {
        $instance = new self();
        $instance->syncProduct($productId, 'add');
    }
    /**
     * Programa la sincronización de la edición del producto como tarea asíncrona
     */
    public function scheduleSyncProductEdit($productId)
    {
        if (function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action('wc_appsheet_sync_product_edit', ['product_id' => $productId]);
        } else {
            $this->syncProduct($productId, 'edit');
        }
    }
    /**
     * Acción para procesar la sincronización de la edición del producto
     */
    public static function actionSyncProductEdit($productId)
    {
        $instance = new self();
        $instance->syncProduct($productId, 'edit');
    }    
    /**
     * Acción para procesar la sincronización de la eliminación del producto
     */
    public function syncProductDelete($productId)
    {
        $this->syncProduct($productId, 'delete');
    }

    public static function registerActionSchedulerHooks()
    {
        add_action('wc_appsheet_sync_product', [self::class, 'actionSyncProduct']);
        add_action('wc_appsheet_sync_product_edit', [self::class, 'actionSyncProductEdit']);
        add_action('wc_appsheet_sync_product_delete', [self::class, 'actionSyncProductDelete']);
    }

    public function syncProduct($productId, string $action)
    {
        $product = wc_get_product($productId);
        if (!$product) return;
        $appSheetClient = new AppSheetClient();
        $items = [
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'slug' => $product->get_slug(),
            'sku' => $product->get_sku(),
            'price' => $product->get_price(),
            'regular_price' => $product->get_regular_price(),
            'sale_price' => $product->get_sale_price(),
            'stock_quantity' => $product->get_stock_quantity(),
            'status' => $product->get_status(),
            'date_created' => $product->get_date_created() ? $product->get_date_created()->date('Y-m-d H:i:s') : null,
            'date_modified' => $product->get_date_modified() ? $product->get_date_modified()->date('Y-m-d H:i:s') : null,
        ];
        $table = get_option('wc_appsheet_table_products', 'products');
        switch ($action) {
            case 'add':
                error_log('[AppSheetSync] Acción: ADD | ID: ' . $product->get_id() . ' | Tabla: ' . $table);
                $appSheetClient->sendData($items, $table);
                break;
            case 'edit':
                // Verificar si existe en AppSheet antes de editar
                if ($appSheetClient->existsInAppSheet($product->get_id(), $table)) {
                    error_log('[AppSheetSync] Acción: EDIT | ID: ' . $product->get_id() . ' | Tabla: ' . $table);
                    $appSheetClient->editData($items, $table);
                } else {
                    error_log('[AppSheetSync] Acción: ADD (por no existir) | ID: ' . $product->get_id() . ' | Tabla: ' . $table);
                    $appSheetClient->sendData($items, $table);
                }
                break;
            case 'delete':
                error_log('[AppSheetSync] Acción: DELETE | ID: ' . $product->get_id() . ' | Tabla: ' . $table);
                $appSheetClient->deleteData($items, $table);
                break;
        }
    }
}