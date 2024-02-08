<?php

namespace UserAccessHub\Endpoint;

use UserAccessHub\Plugin;

/**
 * SSO Endpoint class.
 *
 * @since 1.0
 */
class Sso extends Base {

  /**
   * Handle request.
   *
   * @since 1.0
   */
  public function handleRequest() {

    // Load settings.
    $authentication = get_option(Plugin::OPTIONS_AUTHENTICATION);
    $role_options = get_option(Plugin::OPTIONS_ROLES);
    $settings = get_option(Plugin::OPTIONS_SETTINGS);

    // Only proceed with the login if the plugin functionality is enabled.
    if (empty($settings[Plugin::OPTION_ENABLED])) {
      $this->errorResponseLogin();
    }

    if (!$this->validateMethod(['GET', 'POST'])) {
      $this->errorResponseMethod();
    }

    switch ($_SERVER['REQUEST_METHOD']) {

      // The initial request will hit this URL, which will redirect immediately
      // to the User Access Hub for user login/validation.
      case 'GET':
        header('Location: https://www.useraccesshub.com/sso/request?site=' . $authentication['useraccesshub_site_id'], 302);
        exit;

      // Once the user has logged into the User Access Hub and is validated, the
      // hub will post a signed message to the site, signalling that the user is
      // good to go.
      case 'POST':

        // Message is required.
        $message = $_POST['message'];
        if (!$message) {
          $this->errorResponseMessage();
        }

        // Message needs to be base64 decoded.
        $message = base64_decode($message);
        if (!$message) {
          $this->errorResponseMessage();
        }

        if ($errors = $this->validateBody($message, ['data', 'signature'])) {
          $this->errorResponseBody($errors);
        }

        $message = json_decode($message);
        $data = wp_json_encode($message->data);
        $properties = ['email', 'roles', 'created', 'expiry'];
        if ($errors = $this->validateBody($data, $properties)) {
          $this->errorResponseBody($errors);
        }

        $signature = base64_decode($message->signature);
        if (!$signature || !$this->validateSignature($data, $signature, $authentication[Plugin::OPTION_PUBLIC_KEY])) {
          $this->errorResponseSignature();
        }

        if (!$this->validateTimes($message->data)) {
          $this->errorResponseTimes();
        }

        // Load or create the user.
        $user = get_user_by('email', $message->data->email);
        if ($user) {

          // If local accounts are allowed, don't change anything about the
          // user (except adding the role below).
          if (empty($role_options[Plugin::OPTION_ALLOW_LOCAL])) {

            // Reset the roles of the user.
            $roles = $user->roles;
            foreach ($roles as $role) {
              $user->remove_role($role);
            }

            // Reset the password.
            $user->set('user_pass', Plugin::getInstance()->generateApiKey());
          }
        }
        else {
          $id = wp_create_user($message->data->email, Plugin::getInstance()->generateApiKey(), $message->data->email);
          $user = get_user_by('id', $id);
        }

        // Set the roles.
        foreach ($message->data->roles as $role) {
          if ($role == 0) {
            $role = $role_options[Plugin::OPTION_DEFAULT_ROLE];
          }
          $user->add_role($role);
        }

        // Login the user in.
        wp_update_user($user);
        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        // Redirect.
        $redirect = empty($settings[Plugin::OPTION_REDIRECT]) ? '/' : $settings[Plugin::OPTION_REDIRECT];
        header('Location: ' . $redirect);
        exit();
    }
  }

}
