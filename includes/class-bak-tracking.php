<?php

/**
 * Fingerprint Tracking
 *
 * Lets a user see the existence of a fingerprint
 *
 * @package BakExtension\controllers
 * @version 1.0.0
 * @since   1.0.0
 */

namespace BakExtension\controllers;

defined('ABSPATH') || exit;

/**
 * Shortcode order tracking class.
 */
class AssetTracking
{

    /**
     * Output the shortcode.
     *
     * @param array $atts Shortcode attributes.
     */
    public static function asset_tracking($atts)
    {

        $atts        = shortcode_atts(array(), $atts, 'bak_asset_tracking');
        $nonce_value = wc_get_var($_REQUEST['bak-asset-tracking-nonce'], wc_get_var($_REQUEST['_wpnonce'], '')); // @codingStandardsIgnoreLine.

        if (isset($_REQUEST['fingerprint']) && strlen($_REQUEST['fingerprint']) && wp_verify_nonce($nonce_value, 'bak-asset_tracking')) { // WPCS: input var ok.

            $fingerprint = empty($_REQUEST['fingerprint']) ? '' : trim(wc_clean(wp_unslash($_REQUEST['fingerprint']))); // WPCS: input var ok.

            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => -1,
                'meta_query'     => array(
                    array(
                        'key'     => 'bk_token_fingerprint',
                        'value'   => strtolower($fingerprint), // Convert to lowercase for case-insensitivity
                        'compare' => 'LIKE', // Use 'LIKE' for a partial match
                    ),
                ),
                'orderby'        => 'date',  // Order by post date
                'order'          => 'DESC',  // Use DESC to get the latest post first
            );

            $custom_query = new \WP_Query($args);

            if ($custom_query->have_posts()) {
                while ($custom_query->have_posts()) {
                    $custom_query->the_post();

                    // Get the product object
                    $product = wc_get_product(get_the_ID());
                }

                wp_reset_postdata();

                return bak_get_template(
                    'product/tracking.php',
                    array(
                        'product' => $product,
                        'fingerprint' => $fingerprint
                    )
                );
            } else {
                wc_print_notice(__('Sorry, the asset could not be found.', 'bak'), 'error');
            }
        }

        return bak_get_template('product/form-tracking.php');
    }
}
