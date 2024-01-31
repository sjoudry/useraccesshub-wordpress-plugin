<?php
/**
 * Plugin Name:  User Access Hub
 * Plugin URI:   https://www.useraccesshub.com
 * Description:  Plugin to allow user authentication through the User Access Hub.
 * Version:      1.0
 * Author:       Scott Joudry
 * Author URI:   https://profiles.wordpress.org/sjoudry/
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 7
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

include_once __DIR__ . '/autoload.php';

use UserAccessHub\Plugin;

$plugin = Plugin::getInstance();
$plugin->init();
