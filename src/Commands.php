<?php

namespace UserAccessHub;

/**
 * Commands class.
 *
 * @since 1.0
 */
final class Commands {

  /**
   * Enable the handshake endpoint for User Access Hub.
   *
   * @since 1.0
   */
  public function enableHandshake() {
    $authentication = get_option(Plugin::OPTIONS_AUTHENTICATION);
    $authentication[Plugin::OPTION_HANDSHAKE_ENABLED] = 'on';
    update_option(Plugin::OPTIONS_AUTHENTICATION, $authentication);
    \WP_CLI::success('Handshake endpoint enabled successfully.');
  }

  /**
   * Disable the handshake endpoint for User Access Hub.
   *
   * @since 1.0
   */
  public function disableHandshake() {
    $authentication = get_option(Plugin::OPTIONS_AUTHENTICATION);
    $authentication[Plugin::OPTION_HANDSHAKE_ENABLED] = '';
    update_option(Plugin::OPTIONS_AUTHENTICATION, $authentication);
    \WP_CLI::success('Handshake endpoint disabled successfully.');
  }

  /**
   * Re-generate API Key used for the handshake endpoint for User Access Hub.
   *
   * @since 1.0
   */
  public function regenerateApiKey() {
    $authentication = get_option(Plugin::OPTIONS_AUTHENTICATION);
    $authentication[Plugin::OPTION_API_KEY] = Plugin::getInstance()->generateApiKey();
    update_option(Plugin::OPTIONS_AUTHENTICATION, $authentication);
    \WP_CLI::success('API key re-generated successfully.');
  }

}
