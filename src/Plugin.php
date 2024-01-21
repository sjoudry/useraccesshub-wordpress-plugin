<?php

namespace UserAccessHub;

final class Plugin {

  const FILE = __DIR__ . '/../useraccesshub.php';

  const NAME = 'User Access Hub';

  const OPTION_ALLOW_LOCAL = 'useraccesshub_allow_local';

  const OPTION_API_KEY = 'useraccesshub_api_key';

  const OPTION_DEFAULT_ROLE = 'useraccesshub_default_role';

  const OPTION_ENABLED = 'useraccesshub_enabled';

  const OPTION_HANDSHAKE_ENABLED = 'useraccesshub_handshake_enabled';

  const OPTION_PUBLIC_KEY = 'useraccesshub_public_key';

  const OPTION_REDIRECT = 'useraccesshub_redirect';

  const OPTION_ROLES = 'useraccesshub_roles';

  const OPTION_SITE_ID = 'useraccesshub_site_id';

  const OPTIONS_CAPABILITY = 'manage_options';

  const OPTIONS_AUTHENTICATION = 'useraccesshub_authentication';

  const OPTIONS_AUTHENTICATION_NAME = 'Authentication';

  const OPTIONS_AUTHENTICATION_SLUG = 'useraccesshub-authentication';

  const OPTIONS_ROLES = 'useraccesshub_roles';

  const OPTIONS_ROLES_NAME = 'Roles';

  const OPTIONS_ROLES_SLUG = 'useraccesshub-roles';

  const OPTIONS_SETTINGS = 'useraccesshub_settings';

  const OPTIONS_SETTINGS_NAME = 'Settings';

  const OPTIONS_SETTINGS_SLUG = 'useraccesshub';

  const QUERY_ENDPOINT = 'uah_endpoint';

  const SLUG = 'useraccesshub';

  const VERSION = '1.0';

  private static ?Plugin $instance = NULL;

  public static function getInstance() {
    if (empty(self::$instance)) {
      self::$instance = new Plugin();
    }

    return self::$instance;
  }

  public function generateApiKey(int $length = 255, string $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz') {
    $key = '';
  
    $seed = str_split($chars);
    $max = count($seed) - 1;
  
    for ($i = 0; $i < $length; $i++) {
      $key .= $seed[random_int(0, $max)];
    }
  
    return $key;
  }

  public function init() {
    $this->registerActivationHook();
    $this->registerDeacticationHook();
    $this->registerActions();
    $this->registerFilters();
    $this->registerCommands();
  }

  private function registerActivationHook() {
    $activation = new Activation();
    register_activation_hook(Plugin::FILE, [$activation, 'activation']);
  }

  private function registerDeacticationHook() {
    $deactivation = new Deactivation();
    register_deactivation_hook(Plugin::FILE, [$deactivation, 'deactivation']);
  }

  private function registerActions() {
    $actions = new Actions();
    add_action('admin_init', [$actions, 'registerSettings']);
    add_action('admin_menu', [$actions, 'addSettingsPages']);
  }

  private function registerFilters() {
    $filters = new Filters();
    add_filter('plugin_action_links_' . PLUGIN::SLUG . '/' . PLUGIN::SLUG . '.php', [$filters, 'settingsLink']);
    add_filter('query_vars', [$filters, 'queryVars']);
    add_filter('template_include', [$filters, 'templateInclude']);
  }

  private function registerCommands() {
    // @todo
  }
}