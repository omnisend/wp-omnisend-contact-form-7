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

	public static function debug( $arg ) {
		$bt     = debug_backtrace(); // phpcs:ignore
		$caller = array_shift( $bt );

		error_log( print_r( $caller['file'] . ':' . $caller['line'] . "\n" . print_r( $arg, 1 ), 1 ) . "\n", 0 ); // phpcs:ignore
	}
}
