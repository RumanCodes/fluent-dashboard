<?php

namespace FluentDashboard\Models;

/**
 * Model for handling user preferences
 */
class UserPreference {

    const META_KEY = 'fluent_dashboard_mode';
    const MODE_STANDARD = 'standard';
    const MODE_FLUENT = 'fluent';

    /**
     * Get the current dashboard mode for a user
     *
     * @param int|null $user_id User ID, defaults to current user
     * @return string 'fluent' or 'standard'
     */
    public static function get_mode($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        if (!$user_id) {
            return self::MODE_STANDARD;
        }

        $mode = get_user_meta($user_id, self::META_KEY, true);
        
        // Default to standard if no preference is set
        if (empty($mode) || !in_array($mode, [self::MODE_STANDARD, self::MODE_FLUENT])) {
            return self::MODE_STANDARD;
        }

        return $mode;
    }

    /**
     * Set the dashboard mode for a user
     *
     * @param string $mode 'fluent' or 'standard'
     * @param int|null $user_id User ID, defaults to current user
     * @return bool
     */
    public static function set_mode($mode, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        if (!$user_id) {
            return false;
        }

        if (!in_array($mode, [self::MODE_STANDARD, self::MODE_FLUENT])) {
            return false;
        }

        return update_user_meta($user_id, self::META_KEY, $mode);
    }

    /**
     * Check if Fluent Dashboard mode is active
     *
     * @param int|null $user_id User ID, defaults to current user
     * @return bool
     */
    public static function is_fluent_mode($user_id = null) {
        return self::get_mode($user_id) === self::MODE_FLUENT;
    }
}

