<?php
/**
 * Endpoint: Roles
 *
 * @package useraccesshub
 * @since 1.0.0
 */

namespace UserAccessHub\Endpoint;

use UserAccessHub\Plugin;

/**
 * Roles Endpoint class.
 *
 * @since 1.0.0
 */
class Roles extends Base {

	/**
	 * Handle request.
	 *
	 * @since 1.0.0
	 */
	public function handle_request() {

		// Load settings.
		$authentication = get_option( Plugin::OPTIONS_AUTHENTICATION );
		$roles          = get_option( Plugin::OPTIONS_ROLES );

		if ( ! $this->validate_method( array( 'POST' ) ) ) {
			$this->error_response_method();
		}

		if ( empty( $_SERVER['HTTP_SIGNATURE'] ) ) {
			$this->error_response_signature();
		}

		$body      = wp_kses( file_get_contents( 'php://input' ), array() );
		$signature = base64_decode( wp_kses( wp_unslash( $_SERVER['HTTP_SIGNATURE'] ), array() ) );
		if ( ! $signature || ! $this->validate_signature( $body, $signature, $authentication[ Plugin::OPTION_PUBLIC_KEY ] ) ) {
			$this->error_response_signature();
		}

		$body = json_decode( $body );
		if ( ! $this->validate_times( $body ) ) {
			$this->error_response_times();
		}

		$managed_roles = empty( $roles[ Plugin::OPTION_ROLES ] ) ? array() : $roles[ Plugin::OPTION_ROLES ];
		$this->response( array( 'roles' => $this->get_roles( $managed_roles ) ), 200 );
	}
}
