<?php
/**
 * Product search of the editor's product picker.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor;

defined( 'ABSPATH' ) || exit;

/**
 * Admin-ajax endpoint returning products as select2 results.
 */
final class Ajax {

	/**
	 * Registers the endpoint.
	 */
	public static function init() {
		add_action( 'wp_ajax_hikashop_elementor_products', array( __CLASS__, 'products' ) );
	}

	/**
	 * Searches main products by name or code, or returns the names of given ids.
	 */
	public static function products() {
		check_ajax_referer( 'hikashop_elementor_search', 'nonce' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( null, 403 );
		}

		$ids  = isset( $_GET['ids'] ) ? Listing::ids( sanitize_text_field( wp_unslash( $_GET['ids'] ) ) ) : array();
		$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

		$db    = \JFactory::getDBO();
		$where = array( 'product_type = ' . $db->quote( 'main' ) );
		if ( ! empty( $ids ) ) {
			$where[] = 'product_id IN (' . implode( ',', $ids ) . ')';
		} elseif ( '' !== $term ) {
			$like    = $db->quote( '%' . addcslashes( $term, '%_\\' ) . '%' );
			$or      = array( 'product_name LIKE ' . $like, 'product_code LIKE ' . $like );
			$where[] = '(' . implode( ' OR ', $or ) . ( ctype_digit( $term ) ? ' OR product_id = ' . (int) $term : '' ) . ')';
		}

		$db->setQuery(
			'SELECT product_id, product_name, product_code FROM ' . hikashop_table( 'product' )
			. ' WHERE ' . implode( ' AND ', $where ) . ' ORDER BY product_name ASC',
			0,
			empty( $ids ) ? 30 : count( $ids )
		);
		$rows = (array) $db->loadObjectList( 'product_id' );

		if ( ! empty( $ids ) ) {
			$sorted = array();
			foreach ( $ids as $id ) {
				if ( isset( $rows[ $id ] ) ) {
					$sorted[] = $rows[ $id ];
				}
			}
			$rows = $sorted;
		}

		$results = array();
		foreach ( $rows as $row ) {
			$name      = function_exists( 'hikashop_translate' ) ? hikashop_translate( $row->product_name ) : $row->product_name;
			$results[] = array(
				'id'   => (string) (int) $row->product_id,
				'text' => wp_strip_all_tags( $name ) . ( '' !== (string) $row->product_code ? ' (' . wp_strip_all_tags( $row->product_code ) . ')' : '' ),
			);
		}
		wp_send_json_success( $results );
	}
}
