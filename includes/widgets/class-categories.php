<?php
/**
 * Categories widget: a HikaShop category listing.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor\Widgets;

use Elementor\Controls_Manager;
use HikaShopElementor\Listing;
use HikaShopElementor\Render;

defined( 'ABSPATH' ) || exit;

/**
 * Category listing.
 */
class Categories extends Products {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hikashop-categories';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'HikaShop Categories', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-categories';
	}

	/**
	 * Settings of the widget.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'Categories', 'hikashop-for-elementor' ),
			)
		);

		$this->add_control(
			'category',
			array(
				'label'   => esc_html__( 'Parent category', 'hikashop-for-elementor' ),
				'type'    => Controls_Manager::SELECT2,
				'options' => Listing::category_options(),
				'default' => '0',
			)
		);

		$this->add_control(
			'subcategories',
			array(
				'label'        => esc_html__( 'Include all levels below', 'hikashop-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => '',
			)
		);

		$this->add_control(
			'hide_empty',
			array(
				'label'        => esc_html__( 'Hide empty categories', 'hikashop-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => '1',
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order by', 'hikashop-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category_ordering',
				'options' => array(
					'category_ordering' => esc_html__( 'HikaShop ordering', 'hikashop-for-elementor' ),
					'category_name'     => esc_html__( 'Name', 'hikashop-for-elementor' ),
					'category_created'  => esc_html__( 'Creation date', 'hikashop-for-elementor' ),
					'category_modified' => esc_html__( 'Modification date', 'hikashop-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'order_dir',
			array(
				'label'   => esc_html__( 'Direction', 'hikashop-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => array(
					'ASC'  => esc_html__( 'Ascending', 'hikashop-for-elementor' ),
					'DESC' => esc_html__( 'Descending', 'hikashop-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'   => esc_html__( 'Number of categories', 'hikashop-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 100,
				'default' => 8,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'hikashop-for-elementor' ),
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'   => esc_html__( 'Columns', 'hikashop-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 12,
				'default' => 4,
			)
		);

		$this->add_control(
			'item_layout',
			array(
				'label'   => esc_html__( 'Category layout', 'hikashop-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inherit',
				'options' => $this->item_layout_options(),
			)
		);

		$this->add_control(
			'count',
			array(
				'label'        => esc_html__( 'Number of products', 'hikashop-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		$this->register_style_controls();
	}

	/**
	 * Frontend and editor output.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$html     = Listing::categories( $settings, $this->div_name( 'hikashop_elementor_categories' ) );
		if ( '' === $html ) {
			Render::placeholder( $this->get_title(), esc_html__( 'No category to show with these settings.', 'hikashop-for-elementor' ) );
			return;
		}
		Render::output( $html );
	}
}
