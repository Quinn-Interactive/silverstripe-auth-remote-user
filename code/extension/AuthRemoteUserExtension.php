<?php

class AuthRemoteUserExtension extends Extension
{
    /**
     * @var bool Whether to create a new user if the credential is not recognized.
     */
    private static $auto_create_user = false;

    /**
     * @var string The group to which to add auto-created users.
     */
    private static $auto_user_group = null;

    /**
     * Set whether to create a new user if the credential is not recognized.
     *
     * @param bool $setting
     */
    public static function setAutoCreateUser($setting)
    {
        Config::inst()->update('AuthRemoteUserExtension', 'auto_create_user', (bool) $setting);
    }

    /**
     * Set the group name for auto-created users.
     *
     * @param string $group_name A valid group name
     */
    public static function setAutoUserGroup($group_name)
    {
        Config::inst()->update('AuthRemoteUserExtension', 'auto_user_group', $group_name);
    }

    /**
     * If the REMOTE_USER is set and is in the Member table, log that member in. If
     * not, and Config::inst()->get('AuthRemoteUserExtension', 'auto_create_user') is set, add that
     * Member to the configured group, and log the new user in. Otherwise, do nothing.
     */
    public function onAfterInit()
    {
        if (isset($_SERVER['REMOTE_USER'])) {
            $unique_identifier = $_SERVER['REMOTE_USER'];
        } elseif (isset($_SERVER['REDIRECT_REMOTE_USER'])) {
            $unique_identifier = $_SERVER['REDIRECT_REMOTE_USER'];
        }
        if (isset($unique_identifier)) {
            $unique_identifier_field = Member::config()->unique_identifier_field;
            $member = Member::get()->filter($unique_identifier_field, $unique_identifier)->first();
            if ($member) {
                $member->logIn();
                $this->owner->redirectBack();
            } elseif (Config::inst()->get('AuthRemoteUserExtension', 'auto_create_user') &&
                    strlen(Config::inst()->get('AuthRemoteUserExtension', 'auto_user_group'))) {
                $group = Group::get()
                    ->filter('Title', Config::inst()->get('AuthRemoteUserExtension', 'auto_user_group'))->first();
                if ($group) {
                    $member = new Member();
                    $member->$unique_identifier_field = $unique_identifier;
                    $member->write();
                    $member->Groups()->add($group);
                    $member->logIn();
                }
            }
        }
    }
}
