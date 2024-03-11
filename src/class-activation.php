<?php
/**
 * Activation
 *
 * @package useraccesshub
 * @since 1.0
 */

namespace UserAccessHub;

/**
 * Activation class.
 *
 * @since 1.0
 */
final class Activation {

	/**
	 * Activation.
	 *
	 * @since 1.0
	 */
	public function activation() {

		// Handle routing for all custom endpoint URL's.
		add_rewrite_rule( '^spoke/api/([^/]*)', 'index.php?' . PLUGIN::QUERY_ENDPOINT . '=$matches[1]', 'top' );
		flush_rewrite_rules();

		// Add some default options.
		$options = array(
			Plugin::OPTION_HANDSHAKE_ENABLED => 'on',
			Plugin::OPTION_API_KEY           => Plugin::get_instance()->generate_api_key(),
		);
		add_option( Plugin::OPTIONS_AUTHENTICATION, $options );
	}
}
