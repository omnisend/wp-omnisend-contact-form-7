<?php
/**
 * Omnisend Contact form 7 plugin
 *
 * @package OmnisendContactFrom7Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPCF7_Omnisend_Service extends WPCF7_Service {


	private static $instance;
	private $api_key;

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->api_key = get_option( 'omnisend_api_key', '' );
	}

	public function get_title() {
		return 'Omnisend';
	}

	public function is_active() {
		if ( ! $this->is_required_plugin_active() ) {
			return false;
		}

		return $this->api_key != '';
	}

	public function get_categories() {
		return array( 'email_marketing' );
	}

	public function icon() {
	}

	public function link() {
		echo esc_html(
			wpcf7_link(
				'https://www.omnisend.com/',
				'omnisend.com'
			)
		);
	}

	public function load( $action = '' ) {
		// add code here if we want to reach on action like "setup".
	}

	public function display( $action = '' ) {
		$info = 'With Omnisend add-on, your form submissions flow directly into Omnisend, setting the stage for effective email marketing. From there you can use those contacts to create effective email campaigns, set automated emails, and build meaningful customer relationships that drive revenue.';
		printf( '<p>%s</p>', esc_html( $info ) );

		echo '<p><strong><a target="_blank" href="' . esc_url( WPCP7_OMNISEND_SUPPORT_ARTICLE_LINK ) . '">Omnisend integration</a></strong></p>';

		if ( $this->is_active() ) {
			printf(
				'<p class="dashicons-before dashicons-yes">%s</p>',
				esc_html( 'Omnisend is active on this site.' )
			);
		}

		if ( 'setup' == $action ) {
			$this->display_setup();
		} elseif ( 'check' == $action ) {
			$this->check_setup();
		}

		if ( empty( $action ) && ! $this->is_active() ) {
			printf(
				'<p><a href="%1$s" class="button">%2$s</a></p>',
				esc_url( $this->menu_page_url( 'action=setup' ) ),
				esc_html( __( 'Setup integration', 'contact-form-7' ) )
			);
		}
	}

	public function get_api_key() {
		return $this->api_key;
	}

	public function is_required_plugin_active(): bool {
		return is_plugin_active( 'omnisend-connect/omnisend-woocommerce.php' );
	}

	private function display_setup() {
		echo '<strong>To integrate your Contact Form 7 with Omnisend, complete these steps:</strong>
            <br />
            <br />
            <li>Install the <a href="https://wordpress.org/plugins/omnisend-connect/">Email Marketing for WooCommerce by Omnisend</a> plugin</li>
            <li>Connect your WooCommerce store to your Omnisend account</li>
            <br />';

		printf(
			'<p><a href="%1$s" class="button">%2$s</a></p>',
			esc_url( $this->menu_page_url( 'action=check' ) ),
			'Connect Omnisend'
		);
	}

	private function check_setup(): void {
		if ( ! $this->is_required_plugin_active() ) {
			echo '<div class="notice notice-error"><p><strong>Error</strong>: <a href="https://wordpress.org/plugins/omnisend-connect/">Email Marketing for WooCommerce by Omnisend</a> plugin is not installed or activated</p></div>';
			$this->display_setup();
		} elseif ( empty( $this->api_key ) ) {
			echo '<div class="notice notice-error"><p><strong>Error</strong>: WooCommerce store is not connected to your Omnisend account</p></div>';
			$this->display_setup();
		} else {
			echo '<div class="notice notice-success"><p><strong>Omnisend</strong> integration for Contact form 7 is set up!</p></div>';
		}
	}

	private function menu_page_url( $args = '' ) {
		$args = wp_parse_args( $args );

		$url = menu_page_url( 'wpcf7-integration', false );
		$url = add_query_arg( array( 'service' => 'omnisend' ), $url );

		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}
}