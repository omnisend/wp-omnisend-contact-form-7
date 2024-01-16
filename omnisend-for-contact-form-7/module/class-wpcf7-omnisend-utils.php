<?php
/**
 * Omnisend Contact form 7 plugin
 *
 * @package OmnisendContactFrom7Plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPCF7_Omnisend_Utils {

	public static function is_valid_custom_property_ame( $name ): bool {
		return preg_match( '/^[a-zA-Z0-9_]{1,128}$/', $name );
	}

	public static function is_valid_tag( $tag ): bool {
		return preg_match( '/^[a-zA-Z0-9_\- ]{1,128}$/', $tag );
	}

	public static function clean_up_tag( $tag ): string {
		$tag = preg_replace( '/[^A-Za-z0-9_\- ]/', '', $tag );
		return mb_strimwidth( $tag, 0, 128 );
	}

	public static function debug( $arg ) {
		$bt     = debug_backtrace(); // phpcs:ignore
		$caller = array_shift( $bt );

		error_log( print_r( $caller['file'] . ':' . $caller['line'] . "\n" . print_r( $arg, 1 ), 1 ) . "\n", 0 ); // phpcs:ignore
	}
}
