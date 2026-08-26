<?php

namespace htrxuan\hdinv;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The security-critical file. Every document request is authorized before anything is
 * rendered -- never trusting a bare order ID. This is the direct, deliberate fix for two real
 * 2026 CVEs in a competing plugin: sensitive data exposure (CVE-2026-49056) and an IDOR letting
 * an authenticated attacker view another customer's invoice (CVE-2026-13116).
 */
class HDINV_Document
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
        add_action('admin_post_hdinv_document', array($this, 'handle_request'));
        add_action('admin_post_nopriv_hdinv_document', array($this, 'handle_request'));
    }

    public function handle_request()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- this is a read-only document view, not a state-changing action; access is controlled entirely by can_view() below (capability, order ownership, or the order's own secret key), not by a nonce.
        $order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $type = (isset($_GET['type']) && 'packing-slip' === $_GET['type']) ? 'packing-slip' : 'invoice';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $key = isset($_GET['key']) ? sanitize_text_field(wp_unslash($_GET['key'])) : '';

        $order = $order_id ? wc_get_order($order_id) : false;

        if (!$order instanceof \WC_Order || !$this->can_view($order, $key)) {
            wp_die(esc_html__('You are not allowed to view this document.', 'hdwebmobile-invoices-packing-slips'), '', array('response' => 403));
        }

        HDINV_Template::render($order, $type);
        exit;
    }

    /**
     * Authorizes a request against a specific order via one of three paths: admin capability,
     * confirmed ownership of a logged-in customer's own order, or the order's real secret key
     * (guest access) -- the exact same hash_equals()-based check WooCommerce core's own
     * thank-you-page and cancel-order links use (WC_Order::key_is_valid()).
     */
    private function can_view($order, $key)
    {
        if (current_user_can('manage_woocommerce')) {
            return true;
        }

        if (is_user_logged_in() && $order->get_customer_id() > 0 && (int) $order->get_customer_id() === get_current_user_id()) {
            return true;
        }

        if ('' !== $key && $order->key_is_valid($key)) {
            return true;
        }

        return false;
    }
}
