<?php
/**
 * Plugin Name:       HikaShop for Elementor
 * Plugin URI:        https://github.com/hikashop-nicolas/hikashop-for-elementor
 * Description:       HikaShop product listings, product page parts, cart and filters as Elementor widgets, and HikaShop product pages designed with Elementor.
 * Version:           1.0.1
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * Author:            Hikari Software
 * Author URI:        https://www.hikashop.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hikashop-for-elementor
 *
 * Elementor tested up to: 4.4.0
 *
 * @package HikaShopElementor
 */

defined( 'ABSPATH' ) || exit;

define( 'HIKASHOP_ELEMENTOR_VERSION', '1.0.1' );
define( 'HIKASHOP_ELEMENTOR_FILE', __FILE__ );
define( 'HIKASHOP_ELEMENTOR_DIR', plugin_dir_path( __FILE__ ) );
define( 'HIKASHOP_ELEMENTOR_URL', plugin_dir_url( __FILE__ ) );

require_once HIKASHOP_ELEMENTOR_DIR . 'includes/class-plugin.php';

\HikaShopElementor\Plugin::boot();
