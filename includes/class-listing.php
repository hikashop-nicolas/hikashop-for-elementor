<?php
/**
 * Product and category listings rendered with HikaShop's own listing layouts.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor;

defined( 'ABSPATH' ) || exit;

/**
 * Builds the parameters of a HikaShop listing from widget settings.
 */
final class Listing {

	/**
	 * Hand-picked product ids of the listings being rendered, by listing div name.
	 *
	 * @var array
	 */
	private static $picked = array();

	/**
	 * Hooks the listing query of HikaShop.
	 */
	public static function init() {
		add_action( 'onBeforeProductListingLoad', array( __CLASS__, 'restrict' ), 10, 8 );
	}

	/**
	 * HikaShop's default listing parameters, as its configuration sets them.
	 *
	 * @return \HikaParameter
	 */
	private static function default_params() {
		$config = hikashop_config();
		$params = new \HikaParameter( '' );
		$stored = $config->get( 'default_params' );
		if ( is_array( $stored ) || is_object( $stored ) ) {
			foreach ( $stored as $key => $value ) {
				$params->set( $key, $value );
			}
		}
		return $params;
	}

	/**
	 * Sets the "inherit / yes / no" switches: inherit keeps the HikaShop configuration.
	 *
	 * @param \HikaParameter $params   Listing parameters.
	 * @param array          $settings Widget settings.
	 * @param array          $map      Setting name => listing parameter.
	 */
	private static function apply_switches( $params, $settings, $map ) {
		foreach ( $map as $setting => $param ) {
			if ( isset( $settings[ $setting ] ) && in_array( $settings[ $setting ], array( '0', '1' ), true ) ) {
				$params->set( $param, (int) $settings[ $setting ] );
			}
		}
	}

	/**
	 * Renders a product listing.
	 *
	 * @param array  $settings Widget settings.
	 * @param string $div_name Unique id of the listing.
	 * @return string HTML.
	 */
	public static function products( $settings, $div_name ) {
		Render::prepare();
		$params = self::default_params();

		$limit  = max( 1, min( 100, (int) ( $settings['limit'] ?? 8 ) ) );
		$source = $settings['source'] ?? 'category';
		$values = array(
			'main_div_name'              => $div_name,
			'content_type'               => 'product',
			'from_module'                => 'elementor',
			'limit'                      => $limit,
			'show_limit'                 => 0,
			'random'                     => 0,
			'filter_type'                => empty( $settings['subcategories'] ) ? 0 : 1,
			'selectparentlisting'        => (int) ( $settings['category'] ?? 0 ),
			'content_synchronize'        => 0,
			'product_synchronize'        => 0,
			'recently_viewed'            => 0,
			'related_products_from_cart' => 0,
			'discounted_only'            => 0,
			'enable_carousel'            => 0,
			'infinite_scroll'            => 0,
			'layout_type'                => 'div',
			'columns'                    => max( 1, min( 12, (int) ( $settings['columns'] ?? 4 ) ) ),
			'order_dir'                  => ( 'DESC' === ( $settings['order_dir'] ?? 'ASC' ) ) ? 'DESC' : 'ASC',
		);

		$order = $settings['order'] ?? 'ordering';
		if ( 'random' === $order ) {
			$values['random'] = 1;
		} elseif ( in_array( $order, array( 'ordering', 'b.product_name', 'b.product_created', 'b.product_sales', 'b.product_hit', 'b.product_sort_price', 'b.product_average_score', 'b.product_modified' ), true ) ) {
			$values['product_order'] = $order;
		}

		switch ( $source ) {
			case 'sale':
				$values['discounted_only'] = 1;
				break;
			case 'recent':
				$values['recently_viewed'] = 1;
				break;
			case 'related':
				$values['content_synchronize'] = 1;
				$values['product_synchronize'] = 2;
				break;
			case 'manual':
				$ids = self::ids( $settings['products'] ?? array() );
				if ( empty( $ids ) ) {
					return '';
				}
				self::$picked[ $div_name ] = array(
					'ids'   => $ids,
					'order' => 'manual' === $order,
				);
				$values['limit'] = count( $ids );
				break;
		}

		foreach ( $values as $key => $value ) {
			$params->set( $key, $value );
		}
		if ( ! empty( $settings['item_layout'] ) && 'inherit' !== $settings['item_layout'] ) {
			$params->set( 'div_item_layout_type', sanitize_key( $settings['item_layout'] ) );
		}
		self::apply_switches(
			$params,
			$settings,
			array(
				'show_price'    => 'show_price',
				'add_to_cart'   => 'add_to_cart',
				'quantity'      => 'show_quantity_field',
				'wishlist'      => 'add_to_wishlist',
				'rating'        => 'show_vote',
				'badges'        => 'display_badges',
				'product_links' => 'link_to_product_page',
			)
		);

		$related = 'related' === $source;
		$input   = \hikaInput::get();
		$saved   = $related ? array( $input->getString( 'ctrl' ), $input->getString( 'task' ) ) : null;
		if ( $related ) {
			// related products are read from the product page being shown
			$input->set( 'ctrl', 'product' );
			$input->set( 'task', 'show' );
			if ( ! $input->getInt( 'cid', 0 ) && Render::is_editor() && class_exists( Widgets\Product_Part::class ) ) {
				$input->set( 'cid', Widgets\Product_Part::preview_product_id() );
			}
		}

		$js = '';
		try {
			$html = hikashop_getLayout( 'product', 'listing', $params, $js, 'site' );
		} finally {
			unset( self::$picked[ $div_name ] );
			if ( $related ) {
				$input->set( 'ctrl', $saved[0] );
				$input->set( 'task', $saved[1] );
			}
		}
		return trim( (string) $html );
	}

	/**
	 * Renders a category listing.
	 *
	 * @param array  $settings Widget settings.
	 * @param string $div_name Unique id of the listing.
	 * @return string HTML.
	 */
	public static function categories( $settings, $div_name ) {
		Render::prepare();
		$params = self::default_params();
		$values = array(
			'main_div_name'       => $div_name,
			'content_type'        => 'category',
			'from_module'         => 'elementor',
			'limit'               => max( 1, min( 100, (int) ( $settings['limit'] ?? 8 ) ) ),
			'show_limit'          => 0,
			'random'              => 0,
			'filter_type'         => empty( $settings['subcategories'] ) ? 0 : 1,
			'selectparentlisting' => (int) ( $settings['category'] ?? 0 ),
			'content_synchronize' => 0,
			'enable_carousel'     => 0,
			'infinite_scroll'     => 0,
			'layout_type'         => 'div',
			'columns'             => max( 1, min( 12, (int) ( $settings['columns'] ?? 4 ) ) ),
			'order_dir'           => ( 'DESC' === ( $settings['order_dir'] ?? 'ASC' ) ) ? 'DESC' : 'ASC',
			'number_of_products'  => empty( $settings['count'] ) ? 0 : 1,
			'child_display_type'  => 'nochild',
		);
		$order = $settings['order'] ?? 'category_ordering';
		if ( in_array( $order, array( 'category_ordering', 'category_name', 'category_created', 'category_modified' ), true ) ) {
			$values['category_order'] = $order;
		}
		foreach ( $values as $key => $value ) {
			$params->set( $key, $value );
		}
		if ( ! empty( $settings['item_layout'] ) && 'inherit' !== $settings['item_layout'] ) {
			$params->set( 'div_item_layout_type', sanitize_key( $settings['item_layout'] ) );
		}

		$js = '';
		return trim( (string) hikashop_getLayout( 'category', 'listing', $params, $js, 'site' ) );
	}

	/**
	 * Restricts a hand-picked listing to its products, optionally in the picked order.
	 *
	 * @param array  $filters SQL conditions.
	 * @param mixed  $order   ORDER BY clause.
	 * @param object $view    Listing view.
	 * @param string $select  SELECT clause.
	 * @param string $select2 Extra selected columns.
	 * @param string $a       Main table.
	 * @param string $b       Joined table.
	 * @param string $on      Join condition.
	 */
	public static function restrict( &$filters, &$order, &$view, &$select, &$select2, &$a, &$b, &$on ) {
		if ( empty( self::$picked ) || empty( $view->params ) ) {
			return;
		}
		$div_name = $view->params->get( 'main_div_name' );
		if ( empty( self::$picked[ $div_name ] ) ) {
			return;
		}
		$picked = self::$picked[ $div_name ];
		$list   = implode( ',', array_map( 'intval', $picked['ids'] ) );

		$kept = array();
		foreach ( (array) $filters as $key => $filter ) {
			// The category and product type conditions go, publication, access and stock stay
			if ( is_string( $filter ) && ( preg_match( '#\ba\.#', $filter ) || false !== strpos( $filter, 'b.product_type' ) ) ) {
				continue;
			}
			$kept[ $key ] = $filter;
		}
		$kept[]  = 'b.product_id IN (' . $list . ')';
		$filters = $kept;

		$select = 'SELECT DISTINCT b.*';
		$a      = hikashop_table( 'product' ) . ' AS b';
		$b      = '';
		$on     = '';
		if ( $picked['order'] ) {
			$order = ' ORDER BY FIELD(b.product_id, ' . $list . ')';
		}
	}

	/**
	 * Clean list of product ids.
	 *
	 * @param mixed $value Ids as an array or a comma separated string.
	 * @return int[]
	 */
	public static function ids( $value ) {
		if ( ! is_array( $value ) ) {
			$value = explode( ',', (string) $value );
		}
		$ids = array_filter( array_map( 'absint', $value ) );
		return array_values( array_unique( $ids ) );
	}

	/**
	 * Product categories for the category selects.
	 *
	 * @return array Category id => name, indented by depth.
	 */
	public static function category_options() {
		static $options = null;
		if ( null !== $options ) {
			return $options;
		}
		$options = array( '0' => esc_html__( 'All categories', 'hikashop-for-elementor' ) );

		$db = \JFactory::getDBO();
		$db->setQuery(
			'SELECT category_id, category_name, category_depth FROM ' . hikashop_table( 'category' )
			. ' WHERE category_type = ' . $db->quote( 'product' ) . ' AND category_depth > 1'
			. ' ORDER BY category_left ASC',
			0,
			1000
		);
		$rows = $db->loadObjectList();
		foreach ( (array) $rows as $row ) {
			$name = function_exists( 'hikashop_translate' ) ? hikashop_translate( $row->category_name ) : $row->category_name;
			$options[ (string) (int) $row->category_id ] = str_repeat( '- ', max( 0, (int) $row->category_depth - 2 ) ) . wp_strip_all_tags( $name );
		}
		return $options;
	}
}
