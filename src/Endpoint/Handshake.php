<?php

namespace UserAccessHub\Endpoint;

class Handshake extends Base {

  public function handleRequest() {

    // Load settings.
    $authentication = get_option(Plugin::OPTIONS_AUTHENTICATION);
    $roles = get_option(Plugin::OPTIONS_ROLES);

    // Only proceed if handshakes are enabled.
    if (empty($authentication[Plugin::OPTION_HANDSHAKE_ENABLED])) {
      $this->errorResponseHandshake();
    }

    if (!$this->validateMethod(['POST'])) {
      $this->errorResponseMethod();
    }

    if (!$this->validateApiKey($authentication[Plugin::OPTION_API_KEY])) {
      $this->errorResponseKey();
    }

    $body = file_get_contents('php://input');
    if ($errors = $this->validateBody($body, ['public_key', 'site_id'])) {
      $this->errorResponseBody($errors);
    }

    // Set config values.
    $body = json_decode($body);
    $authentication[Plugin::OPTION_PUBLIC_KEY] = $body->public_key;
    $authentication[Plugin::OPTION_SITE_ID] = $body->site_id;
    $authentication[Plugin::OPTION_HANDSHAKE_ENABLED] = '';
    update_option(Plugin::OPTIONS_AUTHENTICATION, $authentication);

    $configured_roles = empty($roles[Plugin::OPTION_ROLES]) ? [] : $roles[Plugin::OPTION_ROLES];
    $this->response(['roles' => $this->getRoles($configured_roles)], 200);
  }

}
