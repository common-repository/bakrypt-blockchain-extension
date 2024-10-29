<?php

/**
 * Product List
 * A class which represents Product list operations and renderization.
 *
 * @package BakExtension\controllers
 * @version 1.0.0
 * @since   1.0.0
 */

namespace BakExtension\controllers;

defined('ABSPATH') || exit;

use BakExtension\api\RestAdapter;
use BakExtension\controllers\Product;

class ProductList
{
	private static $adapter;

	protected function __construct()
	{
	}

	public static function bak_fingerprint_column($columns)
	{
		$columns['asset_fingerprint'] = __('Token');

		return $columns;
	}

	public static function bak_fingerprint_column_data($column, $post_id)
	{

		switch ($column) {
			case 'asset_fingerprint': // This has to match to the defined column in function above
				$get_fingerprint = get_post_meta($post_id, 'bk_token_fingerprint', true);
				$get_status = get_post_meta($post_id, 'bk_token_status', true);
				if ($get_fingerprint) {
					echo "<a target='_blank' rel='nofollow' href='https://cexplorer.io/asset/" . esc_html($get_fingerprint) . "'>" . esc_html($get_fingerprint) . "</a>";
				} else {
					echo esc_html($get_status);
				}
				break;
		}
	}

	private static function bak_get_filter_options()
	{
		$options = [
			[
				'name' => 'Filter by Tokenization Status',
				'value' => '',
				'selected' => (!isset($_GET['tokenize']) || empty($_GET['tokenize'])) ? 'selected' : '',
			],
			[
				'name' => 'Tokenized',
				'value' => 'yes',
				'selected' => (isset($_GET['tokenize']) && $_GET['tokenize'] == 'yes') ? 'selected="selected"' : '',
			],
			[
				'name' => 'Non-Token',
				'value' => 'no',
				'selected' => (isset($_GET['tokenize']) && $_GET['tokenize'] == 'no') ? 'selected="selected"' : '',
			],
		];

		// html
		$output = '';
		foreach ($options as $option) {
			$output .= '<option value="' . $option['value'] . '" ' . $option['selected'] . '>' . $option['name'] . '</option>';
		}

		return $output;
	}

	public static function bak_custom_filter($output)
	{
		global $wp_query;
		$output .= '<select class="token-filter dropdown_product_cat" name="tokenize">' . self::bak_get_filter_options() . '</select>';

		return $output;
	}

	public static function bak_products_filter_query($query)
	{
		if (is_admin()) {
			if (isset($_GET['tokenize']) && !empty($_GET['tokenize'])) {

				$meta_query = (array) $query->get('meta_query');

				if ($_GET['tokenize'] == "yes") {
					$meta_query[] = [
						'relation' => "AND",
						array(
							'key' => 'bk_token_fingerprint',
							'compare' => 'EXISTS',
						),
						array(
							'key' => 'bk_token_fingerprint',
							'compare' => '!=',
							"value" => ""
						)

					];
				} else {
					$meta_query[] = [
						'relation' => "OR",
						array(
							'key' => 'bk_token_fingerprint',
							'compare' => "NOT EXISTS",
						),
						array(
							'key' => 'bk_token_fingerprint',
							'compare' => "=",
							'value' => ""
						)

					];
				}


				$query->set('meta_query', $meta_query);
			}
		}
	}

	public static function add_mint_bulk_action($actions)
	{
		$actions['mint'] = 'Mint as Tokens';
		$actions['qr'] = 'Create QR Codes';
		return $actions;
	}


	public static function update_products($products)
	{
		$func = function ($product) {

			$data = array(
				'bk_token_uuid' => $product['uuid'],
				'bk_token_asset_name' => $product['asset_name'],
				'bk_token_name' => $product['name'],
				'bk_token_image' => $product['image'],
				'bk_token_amount' => $product['amount'],
				'bk_token_status' => $product['status'],
				'bk_token_transaction' => $product['transaction'],
				'bk_att_token_image' => $product['image'],
			);

			return Product::update_record($product["product_id"], $data);
		};

		return array_map($func, $products);
	}

	public static function get_products($ids)
	{
		$func = function ($id) {
			return Product::get_product_data($id);
		};

		return array_map($func, $ids);
	}
}
