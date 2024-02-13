<?php
/**
 * Omnisend Contact form 7 plugin
 *
 * @package OmnisendContactFrom7Plugin
 */

use Omnisend\SDK\V1\Contact;
use Omnisend\SDK\V1\Omnisend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPCF7_Omnisend {


	private static $instance;

	/**
	 * Returns the singleton instance of this class.
	 *
	 * @return WPCF7_Omnisend The instance.
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function load() {
		add_action( 'wpcf7_before_send_mail', array( $this, 'on_wpcf7_before_send_mail' ) );
		add_action( 'wpcf7_after_save', array( $this, 'on_wpcf7_after_save' ) );
		add_action( 'wpcf7_editor_panels', array( $this, 'on_wpcf7_editor_panels' ) );
	}

	/**
	 * Callback to the wpcf7_before_send_mail action hook. Creates Omnisend contact based on the submission.
	 */
	public function on_wpcf7_before_send_mail( $contact_form ) {
		if ( ! WPCF7_Omnisend_Service::get_instance()->is_active() ) {
			return; // service is not active, no need for other hooks.
		}

		if ( ! class_exists( 'Omnisend\SDK\V1\Omnisend' ) ) {
			return;
		}

		/** @var $contact_form WPCF7_ContactForm */
		$form_id = $contact_form->id();

		$submission  = WPCF7_Submission::get_instance();
		$posted_data = $submission->get_posted_data();

		$form_meta_data = new WPCF7_Omnisend_Form_Meta_Data( $form_id, true );
		if ( ! $form_meta_data->is_form_enabled() ) {
			return; // form is not enabled for Omnisend.
		}

		$contact = new Contact();

		$contact->add_tag( 'Contact Form 7' );
		$contact->add_tag( $contact_form->title() );

		$email = $form_meta_data->get_email_field_name();

		if ( ! empty( $email ) && $email !== '---' ) {
			$contact->set_email( $posted_data[ $email ] );
		}

		$is_welcome_email_enabled = $form_meta_data->is_send_welcome_email_enabled();
		if ( ! empty( $is_welcome_email_enabled ) && $is_welcome_email_enabled !== '---' ) {
			$contact->set_welcome_email( true );
		}

		$address = $form_meta_data->get_address_field_name();
		if ( ! empty( $address ) && $address !== '---' ) {
			$contact->set_address( $posted_data[ $address ] );
		}

		$city_name = $form_meta_data->get_city_field_name();
		if ( ! empty( $city_name ) && $city_name !== '---' ) {
			$contact->set_city( $posted_data[ $city_name ] );
		}

		$state = $form_meta_data->get_state_field_name();
		if ( ! empty( $state ) && $state !== '---' ) {
			$contact->set_state( $posted_data[ $state ] );
		}

		$country = $form_meta_data->get_country_field_name();
		if ( ! empty( $country ) && $country !== '---' ) {
			$contact->set_country( $posted_data[ $country ] );
		}

		$postal_code = $form_meta_data->get_postal_code_field_name();
		if ( ! empty( $postal_code ) && $postal_code !== '---' ) {
			$contact->set_postal_code( $posted_data[ $postal_code ] );
		}

		$phone = $form_meta_data->get_phone_field_name();
		if ( ! empty( $phone ) && $phone !== '---' ) {
			$contact->set_phone( $posted_data[ $phone ] );
		}

		$birthday = $form_meta_data->get_birthday_field_name();
		if ( ! empty( $birthday ) && $birthday !== '---' ) {
			$contact->set_birthday( $posted_data[ $birthday ] );
		}

		$first_name = $form_meta_data->get_first_name_field_name();
		if ( ! empty( $first_name ) && $first_name !== '---' ) {
			$contact->set_first_name( $posted_data[ $first_name ] );
		}

		$last_name = $form_meta_data->get_last_name_field_name();
		if ( ! empty( $last_name ) && $last_name !== '---' ) {
			$contact->set_last_name( $posted_data[ $last_name ] );
		}

		$email_consent = $form_meta_data->get_email_consent_field_name();
		if ( ! empty( $posted_data[ $email_consent ] ) && $email_consent !== '---' ) {
			$contact->set_email_consent( 'plugin (contact form 7), form id (' . $form_id . ')' );
			$contact->set_email_opt_in( 'plugin (contact form 7), form id (' . $form_id . ')' );
		}

		$phone_consent = $form_meta_data->get_phone_consent_field_name();
		if ( ! empty( $posted_data[ $phone_consent ] ) && $phone_consent !== '---' ) {
			$contact->set_phone_consent( 'plugin (contact form 7), form id (' . $form_id . ')' );
			$contact->set_phone_opt_in( 'plugin (contact form 7), form id (' . $form_id . ')' );
		}

		$form_tags = $contact_form->scan_form_tags();

		$all_fields = array();
		foreach ( $form_tags as $form_tag ) {
			/** @var $form_tag WPCF7_FormTag */
			if ( ! $form_tag->name ) {
				continue;
			}
			$all_fields[] = $form_tag->name;
		}

		foreach ( $posted_data as $prop => $value ) {
			if ( ! $value ) {
				continue;
			}

			if ( ! in_array( $prop, $all_fields ) ) {
				continue;
			}

			if ( $form_meta_data->is_field_mapped( $prop ) ) {
				continue;
			}

			$name = 'contact_form_7_' . $prop;
			$contact->add_custom_property( $name, $value );
		}

		$response = Omnisend::get_client( WPCP7_OMNISEND_PLUGIN_NAME, WPCP7_OMNISEND_PLUGIN_VERSION )->create_contact( $contact );
		if ( $response->get_wp_error()->has_errors() ) {
			error_log( 'Error in after_submission: ' . $response->get_wp_error()->get_error_message()); // phpcs:ignore
			return;
		}

		$this->enable_web_tracking( $response->get_contact_id() );
	}

	/**
	 * Callback to the wpcf7_after_save action hook. Updates form field mapping, enabled/disable form with Omnisend
	 */
	public function on_wpcf7_after_save( $post ) {
		if ( ! WPCF7_Omnisend_Service::get_instance()->is_active() ) {
			return; // service is not active, no need for other hooks.
		}

		$id = isset( $_POST['post_ID'] ) ? sanitize_text_field( wp_unslash( $_POST['post_ID'] ) ) : '-1';
		check_admin_referer( 'wpcf7-save-contact-form_' . $id );

		if ( empty( $_POST ) ) {
			return;
		}

		$get_var = function ( $name ) use ( $id ) {

			check_admin_referer( 'wpcf7-save-contact-form_' . $id );
			if ( empty( $_POST['wpcf7-omnisend'][ $name ] ) ) {
				return null;
			}

			return sanitize_text_field( wp_unslash( $_POST['wpcf7-omnisend'][ $name ] ) );
		};

		$form_meta_data = new WPCF7_Omnisend_Form_Meta_Data( $post->id(), false );

		$form_meta_data->set_form_enabled( ! empty( $get_var( 'enabled' ) ) );
		$form_meta_data->set_send_welcome_email_enabled( ! empty( $get_var( 'send_welcome_email' ) ) );
		$form_meta_data->set_email_field_name( $get_var( 'email' ) );
		$form_meta_data->set_address_field_name( $get_var( 'address' ) );
		$form_meta_data->set_city_field_name( $get_var( 'city' ) );
		$form_meta_data->set_state_field_name( $get_var( 'state' ) );
		$form_meta_data->set_country_field_name( $get_var( 'country' ) );
		$form_meta_data->set_postal_code_field_name( $get_var( 'postal_code' ) );
		$form_meta_data->set_phone_field_name( $get_var( 'phone' ) );
		$form_meta_data->set_birthday_field_name( $get_var( 'birthday' ) );
		$form_meta_data->set_phone_consent_field_name( $get_var( 'consent_phone' ) );
		$form_meta_data->set_first_name_field_name( $get_var( 'first_name' ) );
		$form_meta_data->set_last_name_field_name( $get_var( 'last_name' ) );
		$form_meta_data->set_email_consent_field_name( $get_var( 'consent_email' ) );

		$form_meta_data->save();
	}

	/**
	 * Callback to the wpcf7_editor_panels action hook. Generates Omnisend tab content for specific form
	 */
	public function on_wpcf7_editor_panels( $panels ) {
		if ( ! WPCF7_Omnisend_Service::get_instance()->is_active() ) {
			return $panels; // service is not active, no need for other hooks.
		}

		$panels['omnisend-panel'] = array(
			'title'    => 'Omnisend',
			'callback' => function ($post) { // phpcs:ignore
				require_once 'form-settings.php';
			},
		);
		return $panels;
	}

	private function enable_web_tracking( $contact_id ) {
		$host   = wp_parse_url( home_url(), PHP_URL_HOST );
		$expiry = strtotime( '+1 year' );
		setcookie( 'omnisendContactID', $contact_id, $expiry, '/', $host );
	}
}
