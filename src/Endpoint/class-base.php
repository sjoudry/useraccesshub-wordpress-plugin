<?php
/**
 * Endpoint: Base
 *
 * @package useraccesshub
 * @since 1.0.0
 */

namespace UserAccessHub\Endpoint;

/**
 * Base Endpoint class.
 *
 * @since 1.0.0
 */
class Base {

	/**
	 * Error response if the body is malformed.
	 *
	 * @param string[] $errors The list of errors to add to the response.
	 *
	 * @since 1.0.0
	 */
	protected function error_response_body( array $errors = array() ) {
		$message = array(
			'message' => 'The body of the request is required.',
			'errors'  => $errors,
		);
		$this->response( $message, 400 );
	}

	/**
	 * Handshake error response.
	 *
	 * @since 1.0.0
	 */
	protected function error_response_handshake() {
		$message = array(
			'error'   => 1,
			'message' => 'Handshake is forbidden.',
		);
		$this->response( $message, 200 );
	}

	/**
	 * API Key error response.
	 *
	 * @since 1.0.0
	 */
	protected function error_response_key() {
		$message = array(
			'message' => 'The API key is invalid.',
		);
		$this->response( $message, 401 );
	}

	/**
	 * Login error response.
	 *
	 * @since 1.0.0
	 */
	protected function error_response_login() {
		$message = array(
			'message' => 'Login functionality using this method is disabled.',
		);
		$this->response( $message, 403 );
	}

	/**
	 * Message error response.
	 *
	 * @since 1.0.0
	 */
	protected function error_response_message() {
		$message = array(
			'message' => 'The message POST variable is required and needs to be base 64 encoded.',
		);
		$this->response( $message, 405 );
	}

	/**
	 * HTTP method error response.
	 *
	 * @since 1.0.0
	 */
	protected function error_response_method() {
		$message = array(
			'message' => 'The method is not allowed.',
		);
		$this->response( $message, 405 );
	}

	/**
	 * Signature error response.
	 *
	 * @since 1.0.0
	 */
	protected function error_response_signature() {
		$message = array(
			'message' => 'The signature is invalid.',
		);
		$this->response( $message, 401 );
	}

	/**
	 * Time error response.
	 *
	 * @since 1.0.0
	 */
	protected function error_response_times() {
		$message = array(
			'message' => 'The login request has expired.',
		);
		$this->response( $message, 401 );
	}

	/**
	 * Get roles.
	 *
	 * @param array $managed_roles The roles that are configured for user access hub.
	 *
	 * @return array A list of role names, keyed by role id.
	 *
	 * @since 1.0.0
	 */
	protected function get_roles( array $managed_roles ) {
		global $wp_roles;

		// Return the roles of the site.
		$roles = array();
		foreach ( $wp_roles->roles as $role_id => $wp_role ) {
			if ( in_array( $role_id, $managed_roles, true ) ) {
				$roles[ $role_id ] = $wp_role['name'];
			}
		}

		return $roles;
	}

	/**
	 * Response.
	 *
	 * @param array $body      The body of the response.
	 * @param int   $http_code The response HTTP status code.
	 *
	 * @since 1.0.0
	 */
	protected function response( array $body, int $http_code ) {
		switch ( $http_code ) {
			case 200:
				header( 'Status: 200 Accepted' );
				header( 'HTTP/1.1 200 Accepted' );
				break;
			case 400:
				header( 'Status: 400 Bad Request' );
				header( 'HTTP/1.1 400 Bad Request' );
				break;
			case 401:
				header( 'Status: 401 Unauthorized' );
				header( 'HTTP/1.1 401 Unauthorized' );
				break;
			case 403:
				header( 'Status: 403 Forbidden' );
				header( 'HTTP/1.1 403 Forbidden' );
				break;
			case 405:
				header( 'Status: 405 Method Not Allowed' );
				header( 'HTTP/1.1 405 Method Not Allowed' );
				break;
		}

		if ( count( $body ) ) {
			header( 'Content-Type: application/json' );
			print wp_json_encode( $body );
		}

		exit();
	}

	/**
	 * Validate API key.
	 *
	 * @param string $api_key The site API key.
	 *
	 * @return bool TRUE if the apikey header value matches key and FALSE otherwise.
	 *
	 * @since 1.0.0
	 */
	protected function validate_api_key( string $api_key ) {
		if ( isset( $_SERVER['HTTP_APIKEY'] ) && $_SERVER['HTTP_APIKEY'] === $api_key ) {
			return true;
		}

		return false;
	}

	/**
	 * Validate request body.
	 *
	 * @param string $body       The body of the request.
	 * @param array  $properties The properties required for the body.
	 *
	 * @return string[] The array of errors.
	 *
	 * @since 1.0.0
	 */
	protected function validate_body( string $body = null, array $properties = array() ) {
		$errors = array();

		if ( empty( $body ) ) {
			$errors[] = 'Body is missing and is required.';
		} else {
			$json_body = json_decode( $body );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				$errors[] = 'Invalid JSON: ' . json_last_error_msg();
			}

			foreach ( $properties as $property ) {
				if ( ! isset( $json_body->{ $property } ) ) {
					$errors[] = "The '" . $property . "' property is missing";
				}
			}
		}

		return $errors;
	}

	/**
	 * Validate HTTP method.
	 *
	 * @param array $methods The accepted methods.
	 *
	 * @return bool TRUE if the http method matches the method's passed in and FALSE otherwise.
	 *
	 * @since 1.0.0
	 */
	protected function validate_method( array $methods ) {
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && in_array( $_SERVER['REQUEST_METHOD'], $methods, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Validate Signature.
	 *
	 * @param string $data       The data to validate.
	 * @param string $signature  The signature to validate.
	 * @param string $public_key The site's public key.
	 *
	 * @return bool TRUE if the signature header value matches key and FALSE otherwise.
	 *
	 * @since 1.0.0
	 */
	protected function validate_signature( string $data, string $signature, string $public_key ) {
		if ( openssl_verify( $data, $signature, $public_key, OPENSSL_ALGO_SHA384 ) === 1 ) {
			return true;
		}

		return false;
	}

	/**
	 * Validate times.
	 *
	 * @param object $data The message data.
	 *
	 * @return string[] The array of errors.
	 *
	 * @since 1.0.0
	 */
	protected function validate_times( object $data ) {

		// These dates will be passed in ISO 8601 date format.
		$created = strtotime( $data->created );
		$expiry  = strtotime( $data->expiry );

		// The request from the User Access Hub must have been created before now and must expire after now.
		$now = time();

		if ( $created <= $now && $expiry >= $now ) {
			return true;
		}

		return false;
	}
}
