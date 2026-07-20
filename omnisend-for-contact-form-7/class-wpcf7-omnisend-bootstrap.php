<?php
/**
 * Omnisend Contact form 7 plugin
 *
 * Plugin Name: Omnisend for Contact Form 7 Add-On
 * Description: A Contact Form 7 add-on to sync contacts with Omnisend. In collaboration with Omnisend for WooCommerce plugin it enables better customer tracking
 * Version: 1.2.0
 * Requires PHP: 7.4
 * Author: Omnisend
 * Author URI: https://www.omnisend.com
 * Developer: Omnisend
 * Developer URI: https://developers.omnisend.com
 * Text Domain: omnisend-for-contact-form-7
 * ------------------------------------------------------------------------
 * Copyright 2023 Omnisend
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package OmnisendContactFrom7Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wpcf7_init', array( 'WPCF7_Omnisend_Bootstrap', 'load' ), 5 );
add_action( 'admin_init', array( 'WPCF7_Omnisend_Bootstrap', 'add_privacy_policy_content' ) );

const WPCP7_OMNISEND_PLUGIN_NAME                     = 'Omnisend for Contact Form 7';
const WPCP7_OMNISEND_PLUGIN_VERSION                  = '1.2.0';
const WPCP7_OMNISEND_SUPPORT_ARTICLE_LINK            = 'https://support.omnisend.com/en/articles/8672359-integration-with-contact-form-7';
const WPCP7_OMNISEND_WELCOME_AUTOMATION_ARTICLE_LINK = 'https://support.omnisend.com/en/articles/1061818-welcome-email-automation';

class WPCF7_Omnisend_Bootstrap {
	public static function load(): void {
		if ( self::has_breaking_changes() ) {
			return;
		}

		require_once 'module/class-wpcf7-omnisend-service.php';
		require_once 'module/class-wpcf7-omnisend.php';
		require_once 'module/class-wpcf7-omnisend-form-meta-data.php';

		WPCF7_Integration::get_instance()->add_service(
			'omnisend',
			WPCF7_Omnisend_Service::get_instance()
		);

		WPCF7_Omnisend::get_instance()->load();
	}

	public static function add_privacy_policy_content(): void {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$content =
			'<p>' . esc_html__( 'When you submit a Contact Form 7 form on this site, the Omnisend for Contact Form 7 Add-On sends the personal data you provide to Omnisend for email and SMS marketing purposes. Depending on how the form is configured, this may include your email address, phone number, first and last name, your consent choices, and any other form fields you fill in.', 'omnisend-for-contact-form-7' ) . '</p>' .
			'<p>' . esc_html__( 'This data is transmitted to and stored by Omnisend, a third-party service, and is retained there according to Omnisend’s data retention practices for as long as your contact record exists. The plugin itself does not store this data separately in your WordPress database.', 'omnisend-for-contact-form-7' ) . '</p>' .
			'<p>' . esc_html__( 'To enable web tracking, the add-on sets an omnisendContactID cookie in your browser after submission so Omnisend can identify you and track your activity on the site.', 'omnisend-for-contact-form-7' ) . '</p>' .
			'<p>' . sprintf(
				/* translators: 1: Omnisend Privacy Policy URL, 2: Omnisend Terms of Use URL */
				esc_html__( 'You have the right to request access to, export of, or deletion of your personal data. For details on how Omnisend processes personal data and how to exercise these rights, see Omnisend’s Privacy Policy at %1$s and Terms of Use at %2$s.', 'omnisend-for-contact-form-7' ),
				'<a href="https://www.omnisend.com/privacy/" target="_blank">https://www.omnisend.com/privacy/</a>',
				'<a href="https://www.omnisend.com/terms" target="_blank">https://www.omnisend.com/terms</a>'
			) . '</p>';

		wp_add_privacy_policy_content( WPCP7_OMNISEND_PLUGIN_NAME, wp_kses_post( $content ) );
	}

	public static function has_breaking_changes(): bool {
		if ( ! method_exists( 'WPCF7_Integration', 'add_service' ) ) {
			return true;
		}

		if ( ! method_exists( 'WPCF7_ContactForm', 'scan_form_tags' ) ) {
			return true;
		}

		if ( ! method_exists( 'WPCF7_ContactForm', 'id' ) ) {
			return true;
		}

		if ( ! method_exists( 'WPCF7_Submission', 'get_posted_data' ) ) {
			return true;
		}

		return false;
	}
}

add_action(
	'admin_notices',
	function () {
		if ( ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			echo '<div class="notice notice-error"><strong>' . esc_html( WPCP7_OMNISEND_PLUGIN_NAME ) . '</strong> requires <strong>Contact form 7</strong> to be installed and active.</p></div>';
		} elseif ( WPCF7_Omnisend_Bootstrap::has_breaking_changes() ) {
			echo '<div class="notice notice-error"><strong>' . esc_html( WPCP7_OMNISEND_PLUGIN_NAME ) . '</strong> plugin is not loaded due compatibility issue with <strong>Contact Form 7</strong> plugin. Please update <strong>' . esc_html( WPCP7_OMNISEND_PLUGIN_NAME ) . '</strong> plugin to latest version.</p></div>';
		}
	}
);
