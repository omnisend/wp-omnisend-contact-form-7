<?php
/**
 * Omnisend Contact form 7 plugin
 *
 * @package OmnisendContactFrom7Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var $post WPCF7_ContactForm */

$form_id        = $post->id();
$form_meta_data = new WPCF7_Omnisend_Form_Meta_Data( $form_id, true );

$form_validation_error = $form_meta_data->is_form_enabled() === true && $form_meta_data->get_email_field_name() === '---';

$email_type_tags           = $post->scan_form_tags( array( 'basetype' => 'email' ) );
$text_type_tags            = $post->scan_form_tags( array( 'basetype' => 'text' ) );
$tel_type_tags             = $post->scan_form_tags( array( 'basetype' => 'tel' ) );
$date_type_tags            = $post->scan_form_tags( array( 'basetype' => 'date' ) );
$single_checkbox_type_tags = $post->scan_form_tags( array( 'basetype' => 'acceptance' ) );
$all_tags                  = $post->scan_form_tags();
?>

<style>
	<?php require plugin_dir_path( __FILE__ ) . '../styles/styles.css'; ?><?php require plugin_dir_path( __FILE__ ) . '../fonts/fonts.css'; ?>
</style>

<div class="omnisend-h4 omnisend-margin-bottom-16">Omnisend form settings</div>

<label class="omnisend-checkbox-wrapper omnisend-margin-bottom-40" for="cf7_omnisend_form_enabled">
	<input class="omnisend-checkbox " id="cf7_omnisend_form_enabled" type="checkbox" value="enabled" name="wpcf7-omnisend[enabled]" <?php checked( $form_meta_data->is_form_enabled() ? 'true' : '', 'true', true ); ?> />
	<div class="omnisend-checkbox-text-wrapper">
		<div class="omnisend-checkbox-label">Send form data to Omnisend</div>
		<div class="omnisend-checkbox-description">
			Check this to see all data collected through your form in Omnisend
		</div>
	</div>
</label>

<div id="mapping_content" style="<?php echo $form_meta_data->is_form_enabled() ? 'display: block;' : 'display: none;'; ?>">

	<div class="omnisend-lead-strong omnisend-margin-bottom-8">Welcome email</div>
	<div class="omnisend-content-body omnisend-margin-bottom-24">
		Check this to automatically send your custom welcome email, created in Omnisend, to subscribers joining through Contact Form 7.
	</div>


	<label class="omnisend-checkbox-wrapper omnisend-margin-bottom-40" for="cf7_omnisend_send_welcome_email">
		<input class="omnisend-checkbox " id="cf7_omnisend_send_welcome_email" type="checkbox" value="enabled" name="wpcf7-omnisend[send_welcome_email]" <?php checked( $form_meta_data->is_send_welcome_email_enabled() ? 'true' : '', 'true', true ); ?> />
		<div class="omnisend-checkbox-text-wrapper">
			<div class="omnisend-checkbox-label">Send a welcome email to new subscribers</div>
			<div class="omnisend-checkbox-description omnisend-margin-bottom-8">
				After checking this, don’t forget to design your welcome email in Omnisend.
			</div>
			<div>
				<a class="omnisend-link" href="<?php echo esc_url( WPCP7_OMNISEND_WELCOME_AUTOMATION_ARTICLE_LINK ); ?>" target="_blank">Learn more about Welcome automation</a>
			</div>
		</div>
	</label>

	<div class="omnisend-lead-strong omnisend-margin-bottom-8">Field mapping</div>
	<div class="omnisend-content-body omnisend-margin-bottom-24">Field mapping lets you align your Contact Form 7 fields with Omnisend. It's important to match them correctly, so the information collected through Contact Form 7 goes into the right place in Omnisend.
	</div>

	<div class="omnisend-notice omnisend-margin-bottom-24">
		<img class="omnisend-notice-icon" alt="notice" src="<?php echo esc_html( plugin_dir_url( __FILE__ ) . '../img/notice.svg' ); ?>" />
		<div class="omnisend-notice-text-wrapper">
			<div class="omnisend-notice-text omnisend-content-body">
				If you have made changes in the Form tab and don't see them here, make sure you have saved those changes.
			</div>
			<a class="omnisend-link" href="<?php echo esc_url( WPCP7_OMNISEND_SUPPORT_ARTICLE_LINK ); ?>" target="_blank">Learn more how to set up everything</a>
		</div>
	</div>

	<div class="omnisend-notice omnisend-danger-notice omnisend-margin-bottom-24" style="<?php echo $form_validation_error ? 'display: flex;' : 'display: none;'; ?>">
		<img class="omnisend-notice-icon" alt="notice" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . '../img/notice.svg' ); ?>" />
		<div class="omnisend-notice-text omnisend-content-body">
			To activate Omnisend, please map the 'Email' field.
			This is required to create contact from your form submissions.
		</div>
	</div>

	<div class="omnisend-input-label">Email</div>
	<select class="omnisend-select" name="wpcf7-omnisend[email]">
		<option>---</option>
		<?php
		foreach ( $email_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_email_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>

	<div class="omnisend-input-label">Address</div>
	<select class="omnisend-select" name="wpcf7-omnisend[address]">
		<option>---</option>
		<?php
		foreach ( $text_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_address_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>

	<div class="omnisend-input-label">City</div>
	<select class="omnisend-select" name="wpcf7-omnisend[city]">
		<option>---</option>
		<?php
		foreach ( $text_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_city_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>

	<div class="omnisend-input-label">State</div>
	<select class="omnisend-select" name="wpcf7-omnisend[state]">
		<option>---</option>
		<?php
		foreach ( $text_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_state_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>

	<div class="omnisend-input-label">Country</div>
	<select class="omnisend-select" name="wpcf7-omnisend[country]">
		<option>---</option>
		<?php
		foreach ( $text_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_country_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>

	<div class="omnisend-input-label">Postal Code</div>
	<select class="omnisend-select" name="wpcf7-omnisend[postal_code]">
		<option>---</option>
		<?php
		foreach ( $text_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_postal_code_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>

	<div class="omnisend-input-label">First name</div>
	<select class="omnisend-select" name="wpcf7-omnisend[first_name]">
		<option>---</option>
		<?php
		foreach ( $text_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_first_name_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>

	<div class="omnisend-input-label">Last name</div>
	<select class="omnisend-select" name="wpcf7-omnisend[last_name]">
		<option>---</option>
		<?php
		foreach ( $text_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_last_name_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>

	<div class="omnisend-input-label">Phone Number</div>
	<select class="omnisend-select" name="wpcf7-omnisend[phone]">
		<option>---</option>
		<?php
		foreach ( $tel_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_phone_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>

	<div class="omnisend-input-label">Birthday</div>
	<select class="omnisend-select" name="wpcf7-omnisend[birthday]">
		<option>---</option>
		<?php
		foreach ( $date_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_birthday_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>


	<div class="omnisend-input-label">Email consent</div>
	<select class="omnisend-select" name="wpcf7-omnisend[consent_email]">
		<option>---</option>
		<?php
		foreach ( $single_checkbox_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_email_consent_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>

	<div class="omnisend-input-label">Phone consent</div>
	<select class="omnisend-select" name="wpcf7-omnisend[consent_phone]">
		<option>---</option>
		<?php
		foreach ( $single_checkbox_type_tags as $input_tage ) {
			echo '<option value="' . esc_attr( $input_tage['name'] ) . '" '
				. selected( $form_meta_data->get_phone_consent_field_name(), $input_tage['name'] ) . '>'
				. esc_attr( $input_tage['name'] ) . '</option>';
		}
		?>
	</select>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		var form_enabled_checkbox = document.getElementById('cf7_omnisend_form_enabled');
		var mappingContent = document.getElementById('mapping_content');

		form_enabled_checkbox.addEventListener('change', function() {
			mappingContent.style.display = this.checked ? 'block' : 'none';
		});
	});
</script>
