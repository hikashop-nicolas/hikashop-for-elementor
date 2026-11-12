<?php
/**
 * HikaShop product pages rendered with an Elementor template.
 *
 * @package HikaShopElementor
 */

namespace HikaShopElementor;

defined( 'ABSPATH' ) || exit;

/**
 * The product page template setting and its rendering.
 */
final class Product_Template {

	const OPTION = 'hikashop_elementor_product_template';

	/**
	 * Hooks the setting and the page rendering.
	 */
	public static function init() {
		add_filter( 'hikashop_wp_page_output', array( __CLASS__, 'page_output' ), 10, 4 );
		add_action( 'admin_init', array( __CLASS__, 'register_setting' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_page' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( HIKASHOP_ELEMENTOR_FILE ), array( __CLASS__, 'action_links' ) );
	}

	/**
	 * Id of the chosen template, 0 when HikaShop shows its own product page.
	 *
	 * @return int
	 */
	public static function template_id() {
		$id = absint( get_option( self::OPTION, 0 ) );
		if ( ! $id || 'elementor_library' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) {
			return 0;
		}
		return $id;
	}

	/**
	 * Replaces HikaShop's product page by the template.
	 *
	 * @param string|null $output  Output of an earlier filter, null to let HikaShop render.
	 * @param string      $view    HikaShop view.
	 * @param string      $layout  HikaShop layout.
	 * @param string      $content Page content.
	 * @return string|null
	 */
	public static function page_output( $output, $view, $layout, $content = '' ) {
		unset( $content );
		if ( null !== $output || 'product' !== $view || 'show' !== $layout ) {
			return $output;
		}
		$template_id = self::template_id();
		if ( ! $template_id || ! self::product_viewable() ) {
			return $output;
		}

		Render::enqueue_shop_assets();
		$html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, true );
		if ( '' === trim( (string) $html ) ) {
			return $output;
		}
		return '<div class="hikashop-elementor-product-page hikashop_product_page">' . $html . '</div>';
	}

	/**
	 * Whether the product of the request exists and the visitor may see it; otherwise HikaShop answers.
	 *
	 * @return bool
	 */
	private static function product_viewable() {
		$product_id = \hikaInput::get()->getInt( 'cid', 0 );
		if ( ! $product_id ) {
			$product_id = \hikaInput::get()->getInt( 'product_id', 0 );
		}
		if ( ! $product_id ) {
			return false;
		}
		$product_class = hikashop_get( 'class.product' );
		$product       = $product_class->get( $product_id );
		if ( empty( $product ) || 'main' !== $product->product_type || empty( $product->product_published ) ) {
			return false;
		}
		if ( method_exists( $product_class, 'isHiddenInLanguage' ) && $product_class->isHiddenInLanguage( $product ) ) {
			return false;
		}
		if ( function_exists( 'hikashop_isAllowed' ) && isset( $product->product_access ) && ! hikashop_isAllowed( $product->product_access ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Registers the option.
	 */
	public static function register_setting() {
		register_setting(
			'hikashop_elementor',
			self::OPTION,
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);
	}

	/**
	 * Adds Settings > HikaShop for Elementor.
	 */
	public static function add_page() {
		add_options_page(
			esc_html__( 'HikaShop for Elementor', 'hikashop-for-elementor' ),
			esc_html__( 'HikaShop for Elementor', 'hikashop-for-elementor' ),
			'manage_options',
			'hikashop-for-elementor',
			array( __CLASS__, 'render_page' )
		);
	}

	/**
	 * Settings link in the plugin list.
	 *
	 * @param array $links Plugin action links.
	 * @return array
	 */
	public static function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=hikashop-for-elementor' ) ) . '">' . esc_html__( 'Settings', 'hikashop-for-elementor' ) . '</a>' );
		return $links;
	}

	/**
	 * The settings page.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$templates = get_posts(
			array(
				'post_type'      => 'elementor_library',
				'post_status'    => 'publish',
				'posts_per_page' => 200,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		$current   = absint( get_option( self::OPTION, 0 ) );
		$new_url   = admin_url( 'edit.php?post_type=elementor_library&tabs_group=library' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'HikaShop for Elementor', 'hikashop-for-elementor' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'hikashop_elementor' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="hikashop_elementor_product_template"><?php esc_html_e( 'Product page template', 'hikashop-for-elementor' ); ?></label></th>
						<td>
							<select name="<?php echo esc_attr( self::OPTION ); ?>" id="hikashop_elementor_product_template">
								<option value="0"><?php esc_html_e( 'None: HikaShop shows its own product page', 'hikashop-for-elementor' ); ?></option>
								<?php foreach ( $templates as $template ) : ?>
									<option value="<?php echo esc_attr( $template->ID ); ?>" <?php selected( $current, $template->ID ); ?>><?php echo esc_html( $template->post_title ? $template->post_title : '#' . $template->ID ); ?></option>
								<?php endforeach; ?>
							</select>
							<p class="description">
								<?php esc_html_e( 'Every HikaShop product page is shown with this Elementor template. Design it with the product widgets of the HikaShop group (title, images, price, add to cart...): they show the product being viewed.', 'hikashop-for-elementor' ); ?>
								<a href="<?php echo esc_url( $new_url ); ?>"><?php esc_html_e( 'Manage the Elementor templates', 'hikashop-for-elementor' ); ?></a>
							</p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
