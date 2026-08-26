<?php

/**
 * Plugin Name: HDWebmobile Invoices & Packing Slips
 * Plugin URI: https://hdwebmobile.com/plugins/hdwebmobile-invoices-packing-slips/
 * Description: On-demand invoice and packing-slip documents for WooCommerce orders, printable to PDF from any browser. Every document request is authorization-checked, never trusting a bare order ID.
 * Version: 1.0.0
 * Author: htrxuan - Han Tran
 * Author URI: https://hdwebmobile.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: hdwebmobile-invoices-packing-slips
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 * Requires PHP: 7.4
 * Requires at least: 6.9
 */

namespace htrxuan\hdinv;

if (!defined('ABSPATH')) {
    exit;
}

define('HDINV_VERSION', '1.0.0');
define('HDINV_PLUGIN_FILE', __FILE__);
define('HDINV_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HDINV_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once HDINV_PLUGIN_DIR . 'includes/class-hdinv-activator.php';

register_activation_hook(__FILE__, array(HDINV_Activator::class, 'activate'));

add_action('before_woocommerce_init', function () {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', HDINV_PLUGIN_FILE, true);
    }
});

add_action('plugins_loaded', function () {
    require_once HDINV_PLUGIN_DIR . 'includes/class-hdinv-core.php';
    HDINV_Core::get_instance();
});

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $donate_link = '<a href="https://paypal.me/htrxuan/20" target="_blank" rel="noopener noreferrer">' . esc_html__('Donate', 'hdwebmobile-invoices-packing-slips') . '</a>';
    array_unshift($links, $donate_link);
    return $links;
});
