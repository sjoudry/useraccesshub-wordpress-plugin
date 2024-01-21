<?php

namespace UserAccessHub;

final class Filters {

  public function queryVars(array $query_vars) {
    $query_vars[] = Plugin::QUERY_ENDPOINT;

    return $query_vars;
  }

  public function settingsLink(array $action_links) {
    $action_links[] = '<a href="' . get_admin_url() . 'admin.php?page=' . Plugin::SLUG . '">' . Plugin::OPTIONS_SETTINGS_NAME . '</a>';
  
    return $action_links;
  }

  public function templateInclude($template) {
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
