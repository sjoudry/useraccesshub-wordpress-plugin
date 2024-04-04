<?php
/**
 * Deactivation
 *
 * @package useraccesshub
 * @since 1.0.0
 */

namespace UserAccessHub;

/**
 * Deactivation class.
 *
 * @since 1.0.0
 */
final class Deactivation {

	/**
	 * Dectivation.
	 *
	 * @since 1.0.0
	 */
	public function deactivation() {
		flush_rewrite_rules();

		// Remove options.
		delete_option( Plugin::OPTIONS_AUTHENTICATION );
		delete_option( Plugin::OPTIONS_ROLES );
		delete_option( Plugin::OPTIONS_SETTINGS );
	}
}
