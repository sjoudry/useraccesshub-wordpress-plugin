<?php
/**
 * Endpoint: SSO
 *
 * @package useraccesshub
 * @since 1.0.0
 */

namespace UserAccessHub\Endpoint;

use UserAccessHub\Options;
use UserAccessHub\Plugin;

/**
 * SSO Endpoint class.
 *
 * @since 1.0.0
 */
class Sso extends Base {

	/**
	 * Handle request.
	 *
	 * @since 1.0.0
	 */
	public function handle_request() {

		// Only proceed with the login if the plugin functionality is enabled.
		if ( empty( Options::enabled() ) ) {
			$this->error_response_login();
		}

		if ( ! $this->validate_method( array( 'GET', 'POST' ) ) ) {
			$this->error_response_method();
		}

		if ( isset( $_SERVER['REQUEST_METHOD'] ) ) {
			switch ( $_SERVER['REQUEST_METHOD'] ) {

				// The initial request will hit this URL, which will redirect immediately to the User Access Hub for user login/validation.
				case 'GET':
					header( 'Location: https://www.useraccesshub.com/sso/request?site=' . $authentication['useraccesshub_site_id'], 302 );
					exit;

				// Once the user has logged into the User Access Hub and is validated, the hub will post a signed message to the site, signalling that the user is good to go.
				case 'POST':
					if ( isset( $_POST['message'] ) ) { // phpcs:ignore

						// Message is required.
						$message = wp_kses( wp_unslash( $_POST['message'] ), array() ); // phpcs:ignore
						if ( ! $message ) {
							$this->error_response_message();
						}

						// Message needs to be base64 decoded.
						$message = base64_decode( $message ); // phpcs:ignore
						if ( ! $message ) {
							$this->error_response_message();
						}

						$errors = $this->validate_body( $message, array( 'data', 'signature' ) );
						if ( $errors ) {
							$this->error_response_body( $errors );
						}

						$message    = json_decode( $message );
						$data       = wp_json_encode( $message->data );
						$properties = array( 'email', 'roles', 'created', 'expiry' );
						$errors     = $this->validate_body( $data, $properties );
						if ( $errors ) {
							$this->error_response_body( $errors );
						}

						$signature = base64_decode( $message->signature ); // phpcs:ignore
						if ( ! $signature || ! $this->validate_signature( $data, $signature, Options::public_key() ) ) {
							$this->error_response_signature();
						}

						if ( ! $this->validate_times( $message->data ) ) {
							$this->error_response_times();
						}

						// Load or create the user.
						$user = get_user_by( 'email', $message->data->email );
						if ( $user ) {

							// If local accounts are allowed, don't change anything about the user (except adding the role below).
							if ( empty( Options::allow_local() ) ) {

								// Reset the roles of the user.
								$roles = $user->roles;
								foreach ( $roles as $role ) {
									$user->remove_role( $role );
								}

								// Reset the password.
								$user->set( 'user_pass', Plugin::get_instance()->generate_api_key() );
							}
						} else {
							$id   = wp_create_user( $message->data->email, Plugin::get_instance()->generate_api_key(), $message->data->email );
							$user = get_user_by( 'id', $id );
						}

						// Set the roles.
						foreach ( $message->data->roles as $role ) {
							if ( 0 === $role ) {
								$role = Options::default_role();
							}
							$user->add_role( $role );
						}

						// Login the user in.
						wp_update_user( $user );
						wp_clear_auth_cookie();
						wp_set_current_user( $user->ID );
						wp_set_auth_cookie( $user->ID );

						// Redirect.
						$redirect = empty( Options::redirect() ) ? '/' : Options::redirect();
						header( 'Location: ' . $redirect );
						exit();
					}
			}
		}
	}
}
