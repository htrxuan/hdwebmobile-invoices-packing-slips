<?php

namespace htrxuan\hdinv;

if (!defined('ABSPATH')) {
    exit;
}

class HDINV_Admin
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
        require_once HDINV_PLUGIN_DIR . 'includes/class-hdinv-hub.php';
        add_filter('hdwebmobile_hub_tabs', array($this, 'register_hub_tabs'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_admin_assets($hook)
    {
        if ('woocommerce_page_hdwebmobile' !== $hook) {
            return;
        }

        // Read-only tab selector, same pattern as core's own admin tab UIs -- no state
        // change occurs from reading it, so nonce verification doesn't apply here.
        $tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ('invoices-packing-slips' !== $tab) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('hdinv-admin', HDINV_PLUGIN_URL . 'assets/js/hdinv-admin.js', array('jquery', 'wp-color-picker'), self::asset_version('assets/js/hdinv-admin.js'), true);
    }

    /**
     * Uses the file's own last-modified time as the cache-busting version instead of the
     * static plugin version, so a JS edit is picked up immediately without a manual bump.
     */
    private static function asset_version($relative_path)
    {
        $path = HDINV_PLUGIN_DIR . $relative_path;
        return file_exists($path) ? (string) filemtime($path) : HDINV_VERSION;
    }

    public function register_hub_tabs($tabs)
    {
        $tabs['invoices-packing-slips'] = array(
            'label'  => __('Invoices & Packing Slips', 'hdwebmobile-invoices-packing-slips'),
            'order'  => 80,
            'render' => array($this, 'render_settings_page'),
        );
        return $tabs;
    }

    public function render_settings_page()
    {
        ?>
        <p><?php esc_html_e('On-demand invoice and packing-slip documents for WooCommerce orders, printable to PDF from any browser -- no bundled PDF library. Set your business details below; customers get "Download" links on their order page automatically.', 'hdwebmobile-invoices-packing-slips'); ?></p>
        <form method="post" action="options.php">
            <?php
            settings_fields('hdinv_option_group');
            do_settings_sections('hdinv-settings');
            submit_button();
            ?>
        </form>
        <?php
    }

    public function page_init()
    {
        register_setting(
            'hdinv_option_group',
            'hdinv_options',
            array(
                'type'              => 'array',
                'sanitize_callback' => array($this, 'sanitize'),
                'default'           => array(),
            )
        );

        add_settings_section(
            'hdinv_section_general',
            __('Business Details', 'hdwebmobile-invoices-packing-slips'),
            '__return_false',
            'hdinv-settings'
        );

        add_settings_field('enabled', __('Enable Invoices & Packing Slips', 'hdwebmobile-invoices-packing-slips'), array($this, 'enabled_callback'), 'hdinv-settings', 'hdinv_section_general');
        add_settings_field('logo_id', __('Logo', 'hdwebmobile-invoices-packing-slips'), array($this, 'logo_callback'), 'hdinv-settings', 'hdinv_section_general');
        add_settings_field('accent_color', __('Accent color', 'hdwebmobile-invoices-packing-slips'), array($this, 'accent_color_callback'), 'hdinv-settings', 'hdinv_section_general');
        add_settings_field('business_name', __('Business name', 'hdwebmobile-invoices-packing-slips'), array($this, 'business_name_callback'), 'hdinv-settings', 'hdinv_section_general');
        add_settings_field('address', __('Business address', 'hdwebmobile-invoices-packing-slips'), array($this, 'address_callback'), 'hdinv-settings', 'hdinv_section_general');
        add_settings_field('tax_id', __('Tax / VAT ID', 'hdwebmobile-invoices-packing-slips'), array($this, 'tax_id_callback'), 'hdinv-settings', 'hdinv_section_general');
        add_settings_field('footer_note', __('Invoice footer note', 'hdwebmobile-invoices-packing-slips'), array($this, 'footer_note_callback'), 'hdinv-settings', 'hdinv_section_general');
    }

    public static function get_options()
    {
        $defaults = array(
            'enabled'       => 1,
            'logo_id'       => 0,
            'accent_color'  => '',
            'business_name' => get_bloginfo('name'),
            'address'       => '',
            'tax_id'        => '',
            'footer_note'   => __('Thank you for your business!', 'hdwebmobile-invoices-packing-slips'),
        );

        return wp_parse_args(get_option('hdinv_options', array()), $defaults);
    }

    public function sanitize($input)
    {
        $new_input = array();

        $new_input['enabled']       = isset($input['enabled']) ? 1 : 0;
        $new_input['logo_id']       = isset($input['logo_id']) ? absint($input['logo_id']) : 0;
        $new_input['accent_color']  = isset($input['accent_color']) && sanitize_hex_color($input['accent_color']) ? sanitize_hex_color($input['accent_color']) : '';
        $new_input['business_name'] = isset($input['business_name']) ? sanitize_text_field($input['business_name']) : '';
        $new_input['address']       = isset($input['address']) ? sanitize_textarea_field($input['address']) : '';
        $new_input['tax_id']        = isset($input['tax_id']) ? sanitize_text_field($input['tax_id']) : '';
        $new_input['footer_note']   = isset($input['footer_note']) ? sanitize_text_field($input['footer_note']) : '';

        return $new_input;
    }

    public function enabled_callback()
    {
        $options = self::get_options();
        printf(
            '<input type="checkbox" name="hdinv_options[enabled]" value="1" %s />',
            checked(1, $options['enabled'], false)
        );
    }

    public function logo_callback()
    {
        $options  = self::get_options();
        $logo_id  = $options['logo_id'];
        $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'medium') : '';
        ?>
        <div class="hdinv-logo-field">
            <img
                src="<?php echo esc_url($logo_url); ?>"
                id="hdinv-logo-preview"
                style="max-width:150px;max-height:80px;display:<?php echo $logo_url ? 'block' : 'none'; ?>;margin-bottom:8px;"
                alt=""
            />
            <input type="hidden" name="hdinv_options[logo_id]" id="hdinv-logo-id" value="<?php echo esc_attr($logo_id); ?>" />
            <p>
                <button type="button" class="button" id="hdinv-logo-select"><?php esc_html_e('Choose Logo', 'hdwebmobile-invoices-packing-slips'); ?></button>
                <button type="button" class="button" id="hdinv-logo-remove" style="<?php echo $logo_url ? '' : 'display:none;'; ?>"><?php esc_html_e('Remove', 'hdwebmobile-invoices-packing-slips'); ?></button>
            </p>
            <p class="description"><?php esc_html_e('Shown at the top of every document instead of the business name text, if set.', 'hdwebmobile-invoices-packing-slips'); ?></p>
        </div>
        <?php
    }

    public function accent_color_callback()
    {
        $options = self::get_options();
        printf(
            '<input type="text" name="hdinv_options[accent_color]" id="hdinv-accent-color" value="%s" class="hdinv-color-picker" data-default-color="#1d2327" />',
            esc_attr($options['accent_color'])
        );
    }

    public function business_name_callback()
    {
        $options = self::get_options();
        printf('<input type="text" name="hdinv_options[business_name]" value="%s" class="regular-text" />', esc_attr($options['business_name']));
    }

    public function address_callback()
    {
        $options = self::get_options();
        printf(
            '<textarea name="hdinv_options[address]" rows="3" class="regular-text">%s</textarea><p class="description">%s</p>',
            esc_textarea($options['address']),
            esc_html__('Shown in the invoice/packing-slip header. One line per address line.', 'hdwebmobile-invoices-packing-slips')
        );
    }

    public function tax_id_callback()
    {
        $options = self::get_options();
        printf('<input type="text" name="hdinv_options[tax_id]" value="%s" class="regular-text" />', esc_attr($options['tax_id']));
    }

    public function footer_note_callback()
    {
        $options = self::get_options();
        printf('<input type="text" name="hdinv_options[footer_note]" value="%s" class="regular-text" />', esc_attr($options['footer_note']));
    }
}
