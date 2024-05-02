<?php
/**
 * Actions
 *
 * @package useraccesshub
 * @since 1.0.0
 */

namespace UserAccessHub;

/**
 * Actions class.
 *
 * @since 1.0.0
 */
final class Actions {

	/**
	 * After Theme Switch.
	 *
	 * @since 1.0.2
	 */
	public function after_theme_switch() {
		Plugin::get_instance()->install_rewrites();
	}

	/**
	 * Login CSS.
	 *
	 * @since 1.0.0
	 */
	public function login_css() {
		$css_path = dirname( realpath( Plugin::FILE ) ) . '/includes/css/login.css';
		$css_url  = plugin_dir_url( realpath( Plugin::FILE ) ) . 'includes/css/login.css';
		wp_enqueue_style( 'useraccesshub', $css_url, array(), filemtime( $css_path ) );
	}

	/**
	 * Login Message.
	 *
	 * @param string|NULL $message The existing login message.
	 *
	 * @since 1.0.0
	 */
	public function login_footer( $message ) {
		if ( isset( $_GET['action'] ) ) { // phpcs:ignore
			if ( 'lostpassword' === $_GET['action'] ) { // phpcs:ignore
				print '<div class="useraccesshub_wrapper">';
				print '<div class="notice"><p>';
				print '<b>User Access Hub</b><br/>';
				print '<a href="https://www.useraccesshub.com/user/password">Reset password with User Access Hub</a>';
				print '</p></div>';
				print '</div>';
			}
		} else {
			print '<div class="useraccesshub_wrapper">';
			print '<div class="notice"><p>';
			print '<b>User Access Hub</b><br/>';
			print '<a href="https://www.useraccesshub.com/user/login">Login with User Access Hub</a>';
			print '</p></div>';
			print '</div>';
		}
	}
}
