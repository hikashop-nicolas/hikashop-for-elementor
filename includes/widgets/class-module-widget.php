<?php
/**
 * Base of the widgets wrapping a HikaShop module (cart, wishlist, currency, filters).
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor\Widgets;

use Elementor\Controls_Manager;
use HikaShopElementor\Render;

defined( 'ABSPATH' ) || exit;

/**
 * Renders one of HikaShop's WordPress widgets inside Elementor.
 */
abstract class Module_Widget extends Base_Widget {

	/**
	 * HikaShop widget class doing the rendering.
	 *
	 * @var string
	 */
	protected $hikashop_class = '';

	/**
	 * Module name, for the HikaShop settings page.
	 *
	 * @var string
	 */
	protected $module_name = '';

	/**
	 * HikaShop view editing the module settings, empty when the module has none.
	 *
	 * @var string
	 */
	protected $options_view = '';

	/**
	 * Field holding the settings in that view.
	 *
	 * @var string
	 */
	protected $options_field = '';

	/**
	 * Classes of every module widget.
	 *
	 * @return string[]
	 */
	public static function classes() {
		return array(
			Cart::class,
			Wishlist::class,
			Currency::class,
			Filter::class,
		);
	}

	/**
	 * Id under which HikaShop stores the settings of this widget instance.
	 *
	 * @param string $element_id Elementor element id.
	 * @return int
	 */
	public static function numeric_id( $element_id ) {
		$element_id = (string) $element_id;
		if ( '' === $element_id || ! ctype_xdigit( $element_id ) ) {
			return 0;
		}
		// Same formula as assets/js/editor.js, which opens the settings of this id
		return 300000000 + ( hexdec( substr( $element_id, 0, 7 ) ) % 600000000 );
	}

	/**
	 * Settings of the widget.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => $this->get_title(),
			)
		);

		if ( '' !== $this->options_view && current_user_can( 'manage_options' ) ) {
			$this->add_control(
				'hikashop_settings',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw'  => sprintf(
						'<p>%1$s</p><p><button type="button" class="elementor-button elementor-button-default hikashop-elementor-module-settings" data-module="%2$s" data-view="%3$s" data-field="%4$s">%5$s</button></p>',
						esc_html__( 'This widget uses the settings of the HikaShop module. They open in a new window; the preview refreshes when you close it.', 'hikashop-for-elementor' ),
						esc_attr( $this->module_name ),
						esc_attr( $this->options_view ),
						esc_attr( $this->options_field ),
						esc_html__( 'Edit the HikaShop settings', 'hikashop-for-elementor' )
					),
				)
			);
		} else {
			$this->add_control(
				'hikashop_settings',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => esc_html__( 'This widget shows the HikaShop module with its default settings.', 'hikashop-for-elementor' ),
					'content_classes' => 'elementor-descriptor',
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Frontend and editor output.
	 */
	protected function render() {
		Render::prepare();
		if ( ! class_exists( $this->hikashop_class ) && defined( 'HIKASHOP_WP_PLUGIN_DIR' ) && is_file( HIKASHOP_WP_PLUGIN_DIR . 'widgets.php' ) ) {
			require_once HIKASHOP_WP_PLUGIN_DIR . 'widgets.php';
		}
		if ( ! class_exists( $this->hikashop_class ) ) {
			return;
		}

		$widget     = new $this->hikashop_class();
		$numeric_id = self::numeric_id( $this->get_id() );
		if ( $numeric_id && method_exists( $widget, 'set_block_numeric_id' ) ) {
			$widget->set_block_numeric_id( $numeric_id );
		}
		$html = Render::as_site(
			function () use ( $widget ) {
				return $widget->render_module( array() );
			}
		);

		if ( '' === trim( wp_strip_all_tags( $html ) ) && ! preg_match( '#<(img|input|select|button)\b#i', $html ) ) {
			Render::placeholder( $this->get_title(), esc_html__( 'Empty for now: it fills in on the site (a cart with products, filters on a listing page).', 'hikashop-for-elementor' ) );
			return;
		}
		Render::output( $html );
	}
}
