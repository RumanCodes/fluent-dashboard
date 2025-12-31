<?php

namespace FluentDashboard\Controllers;

use FluentDashboard\Models\UserPreference;

/**
 * Controller for toggle functionality
 */
class ToggleController {

    /**
     * Initialize the controller
     */
    public function __construct() {
        // REST API routes are registered in RestApiHandlers
    }

    /**
     * Toggle the dashboard mode
     *
     * @param string $mode 'fluent' or 'standard'
     * @return array Response data
     */
    public function toggle_mode($mode) {
        if (!in_array($mode, [UserPreference::MODE_STANDARD, UserPreference::MODE_FLUENT])) {
            return [
                'success' => false,
                'message' => __('Invalid mode specified.', 'fluent-dashboard')
            ];
        }

        $result = UserPreference::set_mode($mode);

        if ($result) {
            return [
                'success' => true,
                'mode' => $mode,
                'message' => $mode === UserPreference::MODE_FLUENT 
                    ? __('Switched to Fluent Dashboard', 'fluent-dashboard')
                    : __('Switched to Standard Admin', 'fluent-dashboard')
            ];
        }

        return [
            'success' => false,
            'message' => __('Failed to update preference.', 'fluent-dashboard')
        ];
    }

    /**
     * Get current dashboard mode
     *
     * @return array Response data
     */
    public function get_mode() {
        $mode = UserPreference::get_mode();
        
        return [
            'success' => true,
            'mode' => $mode,
            'is_fluent' => $mode === UserPreference::MODE_FLUENT
        ];
    }
}

