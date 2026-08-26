<?php

namespace htrxuan\hdinv;

if (!defined('ABSPATH')) {
    exit;
}

class HDINV_Activator
{

    public static function activate()
    {
        if (!self::is_woocommerce_active()) {
            deactivate_plugins(plugin_basename(HDINV_PLUGIN_FILE));
            set_transient('hdinv_wc_missing_notice', true, 30);
            return;
        }
    }

    public static function is_woocommerce_active()
    {
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        return is_plugin_active('woocommerce/woocommerce.php') || class_exists('WooCommerce');
    }
}
