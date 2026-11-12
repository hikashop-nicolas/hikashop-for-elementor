<?php
/**
 * The product page parts.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor\Widgets;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Adds the "Inherit / Show / Hide" selects HikaShop's product blocks take.
 */
trait Block_Switches {

	/**
	 * Adds one select per block attribute.
	 *
	 * @param array $switches Block attribute => label.
	 */
	protected function add_block_switches( $switches ) {
		foreach ( $switches as $name => $label ) {
			$this->add_control(
				$name,
				array(
					'label'   => $label,
					'type'    => Controls_Manager::SELECT,
					'default' => 'inherit',
					'options' => array(
						'inherit' => esc_html__( 'Inherit from HikaShop', 'hikashop-for-elementor' ),
						'show'    => esc_html__( 'Show', 'hikashop-for-elementor' ),
						'hide'    => esc_html__( 'Hide', 'hikashop-for-elementor' ),
					),
				)
			);
		}
	}

	/**
	 * Block attributes from the selects.
	 *
	 * @param array $settings Widget settings.
	 * @param array $names    Block attribute names.
	 * @return array
	 */
	protected function block_switches( $settings, $names ) {
		$attributes = array();
		foreach ( $names as $name ) {
			$value = $settings[ $name ] ?? 'inherit';
			if ( in_array( $value, array( 'show', 'hide' ), true ) ) {
				$attributes[ $name ] = $value;
			}
		}
		return $attributes;
	}
}

/**
 * Product name.
 */
class Product_Title extends Product_Part {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'title';

	/**
	 * Style target.
	 *
	 * @var string
	 */
	protected $style_selector = '{{WRAPPER}} .hikashop_product_name_main';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Title', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-title';
	}

	/**
	 * Settings of the part.
	 */
	protected function register_part_controls() {
		$this->add_control(
			'heading_level',
			array(
				'label'   => esc_html__( 'HTML tag', 'hikashop-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => array(
					'1' => 'H1',
					'2' => 'H2',
					'3' => 'H3',
					'4' => 'H4',
					'5' => 'H5',
					'6' => 'H6',
				),
			)
		);
		$this->add_control(
			'show_code',
			array(
				'label'        => esc_html__( 'Product code', 'hikashop-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => '1',
				'default'      => '',
			)
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return array(
			'headingLevel' => max( 1, min( 6, (int) ( $settings['heading_level'] ?? 1 ) ) ),
			'showCode'     => ! empty( $settings['show_code'] ),
		);
	}
}

/**
 * Product images.
 */
class Product_Gallery extends Product_Part {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'gallery';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Images', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-images';
	}
}

/**
 * Product price.
 */
class Product_Price extends Product_Part {
	use Block_Switches;

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'price';

	/**
	 * Style target.
	 *
	 * @var string
	 */
	protected $style_selector = '{{WRAPPER}} .hikashop_product_price';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Price', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-price';
	}

	/**
	 * Settings of the part.
	 */
	protected function register_part_controls() {
		$this->add_block_switches(
			array(
				'showOriginalPrice' => esc_html__( 'Price before discount', 'hikashop-for-elementor' ),
				'showDiscount'      => esc_html__( 'Discount', 'hikashop-for-elementor' ),
			)
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return $this->block_switches( $settings, array( 'showOriginalPrice', 'showDiscount' ) );
	}
}

/**
 * Quantity and add to cart button.
 */
class Product_Add_To_Cart extends Product_Part {
	use Block_Switches;

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'add-to-cart';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Add to Cart', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-add-to-cart';
	}

	/**
	 * Settings of the part.
	 */
	protected function register_part_controls() {
		$this->add_block_switches(
			array(
				'showQuantity' => esc_html__( 'Quantity field', 'hikashop-for-elementor' ),
			)
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return $this->block_switches( $settings, array( 'showQuantity' ) );
	}
}

/**
 * Product description.
 */
class Product_Description extends Product_Part {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'description';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Description', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-description';
	}
}

/**
 * Characteristic selectors of a product with variants.
 */
class Product_Variants extends Product_Part {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'variants';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Variants', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-meta';
	}
}

/**
 * Product options.
 */
class Product_Options extends Product_Part {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'options';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Options', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-bullet-list';
	}
}

/**
 * Product specifications (custom product fields shown as a table).
 */
class Product_Specifications extends Product_Part {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'specifications';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Specifications', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-table';
	}
}

/**
 * Item custom fields the customer fills before adding to the cart.
 */
class Product_Custom_Fields extends Product_Part {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'custom-fields';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Custom Fields', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}
}

/**
 * Weight and dimensions.
 */
class Product_Dimensions extends Product_Part {
	use Block_Switches;

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'dimensions';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Dimensions', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-info';
	}

	/**
	 * Settings of the part.
	 */
	protected function register_part_controls() {
		$this->add_block_switches(
			array(
				'showWeight'     => esc_html__( 'Weight', 'hikashop-for-elementor' ),
				'showDimensions' => esc_html__( 'Dimensions', 'hikashop-for-elementor' ),
			)
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return $this->block_switches( $settings, array( 'showWeight', 'showDimensions' ) );
	}
}

/**
 * Products bundled with the product.
 */
class Product_Bundled extends Product_Part {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'bundled';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Bundled Products', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-products';
	}
}

/**
 * Files attached to the product.
 */
class Product_Files extends Product_Part {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'files';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Files', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-download-button';
	}
}

/**
 * Rating stars.
 */
class Product_Rating extends Product_Part {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'rating';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Rating', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-rating';
	}
}

/**
 * Social sharing buttons.
 */
class Product_Social extends Product_Part {

	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $part = 'social';

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Product Sharing', 'hikashop-for-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-share';
	}
}
