<?php
/**
 * Omnisend Contact form 7 plugin
 *
 * @package OmnisendContactFrom7Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPCF7_Omnisend_Contact {


	private ?string $first_name       = null;
	private ?string $last_name        = null;
	private ?string $email            = null;
	private ?string $address          = null;
	private ?string $city             = null;
	private ?string $state            = null;
	private ?string $country          = null;
	private ?string $postal_code      = null;
	private ?string $phone            = null;
	private ?string $birthday         = null;
	private ?bool $send_welcome_email = null;

	private array $tags              = array( 'Contact Form 7' );
	private ?string $email_consent   = null;
	private ?string $phone_consent   = null;
	private array $custom_properties = array();

	public function is_valid(): bool {
		return $this->email != '';
	}

	public function to_array(): array {
		if ( ! $this->is_valid() ) {
			return array();
		}

		$time_now = gmdate( 'c' );

		$email_identifier = array(
			'type'     => 'email',
			'id'       => $this->email,
			'channels' => array(
				'email' => array(
					'status'     => $this->email_consent ? 'subscribed' : 'nonSubscribed',
					'statusDate' => $time_now,
				),
			),
		);
		if ( $this->email_consent ) {
			$email_identifier['consent'] = array(
				'source'    => $this->email_consent,
				'createdAt' => $time_now,
			);
		}

		$arr = array(
			'identifiers' => array(
				$email_identifier,
			),
			'tags'        => $this->tags,
		);

		if ( $this->custom_properties ) {
			$arr['customProperties'] = $this->custom_properties;
		}

		if ( $this->phone ) {
			$phone_identifier = array(
				'type'     => 'phone',
				'id'       => $this->phone,
				'channels' => array(
					'sms' => array(
						'status'     => $this->phone_consent ? 'subscribed' : 'nonSubscribed',
						'statusDate' => $time_now,
					),
				),
			);
			if ( $this->phone_consent ) {
				$phone_identifier['consent'] = array(
					'source'    => $this->phone_consent,
					'createdAt' => $time_now,
				);
			}
			$arr['identifiers'][] = $phone_identifier;
		}

		if ( $this->first_name ) {
			$arr['firstName'] = $this->first_name;
		}

		if ( $this->last_name ) {
			$arr['lastName'] = $this->last_name;
		}

		if ( $this->address ) {
			$arr['address'] = $this->address;
		}

		if ( $this->city ) {
			$arr['city'] = $this->city;
		}

		if ( $this->state ) {
			$arr['state'] = $this->state;
		}

		if ( $this->country ) {
			$arr['country'] = $this->country;
		}

		if ( $this->postal_code ) {
			$arr['postalCode'] = $this->postal_code;
		}

		if ( $this->birthday ) {
			$arr['birthdate'] = $this->birthday;
		}

		if ( $this->send_welcome_email ) {
			$arr['sendWelcomeEmail'] = $this->send_welcome_email;
		}

		return $arr;
	}

	public function set_email( $email ): void {
		if ( $email && is_string( $email ) ) {
			$this->email = $email;
		}
	}

	public function set_send_welcome_email( $send_welcome_email ): void {
		$this->send_welcome_email = (bool) $send_welcome_email;
	}

	public function set_first_name( $first_name ): void {
		if ( $first_name && is_string( $first_name ) ) {
			$this->first_name = $first_name;
		}
	}

	public function set_last_name( $last_name ): void {
		if ( $last_name && is_string( $last_name ) ) {
			$this->last_name = $last_name;
		}
	}

	public function set_address( $address ): void {
		if ( $address && is_string( $address ) ) {
			$this->address = $address;
		}
	}

	public function set_city( $city ): void {
		if ( $city && is_string( $city ) ) {
			$this->city = $city;
		}
	}

	public function set_state( $state ): void {
		if ( $state && is_string( $state ) ) {
			$this->state = $state;
		}
	}

	public function set_country( $country ): void {
		if ( $country && is_string( $country ) ) {
			$this->country = $country;
		}
	}

	public function set_postal_code( $postal_code ): void {
		if ( $postal_code && is_string( $postal_code ) ) {
			$this->postal_code = $postal_code;
		}
	}

	public function set_phone( $phone ): void {
		if ( $phone && is_string( $phone ) ) {
			$this->phone = $phone;
		}
	}

	public function set_birthday( $birthday ): void {
		if ( $birthday && is_string( $birthday ) ) {
			$this->birthday = $birthday;
		}
	}

	public function set_email_consent( $consent_text ): void {
		if ( $consent_text && is_string( $consent_text ) ) {
			$this->email_consent = $consent_text;
		}
	}

	public function set_phone_consent( $consent_text ): void {
		if ( $consent_text && is_string( $consent_text ) ) {
			$this->phone_consent = $consent_text;
		}
	}


	public function add_custom_property( $key, $value ): bool {
		if ( ! WPCF7_Omnisend_Utils::is_valid_custom_property_ame( $key ) ) {
			return false;
		}

		$this->custom_properties[ $key ] = $value;
		return true;
	}

	public function add_tag( $tag ): bool {
		if ( ! WPCF7_Omnisend_Utils::is_valid_tag( $tag ) ) {
			return false;
		}

		$this->tags[] = $tag;
		return true;
	}
}
