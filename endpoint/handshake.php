<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

use UserAccessHub\Endpoint\Handshake;

$handshake = new Handshake();
$handshake->handleRequest();