<?php
/**
 * Plugin Name:  User Access Hub
 * Plugin URI:   https://www.useraccesshub.com
 * Description:  Plugin to allow user authentication through the User Access Hub.
 * Version:      1.0
 * Author:       User Access Hub
 * Author URI:   https://www.useraccesshub.com
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

// Prevent direct execution of this file.
if (!function_exists('add_filter')) {
	_useraccesshub_response([], 403);
}



// Constants.
define('UAH_OPTIONS_AUTHENTICATION', 'useraccesshub_authentication');
define('UAH_OPTIONS_AUTHENTICATION_NAME', 'Authentication');
define('UAH_OPTIONS_AUTHENTICATION_SLUG', 'useraccesshub-authentication');
define('UAH_OPTIONS_CAPABILITY', 'manage_options');
define('UAH_OPTIONS_ROLES', 'useraccesshub_roles');
define('UAH_OPTIONS_ROLES_NAME', 'Roles');
define('UAH_OPTIONS_ROLES_SLUG', 'useraccesshub-roles');
define('UAH_OPTIONS_SETTINGS', 'useraccesshub_settings');
define('UAH_OPTIONS_SETTINGS_NAME', 'Settings');
define('UAH_OPTIONS_SETTINGS_SLUG', 'useraccesshub');
define('UAH_PLUGIN', __FILE__);
define('UAH_PLUGIN_SLUG', 'useraccesshub');
define('UAH_PLUGIN_NAME', 'User Access Hub');
define('UAH_QUERY_ENDPOINT', 'uah_endpoint');

// Register hooks.
register_activation_hook(UAH_PLUGIN, 'useraccesshub_activation');
register_deactivation_hook(UAH_PLUGIN, 'useraccesshub_deactivation');

// Actions.
add_action('admin_init', 'useraccesshub_register_settings');
add_action('admin_menu', 'useraccesshub_settings_pages_add');

// Filters.
add_filter('plugin_action_links_' . UAH_PLUGIN_SLUG . '/' . UAH_PLUGIN_SLUG . '.php', 'useraccesshub_settings_link');
add_filter('query_vars', 'useraccesshub_query_vars');
add_filter('template_include', 'useraccesshub_template_include');



/**
 * Activation.
 *
 * @since 1.0
 */
function useraccesshub_activation() {

  // Handle routing for all custom endpoint URL's.
  add_rewrite_rule('^spoke/api/([^/]*)', 'index.php?' . UAH_QUERY_ENDPOINT . '=$matches[1]', 'top');
  flush_rewrite_rules();

  // Add some default options.
  $options = [
    'useraccesshub_handshake_enabled' => 'on',
    'useraccesshub_api_key' => _useraccesshub_generate_api_key(),
  ];
  add_option(UAH_OPTIONS_AUTHENTICATION, $options);
}

/**
 * Authentication Top Content.
 *
 * @since 1.0
 */
function useraccesshub_authentication_content() {
  print '<p>The following data is for informational purposes and none of these values can be modified here.</p>';
}

/**
 * Roles Settings Page.
 *
 * @since 1.0
 */
function useraccesshub_authentication_page() {

  // check user capabilities
	if (!current_user_can(UAH_OPTIONS_CAPABILITY)) {
		return;
	}

  ?>
  <form action="options.php" method="post">
      <?php 
      settings_fields(UAH_OPTIONS_AUTHENTICATION);
      do_settings_sections(UAH_OPTIONS_AUTHENTICATION_SLUG);
      ?>
  </form>
  <?php
}

/**
 * Deactivation.
 *
 * @since 1.0
 */
function useraccesshub_deactivation() {
  flush_rewrite_rules();

  // Remove options.
  delete_option(UAH_OPTIONS_AUTHENTICATION);
  delete_option(UAH_OPTIONS_ROLES);
  delete_option(UAH_OPTIONS_SETTINGS);
}

/**
 * Query Vars.
 *
 * @since 1.0
 */
function useraccesshub_query_vars($query_vars) {
  $query_vars[] = UAH_QUERY_ENDPOINT;

  return $query_vars;
}

/**
 * Register Settings.
 *
 * @since 1.0
 */
function useraccesshub_register_settings() {
  register_setting(UAH_OPTIONS_AUTHENTICATION, UAH_OPTIONS_AUTHENTICATION);
  register_setting(UAH_OPTIONS_ROLES, UAH_OPTIONS_ROLES);
  register_setting(UAH_OPTIONS_SETTINGS, UAH_OPTIONS_SETTINGS);

  add_settings_section(UAH_OPTIONS_SETTINGS, UAH_OPTIONS_SETTINGS_NAME, 'useraccesshub_settings_content', UAH_OPTIONS_SETTINGS_SLUG);
  add_settings_field('useraccesshub_enabled', 'Enable all of the User Access Hub functionality.', 'useraccesshub_setting_enabled', UAH_OPTIONS_SETTINGS_SLUG, UAH_OPTIONS_SETTINGS);
  add_settings_field('useraccesshub_redirect', 'Redirect URL/Path', 'useraccesshub_setting_redirect', UAH_OPTIONS_SETTINGS_SLUG, UAH_OPTIONS_SETTINGS);

  add_settings_section(UAH_OPTIONS_ROLES, UAH_OPTIONS_ROLES_NAME, 'useraccesshub_roles_content', UAH_OPTIONS_ROLES_SLUG);
  add_settings_field('useraccesshub_roles', 'Roles', 'useraccesshub_setting_roles', UAH_OPTIONS_ROLES_SLUG, UAH_OPTIONS_ROLES);
  add_settings_field('useraccesshub_default_role', 'Default Role', 'useraccesshub_setting_default_role', UAH_OPTIONS_ROLES_SLUG, UAH_OPTIONS_ROLES);
  add_settings_field('useraccesshub_allow_local', 'Allow local accounts to login (for managed roles above).', 'useraccesshub_setting_allow_local', UAH_OPTIONS_ROLES_SLUG, UAH_OPTIONS_ROLES);

  add_settings_section(UAH_OPTIONS_AUTHENTICATION, UAH_OPTIONS_AUTHENTICATION_NAME, 'useraccesshub_authentication_content', UAH_OPTIONS_AUTHENTICATION_SLUG);
  add_settings_field('useraccesshub_handshake_enabled', 'Handshake Enabled', 'useraccesshub_setting_handshake_enabled', UAH_OPTIONS_AUTHENTICATION_SLUG, UAH_OPTIONS_AUTHENTICATION);
  add_settings_field('useraccesshub_api_key', 'API Key', 'useraccesshub_setting_api_key', UAH_OPTIONS_AUTHENTICATION_SLUG, UAH_OPTIONS_AUTHENTICATION);
  add_settings_field('useraccesshub_public_key', 'Public Key', 'useraccesshub_setting_public_key', UAH_OPTIONS_AUTHENTICATION_SLUG, UAH_OPTIONS_AUTHENTICATION);
  add_settings_field('useraccesshub_site_id', 'Site ID', 'useraccesshub_setting_site_id', UAH_OPTIONS_AUTHENTICATION_SLUG, UAH_OPTIONS_AUTHENTICATION);
}

/**
 * Roles Top Content.
 *
 * @since 1.0
 */
function useraccesshub_roles_content() {
  print '<p>The following are role specific settings for this plugin.</p>';
}

/**
 * Roles Settings Page.
 *
 * @since 1.0
 */
function useraccesshub_roles_page() {

  // check user capabilities
	if (!current_user_can(UAH_OPTIONS_CAPABILITY)) {
		return;
	}

  ?>
  <form action="options.php" method="post">
      <?php 
      settings_fields(UAH_OPTIONS_ROLES);
      do_settings_sections(UAH_OPTIONS_ROLES_SLUG);
      submit_button('Save Settings');
      ?>
  </form>
  <?php
}

/**
 * Settings: Allow Local.
 *
 * @since 1.0
 */
function useraccesshub_setting_allow_local() {
  $options = get_option(UAH_OPTIONS_ROLES);
  $allow_local = empty($options['useraccesshub_allow_local']) ? '' : ' checked="checked"';

  print '<input id="useraccesshub_allow_local" name="' . UAH_OPTIONS_ROLES . '[useraccesshub_allow_local]" type="checkbox"' . $allow_local . ' />';
}

/**
 * Settings: API Key.
 *
 * @since 1.0
 */
function useraccesshub_setting_api_key() {
  $options = get_option(UAH_OPTIONS_AUTHENTICATION);
  $api_key = empty($options['useraccesshub_api_key']) ? '' : $options['useraccesshub_api_key'];

  print '<input id="useraccesshub_api_key" name="' . UAH_OPTIONS_AUTHENTICATION . '[useraccesshub_api_key]" type="text" value="' . $api_key . '" disabled="disabled" class="large-text" />';
  print '<p class="description">The API key is generated on plugin activation. This API key is only used during the handshake process between the User Access Hub and this site. Once the handshake is complete the public key below is used during communication between the two. This value can be regenerated using the WP CLI.</p>';
}

/**
 * Settings: Default Role.
 *
 * @since 1.0
 */
function useraccesshub_setting_default_role() {
  global $wp_roles;

  $options = get_option(UAH_OPTIONS_ROLES);
  $default_role = empty($options['useraccesshub_default_role']) ? 'editor' : $options['useraccesshub_default_role'];

  print '<select id="useraccesshub_default_role" name="' . UAH_OPTIONS_ROLES . '[useraccesshub_default_role]">';
  foreach ($wp_roles->roles as $role_id => $wp_role) {
    print '<option value="' . $role_id . '"';
    if ($role_id == $default_role) {
      print ' selected="selected"';
    }
    print '>' . $wp_role['name'] . '</option>';
  }
  print '</select>';
  print '<p class="description">Select which role should be considered the default.</p>';
}

/**
 * Settings: Enabled.
 *
 * @since 1.0
 */
function useraccesshub_setting_enabled() {
  $options = get_option(UAH_OPTIONS_SETTINGS);
  $enabled = empty($options['useraccesshub_enabled']) ? '' : ' checked="checked"';

  print '<input id="useraccesshub_enabled" name="' . UAH_OPTIONS_SETTINGS . '[useraccesshub_enabled]" type="checkbox"' . $enabled . ' />';
  print '<p class="description">Toggle all of the functionality of this plugin on or off.</p>';
}

/**
 * Settings: Handshake Enabled.
 *
 * @since 1.0
 */
function useraccesshub_setting_handshake_enabled() {
  $options = get_option(UAH_OPTIONS_AUTHENTICATION);
  $enabled = empty($options['useraccesshub_handshake_enabled']) ? '' : ' checked="checked"';

  print '<input id="useraccesshub_handshake_enabled" name="' . UAH_OPTIONS_AUTHENTICATION . '[useraccesshub_handshake_enabled]" type="checkbox"' . $enabled . ' disabled="disabled" />';
  print '<p class="description">Once the handshake is done, the handshake endpoint is disabled (for security purposes). This value can be enabled with the WP CLI. Only enable if a new handshake is required.</p>';
}

/**
 * Settings: Public Key.
 *
 * @since 1.0
 */
function useraccesshub_setting_public_key() {
  $options = get_option(UAH_OPTIONS_AUTHENTICATION);
  $public_key = empty($options['useraccesshub_public_key']) ? '' : $options['useraccesshub_public_key'];

  print '<textarea id="useraccesshub_public_key" name="' . UAH_OPTIONS_AUTHENTICATION . '[useraccesshub_public_key]" disabled="disabled" style="resize:none" cols="40" rows="5" class="large-text">' . $public_key . '</textarea>';
  print '<p class="description">The public key is set during the handshake process between the User Access Hub and this site and is used for all communication between this site and the User Access Hub.</p>';
}

/**
 * Settings: Redirect.
 *
 * @since 1.0
 */
function useraccesshub_setting_redirect() {
  $options = get_option(UAH_OPTIONS_SETTINGS);
  $redirect = empty($options['useraccesshub_redirect']) ? '' : $options['useraccesshub_redirect'];

  print '<input id="useraccesshub_redirect" name="' . UAH_OPTIONS_SETTINGS . '[useraccesshub_redirect]" type="text" value="' . $redirect . '" class="regular-text" />';
  print '<p class="description">Where to redirect the user after a login, including a preceeding /. For example: /welcome.</p>';
}

/**
 * Settings: Roles.
 *
 * @since 1.0
 */
function useraccesshub_setting_roles() {
  global $wp_roles;

  $options = get_option(UAH_OPTIONS_ROLES);
  $roles = empty($options['useraccesshub_roles']) ? [] : $options['useraccesshub_roles'];

  foreach ($wp_roles->roles as $role_id => $wp_role) {
    $checked = in_array($role_id, $roles) ? ' checked="checked"' : '';
    print '<p><input id="useraccesshub_roles-' . $role_id . '" name="' . UAH_OPTIONS_ROLES . '[useraccesshub_roles][]" value="' . $role_id . '" type="checkbox"' . $checked . ' /><label for="useraccesshub_roles-' . $role_id . '">' . $wp_role['name'] . '</label></p>';
  }
  print '<p class="description">Select which roles should have SSO login enabled from the User Access Hub. All roles not selected will still be able to login using the site login form.</p>';
}

/**
 * Settings: Site ID.
 *
 * @since 1.0
 */
function useraccesshub_setting_site_id() {
  $options = get_option(UAH_OPTIONS_AUTHENTICATION);
  $site_id = empty($options['useraccesshub_site_id']) ? '' : $options['useraccesshub_site_id'];

  print '<input id="useraccesshub_site_id" name="' . UAH_OPTIONS_AUTHENTICATION . '[useraccesshub_site_id]" type="text" value="' . $site_id . '" disabled="disabled" class="small-text" />';
  print '<p class="description">The site ID is the unique identifier for this site in the User Access Hub and is using during communication between the and this site. This value is set during the handshake process between the two.</p>';
}

/**
 * Settings Top Content.
 *
 * @since 1.0
 */
function useraccesshub_settings_content() {
  print '<p>The following are general settings for this plugin.</p>';
}

/**
 * Settings Link.
 *
 * @since 1.0
 */
function useraccesshub_settings_link($action_links) {
	$url = esc_url(add_query_arg('page', UAH_PLUGIN_SLUG, get_admin_url() . 'admin.php'));
	array_push($action_links, '<a href="' . $url . '">' . __( 'Settings' ) . '</a>');

	return $action_links;
}

/**
 * Settings Page.
 *
 * @since 1.0
 */
function useraccesshub_settings_page() {

  // check user capabilities
	if (!current_user_can(UAH_OPTIONS_CAPABILITY)) {
		return;
	}

  ?>
  <form action="options.php" method="post">
      <?php 
      settings_fields(UAH_OPTIONS_SETTINGS);
      do_settings_sections(UAH_OPTIONS_SETTINGS_SLUG);
      submit_button('Save Settings');
      ?>
  </form>
  <?php
}

/**
 * Settings Page Add.
 *
 * @since 1.0
 */
function useraccesshub_settings_pages_add() {
  global $submenu;

  // Load the icon.
  $data = file_get_contents(dirname(UAH_PLUGIN) . '/includes/images/useraccesshub_icon.svg');
  $image = 'data:image/svg+xml;base64,' . base64_encode($data);

  add_menu_page(UAH_OPTIONS_SETTINGS_NAME, UAH_PLUGIN_NAME, UAH_OPTIONS_CAPABILITY, UAH_OPTIONS_SETTINGS_SLUG, 'useraccesshub_settings_page', $image);
  add_submenu_page(UAH_OPTIONS_SETTINGS_SLUG, UAH_OPTIONS_ROLES_NAME, UAH_OPTIONS_ROLES_NAME, UAH_OPTIONS_CAPABILITY, UAH_OPTIONS_ROLES_SLUG, 'useraccesshub_roles_page');
  add_submenu_page(UAH_OPTIONS_SETTINGS_SLUG, UAH_OPTIONS_AUTHENTICATION_NAME, UAH_OPTIONS_AUTHENTICATION_NAME, UAH_OPTIONS_CAPABILITY, UAH_OPTIONS_AUTHENTICATION_SLUG, 'useraccesshub_authentication_page');

  // Change the parent link title in the submenu.
  $submenu[UAH_PLUGIN_SLUG][0][0] = 'Settings';
}

/**
 * Template Include.
 *
 * @since 1.0
 */
function useraccesshub_template_include($template) {
  $endpoint = get_query_var(UAH_QUERY_ENDPOINT);
  if (!empty($endpoint)) {
    switch ($endpoint) {
      case 'handshake':
        return plugin_dir_path(UAH_PLUGIN) . '/endpoint/handshake.php';
      case 'roles':
        return plugin_dir_path(UAH_PLUGIN) . '/endpoint/roles.php';
      case 'sso':
        return plugin_dir_path(UAH_PLUGIN) . '/endpoint/sso.php';
    }
  }
  
  return $template;
}




/**
 * Generic response.
 *
 * @param array $body
 *   The body to return.
 * @param int $http_code
 *   The status code to return.
 */
function _useraccesshub_response(array $body, int $http_code) {
  switch ($http_code) {
    case 200:
      header('Status: 200 Accepted');
      header('HTTP/1.1 200 Accepted');
      break;
    case 400:
      header('Status: 400 Bad Request');
      header('HTTP/1.1 400 Bad Request');
      break;
    case 401:
      header('Status: 401 Unauthorized');
      header('HTTP/1.1 401 Unauthorized');
      break;
    case 403:
      header('Status: 403 Forbidden');
      header('HTTP/1.1 403 Forbidden');
      break;
    case 405:
      header('Status: 405 Method Not Allowed');
      header('HTTP/1.1 405 Method Not Allowed');
      break;
  }

  if (count($body)) {
    header('Content-Type: application/json');
    $body = json_encode($body);

    // Compress only if client request allows gzip.
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
      $body = gzencode($body, 9, FORCE_GZIP);
      header('Content-Encoding: gzip');
    }

    print $body;
  }

	exit();
}

/**
 * Generate API Key.
 *
 * @since 1.0
 */
function _useraccesshub_generate_api_key($length = 255, $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz') {
  $key = '';

  $seed = str_split($chars);
  $max = count($seed) - 1;

  for ($i = 0; $i < $length; $i++) {
    $key .= $seed[random_int(0, $max)];
  }

  return $key;
}
