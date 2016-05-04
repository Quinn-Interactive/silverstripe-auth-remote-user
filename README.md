# REMOTE_USER authentication

## Description

This module extends `Controller` to automatically log in the SilverStripe member whose Email field matches the server REMOTE_USER variable (`$_SERVER['REMOTE_USER']`). Optionally, it can create a new Member record for a server-authenticated user when no matching record is found. If auto-creation is not enabled, and there is no match, then this module does nothing.

This module was created to support Apache with mod_shib, but it works the same for any mechanism that sets REMOTE_USER, including basic auth.

This module requires SilverStripe framework 3.1.x.

This is version 1.0.0 of this module.

## Configuration

To enable the optional features, put static calls into your `_config.php`:

	AuthRemoteUserExtension::setAutoCreateUser(true);
	AuthRemoteUserExtension::setAutoUserGroup($group_name);

`AuthRemoteUserExtension::setAutoCreateUser(true)` enables the creation of a new Member record when an authenticated user does not match any existing record. The only field populated is Email.

`AuthRemoteUserExtension::setAutoUserGroup($group_name)` sets the group to which new users will be added. If the group does not exist, no Member record will be created, even if auto-creation is enabled.
