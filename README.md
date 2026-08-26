# HDWebmobile Invoices & Packing Slips

On-demand invoice and packing-slip documents for WooCommerce orders, printable to PDF from any browser -- no bundled PDF library.

- **WordPress.org:** https://wordpress.org/plugins/hdwebmobile-invoices-packing-slips/
- **Requires:** WordPress 6.9+, WooCommerce, PHP 7.4+
- **License:** GPLv2 or later

## Description

HDWebmobile Invoices & Packing Slips adds a clean, print-ready invoice and packing slip to every WooCommerce order. Customers get a "Download Invoice" and "Download Packing Slip" link on their order page, and admins get matching "Print" links on the Edit Order screen. Both open a standalone, print-optimized page -- click Print, choose "Save as PDF", and you have your PDF. No PDF library is bundled with this plugin, which keeps it small and avoids an entire category of library-specific bugs.

Every document request is checked before anything is shown. A leading competing plugin in this space has two disclosed 2026 vulnerabilities in exactly this area: one exposing customer documents without proper authorization, and one letting a logged-in attacker view a completely different customer's invoice just by changing a number in the URL. This plugin never trusts an order ID alone -- every request is authorized as the order's owner, an administrator, or (for guest orders) the order's own real secret key, using the same `hash_equals()`-based check WooCommerce's own core checkout pages use for guest order access.

## Features

* On-demand Invoice (full line items, prices, totals, tax, payment method) and Packing Slip (shipping address and item quantities only, no prices) documents
* Print-ready HTML that becomes a PDF via the browser's own Print dialog -- no bundled PDF library, no extra plugin weight
* Upload your own logo and pick an accent color -- shown on every document, using the WordPress media library and color picker you already know
* Design fully overridable by a theme developer: copy the plugin's template to yourtheme/woocommerce/hdinv/document.php, the same override convention WooCommerce itself uses
* "Download Invoice" / "Download Packing Slip" links on the customer's My Account order page; matching "Print" links on the admin Edit Order screen
* Every document request is authorization-checked -- order owner, administrator, or a valid order key -- never a bare guessable order ID
* Shows any per-item options/customizations already attached to the order (e.g. from a product-options plugin) alongside each line item
* Works for guest and logged-in customers alike

## Development

Standard WordPress plugin structure:

```
hdwebmobile-invoices-packing-slips.php    Bootstrap
includes/class-hdinv-activator.php
includes/class-hdinv-admin.php
includes/class-hdinv-core.php
includes/class-hdinv-document.php
includes/class-hdinv-hub.php
includes/class-hdinv-links.php
includes/class-hdinv-template.php
```

Part of the [HDWebmobile](https://hdwebmobile.com/plugins/) suite of focused, single-purpose WooCommerce plugins.

## License

GPLv2 or later. See [LICENSE](LICENSE).

