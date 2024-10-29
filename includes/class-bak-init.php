<?php

/**
 * Order Item
 *
 * A class which represents an item within an order and handles CRUD.
 * Uses ArrayAccess to be BW compatible with WC_Orders::get_items().
 *
 * @package WooCommerce\Classes
 * @version 3.0.0
 * @since   3.0.0
 */

namespace BakExtension\core;

defined('ABSPATH') || exit;

class BakWCExtension
{
    /**
     * The single instance of the class.
     */
    protected static $_instance = null;

    /**
     * Constructor.
     */
    protected function __construct()
    {
        // Instantiation logic will go here.

    }

    public static function is_woocommerce_activated()
    {
        if (class_exists('woocommerce')) {
            return true;
        } else {
            return false;
        }
    }

    public static function init()
    {
        // Start initiating settings
        if (!self::is_woocommerce_activated()) {
            add_action('admin_notices', array('BakExtension\core\Settings', 'missing_wc_notice'));
            return;
        }

        //==================================== WooCommerce Settings ===================================
        add_action('admin_enqueue_scripts', array('BakExtension\core\Settings', 'add_extension_register_script'));
        add_filter('woocommerce_settings_tabs_array', array('BakExtension\core\Settings', 'add_bak_settings'), 50);
        add_action('woocommerce_settings_tabs_bak_settings', array('BakExtension\core\Settings', 'bak_add_bak_settings'));
        add_action('woocommerce_update_options_bak_settings', array('BakExtension\core\Settings', 'bak_update_options_bak_settings'));

        //==================================== REST api ===================================
        add_action('rest_api_init', array('BakExtension\api\RestRoutes', 'auth_routes'));
        add_action('rest_api_init', array('BakExtension\api\RestRoutes', 'product_routes'));

        //==================================== Product List ===================================
        add_filter('manage_product_posts_columns', array("BakExtension\controllers\ProductList", 'bak_fingerprint_column'));
        add_filter('bulk_actions-edit-product', array("BakExtension\controllers\ProductList", 'add_mint_bulk_action'));
        add_action('manage_product_posts_custom_column', array("BakExtension\controllers\ProductList", 'bak_fingerprint_column_data'), 10, 2);
        add_filter('woocommerce_product_filters', array("BakExtension\controllers\ProductList", 'bak_custom_filter'));
        add_action('pre_get_posts', array("BakExtension\controllers\ProductList", 'bak_products_filter_query'));

        //==================================== Product  ===================================
        add_filter('woocommerce_product_tabs', array("BakExtension\controllers\Product", 'bakrypt_blockchain_product_tab'));
        add_filter('woocommerce_product_data_tabs', array("BakExtension\controllers\Product", 'bakrypt_blockchain_product_data_tab'));
        add_action('woocommerce_product_data_panels', array("BakExtension\controllers\Product", 'bakrypt_blockchain_product_data_fields'));
        add_action("add_meta_boxes", array("BakExtension\controllers\Product", "add_ipfs_meta_box"));
        add_action('woocommerce_process_product_meta', array("BakExtension\controllers\Product", 'bak_save_blockchain_meta'));
        add_action('wp_ajax_product_token_get_image', array("BakExtension\controllers\Product", 'product_token_get_image'));

        // Add shortcodes
        add_shortcode("bak_asset_tracking", array('BakExtension\controllers\AssetTracking', 'asset_tracking'));

        // =================================== Orders ======================================
        add_filter('woocommerce_get_item_data', array("BakExtension\controllers\Order", "display_asset_fingerprint_in_cart"), 10, 2);
        add_action('woocommerce_checkout_create_order_line_item', array("BakExtension\controllers\Order", "add_asset_fingerprint_to_order_line_item_meta"), 10, 4);
        add_filter('woocommerce_new_order_item', array("BakExtension\controllers\Order", 'bak_woocommerce_order_item_name'), 10, 3);
    }


    /**
     * Main Extension Instance.
     * Ensures only one instance of the extension is loaded or can be loaded.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Cloning is forbidden.
     */
    public function __clone()
    {
        // Override this PHP function to prevent unwanted copies of your instance.
        // Implement your own error or use `wc_doing_it_wrong()`
    }

    /**
     * Unserializing instances of this class is forbidden.
     */
    public function __wakeup()
    {
        // Override this PHP function to prevent unwanted copies of your instance.
        // Implement your own error or use `wc_doing_it_wrong()`
    }
}
