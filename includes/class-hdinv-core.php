<?php

namespace htrxuan\hdinv;

if (!defined('ABSPATH')) {
    exit;
}

final class HDINV_Core
{

    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->includes();
        $this->init_hooks();
    }

    private function __clone()
    {
    }

    private function includes()
    {
        require_once HDINV_PLUGIN_DIR . 'includes/class-hdinv-admin.php';
        require_once HDINV_PLUGIN_DIR . 'includes/class-hdinv-template.php';
        require_once HDINV_PLUGIN_DIR . 'includes/class-hdinv-document.php';
        require_once HDINV_PLUGIN_DIR . 'includes/class-hdinv-links.php';
    }

    private function init_hooks()
    {
        add_action('admin_notices', array($this, 'render_missing_woocommerce_notice'));

        if (!class_exists('WooCommerce')) {
            return;
        }

        HDINV_Document::get_instance();
        HDINV_Links::get_instance();

        if (is_admin()) {
            HDINV_Admin::get_instance();
        }
    }

    public function render_missing_woocommerce_notice()
    {
        $screen = get_current_screen();
        if (!$screen || 'plugins' !== $screen->id) {
            return;
        }

        if (!get_transient('hdinv_wc_missing_notice')) {
            return;
        }
        delete_transient('hdinv_wc_missing_notice');
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <?php esc_html_e('HDWebmobile Invoices & Packing Slips requires WooCommerce to be installed and active. The plugin has been deactivated.', 'hdwebmobile-invoices-packing-slips'); ?>
            </p>
        </div>
        <?php
    }
}
