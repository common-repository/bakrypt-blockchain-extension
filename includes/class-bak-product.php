<?php

/**
 * Product
 *
 * A class which represents Product details operations and renderization.
 *
 * @package BakExtension\controllers
 * @version 1.0.0
 * @since   1.0.0
 */

namespace BakExtension\controllers;

defined('ABSPATH') || exit;

use BakExtension\api\RestAdapter;

class Product
{

    private static $adapter;
    /**
     * Constructor.
     */
    protected function __construct()
    {
        // Instantiation logic will go here.
        // self::$adapter = new RestAdapter();    
    }

    public static function bakrypt_blockchain_product_tab_content()
    {
        // The new tab content
        $prod_id = get_the_ID();
        $fingerprint = get_post_meta($prod_id, 'bk_token_fingerprint', true);
        echo '<p><strong>Fingerprint:</strong> <a href="https://cexplorer.io/asset/' . esc_html($fingerprint) . '" target="_blank" rel="nofollow">' . esc_html($fingerprint) . '</a></p>';
    }

    public static function bakrypt_blockchain_product_tab($tabs)
    {
        // Adds the new tab
        $tabs['desc_tab'] = array(
            'title' => __('Blockchain', 'woocommerce'),
            'priority' => 50,
            'callback' => array("BakExtension\controllers\Product", 'bakrypt_blockchain_product_tab_content')
        );

        return $tabs;
    }

    public static function bakrypt_blockchain_product_data_tab(
        $product_data_tabs
    ) {
        $product_data_tabs['bakrypt-blockchain'] = array(
            'label' => __('Blockchain', 'bakrypt_blockchain'),
            'target' => 'blockchain_product_data',
        );
        return $product_data_tabs;
    }

    public static function bakrypt_blockchain_product_data_fields()
    {
        global $woocommerce, $post;
        $asset = array(
            "uuid" => get_post_meta(get_the_ID(), 'bk_token_uuid', true),
            "transaction_uuid" => get_post_meta(get_the_ID(), 'bk_token_transaction', true),
        );

        // Create nonce
        $nonce = wp_create_nonce("bk_nonce");

        // Generate Bakrypt Token on load

        $testnet = self::$adapter->settings['testnet'];
        $access = self::$adapter->generate_access_token();

?>
        <!-- id below must match target registered in above add_blockchain_product_data_tab function -->
        <div id="blockchain_product_data" class="panel woocommerce_options_panel">
            <p class="form-field woocommerce-message" style="float:right" <?php if ($asset['uuid'] == '')
                                                                                echo 'style="display:none"' ?>>
                <button style="line-height:1" id="delete_token" name="delete_token" class="button-primary woocommerce-save-button">
                    <span style="vertical-align:middle" class="dashicons dashicons-trash"></span>
                </button>
            </p>

            <input type="hidden" id="product_id" value="<?php echo get_the_ID() ?>" />
            <input type="hidden" id="bk_nonce" value="<?php echo esc_attr($nonce) ?>" />

            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => 'bk_token_policy',
                    'value' => get_post_meta(get_the_ID(), 'bk_token_policy', true),
                    // 'wrapper_class' => 'show_if_simple',
                    'label' => __('Policy ID', 'my_text_domain'),
                    'description' => __('Policy ID related to this asset', 'my_text_domain'),
                    'default' => '',
                    'desc_tip' => false,
                    'custom_attributes' => array('readonly' => 'readonly'),
                )
            );
            ?>
            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => 'bk_token_fingerprint',
                    'value' => get_post_meta(get_the_ID(), 'bk_token_fingerprint', true),
                    // 'wrapper_class' => 'show_if_simple',
                    'label' => __('Asset Fingerprint', 'my_text_domain'),
                    'description' => __('Recorded in the blockchain.', 'my_text_domain'),
                    'default' => '',
                    'desc_tip' => false,
                    'custom_attributes' => array('readonly' => 'readonly'),
                )
            );
            ?>
            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => 'bk_token_asset_name',
                    'value' => get_post_meta(get_the_ID(), 'bk_token_asset_name', true),
                    // 'wrapper_class' => 'show_if_simple',
                    'label' => __('Asset Name', 'my_text_domain'),
                    'description' => __("Token's asset name.", 'my_text_domain'),
                    'default' => '',
                    'desc_tip' => false,
                    'custom_attributes' => array('readonly' => 'readonly'),
                )
            );
            ?>
            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => 'bk_token_name',
                    'value' => get_post_meta(get_the_ID(), 'bk_token_name', true),
                    // 'wrapper_class' => 'show_if_simple',
                    'label' => __('Token Name', 'my_text_domain'),
                    'description' => __("Token name.", 'my_text_domain'),
                    'default' => '',
                    'desc_tip' => false,
                    'custom_attributes' => array('readonly' => 'readonly'),
                )
            );
            ?>
            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => 'bk_token_image',
                    'value' => get_post_meta(get_the_ID(), 'bk_token_image', true),
                    // 'wrapper_class' => 'show_if_simple',
                    'label' => __('Asset Image', 'my_text_domain'),
                    // 'description' => __("Token image.", 'my_text_domain'),
                    'default' => '',
                    'desc_tip' => false,
                    'custom_attributes' => array('readonly' => 'readonly'),
                )
            );
            ?>
            <p class="form-field"> <a target='_blank' rel='nofollow' href='<?php echo esc_html(str_replace("ipfs://", "https://gateway.bakrypt.io/ipfs/", get_post_meta(get_the_ID(), 'bk_token_image', true))) ?>'>Preview Image</a></p>
            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => 'bk_token_amount',
                    'value' => get_post_meta(get_the_ID(), 'bk_token_amount', true),
                    // 'wrapper_class' => 'show_if_simple',
                    'label' => __('Number of tokens', 'my_text_domain'),
                    'description' => __('Number of tokens with the same fingerprint under the same policy.', 'my_text_domain'),
                    'default' => '',
                    'desc_tip' => false,
                    'custom_attributes' => array('readonly' => 'readonly'),
                )
            );
            ?>
            <?php
            woocommerce_wp_textarea_input(
                array(
                    'id' => 'bk_token_json',
                    'value' => get_post_meta(get_the_ID(), 'bk_token_json', true),
                    // 'wrapper_class' => 'show_if_simple',
                    'label' => __('Token Metadata', 'my_text_domain'),
                    'description' => __('Metadata recorded in the blockchain', 'my_text_domain'),
                    'default' => '',
                    'desc_tip' => false,
                    'custom_attributes' => array('readonly' => 'readonly'),
                )
            );
            ?>
            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => 'bk_token_uuid',
                    'value' => get_post_meta(get_the_ID(), 'bk_token_uuid', true),
                    // 'wrapper_class' => 'show_if_simple',
                    'label' => __('BAK ID', 'my_text_domain'),
                    'description' => __("Bakrypt's Asset Unique Identifier.", "my_text_domain"),
                    'default' => '',
                    'desc_tip' => true,
                    'custom_attributes' => array('readonly' => 'readonly'),
                )
            );
            ?>
            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => 'bk_token_transaction',
                    'value' => get_post_meta(get_the_ID(), 'bk_token_transaction', true),
                    // 'wrapper_class' => 'show_if_simple',
                    'label' => __('Transaction ID', 'my_text_domain'),
                    'description' => __('Transaction Object related to this asset in bakrypt', 'my_text_domain'),
                    'default' => '',
                    'desc_tip' => true,
                    'custom_attributes' => array('readonly' => 'readonly'),
                )
            );
            ?>
            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => 'bk_token_status',
                    'value' => get_post_meta(get_the_ID(), 'bk_token_status', true),
                    // 'wrapper_class' => 'show_if_simple',
                    'label' => __('Status', 'my_text_domain'),
                    'description' => __('Status of the transaction related to the request.', 'my_text_domain'),
                    'default' => '',
                    'desc_tip' => true,
                    'custom_attributes' => array('readonly' => 'readonly'),
                )
            );
            ?>

            <div <?php if ($testnet == "yes")
                        echo "testnet" ?> data-token="<?php echo esc_attr($access->{'access_token'}) ?>" style="display: flex; justify-content: space-between" class="btn-action">
                <p class="form-field mint" <?php if ($asset['uuid'] != '')
                                                echo 'style="display:none"' ?>></p>
                <p class="form-field view-transaction" <?php if ($asset['uuid'] == '')
                                                            echo 'style="display:none"' ?>></p>
                <p class="form-field" <?php if ($asset['uuid'] == '')
                                            echo 'style="display:none"' ?>><button name="update_token" class="components-button is-secondary" id="sync-asset-btn">Sync Token</button></p>

                <?php if (get_post_meta(get_the_ID(), 'bk_token_fingerprint', true)) { ?>
                    <p class="form-field"> <a target='_blank' rel='nofollow' href='https://cexplorer.io/asset/<?php echo esc_html(get_post_meta(get_the_ID(), 'bk_token_fingerprint', true)) ?>'>View
                            in cexplorer.io</a></p>
                <?php } ?>
            </div>

        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('a#bk_token_image_media_manager').click(function(e) {
                    e.preventDefault();
                    var imageFrame;
                    if (imageFrame) {
                        imageFrame.open();
                    }
                    // Define imageFrame as wp.media object
                    imageFrame = wp.media({
                        title: 'Select Media',
                        multiple: false,
                        library: {
                            type: 'image',
                        },
                    });
                    imageFrame.on('close', function() {
                        // On close, get selections and save to the hidden input
                        // plus other AJAX stuff to refresh the image preview
                        const selection = imageFrame.state().get('selection');
                        const galleryIds = new Array();
                        let idx = 0;
                        selection.each(function(attachment) {
                            galleryIds[idx] = attachment.id;
                            idx++;
                        });
                        const ids = galleryIds.join(',');
                        if (ids.length === 0) return true; //if closed withput selecting an image
                        $('input#bk_att_token_image').val(ids);
                        refreshImages(ids);
                    });

                    imageFrame.on('open', function() {
                        // On open, get the id from the hidden input
                        // and select the appropiate images in the media manager
                        const selection = imageFrame.state().get('selection');
                        const ids = $('input#bk_att_token_image').val().split(',');
                        ids.forEach(function(id) {
                            const attachment = wp.media.attachment(id);
                            attachment.fetch();
                            selection.add(attachment ? [attachment] : []);
                        });
                    });

                    imageFrame.open();
                });
            });

            // Ajax request to refresh the image preview
            function refreshImages(id) {
                const data = {
                    action: 'product_token_get_image',
                    id,
                };
                jQuery.get(ajaxurl, data, function(response) {
                    if (response.success === true) {
                        jQuery('#preview_bk_att_token_image').replaceWith(
                            response.data.image
                        );

                        jQuery('#bk_att_token_image_ipfs').val(
                            jQuery('#preview_bk_att_token_image').data('ipfs')
                        );
                    }
                });
            }
        </script>

        <?php

    }

    public static function ipfs_meta_box_markup($post)
    {
        if (!self::$adapter) {
            self::$adapter = new RestAdapter();
        }

        $settings = self::$adapter->settings;

        if ((!$settings['client_id'] && !$settings['client_secret'] && !$settings['username'] && !$settings['password']) and !$settings['auth_token']) { ?>
            <div class="error">
                <p><strong><a href="<?php echo admin_url('admin.php') . '?page=wc-settings&tab=bak_settings' ?>" target="_blank">Bakrypt Auth credentials</a> are required to load data from the remote source.</strong>
                </p>
            </div>
        <?php }

        global $thepostid, $product_object;
        $thepostid = $post->ID;
        $product_object = $thepostid ? wc_get_product($thepostid) : new WC_Product();
        $bk_token_att = get_post_meta(get_the_ID(), 'bk_att_token_image', true);
        $bk_token_status = get_post_meta(get_the_ID(), 'bk_token_status', true);
        $img_metadata = wp_get_attachment_metadata($bk_token_att);
        $img_ipfs = null;
        if ($img_metadata && array_key_exists('ipfs', $img_metadata)) {
            $img_ipfs = $img_metadata['ipfs'];
        }
        wp_nonce_field(basename(__FILE__), "ipfs-box-nonce");
        ?>
        <div id="product_images_container">
            <ul class="product_images">
                <?php
                // $product_image_gallery = $product_object->get_gallery_image_ids('edit');
                $product_image_gallery = array($bk_token_att);

                $attachments = array_filter($product_image_gallery);
                $update_meta = false;
                $updated_gallery_ids = array();

                if (!empty($attachments)) {
                    foreach ($attachments as $attachment_id) {
                        $attachment = wp_get_attachment_image(
                            $attachment_id,
                            'thumbnail',
                            false,
                            array('id' => 'preview_bk_att_token_image', 'data-ipfs' => $img_ipfs)
                        );

                        // if attachment is empty skip.
                        if (empty($attachment)) {
                            $update_meta = true;
                            continue;
                        }
                ?>
                        <li class="image" data-attachment_id="<?php echo esc_attr($attachment_id); ?>">
                            <?php echo $attachment; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                            ?>
                            <?php
                            // Allow for extra info to be exposed or extra action to be executed for this attachment.
                            do_action('woocommerce_admin_after_product_gallery_item', $thepostid, $attachment_id);

                            ?>
                        </li>
                    <?php

                        // rebuild ids to be saved.
                        $updated_gallery_ids[] = $attachment_id;
                    }

                    // need to update product meta to set new gallery ids
                    if ($update_meta) {
                        update_post_meta($post->ID, 'bk_att_token_image', esc_attr($bk_token_att));
                    }
                } else {
                    ?>
                    <li class="image" data-attachment_id="<?php echo (isset($attachment_id) ? esc_attr($attachment_id) : '') ?>">
                        <span id="preview_bk_att_token_image"></span>
                    </li>
                <?php
                }
                ?>
            </ul>
            <input type="hidden" id="bk_att_token_image" readonly name="bk_att_token_image" value="<?php echo esc_attr($bk_token_att); ?>" />
            <input type="hidden" id="bk_att_token_image_ipfs" readonly name="bk_att_token_image_ipfs" value="<?php echo esc_attr($img_ipfs); ?>" />
        </div>
        <?php
        if (!in_array($bk_token_status, ['confirmed', 'canceled'])) {
        ?>
            <a href="#" id="bk_token_image_media_manager">
                <?php esc_attr_e('Choose from gallery', 'mytextdomain'); ?>
            </a>
        <?php } ?>
<?php
    }

    public static function add_ipfs_meta_box()
    {
        add_meta_box("ipfs-meta-box", "Blockchain token image", array("BakExtension\controllers\Product", "ipfs_meta_box_markup"), "product", "side", "low", null);
    }

    public static function product_token_get_image()
    {
        if (isset($_GET['id'])) {

            $attachment_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            # Verify IPFS information
            $img_metadata = wp_get_attachment_metadata($attachment_id);
            $img_ipfs = "";
            if ($img_metadata && array_key_exists('ipfs', $img_metadata)) {
                $img_ipfs = $img_metadata['ipfs'];
            }

            # Upload to IPFS node if nothing is found
            if ($img_ipfs == '') {

                if (!self::$adapter) {
                    self::$adapter = new RestAdapter();
                }

                $bak_file = self::$adapter->upload_attachment_to_ipfs($attachment_id);

                $img_ipfs = $bak_file->{'ipfs'};
                $img_metadata['ipfs'] = $img_ipfs;
                wp_update_attachment_metadata($attachment_id, $img_metadata); // save it back to the db
            }

            // Return image object
            $image = wp_get_attachment_image(
                $attachment_id,
                'thumbnail',
                false,
                array('id' => 'preview_bk_att_token_image', 'data-ipfs' => $img_ipfs)
            );
            $data = array(
                'image' => $image,
            );
            wp_send_json_success($data);
        } else {
            wp_send_json_error();
        }
    }

    public static function update_record($post_id, $_data = null)
    {
        $data = isset($_data) ? $_data : $_POST;

        $bk_token_uuid = isset($data['bk_token_uuid']) ? sanitize_text_field($data['bk_token_uuid']) : null;
        $bk_token_policy = isset($data['bk_token_policy']) ? sanitize_text_field($data['bk_token_policy']) : null;
        $bk_token_fingerprint = isset($data['bk_token_fingerprint']) ? sanitize_text_field($data['bk_token_fingerprint']) : null;
        $bk_token_asset_name = isset($data['bk_token_asset_name']) ? sanitize_text_field($data['bk_token_asset_name']) : null;
        $bk_token_name = isset($data['bk_token_name']) ? sanitize_text_field($data['bk_token_name']) : null;
        $bk_token_image = isset($data['bk_token_image']) ? sanitize_text_field($data['bk_token_image']) : null;
        $bk_token_amount = isset($data['bk_token_amount']) ? sanitize_text_field($data['bk_token_amount']) : null;
        $bk_token_status = isset($data['bk_token_status']) ? sanitize_text_field($data['bk_token_status']) : null;
        $bk_token_transaction = isset($data['bk_token_transaction']) ? sanitize_text_field($data['bk_token_transaction']) : null;
        $bk_token_json = isset($data['bk_token_json']) ? sanitize_text_field($data['bk_token_json']) : null;

        // Update attachment, token_image rel
        $bk_att_token_image = isset($data['bk_att_token_image']) ? sanitize_text_field($data['bk_att_token_image']) : null;

        if (!isset($bk_token_image) && !isset($bk_token_uuid) && !isset($bk_att_token_image)) {
            # Insert attachment
            $att_id = RestAdapter::insert_attachment_from_ipfs($bk_token_image, $post_id);
            $bk_att_token_image = $att_id;
        }

        // grab the product
        $product = wc_get_product($post_id);

        // save the custom SKU using WooCommerce built-in functions
        if ($bk_token_uuid) $product->update_meta_data('bk_token_uuid', $bk_token_uuid);
        if ($bk_token_policy) $product->update_meta_data('bk_token_policy', $bk_token_policy);
        if ($bk_token_fingerprint) $product->update_meta_data('bk_token_fingerprint', $bk_token_fingerprint);
        if ($bk_token_asset_name) $product->update_meta_data('bk_token_asset_name', $bk_token_asset_name);
        if ($bk_token_name) $product->update_meta_data('bk_token_name', $bk_token_name);
        if ($bk_token_image) $product->update_meta_data('bk_token_image', $bk_token_image);
        if ($bk_token_amount) $product->update_meta_data('bk_token_amount', $bk_token_amount);
        if ($bk_token_status) $product->update_meta_data('bk_token_status', $bk_token_status);
        if ($bk_token_transaction) $product->update_meta_data('bk_token_transaction', $bk_token_transaction);
        if ($bk_token_json) $product->update_meta_data('bk_token_json', $bk_token_json);
        if ($bk_att_token_image) $product->update_meta_data('bk_att_token_image', $bk_att_token_image);

        $product->save();

        return $product;
    }

    public static function bak_save_blockchain_meta($post_id)
    {
        self::update_record($post_id);
    }

    public static function delete_record($post_id)
    {
        $meta_keys = array(
            'bk_token_uuid',
            'bk_token_policy',
            'bk_token_fingerprint',
            'bk_token_asset_name',
            'bk_token_image',
            'bk_token_name',
            'bk_token_amount',
            'bk_token_status',
            'bk_token_transaction',
            'bk_token_json',
            'bk_att_token_image'
        );

        foreach ($meta_keys as $meta) {
            delete_post_meta($post_id, $meta);
        }
    }

    public static function get_product_data($product_id)
    {
        // grab the product
        $product = wc_get_product($product_id);

        // save the custom SKU using WooCommerce built-in functions
        $product_data = array(
            "uuid" => $product->get_meta('bk_token_uuid'),
            "policy" => $product->get_meta('bk_token_policy'),
            "fingerprint" => $product->get_meta('bk_token_fingerprint'),
            "asset_name" => $product->get_meta('bk_token_asset_name'),
            "name" => $product->get_meta('bk_token_name'),
            "image" => $product->get_meta('bk_token_image'),
            "amount" => $product->get_meta('bk_token_amount'),
            "status" => $product->get_meta('bk_token_status'),
            "transaction" => $product->get_meta('bk_token_transaction'),
            "json" => $product->get_meta('bk_token_json'),
            "att_image" => $product->get_meta('bk_att_token_image'),
            "sku" => $product->get_sku()
        );

        return $product_data;
    }

    public static function upload_ipfs_image($id)
    {
        $featured_image_url = get_the_post_thumbnail_url($id, 'full');

        if (!$featured_image_url) {
            $featured_image_url = wc_placeholder_img_src();
        }

        if (!self::$adapter) {
            self::$adapter = new RestAdapter();
        }

        $bak_file = self::$adapter->upload_attachment_to_ipfs_from_url($featured_image_url);

        $img_ipfs = $bak_file->{'ipfs'};

        // grab the product
        $product = wc_get_product($id);

        // save the custom SKU using WooCommerce built-in functions
        $product->update_meta_data('bk_token_image', $img_ipfs);
        $product->update_meta_data('bk_att_token_image', $img_ipfs);

        $attachment_id = attachment_url_to_postid($featured_image_url);
        if ($attachment_id) {
            $img_metadata = wp_get_attachment_metadata($attachment_id);
            $img_metadata['ipfs'] = $img_ipfs;
            wp_update_attachment_metadata($attachment_id, $img_metadata); // save it back to the db
        }

        return array(
            'product_id' => $id,
            'image' => $img_ipfs,
        );
    }

    public static function fetch_ipfs_image($id)
    {

        $bk_token_att = get_post_meta($id, 'bk_att_token_image', true);

        if (!$bk_token_att) {
            $featured_image_url = get_the_post_thumbnail_url($id, 'full');

            if (!$featured_image_url) {
                $featured_image_url = wc_placeholder_img_src();
            }

            $bk_token_att = attachment_url_to_postid($featured_image_url);
        }

        $img_metadata = wp_get_attachment_metadata($bk_token_att);
        $img_ipfs = null;
        if ($img_metadata && array_key_exists('ipfs', $img_metadata)) {
            $img_ipfs = $img_metadata['ipfs'];
        }

        if (!$img_ipfs) {
            $img_ipfs = get_post_meta($id, 'bk_token_image', true);
        }

        return array(
            'product_id' => $id,
            'image' => $img_ipfs,
            'name' => get_the_title($id),
            // 'short_description' => wp_trim_excerpt(get_post_field('post_excerpt', $id))
        );
    }
}
