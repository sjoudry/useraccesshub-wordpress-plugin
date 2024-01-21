<?php

namespace UserAccessHub;

/**
 * Filters class.
 *
 * @since 1.0
 */
final class Filters {

  /**
   * Query Vars.
   *
   * @param array $query_vars
   *   The existing query vars to modify.
   *
   * @return array
   *   The modified query vars.
   *
   * @since 1.0
   */
  public function queryVars(array $query_vars) {
    $query_vars[] = Plugin::QUERY_ENDPOINT;

    return $query_vars;
  }

  /**
   * Settings Link.
   *
   * @param array $action_links
   *   The existing action links to modify.
   *
   * @return array
   *   The modified action links.
   *
   * @since 1.0
   */
  public function settingsLink(array $action_links) {
    $action_links[] = '<a href="' . get_admin_url() . 'admin.php?page=' . Plugin::SLUG . '">' . Plugin::OPTIONS_SETTINGS_NAME . '</a>';
  
    return $action_links;
  }

  /**
   * Template Include.
   *
   * @param string $template
   *   The initially selected template.
   *
   * @return string
   *   The modified template.
   *
   * @since 1.0
   */
  public function templateInclude(string $template) {
    $endpoint = get_query_var(Plugin::QUERY_ENDPOINT);
    if (!empty($endpoint)) {
      switch ($endpoint) {
        case 'handshake':
          return plugin_dir_path(Plugin::FILE) . '/endpoint/handshake.php';
        case 'roles':
          return plugin_dir_path(Plugin::FILE) . '/endpoint/roles.php';
        case 'sso':
          return plugin_dir_path(Plugin::FILE) . '/endpoint/sso.php';
      }
    }
    
    return $template;
  }

}
