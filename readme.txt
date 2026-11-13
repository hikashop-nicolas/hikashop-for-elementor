=== HikaShop for Elementor ===
Contributors: hikarisoftware
Tags: hikashop, elementor, ecommerce, shop, product
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Design your HikaShop store with Elementor: product listings, product page parts, cart, filters, and product pages built from an Elementor template.

== Description ==

HikaShop for Elementor adds a HikaShop group to the Elementor widget panel. It works with the free version of Elementor.

= Listings =

* **HikaShop Products**: the products of a category, products chosen by hand (with a search of your catalog), products on sale, the products the visitor viewed recently, or the products related to the product being viewed. Choose the order, the number of products, the columns, the product layout, and whether to show the price, the add to cart button, the quantity field, the wishlist button, the rating and the badges.
* **HikaShop Categories**: the subcategories of a category, with the number of products in each, empty categories hidden if you want.

= Product page =

Pick an Elementor template in Settings > HikaShop for Elementor and every HikaShop product page is shown with it. Design the template with the product widgets, which show the product being viewed:

* Product title, images, price, add to cart (with the quantity field), description
* Variants (the characteristic selectors), options, custom fields
* Specifications, dimensions and weight, bundled products, files, rating, sharing buttons

In the editor, the latest product of your shop stands in so you can see the result while you design.

= Cart and filters =

* **HikaShop Cart** and **HikaShop Wishlist**: the cart and wishlist summaries, with the full set of HikaShop module settings.
* **HikaShop Currency Switcher**
* **HikaShop Filters**: the filters of HikaShop for your product listings.

Prices, taxes, discounts, stock, access rights and the cart all follow your HikaShop configuration: the widgets render with HikaShop's own layouts, so your theme overrides of HikaShop layouts keep working.

= Requirements =

This plugin needs HikaShop 6.8.2 or newer on WordPress (a package downloaded since 12 November 2026: reinstall it over an older 6.8.2), and Elementor 3.24 or newer. HikaShop is not hosted on WordPress.org: its free Starter edition and the Essential and Business editions are available on [hikashop.com](https://www.hikashop.com/download).

== Installation ==

1. Install and activate HikaShop and Elementor.
2. Install and activate HikaShop for Elementor from Plugins > Add New, or upload the zip file.
3. Edit a page with Elementor: the widgets are in the HikaShop group of the panel.
4. To design the product pages, create a template in Templates > Saved Templates, add the product widgets to it, then choose it in Settings > HikaShop for Elementor.

== Frequently Asked Questions ==

= Does it need Elementor Pro? =

No. Every widget and the product page template work with the free Elementor.

= Why is a product missing from a listing? =

The listings follow the HikaShop configuration: an unpublished product, a product the visitor's user group cannot see, or a product out of stock while "Display out of stock products" is off in HikaShop are left out, as in your shop pages.

= Where are the settings of the cart widget? =

Select the widget and click "Edit the HikaShop settings": the HikaShop cart module settings open in a new window, and the preview refreshes when you close it.

= Can I change the HTML of the widgets? =

Yes, with HikaShop's layout overrides: copy the HikaShop layout file into the hikashop folder of your theme, as you would for the HikaShop pages.

== Screenshots ==

1. A product listing and its settings in the Elementor editor.
2. Products chosen by hand, with a search of the HikaShop catalog.
3. A product page template designed with the product widgets of the HikaShop group.
4. A HikaShop product page shown with that template.
5. A shop page built with the category and product listing widgets.
6. The product page template setting.

== Changelog ==

= 1.0.1 =
* A product listing showing the related products of the product being viewed lists those products in the editor preview too; it showed other products there.

= 1.0.0 =
* First release: product and category listings, the product page parts, the product page template, the cart, wishlist, currency and filter widgets.
