<?php

namespace UserAccessHub\Endpoint;

use UserAccessHub\Plugin;

/**
 * Roles Endpoint class.
 *
 * @since 1.0
 */
class Roles extends Base {

  /**
   * Handle request.
   *
   * @since 1.0
   */
  public function handleRequest() {

    // Load settings.
    $authentication = get_option(Plugin::OPTIONS_AUTHENTICATION);
    $roles = get_option(Plugin::OPTIONS_ROLES);

    if (!$this->validateMethod(['POST'])) {
      $this->errorResponseMethod();
    }

    if (empty($_SERVER['HTTP_SIGNATURE'])) {
      $this->errorResponseSignature();
    }

    $body = file_get_contents('php://input');
    $signature = base64_decode($_SERVER['HTTP_SIGNATURE']);
    if (!$signature || !$this->validateSignature($body, $signature, $authentication[Plugin::OPTION_PUBLIC_KEY])) {
      $this->errorResponseSignature();
    }

    $body = json_decode($body);
    if (!$this->validateTimes($body)) {
      $this->errorResponseTimes();
    }

    $managed_roles = empty($roles[Plugin::OPTION_ROLES]) ? [] : $roles[Plugin::OPTION_ROLES];
    $this->response(['roles' => $this->getRoles($managed_roles)], 200);
  }

}
