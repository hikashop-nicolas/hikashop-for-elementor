<?php
/**
 * Product picker control: a select2 searching the HikaShop catalog.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor\Controls;

defined( 'ABSPATH' ) || exit;

/**
 * Select2 control filled by the product search of the plugin.
 */
class Product_Select extends \Elementor\Control_Select2 {

	/**
	 * Control type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'hikashop_product_select';
	}
}
