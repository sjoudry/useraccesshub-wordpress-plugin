=== User Access Hub ===
Contributors: sjoudry
Donate link: https://www.useraccesshub.com/
Tags: admin, administration, authentication
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable Tag: 1.0.3
Tested up to: 6.4
Requires PHP: 7.0

The User Access Hub is a service that allows administrators to manage users, user access, and roles across a network of CMS sites.

== Description ==

## User Access Hub

The User Access Hub is a service that allows administrators to manage users, user access, and roles across a network of CMS sites and will significantly simplify the administrative tasks associated with managing multiple websites. Some reasons why using such a service can help an administrator effectively manage all their CMS sites:

1. Centralized User Management: The User Access Hub provides a single, centralized dashboard where administrators can manage users across all connected CMS sites. This means that instead of logging into each site separately, they can control user access from one location.
1. User Account Creation and Deletion: Administrators can grant a user access to one or all CMS sites and revoke/delete user access to one or all CMS sites simultaneously. This is particularly useful when onboarding new users or removing access for users who no longer require it.
1. Role Management: Administrators can assign CMS site defined roles to users for each CMS site, allowing fine tuned access to each CMS site for each user. Permissions will be assigned to roles that are configured on the CMS site.
1. Single Sign-On (SSO): The User Access Hub allowing users to log in once and access all connected CMS sites without the need for multiple logins. This enhances user experience and security.
1. Audit Trail and Logging: The hub maintains detailed logs and an audit trail of user activities and changes made by administrators. This is essential for tracking changes, diagnosing issues, and maintaining security.
1. Scalability: As the network of Drupal sites grows, the User Access Hub can easily scale to accommodate new CMS sites and users without significantly increasing administrative overhead.
1. Automation: The User Access Hub service is built on the API first methodology and most of the operations that can be performed in the UI can also be performed using the User Access Hub API.

[Create a free account to get started.](https://www.useraccesshub.com/)

### Additional features

- Drupal CMS support
- Core Updates Reporting
- Plugin Updates Reporting
- Theme Updates Reporting

## Requirements

- This plugin requires the openssl_verify() PHP function, which is part of the [OpenSSL library](https://www.php.net/manual/en/book.openssl.php) for PHP.
- This plugin requires an account on [User Access Hub](https://www.useraccesshub.com/).

## Configuration

1. Enable the plugin. This will create an API key at User Access Hub > Authentication. None of the fields on this form can be edited through the UI. The 'Enabled the Handshake Endpoint' checkbox should be checked.
1. Select the roles that should be handled by the hub's SSO functionality at User Access Hub > Roles.
1. Add the site to the [User Access Hub](https://www.useraccesshub.com/) hub, setting the API key that was generated from step 1.
1. In the hub, use the 'Connect' operation to allow the hub to handshake with the site. Once this is complete, settings on User Access Hub > Authentication will be updated - 'Private Key' will be populated, 'Site ID' will be populated and the 'Enabled the Handshake Endpoint' checkbox will be unchecked.
1. To enable all User Access Hub functionality, the final step is to check the 'Enable all of the User Access Hub functionality.' checkbox on User Access Hub > Settings.

## Overriding Configuration

In many cases, the configuration that exists in the database will need to be overridden. The case of different config values for different environments springs to mind. This can be accomplished by overriding the configuration in a wp-config.php file:

```php
define( 'USERACCESSHUB_ALLOW_LOCAL', true );
define( 'USERACCESSHUB_API_KEY', 'string' );
define( 'USERACCESSHUB_DEFAULT_ROLE', 'role' );
define( 'USERACCESSHUB_ENABLED', true );
define( 'USERACCESSHUB_HANDSHAKE_ENABLED', true );
define( 'USERACCESSHUB_PUBLIC_KEY', 'string' );
define( 'USERACCESSHUB_REDIRECT', '/redirect/url' );
define( 'USERACCESSHUB_ROLES', array( 'role1', 'role2' ) );
define( 'USERACCESSHUB_SITE_ID', 1 );
```

## Commands

### Enable Handshake

This WP CLI command will enable the handshake endpoint so a site can be reconnected with the Hub.

```bash
wp enable-handshake
```

### Disable Handshake

This WP CLI command will disable the handshake endpoint.

```bash
wp disable-handshake
```

### Regenerate API Key

This WP CLI command will re-generate the API key used for the handshake.

```bash
wp regenerate-api-key
```
