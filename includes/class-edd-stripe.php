<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main EDD_Stripe Class.
 *
 * Tap tap tap... Is this thing on?
 *
 * @since 1.0.0
 */
final class EDD_Stripe {

	/**
	 * EDD_Stripe version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var EDD_Stripe
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Main EDD_Stripe Instance.
	 *
	 * Ensures only one instance of EDD_Stripe is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see edd_gateway_stripe()
	 * @return EDD_Stripe - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'edd-gateway-stripe' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'edd-gateway-stripe' ), '1.0.0' );
	}

	/**
	 * Is not allowed to call from outside to prevent from creating multiple instances,
	 * to use the singleton, you have to obtain the instance from EDD_Stripe::instance() instead
	 */
	private function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define plugin Constants.
	 */
	private function define_constants() {
		$this->define( 'EDD_STRIPE_ABSPATH', dirname( EDD_STRIPE_PLUGIN_FILE ) . '/' );
		$this->define( 'EDD_STRIPE_PLUGIN_BASENAME', plugin_basename( EDD_STRIPE_PLUGIN_FILE ) );
		$this->define( 'EDD_STRIPE_VERSION', $this->version );
		$this->define( 'EDD_STRIPE_TEMPLATE_DEBUG_MODE', false );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Returns true if the request is a non-legacy REST API request.
	 *
	 * Legacy REST requests should still run some extra code for backwards compatibility.
	 *
	 * @todo: replace this function once core WP function is available: https://core.trac.wordpress.org/ticket/42061.
	 *
	 * @return bool
	 */
	public function is_rest_api_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		return apply_filters( 'edd_gateway_stripe_is_rest_api_request', $is_rest_api_request );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		// Classes.
		require_once EDD_STRIPE_ABSPATH . 'includes/class-edd-stripe-exception.php';
		require_once EDD_STRIPE_ABSPATH . 'includes/class-edd-stripe-helper.php';
		include_once EDD_STRIPE_ABSPATH . 'includes/class-edd-stripe-api.php';
		require_once EDD_STRIPE_ABSPATH . 'includes/abstracts/abstract-edd-stripe-payment-gateway.php';
		require_once EDD_STRIPE_ABSPATH . 'includes/class-edd-stripe-gateway.php';
		require_once EDD_STRIPE_ABSPATH . 'includes/class-edd-stripe-customer.php';
		require_once EDD_STRIPE_ABSPATH . 'includes/class-edd-stripe-intent-controller.php';

		EDD_Stripe_Gateway::instance();

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
			$this->frontend_hooks();
		}
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	public function frontend_hooks() {
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', EDD_STRIPE_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( EDD_STRIPE_PLUGIN_FILE ) );
	}

}
