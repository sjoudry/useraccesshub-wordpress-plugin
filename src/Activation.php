<?php

namespace UserAccessHub;

final class Activation {

  public function activation() {

    // Handle routing for all custom endpoint URL's.
    add_rewrite_rule('^spoke/api/([^/]*)', 'index.php?' . PLUGIN::QUERY_ENDPOINT . '=$matches[1]', 'top');
    flush_rewrite_rules();

    // Add some default options.
    $options = [
      Plugin::OPTION_HANDHSAKE_ENABLED => 'on',
      Plugin::OPTION_API_KEY => Plugin::getInstance()->generateApiKey(),
    ];
    add_option(Plugin::OPTIONS_AUTHENTICATION, $options);
  }

}
