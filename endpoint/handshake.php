<?php
/**
 * Custom Slug: Handshake
 *
 * @package useraccesshub
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use UserAccessHub\Endpoint\Handshake;

$handshake = new Handshake();
$handshake->handle_request();
