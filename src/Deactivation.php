<?php

namespace UserAccessHub;

final class Deactivation {

  public function deactivation() {
    flush_rewrite_rules();

    // Remove options.
    delete_option(Plugin::OPTIONS_AUTHENTICATION);
    delete_option(Plugin::OPTIONS_ROLES);
    delete_option(Plugin::OPTIONS_SETTINGS);
  }

}
