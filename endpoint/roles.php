<?php
/**
 * Custom Slug: Roles
 *
 * @package useraccesshub
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use UserAccessHub\Endpoint\Roles;

$roles = new Roles();
$roles->handle_request();
