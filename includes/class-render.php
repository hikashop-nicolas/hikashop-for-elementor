<?php
/**
 * Output helpers: HikaShop assets, script and style extraction, escaping.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor;

defined( 'ABSPATH' ) || exit;

/**
 * Turns HTML built by HikaShop's layouts into safe widget output.
 */
final class Render {

	/**
	 * Whether the request renders inside the Elementor editor or its preview.
	 *
	 * @return bool
	 */
	public static function is_editor() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}
		$elementor = \Elementor\Plugin::$instance;
		return ( $elementor->editor && $elementor->editor->is_edit_mode() )
			|| ( $elementor->preview && $elementor->preview->is_preview_mode() );
	}

	/**
	 * Loads the HikaShop helper bridge its layouts need outside a HikaShop page.
	 */
	public static function prepare() {
		if ( function_exists( 'hikashop_wp_ensure_helper_bridge' ) ) {
			hikashop_wp_ensure_helper_bridge();
		}
		if ( function_exists( 'hikashop_initModule' ) ) {
			hikashop_initModule();
		}
	}

	/**
	 * Runs a HikaShop rendering as on the site, also in the editor's admin requests.
	 *
	 * @param callable $callback Rendering.
	 * @return mixed What the callback returns.
	 */
	public static function as_site( $callback ) {
		if ( ! function_exists( 'hikashop_siteRendering' ) ) {
			return $callback();
		}
		hikashop_siteRendering( 1 );
		try {
			return $callback();
		} finally {
			hikashop_siteRendering( -1 );
		}
	}

	/**
	 * Enqueues HikaShop's frontend CSS and JavaScript.
	 */
	public static function enqueue_shop_assets() {
		if ( ! function_exists( 'hikashop_wp_enqueue_frontend_assets' ) && defined( 'HIKASHOP_WP_PLUGIN_DIR' ) && is_file( HIKASHOP_WP_PLUGIN_DIR . 'assets.php' ) ) {
			require_once HIKASHOP_WP_PLUGIN_DIR . 'assets.php';
		}
		if ( function_exists( 'hikashop_wp_enqueue_frontend_assets' ) ) {
			hikashop_wp_enqueue_frontend_assets();
		}
		wp_enqueue_style( 'hikashop-elementor', HIKASHOP_ELEMENTOR_URL . 'assets/css/frontend.css', array(), HIKASHOP_ELEMENTOR_VERSION );
	}

	/**
	 * Prints HikaShop HTML through wp_kses(); HikaShop queues the layout's scripts and styles.
	 *
	 * @param string $html HTML from a HikaShop layout.
	 */
	public static function output( $html ) {
		$html = (string) $html;
		if ( '' === trim( $html ) ) {
			return;
		}

		$ajax = wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST );
		if ( ! $ajax && ! is_admin() ) {
			self::enqueue_shop_assets();
		}

		if ( function_exists( 'hikashop_wp_queue_layout_assets' ) ) {
			$html = hikashop_wp_queue_layout_assets( $html, ! $ajax );
		} else {
			$html = preg_replace( '#<(script|style)\b[^>]*>.*?</\1>#is', '', $html );
		}

		echo wp_kses( $html, self::allowed_html() );
	}



	/**
	 * Tags and attributes HikaShop's layouts use: the post allowlist plus forms and their event handlers.
	 *
	 * @return array
	 */
	public static function allowed_html() {
		static $allowed = null;
		if ( null !== $allowed ) {
			return $allowed;
		}

		$allowed = wp_kses_allowed_html( 'post' );

		$form_tags = array(
			'form'     => array( 'action', 'method', 'enctype', 'target', 'novalidate', 'autocomplete', 'accept-charset' ),
			'input'    => array( 'type', 'value', 'checked', 'disabled', 'readonly', 'required', 'min', 'max', 'step', 'size', 'maxlength', 'minlength', 'placeholder', 'pattern', 'inputmode', 'autocomplete', 'multiple', 'accept', 'list', 'src', 'alt' ),
			'select'   => array( 'multiple', 'size', 'disabled', 'required', 'autocomplete' ),
			'option'   => array( 'value', 'selected', 'disabled', 'label' ),
			'optgroup' => array( 'label', 'disabled' ),
			'button'   => array( 'type', 'value', 'disabled' ),
			'textarea' => array( 'rows', 'cols', 'disabled', 'readonly', 'required', 'placeholder', 'maxlength', 'wrap' ),
			'label'    => array( 'for' ),
			'fieldset' => array( 'disabled' ),
			'legend'   => array(),
			'meta'     => array( 'content' ),
			'link'     => array( 'href', 'rel' ),
			'noscript' => array(),
		);
		foreach ( $form_tags as $tag => $attributes ) {
			if ( ! isset( $allowed[ $tag ] ) ) {
				$allowed[ $tag ] = array();
			}
			foreach ( $attributes as $attribute ) {
				$allowed[ $tag ][ $attribute ] = true;
			}
		}

		$global = array( 'id', 'class', 'style', 'title', 'role', 'name', 'form', 'hidden', 'tabindex', 'lang', 'dir', 'data-*', 'aria-*', 'itemprop', 'itemscope', 'itemtype', 'itemid', 'itemref', 'content', 'loading', 'decoding', 'srcset', 'sizes', 'width', 'height', 'onclick', 'ondblclick', 'onchange', 'oninput', 'onsubmit', 'onfocus', 'onblur', 'onkeyup', 'onkeydown', 'onkeypress', 'onmouseover', 'onmouseout', 'onmouseenter', 'onmouseleave', 'onload', 'onerror' );
		foreach ( $allowed as $tag => $attributes ) {
			if ( ! is_array( $attributes ) ) {
				$attributes = array();
			}
			foreach ( $global as $attribute ) {
				$attributes[ $attribute ] = true;
			}
			$allowed[ $tag ] = $attributes;
		}

		return $allowed;
	}

	/**
	 * A grey box naming the widget, shown in the editor when there is nothing to display.
	 *
	 * @param string $label Widget name.
	 * @param string $hint  Why it is empty.
	 */
	public static function placeholder( $label, $hint = '' ) {
		if ( ! self::is_editor() ) {
			return;
		}
		echo '<div class="hikashop-elementor-placeholder" style="padding:16px;border:1px dashed #a4afb7;color:#556068;text-align:center;font-size:13px;">'
			. '<strong>' . esc_html( $label ) . '</strong>';
		if ( '' !== $hint ) {
			echo '<br>' . esc_html( $hint );
		}
		echo '</div>';
	}
}
