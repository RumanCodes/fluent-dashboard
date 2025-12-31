<?php

namespace FluentDashboard\Models;

/**
 * Model for detecting Fluent/WPManageNinja plugins
 */
class PluginDetector {

    /**
     * List of known Fluent plugin identifiers
     * These are typically found in plugin slugs, menu slugs, or plugin names
     */
    private static $fluent_identifiers = [
        'fluentcrm',
        'fluent_forms',
        'fluent-booking',
        'fluent-cart',
        'wpsocialninja.php',
        'fluent-snippets',
        'fluent-auth',
        'fluent-community',
    ];

    /**
     * Check if a menu item belongs to a Fluent plugin
     *
     * @param string $menu_slug Menu slug to check
     * @return bool
     */
    public static function is_fluent_menu($menu_slug) {
        if (empty($menu_slug)) {
            return false;
        }

        $menu_slug_lower = strtolower($menu_slug);

        foreach (self::$fluent_identifiers as $identifier) {
            if (strpos($menu_slug_lower, $identifier) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a plugin is a Fluent plugin
     *
     * @param string $plugin_file Plugin file path
     * @return bool
     */
    public static function is_fluent_plugin($plugin_file) {
        if (empty($plugin_file)) {
            return false;
        }

        $plugin_file_lower = strtolower($plugin_file);

        foreach (self::$fluent_identifiers as $identifier) {
            if (strpos($plugin_file_lower, $identifier) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all active Fluent plugins
     *
     * @return array Array of plugin files
     */
    public static function get_active_fluent_plugins() {
        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins', []);
        $fluent_plugins = [];

        foreach ($active_plugins as $plugin_file) {
            if (self::is_fluent_plugin($plugin_file)) {
                $fluent_plugins[] = $plugin_file;
            }
        }

        return $fluent_plugins;
    }

    /**
     * Get list of Fluent menu slugs from active plugins
     *
     * @return array Array of menu slugs
     */
    public static function get_fluent_menu_slugs() {
        global $menu, $submenu;

        $fluent_menus = [];

        if (is_array($menu)) {
            foreach ($menu as $menu_item) {
                if (isset($menu_item[2]) && self::is_fluent_menu($menu_item[2])) {
                    $fluent_menus[] = $menu_item[2];
                }
            }
        }

        if (is_array($submenu)) {
            foreach ($submenu as $parent_slug => $submenu_items) {
                if (self::is_fluent_menu($parent_slug)) {
                    foreach ($submenu_items as $submenu_item) {
                        if (isset($submenu_item[2])) {
                            $fluent_menus[] = $submenu_item[2];
                        }
                    }
                }
            }
        }

        return array_unique($fluent_menus);
    }
}

