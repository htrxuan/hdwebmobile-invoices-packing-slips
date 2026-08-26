<?php

namespace htrxuan\hdinv;

if (!defined('ABSPATH')) {
    exit;
}

class HDINV_Links
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
        add_action('woocommerce_view_order', array($this, 'render_customer_links'), 5);
        add_action('woocommerce_admin_order_data_after_order_details', array($this, 'render_admin_links'));
    }

    public function render_customer_links($order_id)
    {
        $options = HDINV_Admin::get_options();
        if (empty($options['enabled'])) {
            return;
        }

        $order = wc_get_order($order_id);
        if (!$order instanceof \WC_Order) {
            return;
        }
        ?>
        <p class="hdinv-links">
            <a class="button" href="<?php echo esc_url($this->build_url($order, 'invoice')); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Download Invoice', 'hdwebmobile-invoices-packing-slips'); ?></a>
            <a class="button" href="<?php echo esc_url($this->build_url($order, 'packing-slip')); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Download Packing Slip', 'hdwebmobile-invoices-packing-slips'); ?></a>
        </p>
        <?php
    }

    public function render_admin_links($order)
    {
        $options = HDINV_Admin::get_options();
        if (empty($options['enabled'])) {
            return;
        }
        ?>
        <p class="form-field form-field-wide hdinv-admin-links">
            <strong><?php esc_html_e('Documents:', 'hdwebmobile-invoices-packing-slips'); ?></strong><br />
            <a href="<?php echo esc_url($this->build_url($order, 'invoice', false)); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Print Invoice', 'hdwebmobile-invoices-packing-slips'); ?></a>
            &nbsp;|&nbsp;
            <a href="<?php echo esc_url($this->build_url($order, 'packing-slip', false)); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e('Print Packing Slip', 'hdwebmobile-invoices-packing-slips'); ?></a>
        </p>
        <?php
    }

    /**
     * The customer-facing link includes the order's real key (safe -- WooCommerce core has
     * already authorized this exact viewer to see this exact order before this hook fires).
     * The admin link omits it -- admin requests authorize via manage_woocommerce capability
     * instead, checked in HDINV_Document::can_view().
     */
    private function build_url($order, $type, $include_key = true)
    {
        $args = array(
            'action'   => 'hdinv_document',
            'order_id' => $order->get_id(),
            'type'     => $type,
        );

        if ($include_key) {
            $args['key'] = $order->get_order_key();
        }

        return add_query_arg($args, admin_url('admin-post.php'));
    }
}
