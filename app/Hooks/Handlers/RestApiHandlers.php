<?php

namespace FluentDashboard\Hooks\Handlers;

use FluentDashboard\Controllers\ToggleController;
use FluentDashboard\Models\UserPreference;

/**
 * Handler for REST API routes
 */
class RestApiHandlers {

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
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Toggle dashboard mode
        register_rest_route('fluent-dashboard/v1', '/toggle', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_toggle'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        // Get current mode
        register_rest_route('fluent-dashboard/v1', '/mode', [
            'methods' => 'GET',
            'callback' => [$this, 'handle_get_mode'],
            'permission_callback' => [$this, 'check_permission'],
        ]);
    }

    /**
     * Handle toggle request
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function handle_toggle($request) {
        $mode = $request->get_param('mode');
        
        if (empty($mode)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Mode parameter is required.', 'fluent-dashboard')
            ], 400);
        }

        $controller = new ToggleController();
        $result = $controller->toggle_mode($mode);

        $status = $result['success'] ? 200 : 400;
        
        return new \WP_REST_Response($result, $status);
    }

    /**
     * Handle get mode request
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function handle_get_mode($request) {
        $controller = new ToggleController();
        $result = $controller->get_mode();
        
        return new \WP_REST_Response($result, 200);
    }

    /**
     * Check if user has permission to use the API
     *
     * @return bool
     */
    public function check_permission() {
        return current_user_can('manage_options');
    }
}
