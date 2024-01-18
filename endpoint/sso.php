<?php

include_once(__DIR__ . '/base.php');

// Prevent direct execution of this file.
if (!function_exists('add_filter')) {
	_useraccesshub_response([], 403);
}

// Load settings.
$authentication = get_option(UAH_OPTIONS_AUTHENTICATION);
$role_options = get_option(UAH_OPTIONS_ROLES);
$settings = get_option(UAH_OPTIONS_SETTINGS);

// Only proceed with the login if the plugin functionality is enabled.
if (empty($settings['useraccesshub_enabled'])) {
  _useraccesshub_error_response_login();
}

if (!_useraccesshub_validate_method(['GET', 'POST'])) {
  _useraccesshub_error_response_method();
}

switch ($_SERVER['REQUEST_METHOD']) {

  // The initial request will hit this URL, which will redirect immediately
  // to the User Access Hub for user login/validation.
  case 'GET':
    // header('Location: https://www.useraccesshub.com/sso/request?site=' . $authentication['useraccesshub_site_id'], 302);
    header('Location: http://drupaladmin.com/sso/request?site=' . $authentication['useraccesshub_site_id'], 302);
    exit;

  // Once the user has logged into the User Access Hub and is validated, the
  // hub will post a signed message to the site, signalling that the user is
  // good to go.
  case 'POST':

    // Message is required.
    $message = $_POST['message'];
    if (!$message) {
      _useraccesshub_error_response_message();
    }

    // Message needs to be base64 decoded.
    $message = base64_decode($message);
    if (!$message) {
      _useraccesshub_error_response_message();
    }

    if ($errors = _useraccesshub_validate_body($message, ['data', 'signature'])) {
      _useraccesshub_error_response_body($errors);
    }

    $message = json_decode($message);
    $data = json_encode($message->data);
    $properties = ['email', 'roles', 'created', 'expiry'];
    if ($errors = _useraccesshub_validate_body($data, $properties)) {
      _useraccesshub_error_response_body($errors);
    }

    $signature = base64_decode($message->signature);
    if (!$signature || !_useraccesshub_validate_signature($data, $signature, $authentication['useraccesshub_public_key'])) {
      _useraccesshub_error_response_signature();
    }

    if (!_useraccesshub_validate_times($message->data)) {
      _useraccesshub_error_response_times();
    }

    // // Load or create the user.
    $user = get_user_by('email', $message->data->email);
    if ($user) {

      // If local accounts are allowed, don't change anything about the
      // user (except adding the role below).
      if (empty($role_options['useraccesshub_allow_local'])) {

        // Reset the roles of the user.
        $roles = $user->roles;
        foreach ($roles as $role) {
          $user->remove_role($role);
        }

        // Reset the password.
        $user->set('user_pass', _useraccesshub_generate_api_key());
      }
    }
    else {
      $id = wp_create_user($message->data->email, _useraccesshub_generate_api_key(), $message->data->email);
      $user = get_user_by('id', $id);
    }

    // Set the roles.
    foreach ($message->data->roles as $role) {
      if ($role == 0) {
        $role = $role_options['useraccesshub_default_role'];
      }
      $user->add_role($role);
    }

    // Login the user in.
    wp_update_user($user);
    wp_clear_auth_cookie();
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);

    // Redirect.
    $redirect = empty($settings['useraccesshub_redirect']) ? '/' : $settings['useraccesshub_redirect'];
    header('Location: ' . $redirect);
    exit();
}
