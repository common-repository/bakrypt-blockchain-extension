<?php

/**
 * Plugin Name: Brand Guard QR
 * Plugin URI: https://bakrypt.io
 * Description: Mint your products into the Cardano Blockchain
 * Version: 1.3.8
 * Author: Wolfgang Leon
 * Author URI: https://bakrypt.io/
 * Developer: Wolfgang Leon
 * Developer URI: https://bakrypt.io/pool/
 * Text Domain: bakrypt-wc-extension
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * 
 * WC requires at least: 7.1
 * WC tested up to: 9.1.4
 *
 */

defined('ABSPATH') || exit;

define('WCBAK_ABSPATH', __DIR__ . '/');
define('WCBAK_PLUGIN_FILE', __FILE__);

# Autoload Classes with Composer
require_once "vendor/autoload.php";

// Initiate wc bakrypt class
use BakExtension\core\BakWCExtension;


# Add custom interval for every 3 minutes
add_filter('cron_schedules', 'bak_add_every_three_minutes');
function bak_add_every_three_minutes($schedules)
{
	$schedules['every_three_minutes'] = array(
		'interval' => 180,
		'display' => __('Every 3 Minutes', 'textdomain')
	);
	return $schedules;
}

function wcbakrypt_init()
{
	BakWCExtension::init();
}

add_action('plugins_loaded', 'wcbakrypt_init', 11);

// ========= Cron Tasks ======= 
function cron_activate()
{
	if (!wp_next_scheduled('bak_plugin_cron_task')) {
		// Schedule the cron task to run every 3 minutes
		wp_schedule_event(time(), 'every_three_minutes', 'bak_plugin_cron_task');
	}
}

function cron_deactivate()
{
	wp_clear_scheduled_hook('bak_plugin_cron_task');

	// Remove the lock when the task is completed
	delete_transient('bak_plugin_cron_lock');
}

register_activation_hook(WCBAK_PLUGIN_FILE, 'cron_activate');
add_action('bak_plugin_cron_task', array("BakExtension\core\Cron", "bak_run_cron_task"), 12);
register_deactivation_hook(WCBAK_PLUGIN_FILE, 'cron_deactivate');
