<?php

namespace UserAccessHub;

/**
 * Actions class.
 *
 * @since 1.0
 */
final class Actions {

  /**
   * Authenticate: Handle Login.
   *
   * @param \WP_User|\WP_Error|NULL $user
   *   The user if authenticated.
   * @param string $username
   *   The username of the user.
   * @param string $password
   *   The password of the user.
   *
   * @return \WP_User|\WP_Error|NULL
   *   The user, an error or NULL depending on the state of the login.
   *
   * @since 1.0
   */
  public function handleLogin($user, ?string $username, ?string $password) {
    if ($user instanceof \WP_User) {

      // Load settings.
      $settings = get_option(Plugin::OPTIONS_SETTINGS);
      $roles = get_option(Plugin::OPTIONS_ROLES);
      if (!empty($settings[Plugin::OPTION_ENABLED])) {

        // Prevent login if the user has a role that is managed by User Access Hub,
        // unless local accounts are allowed.
        if (empty($roles[Plugin::OPTION_ALLOW_LOCAL])) {
          $managed_roles = array_intersect($roles[Plugin::OPTION_ROLES], $user->roles);
          if (count($managed_roles)) {
            return new \WP_Error('useraccesshub', 'This user must login using User Access Hub.');
          }
        }
      }
    }

    return $user;
  }

  /**
   * Login CSS.
   *
   * @since 1.0
   */
  public function loginCss() {
    $css_path = dirname(realpath(Plugin::FILE)) . '/includes/css/login.css';
    $css_url = plugin_dir_url(realpath(Plugin::FILE)) . 'includes/css/login.css';
    wp_enqueue_style('useraccesshub', $css_url, [], filemtime($css_path));
  }

  /**
   * Login Message.
   *
   * @param string|NULL $message
   *   The existing login message.
   *
   * @return string
   *   The alterd login message.
   *
   * @since 1.0
   */
  public function loginFooter() {
    if (isset($_GET['action'])) {
      if ($_GET['action'] == 'lostpassword') {
        print '<div class="useraccesshub_wrapper">';
        print wp_get_admin_notice('<b>User Access Hub</b><br/><a href="https://www.useraccesshub.com/user/password">Reset password with User Access Hub</a>');
        print '</div>';
      }
    }
    else {
      print '<div class="useraccesshub_wrapper">';
      print wp_get_admin_notice('<b>User Access Hub</b><br/><a href="https://www.useraccesshub.com/user/login">Login with User Access Hub</a>');
      print '</div>';
    }
  }

}
