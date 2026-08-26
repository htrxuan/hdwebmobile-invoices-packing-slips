<?php
/**
 * Invoice / packing-slip document template.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/hdinv/document.php,
 * the same override convention WooCommerce itself uses for its own templates (via
 * wc_get_template()). The same file renders both document types, switched by $is_invoice.
 *
 * Available variables: $order (WC_Order), $options (array from HDINV_Admin::get_options()),
 * $is_invoice (bool), $title (string).
 *
 * @package HDWebmobile_Invoices_Packing_Slips
 */

namespace htrxuan\hdinv;

if (!defined('ABSPATH')) {
    exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- this is a wc_get_template() template file, not global scope; variable names deliberately match WooCommerce's own bundled-template convention ($order, $item, etc.) rather than being prefixed.

$logo_url     = !empty($options['logo_id']) ? wp_get_attachment_image_url($options['logo_id'], 'medium') : '';
$accent_color = !empty($options['accent_color']) ? $options['accent_color'] : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo esc_html($title . ' #' . $order->get_order_number()); ?></title>
    <?php HDINV_Template::print_styles(); ?>
    <?php if ($accent_color) : ?>
        <style>:root { --hdinv-accent: <?php echo esc_html($accent_color); ?>; }</style>
    <?php endif; ?>
</head>
<body>
    <button type="button" class="hdinv-print-button no-print" onclick="window.print()">
        <?php esc_html_e('Print / Save as PDF', 'hdwebmobile-invoices-packing-slips'); ?>
    </button>

    <div class="hdinv-document">
        <header class="hdinv-header">
            <div class="hdinv-business">
                <?php if ($logo_url) : ?>
                    <img class="hdinv-logo" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($options['business_name']); ?>" />
                <?php else : ?>
                    <strong><?php echo esc_html($options['business_name']); ?></strong>
                <?php endif; ?>
                <?php if ($options['address']) : ?>
                    <div class="hdinv-address"><?php echo nl2br(esc_html($options['address'])); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_html() already applied before nl2br(). ?></div>
                <?php endif; ?>
                <?php if ($options['tax_id']) : ?>
                    <div><?php echo esc_html__('Tax/VAT ID:', 'hdwebmobile-invoices-packing-slips') . ' ' . esc_html($options['tax_id']); ?></div>
                <?php endif; ?>
            </div>
            <div class="hdinv-doc-title">
                <h1><?php echo esc_html($title); ?></h1>
                <div><?php echo esc_html__('Order #', 'hdwebmobile-invoices-packing-slips') . esc_html($order->get_order_number()); ?></div>
                <div><?php echo esc_html($order->get_date_created() ? wc_format_datetime($order->get_date_created()) : ''); ?></div>
            </div>
        </header>

        <section class="hdinv-addresses">
            <?php if ($is_invoice) : ?>
                <div>
                    <h3><?php esc_html_e('Billing Address', 'hdwebmobile-invoices-packing-slips'); ?></h3>
                    <?php echo wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'hdwebmobile-invoices-packing-slips'))); ?>
                </div>
            <?php endif; ?>
            <div>
                <h3><?php esc_html_e('Shipping Address', 'hdwebmobile-invoices-packing-slips'); ?></h3>
                <?php
                $shipping_address = $order->get_formatted_shipping_address();
                echo wp_kses_post($shipping_address ? $shipping_address : $order->get_formatted_billing_address(esc_html__('N/A', 'hdwebmobile-invoices-packing-slips')));
                ?>
            </div>
        </section>

        <table class="hdinv-items">
            <thead>
                <tr>
                    <th><?php esc_html_e('Item', 'hdwebmobile-invoices-packing-slips'); ?></th>
                    <th class="hdinv-num"><?php esc_html_e('Qty', 'hdwebmobile-invoices-packing-slips'); ?></th>
                    <?php if ($is_invoice) : ?>
                        <th class="hdinv-num"><?php esc_html_e('Price', 'hdwebmobile-invoices-packing-slips'); ?></th>
                        <th class="hdinv-num"><?php esc_html_e('Total', 'hdwebmobile-invoices-packing-slips'); ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order->get_items() as $item) : ?>
                    <tr>
                        <td>
                            <?php echo esc_html($item->get_name()); ?>
                            <?php foreach ($item->get_formatted_meta_data() as $meta) : ?>
                                <br /><small class="hdinv-item-meta"><?php echo wp_kses_post($meta->display_key); ?>: <?php echo wp_kses_post($meta->display_value); ?></small>
                            <?php endforeach; ?>
                        </td>
                        <td class="hdinv-num"><?php echo esc_html($item->get_quantity()); ?></td>
                        <?php if ($is_invoice) : ?>
                            <td class="hdinv-num"><?php echo wp_kses_post(wc_price($order->get_item_subtotal($item, false, true))); ?></td>
                            <td class="hdinv-num"><?php echo wp_kses_post(wc_price($item->get_total())); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <?php if ($is_invoice) : ?>
                <tfoot>
                    <tr>
                        <td colspan="3"><?php esc_html_e('Subtotal', 'hdwebmobile-invoices-packing-slips'); ?></td>
                        <td class="hdinv-num"><?php echo wp_kses_post(wc_price($order->get_subtotal())); ?></td>
                    </tr>
                    <?php if ((float) $order->get_total_discount() > 0) : ?>
                        <tr>
                            <td colspan="3"><?php esc_html_e('Discount', 'hdwebmobile-invoices-packing-slips'); ?></td>
                            <td class="hdinv-num">&minus;<?php echo wp_kses_post(wc_price($order->get_total_discount())); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ((float) $order->get_shipping_total() > 0) : ?>
                        <tr>
                            <td colspan="3"><?php esc_html_e('Shipping', 'hdwebmobile-invoices-packing-slips'); ?></td>
                            <td class="hdinv-num"><?php echo wp_kses_post(wc_price($order->get_shipping_total())); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ((float) $order->get_total_tax() > 0) : ?>
                        <tr>
                            <td colspan="3"><?php esc_html_e('Tax', 'hdwebmobile-invoices-packing-slips'); ?></td>
                            <td class="hdinv-num"><?php echo wp_kses_post(wc_price($order->get_total_tax())); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr class="hdinv-grand-total">
                        <td colspan="3"><?php esc_html_e('Total', 'hdwebmobile-invoices-packing-slips'); ?></td>
                        <td class="hdinv-num"><?php echo wp_kses_post(wc_price($order->get_total())); ?></td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>

        <?php if ($is_invoice && $order->get_payment_method_title()) : ?>
            <p class="hdinv-payment-method">
                <?php echo esc_html__('Payment method:', 'hdwebmobile-invoices-packing-slips') . ' ' . esc_html($order->get_payment_method_title()); ?>
            </p>
        <?php endif; ?>

        <?php if ($options['footer_note']) : ?>
            <footer class="hdinv-footer"><?php echo esc_html($options['footer_note']); ?></footer>
        <?php endif; ?>
    </div>
</body>
</html>
