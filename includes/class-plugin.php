<?php
/**
 * Bootstrap: checks the requirements and hooks everything in.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 */
final class Plugin {

	const MIN_ELEMENTOR = '3.24.0';
	const MIN_HIKASHOP  = '6.8.3';

	/**
	 * Hooks the plugin in once every plugin is loaded.
	 */
	public static function boot() {
		add_action( 'plugins_loaded', array( __CLASS__, 'init' ), 20 );
	}

	/**
	 * Whether HikaShop is installed, active and recent enough.
	 *
	 * @return bool
	 */
	public static function hikashop_ready() {
		return defined( 'HIKASHOP_COMPONENT' ) && defined( 'HIKASHOP_WP_VERSION' ) && function_exists( 'hikashop_get' )
			&& version_compare( HIKASHOP_WP_VERSION, self::MIN_HIKASHOP, '>=' );
	}

	/**
	 * Whether Elementor is active and recent enough.
	 *
	 * @return bool
	 */
	public static function elementor_ready() {
		return did_action( 'elementor/loaded' ) && defined( 'ELEMENTOR_VERSION' )
			&& version_compare( ELEMENTOR_VERSION, self::MIN_ELEMENTOR, '>=' );
	}

	/**
	 * Registers the hooks, or the admin notice saying what is missing.
	 */
	public static function init() {
		if ( ! self::hikashop_ready() || ! self::elementor_ready() ) {
			add_action( 'admin_notices', array( __CLASS__, 'requirements_notice' ) );
			return;
		}

		require_once HIKASHOP_ELEMENTOR_DIR . 'includes/class-render.php';
		require_once HIKASHOP_ELEMENTOR_DIR . 'includes/class-listing.php';
		require_once HIKASHOP_ELEMENTOR_DIR . 'includes/class-product-template.php';
		require_once HIKASHOP_ELEMENTOR_DIR . 'includes/class-ajax.php';

		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
		add_action( 'elementor/controls/register', array( __CLASS__, 'register_controls' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'editor_scripts' ) );
		add_action( 'elementor/preview/enqueue_styles', array( Render::class, 'enqueue_shop_assets' ) );
		add_filter( 'elementor/widgets/black_list', array( __CLASS__, 'hide_classic_widgets' ) );

		Listing::init();
		Product_Template::init();
		Ajax::init();
	}

	/**
	 * Tells the administrator which requirement is missing.
	 */
	public static function requirements_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( ! self::hikashop_ready() ) {
			$message = sprintf(
				/* translators: 1: minimum HikaShop version, 2: link to the HikaShop download page */
				__( 'HikaShop for Elementor needs HikaShop %1$s or newer, installed and active. HikaShop Starter is free: %2$s', 'hikashop-for-elementor' ),
				self::MIN_HIKASHOP,
				'<a href="https://www.hikashop.com/download.html" target="_blank" rel="noopener noreferrer">' . esc_html__( 'download HikaShop', 'hikashop-for-elementor' ) . '</a>'
			);
		} else {
			$message = sprintf(
				/* translators: %s: minimum Elementor version */
				__( 'HikaShop for Elementor needs Elementor %s or newer, installed and active.', 'hikashop-for-elementor' ),
				self::MIN_ELEMENTOR
			);
		}

		echo '<div class="notice notice-warning"><p>' . wp_kses(
			$message,
			array(
				'a' => array(
					'href'   => true,
					'target' => true,
					'rel'    => true,
				),
			)
		) . '</p></div>';
	}

	/**
	 * Hides the classic HikaShop WordPress widgets this plugin replaces in the Elementor panel.
	 *
	 * @param array $black_list Widget classes Elementor leaves out.
	 * @return array
	 */
	public static function hide_classic_widgets( $black_list ) {
		return array_merge( (array) $black_list, array( 'HikaShop_Widget_Products', 'HikaShop_Widget_Cart', 'HikaShop_Widget_Wishlist', 'HikaShop_Widget_Currency', 'HikaShop_Widget_Filter' ) );
	}

	/**
	 * Adds the HikaShop group to the Elementor widget panel.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
	 */
	public static function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'hikashop',
			array(
				'title' => esc_html__( 'HikaShop', 'hikashop-for-elementor' ),
				'icon'  => 'eicon-cart',
			)
		);
	}

	/**
	 * Registers the custom controls.
	 *
	 * @param \Elementor\Controls_Manager $controls_manager Elementor controls manager.
	 */
	public static function register_controls( $controls_manager ) {
		require_once HIKASHOP_ELEMENTOR_DIR . 'includes/controls/class-product-select.php';
		$controls_manager->register( new Controls\Product_Select() );
	}

	/**
	 * Registers the widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public static function register_widgets( $widgets_manager ) {
		$dir = HIKASHOP_ELEMENTOR_DIR . 'includes/widgets/';
		require_once $dir . 'class-base-widget.php';
		require_once $dir . 'class-products.php';
		require_once $dir . 'class-categories.php';
		require_once $dir . 'class-product-part.php';
		require_once $dir . 'class-product-parts.php';
		require_once $dir . 'class-module-widget.php';
		require_once $dir . 'class-modules.php';

		$widgets = array(
			new Widgets\Products(),
			new Widgets\Categories(),
		);
		foreach ( Widgets\Product_Part::classes() as $class ) {
			$widgets[] = new $class();
		}
		foreach ( Widgets\Module_Widget::classes() as $class ) {
			$widgets[] = new $class();
		}
		foreach ( $widgets as $widget ) {
			$widgets_manager->register( $widget );
		}
	}

	/**
	 * Loads the editor script of the product picker.
	 */
	public static function editor_scripts() {
		wp_enqueue_script(
			'hikashop-elementor-editor',
			HIKASHOP_ELEMENTOR_URL . 'assets/js/editor.js',
			array( 'jquery' ),
			HIKASHOP_ELEMENTOR_VERSION,
			true
		);
		wp_localize_script(
			'hikashop-elementor-editor',
			'hikashopElementor',
			array(
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'hikashop_elementor_search' ),
				'settingsUrl' => admin_url( 'admin.php?page=hikashop-widget-options&tmpl=popup' ),
			)
		);
	}
}
