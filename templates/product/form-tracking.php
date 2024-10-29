<?php

/**
 * Fingeprint tracking form
 *
 * @package BakExtension\Templates
 * @version 1.3.3
 */

defined('ABSPATH') || exit;

global $post;

?>

<form action="<?php echo esc_url(get_permalink($post->ID)); ?>" method="post" class="woocommerce-form woocommerce-form-track-asset track_asset">

	<p><?php esc_html_e('To track an asset please enter the fingerprint in the box below and press the "Track" button.'); ?></p>

	<p class="form-row form-row-first"><label for="fingerprint"><?php esc_html_e('Token'); ?></label></br> <input class="input-text" type="text" name="fingerprint" id="fingerprint" value="<?php echo isset($_REQUEST['fingerprint']) ? esc_attr(wp_unslash($_REQUEST['fingerprint'])) : ''; ?>" placeholder="<?php esc_attr_e('Type a valid fingerprint'); ?>" /></p><?php // @codingStandardsIgnoreLine 
																																																																																							?>
	<div class="clear"></div>

	<p class="form-row"><button type="submit" class="button button-primary wp-element-button" name="track" value="<?php esc_attr_e('Track'); ?>"><?php esc_html_e('Track'); ?></button></p>
	<?php wp_nonce_field('bak-asset_tracking', 'bak-asset-tracking-nonce'); ?>

</form>