=== HDWebmobile Invoices & Packing Slips ===
Contributors: htrxuan
Donate link: https://paypal.me/htrxuan/20
Tags: woocommerce, invoice, packing slip, pdf, order documents
Requires at least: 6.9
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
Requires Plugins: woocommerce
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

On-demand invoice and packing-slip documents for WooCommerce orders, printable to PDF from any browser -- no bundled PDF library.

== Description ==

HDWebmobile Invoices & Packing Slips adds a clean, print-ready invoice and packing slip to every WooCommerce order. Customers get a "Download Invoice" and "Download Packing Slip" link on their order page, and admins get matching "Print" links on the Edit Order screen. Both open a standalone, print-optimized page -- click Print, choose "Save as PDF", and you have your PDF. No PDF library is bundled with this plugin, which keeps it small and avoids an entire category of library-specific bugs.

Every document request is checked before anything is shown. A leading competing plugin in this space has two disclosed 2026 vulnerabilities in exactly this area: one exposing customer documents without proper authorization, and one letting a logged-in attacker view a completely different customer's invoice just by changing a number in the URL. This plugin never trusts an order ID alone -- every request is authorized as the order's owner, an administrator, or (for guest orders) the order's own real secret key, using the same `hash_equals()`-based check WooCommerce's own core checkout pages use for guest order access.

= Key Features =
* On-demand Invoice (full line items, prices, totals, tax, payment method) and Packing Slip (shipping address and item quantities only, no prices) documents
* Print-ready HTML that becomes a PDF via the browser's own Print dialog -- no bundled PDF library, no extra plugin weight
* Upload your own logo and pick an accent color -- shown on every document, using the WordPress media library and color picker you already know
* Design fully overridable by a theme developer: copy the plugin's template to yourtheme/woocommerce/hdinv/document.php, the same override convention WooCommerce itself uses
* "Download Invoice" / "Download Packing Slip" links on the customer's My Account order page; matching "Print" links on the admin Edit Order screen
* Every document request is authorization-checked -- order owner, administrator, or a valid order key -- never a bare guessable order ID
* Shows any per-item options/customizations already attached to the order (e.g. from a product-options plugin) alongside each line item
* Works for guest and logged-in customers alike

= Limitations (please read before installing) =
* No automatic email attachment -- documents are generated on demand, not auto-attached to WooCommerce's outgoing order emails, in this version
* No separate sequential invoice-numbering series -- the document uses the WooCommerce order number as its reference; some jurisdictions require a distinct, gapless invoice number for tax compliance, which this version doesn't provide
* No bulk/multi-order printing -- one order, one document, at a time

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/hdwebmobile-invoices-packing-slips` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress. WooCommerce must already be installed and active.
3. Go to **WooCommerce > Invoices & Packing Slips** to set your business name, address, tax/VAT ID, and invoice footer note.

== How to Use ==

= 1. Set your business details =
Go to **WooCommerce > Invoices & Packing Slips** (Screenshot 1) and fill in your business name, address, tax/VAT ID, and a footer note. Optionally upload a logo and pick an accent color -- these appear in the header, footer, and headings of every generated document.

= 2. Customers download their own documents =
On the My Account order view page, customers see "Download Invoice" and "Download Packing Slip" links. Clicking one opens a clean, standalone document (Screenshot 2) with a "Print / Save as PDF" button.

= 3. Admins print from the order screen =
On the admin Edit Order screen, a "Documents" section offers the same "Print Invoice" and "Print Packing Slip" links (Screenshot 3), authorized by your admin capability rather than an order key.

= 4. Save as an actual PDF =
Click "Print / Save as PDF" on the document page, then choose "Save as PDF" (or "Microsoft Print to PDF", depending on your OS/browser) in the print dialog.

= 5. Full design control for developers (optional) =
For complete visual control beyond the logo and accent color, a theme developer can copy `templates/hdinv/document.php` from the plugin to `yourtheme/woocommerce/hdinv/document.php` and edit it freely -- WooCommerce's own theme-override mechanism, so your custom version is never overwritten by a plugin update.

== Screenshots ==

1. The settings page: business name, address, tax ID, and footer note.
2. A generated invoice, with line items, options, totals, and payment method.
3. The "Documents" links on the admin Edit Order screen.

== Changelog ==

= 1.0.0 =
* Initial release: on-demand invoice and packing-slip documents, print-to-PDF with no bundled library, server-side authorization on every document request (order owner, admin, or a valid order key), logo and accent-color customization, theme-overridable template.
