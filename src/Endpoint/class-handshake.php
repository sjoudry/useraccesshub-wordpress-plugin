<?php
/**
 * Endpoint: Handshake
 *
 * @package useraccesshub
 * @since 1.0.0
 */

namespace UserAccessHub\Endpoint;

use UserAccessHub\Options;
use UserAccessHub\Plugin;

/**
 * Handshake Endpoint class.
 *
 * @since 1.0.0
 */
class Handshake extends Base {

	/**
	 * Handle request.
	 *
	 * @since 1.0.0
	 */
	public function handle_request() {

		// Load settings.
		$authentication = get_option( Plugin::OPTIONS_AUTHENTICATION );

		// Only proceed if handshakes are enabled.
		if ( empty( Options::handshake_enabled() ) ) {
			$this->error_response_handshake();
		}

		if ( ! $this->validate_method( array( 'POST' ) ) ) {
			$this->error_response_method();
		}

		if ( ! $this->validate_api_key( Options::api_key() ) ) {
			$this->error_response_key();
		}

		$body   = wp_kses( file_get_contents( 'php://input' ), array() );
		$errors = $this->validate_body( $body, array( 'public_key', 'site_id' ) );
		if ( $errors ) {
			$this->error_response_body( $errors );
		}

		// Set config values.
		$body                                        = json_decode( $body );
		$authentication[ Plugin::OPTION_PUBLIC_KEY ] = $body->public_key;
		$authentication[ Plugin::OPTION_SITE_ID ]    = $body->site_id;
		$authentication[ Plugin::OPTION_HANDSHAKE_ENABLED ] = '';
		update_option( Plugin::OPTIONS_AUTHENTICATION, $authentication );

		$managed_roles = empty( Options::roles() ) ? array() : Options::roles();
		$this->response( array( 'roles' => $this->get_roles( $managed_roles ) ), 200 );
	}
}
