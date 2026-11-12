<?php
/**
 * Products widget: a HikaShop product listing.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use HikaShopElementor\Listing;
use HikaShopElementor\Render;

defined( 'ABSPATH' ) || exit;

/**
 * Product listing.
 */
class Products extends Base_Widget {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hikashop-products';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'HikaShop Products', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-products';
	}

	/**
	 * Settings of the widget.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'Products', 'hikashop-for-elementor' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Source', 'hikashop-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => array(
					'category' => esc_html__( 'Products of a category', 'hikashop-for-elementor' ),
					'manual'   => esc_html__( 'Products chosen by hand', 'hikashop-for-elementor' ),
					'sale'     => esc_html__( 'Products on sale', 'hikashop-for-elementor' ),
					'recent'   => esc_html__( 'Recently viewed by the visitor', 'hikashop-for-elementor' ),
					'related'  => esc_html__( 'Related to the product shown', 'hikashop-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'category',
			array(
				'label'     => esc_html__( 'Category', 'hikashop-for-elementor' ),
				'type'      => Controls_Manager::SELECT2,
				'options'   => Listing::category_options(),
				'default'   => '0',
				'condition' => array( 'source!' => array( 'manual', 'related' ) ),
			)
		);

		$this->add_control(
			'subcategories',
			array(
				'label'        => esc_html__( 'Include subcategories', 'hikashop-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => '1',
				'condition'    => array( 'source!' => array( 'manual', 'related' ) ),
			)
		);

		$this->add_control(
			'products',
			array(
				'label'       => esc_html__( 'Products', 'hikashop-for-elementor' ),
				'type'        => 'hikashop_product_select',
				'multiple'    => true,
				'label_block' => true,
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->add_control(
			'related_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Place this listing in the product page template: it shows the related products of the product being viewed.', 'hikashop-for-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array( 'source' => 'related' ),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order by', 'hikashop-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ordering',
				'options' => array(
					'ordering'                => esc_html__( 'HikaShop ordering', 'hikashop-for-elementor' ),
					'manual'                  => esc_html__( 'Order chosen above', 'hikashop-for-elementor' ),
					'product_name'          => esc_html__( 'Name', 'hikashop-for-elementor' ),
					'product_sort_price'    => esc_html__( 'Price', 'hikashop-for-elementor' ),
					'product_created'       => esc_html__( 'Creation date', 'hikashop-for-elementor' ),
					'product_modified'      => esc_html__( 'Modification date', 'hikashop-for-elementor' ),
					'product_sales'         => esc_html__( 'Sales', 'hikashop-for-elementor' ),
					'product_hit'           => esc_html__( 'Views', 'hikashop-for-elementor' ),
					'product_average_score' => esc_html__( 'Rating', 'hikashop-for-elementor' ),
					'random'                  => esc_html__( 'Random', 'hikashop-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'order_dir',
			array(
				'label'     => esc_html__( 'Direction', 'hikashop-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ASC',
				'options'   => array(
					'ASC'  => esc_html__( 'Ascending', 'hikashop-for-elementor' ),
					'DESC' => esc_html__( 'Descending', 'hikashop-for-elementor' ),
				),
				'condition' => array( 'order!' => array( 'manual', 'random' ) ),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'     => esc_html__( 'Number of products', 'hikashop-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 100,
				'default'   => 8,
				'condition' => array( 'source!' => 'manual' ),
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
				'label'   => esc_html__( 'Product layout', 'hikashop-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inherit',
				'options' => $this->item_layout_options(),
			)
		);

		$this->add_switch( 'show_price', esc_html__( 'Price', 'hikashop-for-elementor' ) );
		$this->add_switch( 'add_to_cart', esc_html__( 'Add to cart button', 'hikashop-for-elementor' ) );
		$this->add_switch( 'quantity', esc_html__( 'Quantity field', 'hikashop-for-elementor' ) );
		$this->add_switch( 'wishlist', esc_html__( 'Add to wishlist button', 'hikashop-for-elementor' ) );
		$this->add_switch( 'rating', esc_html__( 'Rating', 'hikashop-for-elementor' ) );
		$this->add_switch( 'badges', esc_html__( 'Badges', 'hikashop-for-elementor' ) );
		$this->add_switch( 'product_links', esc_html__( 'Links to the product page', 'hikashop-for-elementor' ) );

		$this->end_controls_section();

		$this->register_style_controls();
	}

	/**
	 * Style tab: title and price typography and colors.
	 */
	protected function register_style_controls() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Products', 'hikashop-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title color', 'hikashop-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hikashop_product_name, {{WRAPPER}} .hikashop_product_name a, {{WRAPPER}} .hikashop_category_name, {{WRAPPER}} .hikashop_category_name a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title typography', 'hikashop-for-elementor' ),
				'selector' => '{{WRAPPER}} .hikashop_product_name, {{WRAPPER}} .hikashop_product_name a, {{WRAPPER}} .hikashop_category_name, {{WRAPPER}} .hikashop_category_name a',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => esc_html__( 'Price color', 'hikashop-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .hikashop_product_price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'label'    => esc_html__( 'Price typography', 'hikashop-for-elementor' ),
				'selector' => '{{WRAPPER}} .hikashop_product_price',
			)
		);

		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => esc_html__( 'Alignment', 'hikashop-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'hikashop-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'hikashop-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'hikashop-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .hikashop_listing_img_title, {{WRAPPER}} .hikashop_subcontainer' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Frontend and editor output.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$html     = Listing::products( $settings, $this->div_name( 'hikashop_elementor_products' ) );
		if ( '' === $html ) {
			Render::placeholder( $this->get_title(), esc_html__( 'No product to show with these settings.', 'hikashop-for-elementor' ) );
			return;
		}
		Render::output( $html );
	}
}
