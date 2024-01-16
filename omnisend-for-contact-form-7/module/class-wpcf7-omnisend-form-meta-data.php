<?php
/**
 * Omnisend Contact form 7 plugin
 *
 * @package OmnisendContactFrom7Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPCF7_Omnisend_Form_Meta_Data {


	private const FIELD_ENABLED            = 'wpcf7_omnisend_enabled';
	private const FIELD_SEND_WELCOME_EMAIL = 'wpcf7_omnisend_send_welcome_email';
	private const FIELD_EMAIL              = 'wpcf7_omnisend_email';
	private const FIELD_FIRST_NAME         = 'wpcf7_omnisend_first_name';
	private const FIELD_ADDRESS            = 'wpcf7_omnisend_address';
	private const FIELD_CITY               = 'wpcf7_omnisend_city';
	private const FIELD_BIRTHDAY           = 'wpcf7_omnisend_birthday';
	private const FIELD_PHONE              = 'wpcf7_omnisend_phone';
	private const FIELD_STATE              = 'wpcf7_omnisend_state';
	private const FIELD_POSTAL_CODE        = 'wpcf7_omnisend_postal_code';
	private const FIELD_COUNTRY            = 'wpcf7_omnisend_country';
	private const FIELD_LAST_NAME          = 'wpcf7_omnisend_last_name';
	private const FIELD_EMAIL_CONSENT      = 'wpcf7_omnisend_consent_email';
	private const FIELD_PHONE_CONSENT      = 'wpcf7_omnisend_consent_phone';

	private $form_id;

	private bool $is_form_enabled;

	private bool $is_send_welcome_email_enabled;
	private $state_field_name;
	private $country_field_name;
	private $birthday_field_name;
	private $postal_code_field_name;
	private $email_field_name;
	private $phone_field_name;
	private $first_name_field_name;
	private $city_field_name;
	private $address_field_name;
	private $last_name_field_name;
	private $email_consent_field_name;
	private $phone_consent_field_name;

	public function __construct( $form_id, bool $load ) {
		$this->form_id = $form_id;
		if ( $load ) {
			$this->load_meta_data();
		}
	}

	public function is_form_enabled(): bool {
		return $this->is_form_enabled;
	}

	public function is_send_welcome_email_enabled(): bool {
		return $this->is_send_welcome_email_enabled;
	}

	public function get_email_field_name() {
		return $this->email_field_name;
	}

	public function get_first_name_field_name() {
		return $this->first_name_field_name;
	}

	public function get_birthday_field_name() {
		return $this->birthday_field_name;
	}

	public function get_phone_field_name() {
		return $this->phone_field_name;
	}

	public function get_postal_code_field_name() {
		return $this->postal_code_field_name;
	}

	public function get_country_field_name() {
		return $this->country_field_name;
	}

	public function get_state_field_name() {
		return $this->state_field_name;
	}

	public function get_city_field_name() {
		return $this->city_field_name;
	}

	public function get_address_field_name() {
		return $this->address_field_name;
	}

	public function get_last_name_field_name() {
		return $this->last_name_field_name;
	}

	public function get_email_consent_field_name() {
		return $this->email_consent_field_name;
	}

	public function get_phone_consent_field_name() {
		return $this->phone_consent_field_name;
	}

	public function set_form_enabled( $enabled ) {
		$this->is_form_enabled = ! empty( $enabled );
	}

	public function set_send_welcome_email_enabled( $enabled ) {
		$this->is_send_welcome_email_enabled = ! empty( $enabled );
	}


	public function set_email_field_name( $name ) {
		$this->email_field_name = sanitize_text_field( $name );
	}

	public function set_first_name_field_name( $name ) {
		$this->first_name_field_name = sanitize_text_field( $name );
	}

	public function set_birthday_field_name( $name ) {
		$this->birthday_field_name = sanitize_text_field( $name );
	}

	public function set_phone_field_name( $name ) {
		$this->phone_field_name = sanitize_text_field( $name );
	}

	public function set_postal_code_field_name( $name ) {
		$this->postal_code_field_name = sanitize_text_field( $name );
	}

	public function set_country_field_name( $name ) {
		$this->country_field_name = sanitize_text_field( $name );
	}

	public function set_state_field_name( $name ) {
		$this->state_field_name = sanitize_text_field( $name );
	}

	public function set_city_field_name( $name ) {
		$this->city_field_name = sanitize_text_field( $name );
	}

	public function set_address_field_name( $name ) {
		$this->address_field_name = sanitize_text_field( $name );
	}

	public function set_last_name_field_name( $name ) {
		$this->last_name_field_name = sanitize_text_field( $name );
	}

	public function set_email_consent_field_name( $name ) {
		$this->email_consent_field_name = sanitize_text_field( $name );
	}

	public function set_phone_consent_field_name( $name ) {
		$this->phone_consent_field_name = sanitize_text_field( $name );
	}

	public function is_field_mapped( $name ): bool {
		if ( $name == $this->state_field_name ) {
			return true;
		}
		if ( $name == $this->country_field_name ) {
			return true;
		}
		if ( $name == $this->birthday_field_name ) {
			return true;
		}
		if ( $name == $this->postal_code_field_name ) {
			return true;
		}
		if ( $name == $this->email_field_name ) {
			return true;
		}
		if ( $name == $this->phone_field_name ) {
			return true;
		}
		if ( $name == $this->first_name_field_name ) {
			return true;
		}
		if ( $name == $this->city_field_name ) {
			return true;
		}
		if ( $name == $this->address_field_name ) {
			return true;
		}
		if ( $name == $this->last_name_field_name ) {
			return true;
		}
		if ( $name == $this->email_consent_field_name ) {
			return true;
		}
		if ( $name == $this->phone_consent_field_name ) {
			return true;
		}

		return false;
	}

	public function save(): void {
		update_post_meta( $this->form_id, self::FIELD_ENABLED, $this->is_form_enabled );
		update_post_meta( $this->form_id, self::FIELD_SEND_WELCOME_EMAIL, $this->is_send_welcome_email_enabled );
		update_post_meta( $this->form_id, self::FIELD_ADDRESS, $this->address_field_name );
		update_post_meta( $this->form_id, self::FIELD_EMAIL, $this->email_field_name );
		update_post_meta( $this->form_id, self::FIELD_FIRST_NAME, $this->first_name_field_name );
		update_post_meta( $this->form_id, self::FIELD_CITY, $this->city_field_name );
		update_post_meta( $this->form_id, self::FIELD_BIRTHDAY, $this->birthday_field_name );
		update_post_meta( $this->form_id, self::FIELD_PHONE, $this->phone_field_name );
		update_post_meta( $this->form_id, self::FIELD_PHONE_CONSENT, $this->phone_consent_field_name );
		update_post_meta( $this->form_id, self::FIELD_POSTAL_CODE, $this->postal_code_field_name );
		update_post_meta( $this->form_id, self::FIELD_STATE, $this->state_field_name );
		update_post_meta( $this->form_id, self::FIELD_COUNTRY, $this->country_field_name );
		update_post_meta( $this->form_id, self::FIELD_LAST_NAME, $this->last_name_field_name );
		update_post_meta( $this->form_id, self::FIELD_EMAIL_CONSENT, $this->email_consent_field_name );
	}

	private function load_meta_data(): void {
		$this->is_form_enabled               = ! empty( get_post_meta( $this->form_id, self::FIELD_ENABLED, true ) );
		$this->is_send_welcome_email_enabled = ! empty( get_post_meta( $this->form_id, self::FIELD_SEND_WELCOME_EMAIL, true ) );
		$this->email_field_name              = (string) get_post_meta( $this->form_id, self::FIELD_EMAIL, true );
		$this->phone_field_name              = (string) get_post_meta( $this->form_id, self::FIELD_PHONE, true );
		$this->first_name_field_name         = (string) get_post_meta( $this->form_id, self::FIELD_FIRST_NAME, true );
		$this->birthday_field_name           = (string) get_post_meta( $this->form_id, self::FIELD_BIRTHDAY, true );
		$this->city_field_name               = (string) get_post_meta( $this->form_id, self::FIELD_CITY, true );
		$this->phone_consent_field_name      = (string) get_post_meta( $this->form_id, self::FIELD_PHONE_CONSENT, true );
		$this->postal_code_field_name        = (string) get_post_meta( $this->form_id, self::FIELD_POSTAL_CODE, true );
		$this->country_field_name            = (string) get_post_meta( $this->form_id, self::FIELD_COUNTRY, true );
		$this->state_field_name              = (string) get_post_meta( $this->form_id, self::FIELD_STATE, true );
		$this->address_field_name            = (string) get_post_meta( $this->form_id, self::FIELD_ADDRESS, true );
		$this->last_name_field_name          = (string) get_post_meta( $this->form_id, self::FIELD_LAST_NAME, true );
		$this->email_consent_field_name      = (string) get_post_meta( $this->form_id, self::FIELD_EMAIL_CONSENT, true );
	}
}
