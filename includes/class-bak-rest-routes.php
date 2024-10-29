<?php

/**
 * RestAdapter Routes
 *
 * A class which represents plugin's REST routes
 *
 * @package BakExtension\api
 * @version 1.0.0
 * @since   1.0.0
 */

namespace BakExtension\api;

defined('ABSPATH') || exit;

class RestRoutes
{
    // Function to check if the route is authorized
    public static function check_permission($request)
    {

        if (!current_user_can('edit_posts')) {
            // User doesn't have the required capability, so deny access.
            return new \WP_Error('rest_forbidden', 'You do not have permission to access this endpoint.', array('status' => 403));
        }

        return True;
    }

    // Register a custom REST API endpoint for products
    public static function product_routes()
    {
        # List of product update
        # /wp-json/bak/v1/products/
        register_rest_route(
            'bak/v1',
            '/products',
            array(
                'methods' => 'GET',
                'callback' => array('BakExtension\api\RestAdapter', 'get_products_bulk'),
                'permission_callback' => array("BakExtension\api\RestRoutes", 'check_permission')
            )
        );

        register_rest_route(
            'bak/v1',
            '/products',
            array(
                'methods' => 'PUT',
                'callback' => array('BakExtension\api\RestAdapter', 'update_products_bulk'),
                'permission_callback' => array("BakExtension\api\RestRoutes", 'check_permission')
            )
        );

        # IPFS
        # /wp-json/bak/v1/products/ipfs
        # Get ipfs images. Handle as post due this is not a "safe" endpoints
        # It can potentially generate images in wordpress if an IPFS uri is found
        # but not linked to a domain image.
        register_rest_route(
            'bak/v1',
            '/products/ipfs',
            array(
                'methods' => 'POST',
                'callback' => array('BakExtension\api\RestAdapter', 'get_ipfs_images'),
                'permission_callback' => array("BakExtension\api\RestRoutes", 'check_permission')
            )
        );

        # Set IPFS images to multiple products
        register_rest_route(
            'bak/v1',
            '/products/ipfs',
            array(
                'methods' => 'PUT',
                'callback' => array('BakExtension\api\RestAdapter', 'upload_ipfs_images'),
                'permission_callback' => array("BakExtension\api\RestRoutes", 'check_permission')
            )
        );

        # GET /wp-json/bak/v1/products/<id>
        register_rest_route(
            'bak/v1',
            '/products/(?P<id>\d+)',
            array(
                'methods' => 'GET',
                'callback' => array('BakExtension\api\RestAdapter', 'get_product_detail'),
                'permission_callback' => array("BakExtension\api\RestRoutes", 'check_permission')
            )
        );

        # Product detail update
        register_rest_route(
            'bak/v1',
            '/products/(?P<id>\d+)',
            array(
                'methods' => 'PUT',
                'callback' => array('BakExtension\api\RestAdapter', 'update_product_detail'),
                'permission_callback' => array("BakExtension\api\RestRoutes", 'check_permission')
            )
        );

        # DELETE
        # Delete product tokenization information
        register_rest_route(
            'bak/v1',
            '/products/(?P<id>\d+)',
            array(
                'methods' => 'DELETE',
                'callback' => array('BakExtension\api\RestAdapter', 'delete_product_token'),
                'permission_callback' => array("BakExtension\api\RestRoutes", 'check_permission')
            )
        );
    }

    public static function auth_routes()
    {

        # POST
        register_rest_route(
            'bak/v1',
            '/auth/token',
            array(
                'methods' => 'POST',
                'callback' => array('BakExtension\api\RestAdapter', 'fetch_access_token'),
                'permission_callback' => array("BakExtension\api\RestRoutes", 'check_permission')
            )
        );
    }
}
