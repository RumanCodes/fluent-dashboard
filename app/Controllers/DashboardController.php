<?php

namespace FluentDashboard\Controllers;

use FluentDashboard\Models\UserPreference;
use FluentDashboard\Models\PluginDetector;

/**
 * Controller for dashboard functionality
 */
class DashboardController {

    /**
     * Initialize the controller
     */
    public function __construct() {
        // This controller can be extended for future dashboard features
    }

    /**
     * Render the dashboard view
     */
    public function render() {
        $is_fluent_mode = UserPreference::is_fluent_mode();
        $fluent_plugins = PluginDetector::get_active_fluent_plugins();

        include_once FLUENT_DASHBOARD_PATH . '/app/Views/admin-dashboard.php';
    }
}

