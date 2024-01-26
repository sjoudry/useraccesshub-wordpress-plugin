<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

use UserAccessHub\Endpoint\Sso;

$sso = new Sso();
$sso->handleRequest();