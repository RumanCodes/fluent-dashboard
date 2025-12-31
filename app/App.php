<?php
namespace FluentDashboard;

use FluentDashboard\Hooks\Handlers\AdminBarHandler;
use FluentDashboard\Hooks\Handlers\AdminMenuFilter;
use FluentDashboard\Hooks\Handlers\RestApiHandlers;

class App {

    public function __construct() {
        add_action('init', [$this, 'init']);
    }

    public function init(): void {
        // Define constants
        define( 'FLUENT_DASHBOARD', 'fluent-dashboard' );
        define( 'FLUENT_DASHBOARD_PATH', untrailingslashit( plugin_dir_path( __DIR__ ) ) );
        define( 'FLUENT_DASHBOARD_URL', untrailingslashit( plugin_dir_url( __DIR__ ) ) );
        define( 'FLUENT_DASHBOARD_BUILD_PATH', FLUENT_DASHBOARD_PATH . '/public/assets' );
        define( 'FLUENT_DASHBOARD_BUILD_URL', FLUENT_DASHBOARD_URL . '/public/assets' );
        define( 'FLUENT_DASHBOARD_VERSION', '1.1.1' );

        // Initialize handlers
        new AdminBarHandler();
        new AdminMenuFilter();
        new RestApiHandlers();
    }
}
