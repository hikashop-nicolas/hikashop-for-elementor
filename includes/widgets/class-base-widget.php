<?php
/**
 * Common base of the HikaShop widgets.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Shared settings of every HikaShop widget.
 */
abstract class Base_Widget extends Widget_Base {

	/**
	 * Panel group.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'hikashop' );
	}

	/**
	 * Search words of the panel.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'hikashop', 'shop', 'product', 'ecommerce', 'store' );
	}

	/**
	 * Prices, stock and the cart depend on the visitor: never cache the output.
	 *
	 * @return bool
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * No extra wrapper div around the widget content.
	 *
	 * @return bool
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * The "Inherit from HikaShop / Show / Hide" choices.
	 *
	 * @return array
	 */
	protected function switch_options() {
		return array(
			''  => esc_html__( 'Inherit from HikaShop', 'hikashop-for-elementor' ),
			'1' => esc_html__( 'Show', 'hikashop-for-elementor' ),
			'0' => esc_html__( 'Hide', 'hikashop-for-elementor' ),
		);
	}

	/**
	 * Adds an "Inherit / Show / Hide" select.
	 *
	 * @param string $name  Setting name.
	 * @param string $label Label.
	 * @param array  $extra Extra control arguments.
	 */
	protected function add_switch( $name, $label, $extra = array() ) {
		$this->add_control(
			$name,
			array_merge(
				array(
					'label'   => $label,
					'type'    => Controls_Manager::SELECT,
					'options' => $this->switch_options(),
					'default' => '',
				),
				$extra
			)
		);
	}

	/**
	 * The item layouts of HikaShop listings.
	 *
	 * @return array
	 */
	protected function item_layout_options() {
		return array(
			'inherit'           => esc_html__( 'Inherit from HikaShop', 'hikashop-for-elementor' ),
			'img_title'         => esc_html__( 'Image and title', 'hikashop-for-elementor' ),
			'img'               => esc_html__( 'Image', 'hikashop-for-elementor' ),
			'title'             => esc_html__( 'Title', 'hikashop-for-elementor' ),
			'img_desc'          => esc_html__( 'Image and description', 'hikashop-for-elementor' ),
			'img_description'   => esc_html__( 'Image, title and description', 'hikashop-for-elementor' ),
			'img_pane'          => esc_html__( 'Image with a sliding pane', 'hikashop-for-elementor' ),
			'fade'              => esc_html__( 'Fade', 'hikashop-for-elementor' ),
			'hover_x_img_title' => esc_html__( 'Image swap on hover', 'hikashop-for-elementor' ),
			'slider_horizontal' => esc_html__( 'Horizontal slider', 'hikashop-for-elementor' ),
			'slider_vertical'   => esc_html__( 'Vertical slider', 'hikashop-for-elementor' ),
		);
	}

	/**
	 * Unique id of this widget instance for HikaShop's listing.
	 *
	 * @param string $prefix Prefix of the id.
	 * @return string
	 */
	protected function div_name( $prefix ) {
		return $prefix . '_' . preg_replace( '/[^a-z0-9]/i', '', $this->get_id() );
	}
}
