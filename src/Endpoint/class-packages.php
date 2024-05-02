<?php
/**
 * Endpoint: Packages
 *
 * @package useraccesshub
 * @since 1.0.2
 */

namespace UserAccessHub\Endpoint;

use UserAccessHub\Options;
use UserAccessHub\Plugin;

/**
 * Packages Endpoint class.
 *
 * @since 1.0.2
 */
class Packages extends Base {

	/**
	 * Handle request.
	 *
	 * @since 1.0.2
	 */
	public function handle_request() {
		if ( ! $this->validate_method( array( 'POST' ) ) ) {
			$this->error_response_method();
		}

		if ( empty( $_SERVER['HTTP_SIGNATURE'] ) ) {
			$this->error_response_signature();
		}

		$body      = wp_kses( file_get_contents( 'php://input' ), array() );
		$signature = base64_decode( wp_kses( wp_unslash( $_SERVER['HTTP_SIGNATURE'] ), array() ) ); // phpcs:ignore
		if ( ! $signature || ! $this->validate_signature( $body, $signature, Options::public_key() ) ) {
			$this->error_response_signature();
		}

		$body = json_decode( $body );
		if ( ! $this->validate_times( $body ) ) {
			$this->error_response_times();
		}

		global $wp_version;
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
		require_once ABSPATH . WPINC . '/theme.php';

		$output = array();

		// Core.
		$output[] = array(
			'name'    => 'wordpress',
			'type'    => 'core',
			'version' => $wp_version,
		);

		// Plugins.
		$plugins        = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );
		foreach ( $active_plugins as $active_plugin ) {
			$output[] = array(
				'name'    => dirname( $active_plugin ),
				'type'    => 'plugin',
				'version' => $plugins[ $active_plugin ]['Version'],
			);
		}

		// Theme.
		$theme    = wp_get_theme();
		$output[] = array(
			'name'    => $theme->get_stylesheet(),
			'type'    => 'theme',
			'version' => $theme->get( 'Version' ),
		);

		$this->response( $output, 200 );
	}
}
