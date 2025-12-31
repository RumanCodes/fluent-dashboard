<?php

namespace FluentDashboard\Hooks\Handlers;

use FluentDashboard\Models\UserPreference;
use FluentDashboard\Models\PluginDetector;

/**
 * Handler for filtering admin menus based on dashboard mode
 */
class AdminMenuFilter {

    /**
     * Initialize the handler
     */
    public function __construct() {
        $this->init();
    }

    /**
     * Initialize hooks
     */
    public function init() {
        // Filter admin menus to show only Fluent plugins when in Fluent mode
        add_action('admin_menu', [$this, 'filter_admin_menus'], 999);
        add_action('admin_init', [$this, 'redirect_non_fluent_pages'], 1);
    }

    /**
     * Filter admin menus to hide non-Fluent items when in Fluent mode
     */
    public function filter_admin_menus() {
        // Only filter if Fluent Dashboard mode is active
        if (!UserPreference::is_fluent_mode()) {
            return;
        }

        global $menu, $submenu;

        // Get list of Fluent menu slugs
        $fluent_menu_slugs = PluginDetector::get_fluent_menu_slugs();

        // Filter top-level menus
        if (is_array($menu)) {
            foreach ($menu as $key => $menu_item) {
                if (!isset($menu_item[2])) {
                    continue;
                }

                $menu_slug = $menu_item[2];

                // Keep WordPress core menus (dashboard, posts, media, pages, comments, appearance, plugins, users, tools, settings)
                $core_menus = [
//                    'index.php',           // Dashboard
//                    'edit.php',            // Posts
//                    'upload.php',          // Media
//                    'edit.php?post_type=page', // Pages
//                    'edit-comments.php',   // Comments
//                    'themes.php',          // Appearance
//                    'plugins.php',         // Plugins
//                    'users.php',           // Users
//                    'tools.php',           // Tools
//                    'options-general.php', // Settings
                ];

                // Check if it's a core menu or Fluent menu
                $is_core_menu = false;
                foreach ($core_menus as $core_menu) {
                    if (strpos($menu_slug, $core_menu) !== false) {
                        $is_core_menu = true;
                        break;
                    }
                }

                // Remove if not core and not Fluent
                if (!$is_core_menu && !PluginDetector::is_fluent_menu($menu_slug)) {
                    unset($menu[$key]);
                }
            }
        }

        // Filter submenus
        if (is_array($submenu)) {
            foreach ($submenu as $parent_slug => $submenu_items) {
                // If parent is not Fluent and not core, remove all submenus
                $is_core_parent = $this->is_core_menu($parent_slug);
                $is_fluent_parent = PluginDetector::is_fluent_menu($parent_slug);

                if (!$is_core_parent && !$is_fluent_parent) {
                    unset($submenu[$parent_slug]);
                    continue;
                }

                // Filter submenu items
                if (is_array($submenu_items)) {
                    foreach ($submenu_items as $key => $submenu_item) {
                        if (!isset($submenu_item[2])) {
                            continue;
                        }

                        $submenu_slug = $submenu_item[2];
                        $is_fluent_submenu = PluginDetector::is_fluent_menu($submenu_slug);

                        // Remove if not Fluent (core parent submenus are kept)
                        if (!$is_fluent_submenu && !$is_core_parent) {
                            unset($submenu[$parent_slug][$key]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Redirect non-Fluent pages when in Fluent mode
     */
    public function redirect_non_fluent_pages() {
        // Only redirect if Fluent Dashboard mode is active
        if (!UserPreference::is_fluent_mode()) {
            return;
        }

        // Don't redirect on AJAX requests
        if (wp_doing_ajax()) {
            return;
        }

        // Don't redirect on admin-ajax.php
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        // Get current page
        $current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
        $current_screen = get_current_screen();

        if (!$current_screen) {
            return;
        }

        // Allow core WordPress pages
        $allowed_pages = [
            'index.php',           // Dashboard
            'edit.php',            // Posts
            'upload.php',          // Media
            'edit.php?post_type=page', // Pages
            'edit-comments.php',   // Comments
            'themes.php',          // Appearance
            'plugins.php',         // Plugins
            'users.php',           // Users
            'tools.php',           // Tools
            'options-general.php', // Settings
        ];

        $is_allowed = false;
        foreach ($allowed_pages as $allowed_page) {
            if (strpos($current_screen->id, $allowed_page) !== false || 
                strpos($current_page, $allowed_page) !== false) {
                $is_allowed = true;
                break;
            }
        }

        // Check if current page is a Fluent menu
        if (!$is_allowed && !empty($current_page)) {
            if (!PluginDetector::is_fluent_menu($current_page)) {
                // Redirect to dashboard
                wp_safe_redirect(admin_url('index.php'));
                exit;
            }
        }
    }

    /**
     * Check if a menu slug is a core WordPress menu
     *
     * @param string $menu_slug
     * @return bool
     */
    private function is_core_menu($menu_slug) {
        $core_menus = [
            'index.php',
            'edit.php',
            'upload.php',
            'edit.php?post_type=page',
            'edit-comments.php',
            'themes.php',
            'plugins.php',
            'users.php',
            'tools.php',
            'options-general.php',
        ];

        foreach ($core_menus as $core_menu) {
            if (strpos($menu_slug, $core_menu) !== false) {
                return true;
            }
        }

        return false;
    }
}

