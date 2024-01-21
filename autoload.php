<?php

// Prevent direct execution of this file.
if (!function_exists('add_filter')) {
  header('Status: 403 Forbidden');
  header('HTTP/1.1 403 Forbidden');
	exit();
}

spl_autoload_register(function($class) {
  $class_parts = explode('\\', $class);
  $first_namespace = array_shift($class_parts);
  if ($first_namespace == 'UserAccessHub') {
    $src = implode('/', $class_parts);
    include_once __DIR__ . '/src/' . $src . '.php';
  }
});