<?php
/**
 * Base of the product page parts, each one wrapping a HikaShop product block.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use HikaShopElementor\Render;

defined( 'ABSPATH' ) || exit;

/**
 * One part of the product page (title, price, gallery...), for the current product.
 */
abstract class Product_Part extends Base_Widget {

	/**
	 * Name of the HikaShop block rendering the part, without the "hikashop/product-" prefix.
	 *
	 * @var string
	 */
	protected $part = '';

	/**
	 * CSS selector the style controls apply to.
	 *
	 * @var string
	 */
	protected $style_selector = '{{WRAPPER}}';

	/**
	 * Classes of every part.
	 *
	 * @return string[]
	 */
	public static function classes() {
		return array(
			Product_Title::class,
			Product_Gallery::class,
			Product_Price::class,
			Product_Add_To_Cart::class,
			Product_Description::class,
			Product_Variants::class,
			Product_Options::class,
			Product_Specifications::class,
			Product_Custom_Fields::class,
			Product_Dimensions::class,
			Product_Bundled::class,
			Product_Files::class,
			Product_Rating::class,
			Product_Social::class,
		);
	}

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'hikashop-product-' . $this->part;
	}

	/**
	 * Search words of the panel.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array_merge( parent::get_keywords(), array( 'single', 'product page', $this->part ) );
	}

	/**
	 * Settings of the part, before the style tab.
	 */
	protected function register_part_controls() {}

	/**
	 * Block attributes built from the settings.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		unset( $settings );
		return array();
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
		$this->add_control(
			'part_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Shows the product being viewed: place it in the product page template chosen in Settings > HikaShop for Elementor. In the editor, the latest product stands in.', 'hikashop-for-elementor' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->register_part_controls();
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => $this->get_title(),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text color', 'hikashop-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( $this->style_selector => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => $this->style_selector,
			)
		);
		$this->add_responsive_control(
			'align',
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
				'selectors' => array( '{{WRAPPER}}' => 'text-align: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Points HikaShop at a product while the editor shows a template.
	 */
	protected function prepare_product() {
		$input = \hikaInput::get();
		if ( $input->getInt( 'cid', 0 ) || $input->getInt( 'product_id', 0 ) || ! Render::is_editor() ) {
			return;
		}
		$product_id = self::preview_product_id();
		if ( $product_id ) {
			$input->set( 'cid', $product_id );
		}
	}

	/**
	 * Product shown in the editor: the latest published main product.
	 *
	 * @return int
	 */
	public static function preview_product_id() {
		static $product_id = null;
		if ( null === $product_id ) {
			$db = \JFactory::getDBO();
			$db->setQuery(
				'SELECT product_id FROM ' . hikashop_table( 'product' )
				. ' WHERE product_published = 1 AND product_type = ' . $db->quote( 'main' )
				. ' ORDER BY product_id DESC',
				0,
				1
			);
			$product_id = (int) $db->loadResult();
			/**
			 * Filters the product the editor shows in the product page parts.
			 *
			 * @param int $product_id HikaShop product id.
			 */
			$product_id = (int) apply_filters( 'hikashop_elementor_preview_product', $product_id );
		}
		return $product_id;
	}

	/**
	 * Frontend and editor output.
	 */
	protected function render() {
		if ( ! function_exists( 'render_block' ) || ! \WP_Block_Type_Registry::get_instance()->is_registered( 'hikashop/product-' . $this->part ) ) {
			return;
		}
		Render::prepare();
		$this->prepare_product();

		$block = array(
			'blockName'    => 'hikashop/product-' . $this->part,
			'attrs'        => $this->block_attributes( $this->get_settings_for_display() ),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		);
		$html  = Render::as_site(
			function () use ( $block ) {
				return render_block( $block );
			}
		);

		if ( '' === trim( wp_strip_all_tags( $html ) ) && ! preg_match( '#<(img|input|select|button|iframe)\b#i', $html ) ) {
			Render::placeholder( $this->get_title(), esc_html__( 'Nothing to show for this product.', 'hikashop-for-elementor' ) );
			return;
		}
		Render::output( $html );
	}
}
