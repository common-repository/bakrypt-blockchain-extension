<?php

/**
 * Fingerprint tracking
 *
 * @package BakExtension\Templates
 * @version 1.3.3
 */

defined('ABSPATH') || exit;

$product_attributes = $product->get_attributes();

array_push($product_attributes, array(
	"label" => "Fingerprint",
	"value" => $fingerprint
));

$product_permalink = get_permalink($product->get_id());

?>

<div class="product">
	<a href="" style="float:right">Clear</a>
	<h1 class="product-title"><?php echo esc_html($product->get_name()); ?></h1>

	<!-- Main Product Image -->
	<div class="product-image">
		<?php echo $product->get_image(); ?>
	</div>

	<div class="product-price"><?php echo $product->get_price_html(); ?></div>

	<!-- Product Image Gallery -->
	<?php
	$attachment_ids = $product->get_gallery_image_ids();

	if ($attachment_ids) {
	?>
		<div class="product-image-gallery">
			<?php foreach ($attachment_ids as $attachment_id) : ?>
				<?php echo wp_get_attachment_image($attachment_id, 'thumbnail'); ?>
			<?php endforeach; ?>
		</div>
	<?php
	}
	?>

	<!-- Short Description -->
	<div class="product-short-description">
		<?php echo wpautop($product->get_short_description()); ?>
	</div>

	<div class="product_meta">

		<?php do_action('woocommerce_product_meta_start'); ?>

		<?php if (wc_product_sku_enabled() && ($product->get_sku() || $product->is_type('variable'))) : ?>

			<span class="sku_wrapper"><?php esc_html_e('SKU:', 'woocommerce'); ?> <span class="sku"><?php echo ($sku = $product->get_sku()) ? $sku : esc_html__('N/A', 'woocommerce'); ?></span></span>

		<?php endif; ?>

		<?php echo wc_get_product_category_list($product->get_id(), ', ', '<span class="posted_in">' . _n('Category:', 'Categories:', count($product->get_category_ids()), 'woocommerce') . ' ', '</span>'); ?>

		<?php echo wc_get_product_tag_list($product->get_id(), ', ', '<span class="tagged_as">' . _n('Tag:', 'Tags:', count($product->get_tag_ids()), 'woocommerce') . ' ', '</span>'); ?>

		<?php do_action('woocommerce_product_meta_end'); ?>

	</div>

	<table class="woocommerce-product-attributes shop_attributes">
		<?php foreach ($product_attributes as $product_attribute_key => $product_attribute) : ?>
			<tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--<?php echo esc_attr($product_attribute_key); ?>">
				<th class="woocommerce-product-attributes-item__label"><?php echo wp_kses_post($product_attribute['label']); ?></th>
				<td class="woocommerce-product-attributes-item__value"><?php echo wp_kses_post($product_attribute['value']); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>

	<?php do_action('woocommerce_product_additional_information', $product); ?>

	<p class="form-row"><a href="<?php echo esc_url($product_permalink) ?>" class="button button-primary wp-element-button" value="<?php esc_attr_e('View Product'); ?>"><?php esc_html_e('View Product'); ?></a></p>
</div>