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

    /**
     * Registers this plugin's own print stylesheet through WordPress's real enqueue system
     * (never a raw echoed <style> tag or an inlined file_get_contents()), then prints only that
     * one handle -- deliberately wp_print_styles('hdinv-print'), not the full wp_head(), since
     * this standalone document page has no theme header/footer and calling wp_head() would pull
     * in the entire site's front-end style/script queue (emoji scripts, RSD links, oEmbed
     * discovery, every other plugin's enqueued assets) for no reason.
     */
    public static function print_styles($accent_color = '')
    {
        wp_enqueue_style('hdinv-print', HDINV_PLUGIN_URL . 'assets/css/hdinv-print.css', array(), HDINV_VERSION);
        if ($accent_color) {
            wp_add_inline_style('hdinv-print', ':root { --hdinv-accent: ' . esc_html($accent_color) . '; }');
        }
        wp_print_styles('hdinv-print');
    }
}
