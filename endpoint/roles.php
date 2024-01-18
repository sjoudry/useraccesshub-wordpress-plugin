<?php

include_once(__DIR__ . '/base.php');

// Prevent direct execution of this file.
if (!function_exists('add_filter')) {
	_useraccesshub_response([], 403);
}

// Load settings.
$authentication = get_option(UAH_OPTIONS_AUTHENTICATION);
$roles = get_option(UAH_OPTIONS_ROLES);

if (!_useraccesshub_validate_method(['POST'])) {
  _useraccesshub_error_response_method();
}

if (empty($_SERVER['HTTP_SIGNATURE'])) {
  _useraccesshub_error_response_signature();
}

$body = file_get_contents('php://input');
$signature = base64_decode($_SERVER['HTTP_SIGNATURE']);
if (!$signature || !_useraccesshub_validate_signature($body, $signature, $authentication['useraccesshub_public_key'])) {
  _useraccesshub_error_response_signature();
}

$body = json_decode($body);
if (!_useraccesshub_validate_times($body)) {
  _useraccesshub_error_response_times();
}

$configured_roles = empty($roles['useraccesshub_roles']) ? [] : $roles['useraccesshub_roles'];
_useraccesshub_response(['roles' => _useraccesshub_get_roles($configured_roles)], 200);