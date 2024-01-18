<?php

include_once(__DIR__ . '/base.php');

// Prevent direct execution of this file.
if (!function_exists('add_filter')) {
	_useraccesshub_response([], 403);
}

// Load settings.
$authentication = get_option(UAH_OPTIONS_AUTHENTICATION);
$roles = get_option(UAH_OPTIONS_ROLES);

// Only proceed if handshakes are enabled.
if (empty($authentication['useraccesshub_handshake_enabled'])) {
  _useraccesshub_error_response_handshake();
}

if (!_useraccesshub_validate_method(['POST'])) {
  _useraccesshub_error_response_method();
}

if (!_useraccesshub_validate_api_key($authentication['useraccesshub_api_key'])) {
  _useraccesshub_error_response_key();
}

$body = file_get_contents('php://input');
if ($errors = _useraccesshub_validate_body($body, ['public_key', 'site_id'])) {
  _useraccesshub_error_response_body($errors);
}

// Set config values.
$body = json_decode($body);
$authentication['useraccesshub_public_key'] = $body->public_key;
$authentication['useraccesshub_site_id'] = $body->site_id;
$authentication['useraccesshub_handshake_enabled'] = '';
update_option(UAH_OPTIONS_AUTHENTICATION, $authentication);

$configured_roles = empty($roles['useraccesshub_roles']) ? [] : $roles['useraccesshub_roles'];
_useraccesshub_response(['roles' => _useraccesshub_get_roles($configured_roles)], 200);