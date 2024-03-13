<?php
/**
 * Omnisend Contact form 7 plugin
 *
 * Plugin Name: Omnisend for Contact Form 7
 * Description: A Contact Form 7 add-on to sync contacts with Omnisend. In collaboration with Omnisnnd for WooCommerce plugin it enables better customer tracking
 * Version: 1.1.2
 * Author: Omnisend
 * Author URI: https://www.omnisend.com
 * Developer: Omnisend
 * Developer URI: https://developers.omnisend.com
 * Text Domain: omnisend-for-contact-forms-7
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

const WPCP7_OMNISEND_PLUGIN_NAME                     = 'Omnisend for Contact Form 7';
const WPCP7_OMNISEND_PLUGIN_VERSION                  = '1.1.2';
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
