<?php
/**
 * Activation
 *
 * @package useraccesshub
 * @since 1.0.0
 */

namespace UserAccessHub;

/**
 * Activation class.
 *
 * @since 1.0.0
 */
final class Activation {

	/**
	 * Activation.
	 *
	 * @since 1.0.0
	 */
	public function activation() {
		Plugin::get_instance()->install_rewrites();

		// Add some default options.
		$options = array(
			Plugin::OPTION_HANDSHAKE_ENABLED => 'on',
			Plugin::OPTION_API_KEY           => Plugin::get_instance()->generate_api_key(),
		);
		add_option( Plugin::OPTIONS_AUTHENTICATION, $options );
	}
}
