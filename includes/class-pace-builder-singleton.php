<?php

/**
 * Generic Singleton class used by all PaceBuilder classes
 * @since      1.0.0
 * @package    PTPB
 * @subpackage PTPB/includes
 * @author     Pace Themes <dev@pacethemes.com>
 */

class PTPB_Singleton {

	/**
	 * Returns an instance of the PTPB_Singleton class, creates one if an instance doesn't exist. Implements Singleton pattern
	 *
	 * @return PTPB_Stage
	 */
	public static function instance() {

		static $instances = array();

		$called_class = get_called_class();

		if( ! $called_class ) {
			return;
		}

		if ( ! isset( $instances[ $called_class ] ) ) {
			$instances[ $called_class ] = new $called_class();
		}

		return $instances[ $called_class ];
	}
}

// get_called_class() is only in PHP >= 5.3.
if ( ! function_exists( 'get_called_class' ) ) {
	/**
	 * @return string
	 */
	function get_called_class() {
		$bt = debug_backtrace();
		$l  = 0;
		do {
			$l ++;
						
			if( $bt[ $l ]['function'] === 'instance' && $bt[ $l ]['class'] === 'PTPB_Singleton' && ! empty( $bt[ $l ]['args'] ) ) {
				return $bt[ $l ]['args'][0];
			}

			if( empty($bt[ $l ]['file']) ){
				return;
			}

			$lines      = file( $bt[ $l ]['file'] );
			$callerLine = $lines[ $bt[ $l ]['line'] - 1 ]; 

			if( preg_match( '/([a-zA-Z0-9\_]+)::' . $bt[ $l ]['function'] . '/', $callerLine, $matches ) ) {
				return $matches[1];
			}

		} while ( $l <= count( $bt ) );

		return ;
	}
}
