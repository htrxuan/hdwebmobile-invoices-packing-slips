<?php

namespace htrxuan\hdinv;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Loads the standalone, print-ready document template. Uses WooCommerce's own wc_get_template()
 * so a theme can override the design by copying templates/document.php to
 * yourtheme/woocommerce/hdinv/document.php -- the same override convention WooCommerce itself
 * uses, rather than a custom template-loading mechanism.
 */
class HDINV_Template
{

    public static function render($order, $type)
    {
        $options    = HDINV_Admin::get_options();
        $is_invoice = 'invoice' === $type;
        $title      = $is_invoice
            ? __('Invoice', 'hdwebmobile-invoices-packing-slips')
            : __('Packing Slip', 'hdwebmobile-invoices-packing-slips');

        wc_get_template(
            'hdinv/document.php',
            array(
                'order'      => $order,
                'options'    => $options,
                'is_invoice' => $is_invoice,
                'title'      => $title,
            ),
            '',
            HDINV_PLUGIN_DIR . 'templates/'
        );
    }

    public static function print_styles()
    {
        $css_path = HDINV_PLUGIN_DIR . 'assets/css/hdinv-print.css';
        if (file_exists($css_path)) {
            echo '<style>' . file_get_contents($css_path) . '</style>'; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents, WordPress.Security.EscapeOutput.OutputNotEscaped -- reading and inlining our own plugin's static local CSS file, not user input.
        }
    }
}
