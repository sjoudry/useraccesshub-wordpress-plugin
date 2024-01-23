<?php

namespace UserAccessHub;

/**
 * Plugin class.
 *
 * @since 1.0
 */
final class Plugin {

  /**
   * The path to the plugin file.
   *
   * @var string
   *
   * @since 1.0
   */
  const FILE = __DIR__ . '/../useraccesshub.php';

  /**
   * The name of the plugin
   *
   * @var string
   *
   * @since 1.0
   */
  const NAME = 'User Access Hub';

  /**
   * Allow Local option name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTION_ALLOW_LOCAL = 'useraccesshub_allow_local';

  /**
   * API Key option name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTION_API_KEY = 'useraccesshub_api_key';

  /**
   * Default Role option name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTION_DEFAULT_ROLE = 'useraccesshub_default_role';

  /**
   * Enabled option name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTION_ENABLED = 'useraccesshub_enabled';

  /**
   * Handshake Enabled option name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTION_HANDSHAKE_ENABLED = 'useraccesshub_handshake_enabled';

  /**
   * Public Key option name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTION_PUBLIC_KEY = 'useraccesshub_public_key';

  /**
   * Redirect option name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTION_REDIRECT = 'useraccesshub_redirect';

  /**
   * Roles option name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTION_ROLES = 'useraccesshub_roles';

  /**
   * Site ID option name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTION_SITE_ID = 'useraccesshub_site_id';

  /**
   * Capability name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTIONS_CAPABILITY = 'manage_options';

  /**
   * Authentication options key.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTIONS_AUTHENTICATION = 'useraccesshub_authentication';

  /**
   * Authentication options name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTIONS_AUTHENTICATION_NAME = 'Authentication';

  /**
   * Authentication options slug.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTIONS_AUTHENTICATION_SLUG = 'useraccesshub-authentication';

  /**
   * Roles options key.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTIONS_ROLES = 'useraccesshub_roles';

  /**
   * Roles options name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTIONS_ROLES_NAME = 'Roles';

  /**
   * Roles options slug.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTIONS_ROLES_SLUG = 'useraccesshub-roles';

  /**
   * Settings option key.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTIONS_SETTINGS = 'useraccesshub_settings';

  /**
   * Settings options name.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTIONS_SETTINGS_NAME = 'Settings';

  /**
   * Settings options slug.
   *
   * @var string
   *
   * @since 1.0
   */
  const OPTIONS_SETTINGS_SLUG = 'useraccesshub';

  /**
   * Endpoint query var name.
   *
   * @var string
   *
   * @since 1.0
   */
  const QUERY_ENDPOINT = 'uah_endpoint';

  /**
   * The slug of the plugin.
   *
   * @var string
   *
   * @since 1.0
   */
  const SLUG = 'useraccesshub';

  /**
   * The version of the plugin.
   *
   * @var string
   *
   * @since 1.0
   */
  const VERSION = '1.0';

  /**
   * The Plugin singleton.
   *
   * @var \UserAccessHub\Plugin
   *
   * @since 1.0
   */
  private static ?Plugin $instance = NULL;

  /**
   * Get Plugin Instance.
   *
   * @since 1.0
   */
  public static function getInstance() {
    if (empty(self::$instance)) {
      self::$instance = new Plugin();
    }

    return self::$instance;
  }

  /**
   * Generate API Key.
   *
   * @param int $length
   *   The length of the generated key.
   * @param string $chars
   *   The characters to include in the generated key.
   *
   * @return string
   *   The generated key.
   *
   * @since 1.0
   */
  public function generateApiKey(int $length = 255, string $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz') {
    $key = '';
  
    $seed = str_split($chars);
    $max = count($seed) - 1;
  
    for ($i = 0; $i < $length; $i++) {
      $key .= $seed[random_int(0, $max)];
    }
  
    return $key;
  }

  /**
   * Initialize.
   *
   * Run all of the hooks, actions and filters to fully set up the plugin.
   *
   * @since 1.0
   */
  public function init() {
    $this->registerActivationHook();
    $this->registerDeacticationHook();
    $this->registerActions();
    $this->registerFilters();
    $this->registerCommands();
  }

  /**
   * Register Activation Hook.
   *
   * @since 1.0
   */
  private function registerActivationHook() {
    $activation = new Activation();
    register_activation_hook(Plugin::FILE, [$activation, 'activation']);
  }

  /**
   * Register Deactivation Hook.
   *
   * @since 1.0
   */
  private function registerDeacticationHook() {
    $deactivation = new Deactivation();
    register_deactivation_hook(Plugin::FILE, [$deactivation, 'deactivation']);
  }

  /**
   * Register Actions.
   *
   * @since 1.0
   */
  private function registerActions() {
    $actions = new Actions();
    add_action('admin_init', [$actions, 'registerSettings']);
    add_action('admin_menu', [$actions, 'addSettingsPages']);
    add_action('authenticate', [$actions, 'handleLogin'], 20, 3);
    add_action('login_enqueue_scripts', [$actions, 'loginCss']);
    add_action('login_footer', [$actions, 'loginFooter']);
  }

  /**
   * Register Filters.
   *
   * @since 1.0
   */
  private function registerFilters() {
    $filters = new Filters();
    add_filter('plugin_action_links_' . PLUGIN::SLUG . '/' . PLUGIN::SLUG . '.php', [$filters, 'settingsLink']);
    add_filter('query_vars', [$filters, 'queryVars']);
    add_filter('template_include', [$filters, 'templateInclude']);
    add_filter('lostpassword_user_data', [$filters, 'handleReset'], 10, 2);
  }

  /**
   * Register WP CLI Commands.
   *
   * @since 1.0
   */
  private function registerCommands() {
    if (php_sapi_name() == 'cli') {
      $commands = new Commands();
      \WP_CLI::add_command('enable-handshake', [$commands, 'enableHandshake']);
      \WP_CLI::add_command('disable-handshake', [$commands, 'disableHandshake']);
      \WP_CLI::add_command('regenerate-api-key', [$commands, 'regenerateApiKey']);
    }
  }

}
