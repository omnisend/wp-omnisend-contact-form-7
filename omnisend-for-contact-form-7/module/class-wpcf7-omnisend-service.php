<?php
/**
 * Omnisend Contact form 7 plugin
 *
 * @package OmnisendContactFrom7Plugin
 */

use Omnisend\SDK\V1\Omnisend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPCF7_Omnisend_Service extends WPCF7_Service {


	private static $instance;

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	public function get_title() {
		return 'Omnisend';
	}

	public function is_active() {
		if ( ! $this->is_required_plugin_active() || ! $this->is_required_plugin_updated() ) {
			return false;
		}

		return Omnisend::is_connected();
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

	public function is_required_plugin_active(): bool {
		return is_plugin_active( 'omnisend/class-omnisend-core-bootstrap.php' );
	}

	public function is_required_plugin_updated(): bool {
		return class_exists( 'Omnisend\SDK\V1\Omnisend' );
	}

	private function display_setup() {
		echo '<strong>To integrate your Contact Form 7 with Omnisend, complete these steps:</strong>
            <br />
            <br />
            <li>Install the <a href="https://wordpress.org/plugins/omnisend/">Email Marketing by Omnisend</a> plugin</li>
            <li>Connect your WordPress site to your Omnisend account</li>
            <br />';

		printf(
			'<p><a href="%1$s" class="button">%2$s</a></p>',
			esc_url( $this->menu_page_url( 'action=check' ) ),
			'Connect Omnisend'
		);
	}

	private function check_setup(): void {
		if ( ! $this->is_required_plugin_active() ) {
			echo '<div class="notice notice-error"><p><strong>Error</strong>: <a href="https://wordpress.org/plugins/omnisend/">Email Marketing by Omnisend</a> plugin is not installed or activated</p></div>';
			$this->display_setup();
		} elseif ( ! $this->is_required_plugin_updated() ) {
			echo '<div class="notice notice-error"><p><strong>Error</strong>:Your Email Marketing by Omnisend is not up to date. Please update plugins </p></div>';
			$this->display_setup();
		} elseif ( ! Omnisend::is_connected() ) {
			echo '<div class="notice notice-error"><p><strong>Error</strong>: WordPress site is not connected to your Omnisend account</p></div>';
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
