<?php
/**
 * Custom Slug: Packages
 *
 * @package useraccesshub
 * @since 1.0.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use UserAccessHub\Endpoint\Packages;

$packages = new Packages();
$packages->handle_request();
