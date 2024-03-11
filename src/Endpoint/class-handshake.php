<?php
/**
 * Endpoint: Handshake
 *
 * @package useraccesshub
 * @since 1.0
 */

namespace UserAccessHub\Endpoint;

use UserAccessHub\Plugin;

/**
 * Handshake Endpoint class.
 *
 * @since 1.0
 */
class Handshake extends Base {

	/**
	 * Handle request.
	 *
	 * @since 1.0
	 */
	public function handle_request() {

		// Load settings.
		$authentication = get_option( Plugin::OPTIONS_AUTHENTICATION );
		$roles          = get_option( Plugin::OPTIONS_ROLES );

		// Only proceed if handshakes are enabled.
		if ( empty( $authentication[ Plugin::OPTION_HANDSHAKE_ENABLED ] ) ) {
			$this->error_response_handshake();
		}

		if ( ! $this->validate_method( array( 'POST' ) ) ) {
			$this->error_response_method();
		}

		if ( ! $this->validate_api_key( $authentication[ Plugin::OPTION_API_KEY ] ) ) {
			$this->error_response_key();
		}

		$body   = file_get_contents( 'php://input' );
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

		$managed_roles = empty( $roles[ Plugin::OPTION_ROLES ] ) ? array() : $roles[ Plugin::OPTION_ROLES ];
		$this->response( array( 'roles' => $this->get_roles( $managed_roles ) ), 200 );
	}
}