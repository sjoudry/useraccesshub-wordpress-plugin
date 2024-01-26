<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

spl_autoload_register(function($class) {
  $class_parts = explode('\\', $class);
  $first_namespace = array_shift($class_parts);
  if ($first_namespace == 'UserAccessHub') {
    $src = implode('/', $class_parts);
    include_once __DIR__ . '/src/' . $src . '.php';
  }
});