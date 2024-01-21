<?php
/**
 * Plugin Name:  User Access Hub
 * Plugin URI:   https://www.useraccesshub.com
 * Description:  Plugin to allow user authentication through the User Access Hub.
 * Version:      1.0
 * Author:       User Access Hub
 * Author URI:   https://www.useraccesshub.com
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

// Prevent direct execution of this file.
if (!function_exists('add_filter')) {
  header('Status: 403 Forbidden');
  header('HTTP/1.1 403 Forbidden');
	exit();
}

include_once __DIR__ . '/autoload.php';

use UserAccessHub\Plugin;

$plugin = Plugin::getInstance();
$plugin->init();
