<?php
/**
 * The module widgets.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor\Widgets;

defined( 'ABSPATH' ) || exit;

/**
 * Cart summary.
 */
class Cart extends Module_Widget {

	/**
	 * HikaShop widget class.
	 *
	 * @var string
	 */
	protected $hikashop_class = 'HikaShop_Widget_Cart';

	/**
	 * Module name.
	 *
	 * @var string
	 */
	protected $module_name = 'mod_hikashop_cart';

	/**
	 * Settings view.
	 *
	 * @var string
	 */
	protected $options_view = 'cartmodules';

	/**
	 * Settings field.
	 *
	 * @var string
	 */
	protected $options_field = 'hikashopcartmodule';

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hikashop-cart';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'HikaShop Cart', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-cart';
	}
}

/**
 * Wishlist summary.
 */
class Wishlist extends Module_Widget {

	/**
	 * HikaShop widget class.
	 *
	 * @var string
	 */
	protected $hikashop_class = 'HikaShop_Widget_Wishlist';

	/**
	 * Module name.
	 *
	 * @var string
	 */
	protected $module_name = 'mod_hikashop_wishlist';

	/**
	 * Settings view.
	 *
	 * @var string
	 */
	protected $options_view = 'cartmodules';

	/**
	 * Settings field.
	 *
	 * @var string
	 */
	protected $options_field = 'hikashopwishlistmodule';

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hikashop-wishlist';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'HikaShop Wishlist', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-heart';
	}
}

/**
 * Currency switcher.
 */
class Currency extends Module_Widget {

	/**
	 * HikaShop widget class.
	 *
	 * @var string
	 */
	protected $hikashop_class = 'HikaShop_Widget_Currency';

	/**
	 * Module name.
	 *
	 * @var string
	 */
	protected $module_name = 'mod_hikashop_currency';

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hikashop-currency';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'HikaShop Currency Switcher', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-exchange';
	}
}

/**
 * Product filters.
 */
class Filter extends Module_Widget {

	/**
	 * HikaShop widget class.
	 *
	 * @var string
	 */
	protected $hikashop_class = 'HikaShop_Widget_Filter';

	/**
	 * Module name.
	 *
	 * @var string
	 */
	protected $module_name = 'mod_hikashop_filter';

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hikashop-filter';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'HikaShop Filters', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-filter';
	}
}
