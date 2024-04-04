<?php
/**
 * Autoload file for plugin classes
 *
 * @package useraccesshub
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

spl_autoload_register(
	function ( $class_namespace ) {
		$class_parts     = explode( '\\', $class_namespace );
		$first_namespace = array_shift( $class_parts );
		if ( 'UserAccessHub' === $first_namespace ) {
			$last_classname = 'class-' . strtolower( array_pop( $class_parts ) );
			array_push( $class_parts, $last_classname );
			$src = implode( '/', $class_parts );
			include_once __DIR__ . '/src/' . $src . '.php';
		}
	}
);
