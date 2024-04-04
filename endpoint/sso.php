<?php
/**
 * Custom Slug: SSO
 *
 * @package useraccesshub
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use UserAccessHub\Endpoint\Sso;

$sso = new Sso();
$sso->handle_request();
