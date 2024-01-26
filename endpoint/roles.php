<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

use UserAccessHub\Endpoint\Roles;

$roles = new Roles();
$roles->handleRequest();