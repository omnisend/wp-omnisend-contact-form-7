<?php
/**
 * Omnisend Contact form 7 plugin
 *
 * @package OmnisendContactFrom7Plugin
 */

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

		/** @var $contact_form WPCF7_ContactForm */
		$form_id = $contact_form->id();

		$submission  = WPCF7_Submission::get_instance();
		$posted_data = $submission->get_posted_data();

		$form_meta_data = new WPCF7_Omnisend_Form_Meta_Data( $form_id, true );
		if ( ! $form_meta_data->is_form_enabled() ) {
			return; // form is not enabled for Omnisend.
		}

		$contact = new WPCF7_Omnisend_Contact();
		$contact->add_tag( WPCF7_Omnisend_Utils::clean_up_tag( $contact_form->title() ) );

		if ( ! empty( $form_meta_data->get_email_field_name() ) ) {
			$contact->set_email( $posted_data[ $form_meta_data->get_email_field_name() ] );
		}

		if ( ! empty( $form_meta_data->is_send_welcome_email_enabled() ) ) {
			$contact->set_send_welcome_email( $form_meta_data->is_send_welcome_email_enabled() );
		}

		if ( ! empty( $form_meta_data->get_address_field_name() ) ) {
			$contact->set_address( $posted_data[ $form_meta_data->get_address_field_name() ] );
		}

		if ( ! empty( $form_meta_data->get_city_field_name() ) ) {
			$contact->set_city( $posted_data[ $form_meta_data->get_city_field_name() ] );
		}

		if ( ! empty( $form_meta_data->get_state_field_name() ) ) {
			$contact->set_state( $posted_data[ $form_meta_data->get_state_field_name() ] );
		}

		if ( ! empty( $form_meta_data->get_country_field_name() ) ) {
			$contact->set_country( $posted_data[ $form_meta_data->get_country_field_name() ] );
		}

		if ( ! empty( $form_meta_data->get_postal_code_field_name() ) ) {
			$contact->set_postal_code( $posted_data[ $form_meta_data->get_postal_code_field_name() ] );
		}

		if ( ! empty( $form_meta_data->get_phone_field_name() ) ) {
			$contact->set_phone( $posted_data[ $form_meta_data->get_phone_field_name() ] );
		}

		if ( ! empty( $form_meta_data->get_birthday_field_name() ) ) {
			$contact->set_birthday( $posted_data[ $form_meta_data->get_birthday_field_name() ] );
		}

		if ( ! empty( $form_meta_data->get_first_name_field_name() ) ) {
			$contact->set_first_name( $posted_data[ $form_meta_data->get_first_name_field_name() ] );
		}

		if ( ! empty( $form_meta_data->get_last_name_field_name() ) ) {
			$contact->set_last_name( $posted_data[ $form_meta_data->get_last_name_field_name() ] );
		}

		if ( ! empty( $posted_data[ $form_meta_data->get_email_consent_field_name() ] ) ) {
			$contact->set_email_consent( 'plugin (contact form 7), form ID: ' . $form_id );
		}

		if ( ! empty( $posted_data[ $form_meta_data->get_phone_consent_field_name() ] ) ) {
			$contact->set_phone_consent( 'plugin (contact form 7), form ID: ' . $form_id );
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

			$safe_label = str_replace( array( ' ', '-' ), '_', $prop );
			$safe_label = preg_replace( '/[^A-Za-z0-9_]/', '', $safe_label );
			$safe_label = mb_strimwidth( $safe_label, 0, 128 );
			$contact->add_custom_property( $safe_label, $value );
		}

		if ( ! $contact->is_valid() ) {
			return;
		}

		$contact_id = $this->create_contact( $contact );
		if ( ! $contact_id ) {
			return;
		}

		$this->enable_web_tracking( $contact_id );
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
			'callback' => function ( $post ) { // phpcs:ignore
				require_once 'form-settings.php';
			},
		);
		return $panels;
	}

	private function create_contact( $contact ): string {

		$response = wp_remote_post(
			'https://api.omnisend.com/v3/contacts',
			array(
				'body'    => wp_json_encode( $contact->to_array() ),
				'headers' => array(
					'Content-Type' => 'application/json',
					'X-API-Key'    => WPCF7_Omnisend_Service::get_instance()->get_api_key(),
				),
				'timeout' => 10,
			)
		);

		if ( is_wp_error( $response ) ) {
			error_log( 'wp_remote_post error: ' . $response->get_error_message() ); // phpcs:ignore
			return '';
		}

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( $http_code >= 400 ) {
			$body = wp_remote_retrieve_body( $response );
			error_log( "HTTP error: {$http_code} - " . wp_remote_retrieve_response_message( $response ) . " - {$body}" ); // phpcs:ignore
			return '';
		}

		$body = wp_remote_retrieve_body( $response );
		if ( ! $body ) {
			return '';
		}

		$arr = json_decode( $body, true );

		return ! empty( $arr['contactID'] ) ? $arr['contactID'] : '';
	}

	private function enable_web_tracking( $contact_id ) {
		$host   = wp_parse_url( home_url(), PHP_URL_HOST );
		$expiry = strtotime( '+1 year' );
		setcookie( 'omnisendContactID', $contact_id, $expiry, '/', $host );
	}
}
