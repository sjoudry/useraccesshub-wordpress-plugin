<?php

namespace UserAccessHub;

/**
 * Settings class.
 *
 * @since 1.0
 */
final class Settings {

  /**
   * Settings: Register Settings and Options.
   *
   * @since 1.0
   */
  public function registerSettings() {
    register_setting(Plugin::OPTIONS_AUTHENTICATION, Plugin::OPTIONS_AUTHENTICATION);
    register_setting(Plugin::OPTIONS_ROLES, Plugin::OPTIONS_ROLES);
    register_setting(Plugin::OPTIONS_SETTINGS, Plugin::OPTIONS_SETTINGS);
  
    add_settings_section(Plugin::OPTIONS_SETTINGS, Plugin::OPTIONS_SETTINGS_NAME, [$this, 'settingsContent'], Plugin::OPTIONS_SETTINGS_SLUG);
    add_settings_field(Plugin::OPTION_ENABLED, 'Enable all of the User Access Hub functionality.', [$this, 'optionEnabled'], Plugin::OPTIONS_SETTINGS_SLUG, Plugin::OPTIONS_SETTINGS);
    add_settings_field(Plugin::OPTION_REDIRECT, 'Redirect URL/Path', [$this, 'optionRedirect'], Plugin::OPTIONS_SETTINGS_SLUG, Plugin::OPTIONS_SETTINGS);
  
    add_settings_section(Plugin::OPTIONS_ROLES, Plugin::OPTIONS_ROLES_NAME, [$this, 'rolesContent'], Plugin::OPTIONS_ROLES_SLUG);
    add_settings_field(Plugin::OPTION_ROLES, 'Roles', [$this, 'optionRoles'], Plugin::OPTIONS_ROLES_SLUG, Plugin::OPTIONS_ROLES);
    add_settings_field(Plugin::OPTION_DEFAULT_ROLE, 'Default Role', [$this, 'optionDefaultRole'], Plugin::OPTIONS_ROLES_SLUG, Plugin::OPTIONS_ROLES);
    add_settings_field(Plugin::OPTION_ALLOW_LOCAL, 'Allow local accounts to login (for managed roles above).', [$this, 'optionAllowLocal'], Plugin::OPTIONS_ROLES_SLUG, Plugin::OPTIONS_ROLES);
  
    add_settings_section(Plugin::OPTIONS_AUTHENTICATION, Plugin::OPTIONS_AUTHENTICATION_NAME, [$this, 'authenticationContent'], Plugin::OPTIONS_AUTHENTICATION_SLUG);
    add_settings_field(Plugin::OPTION_HANDSHAKE_ENABLED, 'Handshake Enabled', [$this, 'optionHandshakeEnabled'], Plugin::OPTIONS_AUTHENTICATION_SLUG, Plugin::OPTIONS_AUTHENTICATION);
    add_settings_field(Plugin::OPTION_API_KEY, 'API Key', [$this, 'optionApiKey'], Plugin::OPTIONS_AUTHENTICATION_SLUG, Plugin::OPTIONS_AUTHENTICATION);
    add_settings_field(Plugin::OPTION_PUBLIC_KEY, 'Public Key', [$this, 'optionPublicKey'], Plugin::OPTIONS_AUTHENTICATION_SLUG, Plugin::OPTIONS_AUTHENTICATION);
    add_settings_field(Plugin::OPTION_SITE_ID, 'Site ID', [$this, 'optionSiteId'], Plugin::OPTIONS_AUTHENTICATION_SLUG, Plugin::OPTIONS_AUTHENTICATION);
  }

  /**
   * Menu: Add Settings Pages.
   *
   * @since 1.0
   */
  public function addSettingsPages() {
    global $submenu;

    // Load the icon.
    $data = file_get_contents(dirname(realpath(Plugin::FILE)) . '/includes/images/useraccesshub_icon.svg');
    $image = 'data:image/svg+xml;base64,' . base64_encode($data);
  
    add_menu_page(Plugin::OPTIONS_SETTINGS_NAME, Plugin::NAME, Plugin::OPTIONS_CAPABILITY, Plugin::OPTIONS_SETTINGS_SLUG, [$this, 'settingsPage'], $image);
    add_submenu_page(Plugin::OPTIONS_SETTINGS_SLUG, Plugin::OPTIONS_ROLES_NAME, Plugin::OPTIONS_ROLES_NAME, Plugin::OPTIONS_CAPABILITY, Plugin::OPTIONS_ROLES_SLUG, [$this, 'rolesPage']);
    add_submenu_page(Plugin::OPTIONS_SETTINGS_SLUG, Plugin::OPTIONS_AUTHENTICATION_NAME, Plugin::OPTIONS_AUTHENTICATION_NAME, Plugin::OPTIONS_CAPABILITY, Plugin::OPTIONS_AUTHENTICATION_SLUG, [$this, 'authenticationPage']);
  
    // Change the parent link title in the submenu.
    $submenu[Plugin::SLUG][0][0] = 'Settings';
  }

  /**
   * Settings: Authentication Top Content.
   *
   * @since 1.0
   */
  public function authenticationContent() {
    print '<p>The following data is for informational purposes and none of these values can be modified here.</p>';
  }

  /**
   * Settings: Authentication Page.
   *
   * @since 1.0
   */
  public function authenticationPage() {

    // check user capabilities
    if (!current_user_can(Plugin::OPTIONS_CAPABILITY)) {
      return;
    }

    ?>
    <form action="options.php" method="post">
        <?php 
        settings_fields(Plugin::OPTIONS_AUTHENTICATION);
        do_settings_sections(Plugin::OPTIONS_AUTHENTICATION_SLUG);
        ?>
    </form>
    <?php
  }

  /**
   * Option: Allow Local.
   *
   * @since 1.0
   */
  public function optionAllowLocal() {
    $options = get_option(Plugin::OPTIONS_ROLES);
    $allow_local = empty($options[Plugin::OPTION_ALLOW_LOCAL]) ? '' : ' checked="checked"';
  
    print '<input id="' . Plugin::OPTION_ALLOW_LOCAL . '" name="' . Plugin::OPTIONS_ROLES . '[' . Plugin::OPTION_ALLOW_LOCAL . ']" type="checkbox"' . $allow_local . ' />';
  }

  /**
   * Option: API Key.
   *
   * @since 1.0
   */
  public function optionApiKey() {
    $options = get_option(Plugin::OPTIONS_AUTHENTICATION);
    $api_key = empty($options[Plugin::OPTION_API_KEY]) ? '' : $options[Plugin::OPTION_API_KEY];
  
    print '<input id="' . Plugin::OPTION_API_KEY . '" name="' . Plugin::OPTIONS_AUTHENTICATION . '[' . Plugin::OPTION_API_KEY . ']" type="text" value="' . $api_key . '" disabled="disabled" class="large-text" />';
    print '<p class="description">The API key is generated on plugin activation. This API key is only used during the handshake process between the User Access Hub and this site. Once the handshake is complete the public key below is used during communication between the two. This value can be regenerated using the WP CLI.</p>';
  }

  /**
   * Option: Default Role.
   *
   * @since 1.0
   */
  public function optionDefaultRole() {
    global $wp_roles;

    $options = get_option(Plugin::OPTIONS_ROLES);
    $default_role = empty($options[Plugin::OPTION_DEFAULT_ROLE]) ? 'editor' : $options[Plugin::OPTION_DEFAULT_ROLE];
  
    print '<select id="' . Plugin::OPTION_DEFAULT_ROLE . '" name="' . Plugin::OPTIONS_ROLES . '[' . Plugin::OPTION_DEFAULT_ROLE . ']">';
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
   * Option: Enabled.
   *
   * @since 1.0
   */
  public function optionEnabled() {
    $options = get_option(Plugin::OPTIONS_SETTINGS);
    $enabled = empty($options[Plugin::OPTION_ENABLED]) ? '' : ' checked="checked"';
  
    print '<input id="' . Plugin::OPTION_ENABLED . '" name="' . Plugin::OPTIONS_SETTINGS . '[' . Plugin::OPTION_ENABLED . ']" type="checkbox"' . $enabled . ' />';
    print '<p class="description">Toggle all of the functionality of this plugin on or off.</p>';
  }

  /**
   * Option: Handshake Enabled.
   *
   * @since 1.0
   */
  public function optionHandshakeEnabled() {
    $options = get_option(Plugin::OPTIONS_AUTHENTICATION);
    $enabled = empty($options[Plugin::OPTION_HANDSHAKE_ENABLED]) ? '' : ' checked="checked"';
  
    print '<input id="' . Plugin::OPTION_HANDSHAKE_ENABLED . '" name="' . Plugin::OPTIONS_AUTHENTICATION . '[' . Plugin::OPTION_HANDSHAKE_ENABLED . ']" type="checkbox"' . $enabled . ' disabled="disabled" />';
    print '<p class="description">Once the handshake is done, the handshake endpoint is disabled (for security purposes). This value can be enabled with the WP CLI. Only enable if a new handshake is required.</p>';
  }

  /**
   * Option: Public Key.
   *
   * @since 1.0
   */
  public function optionPublicKey() {
    $options = get_option(Plugin::OPTIONS_AUTHENTICATION);
    $public_key = empty($options[Plugin::OPTION_PUBLIC_KEY]) ? '' : $options['useraccesshub_public_key'];
  
    print '<textarea id="' . Plugin::OPTION_PUBLIC_KEY . '" name="' . Plugin::OPTIONS_AUTHENTICATION . '[' . Plugin::OPTION_PUBLIC_KEY . ']" disabled="disabled" style="resize:none" cols="40" rows="5" class="large-text">' . $public_key . '</textarea>';
    print '<p class="description">The public key is set during the handshake process between the User Access Hub and this site and is used for all communication between this site and the User Access Hub.</p>';
  }

  /**
   * Option: Redirect.
   *
   * @since 1.0
   */
  public function optionRedirect() {
    $options = get_option(Plugin::OPTIONS_SETTINGS);
    $redirect = empty($options[Plugin::OPTION_REDIRECT]) ? '' : $options['useraccesshub_redirect'];
  
    print '<input id="' . Plugin::OPTION_REDIRECT . '" name="' . Plugin::OPTIONS_SETTINGS . '[' . Plugin::OPTION_REDIRECT . ']" type="text" value="' . $redirect . '" class="regular-text" />';
    print '<p class="description">Where to redirect the user after a login, including a preceeding /. For example: /welcome.</p>';
  }

  /**
   * Option: Roles.
   *
   * @since 1.0
   */
  public function optionRoles() {
    global $wp_roles;

    $options = get_option(Plugin::OPTIONS_ROLES);
    $roles = empty($options[Plugin::OPTION_ROLES]) ? [] : $options[Plugin::OPTION_ROLES];
  
    foreach ($wp_roles->roles as $role_id => $wp_role) {
      $checked = in_array($role_id, $roles) ? ' checked="checked"' : '';
      print '<p><input id="' . Plugin::OPTION_ROLES . '-' . $role_id . '" name="' . Plugin::OPTIONS_ROLES . '[' . Plugin::OPTION_ROLES . '][]" value="' . $role_id . '" type="checkbox"' . $checked . ' />';
      print '<label for="' . Plugin::OPTION_ROLES . '-' . $role_id . '">' . $wp_role['name'] . '</label></p>';
    }
    print '<p class="description">Select which roles should have SSO login enabled from the User Access Hub. All roles not selected will still be able to login using the site login form.</p>';
  }

  /**
   * Option: Site ID.
   *
   * @since 1.0
   */
  public function optionSiteId() {
    $options = get_option(Plugin::OPTIONS_AUTHENTICATION);
    $site_id = empty($options[Plugin::OPTION_SITE_ID]) ? '' : $options[Plugin::OPTION_SITE_ID];
  
    print '<input id="' . Plugin::OPTION_SITE_ID . '" name="' . Plugin::OPTIONS_AUTHENTICATION . '[' . Plugin::OPTION_SITE_ID . ']" type="text" value="' . $site_id . '" disabled="disabled" class="small-text" />';
    print '<p class="description">The site ID is the unique identifier for this site in the User Access Hub and is using during communication between the and this site. This value is set during the handshake process between the two.</p>';
  }

  /**
   * Settings: Roles Top Content.
   *
   * @since 1.0
   */
  public function rolesContent() {
    print '<p>The following are role specific settings for this plugin.</p>';
  }

  /**
   * Settings: Roles Page.
   *
   * @since 1.0
   */
  public function rolesPage() {

    // check user capabilities
    if (!current_user_can(Plugin::OPTIONS_CAPABILITY)) {
      return;
    }

    ?>
    <form action="options.php" method="post">
        <?php 
        settings_fields(Plugin::OPTIONS_ROLES);
        do_settings_sections(Plugin::OPTIONS_ROLES_SLUG);
        submit_button('Save Settings');
        ?>
    </form>
    <?php
  }

  /**
   * Settings: Settings Top Content.
   *
   * @since 1.0
   */
  public function settingsContent() {
    print '<p>The following are general settings for this plugin.</p>';
  }

  /**
   * Settings: Settings Page.
   *
   * @since 1.0
   */
  public function settingsPage() {

    // check user capabilities
    if (!current_user_can(Plugin::OPTIONS_CAPABILITY)) {
      return;
    }

    ?>
    <form action="options.php" method="post">
        <?php 
        settings_fields(Plugin::OPTIONS_SETTINGS);
        do_settings_sections(Plugin::OPTIONS_SETTINGS_SLUG);
        submit_button('Save Settings');
        ?>
    </form>
    <?php
  }

}
