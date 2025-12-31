<?php

namespace FluentDashboard\Hooks\Handlers;

use FluentDashboard\Models\UserPreference;

class AdminBarHandler {
    public function __construct() {
        $this->init();
    }

    public function init() {
        add_action('admin_bar_menu', [$this, 'add_toggle_button'], 100);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Add toggle button to admin bar
     *
     * @param \WP_Admin_Bar $wp_admin_bar
     */
    public function add_toggle_button($wp_admin_bar) {
        // Only show in admin area
        if (!is_admin()) {
            return;
        }

        $current_mode = UserPreference::get_mode();
        $is_fluent_mode = $current_mode === UserPreference::MODE_FLUENT;

        $wp_admin_bar->add_node([
            'id' => 'fluent-dashboard-toggle',
            'title' => $this->get_toggle_html($is_fluent_mode),
            'meta' => [
                'class' => 'fluent-dashboard-toggle-wrapper',
            ],
        ]);
    }

    /**
     * Get HTML for toggle button
     *
     * @param bool $is_fluent_mode
     * @return string
     */
    private function get_toggle_html($is_fluent_mode) {
        ob_start();
        ?>
        <div class="fluent-dashboard-toggle-container">
            <div class="fluent-toggle-pill <?php echo $is_fluent_mode ? 'active-fluent' : 'active-wp'; ?>">
                <div class="fluent-toggle-option fluent-option <?php echo $is_fluent_mode ? 'active' : ''; ?>"
                     data-mode="fluent">
                    <span class="fluent-toggle-text">Fluent</span>
                </div>
                <div class="fluent-toggle-option wp-option <?php echo !$is_fluent_mode ? 'active' : ''; ?>"
                     data-mode="standard">
                    <span class="fluent-toggle-text">WP Default</span>
                </div>
                <div class="fluent-toggle-slider"></div>
            </div>
            <input type="hidden"
                   id="fluent-dashboard-mode"
                   value="<?php echo esc_attr($is_fluent_mode ? 'fluent' : 'standard'); ?>">
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Enqueue assets for toggle functionality
     */
    public function enqueue_assets() {
        // Only load in admin area
        if (!is_admin()) {
            return;
        }

        $dev_server = 'http://localhost:5173';
        $hot_file_path = FLUENT_DASHBOARD_PATH . '/.hot';
        $is_dev = file_exists($hot_file_path);

        if ($is_dev) {
            wp_enqueue_script('vite-client', $dev_server . '/@vite/client', [], null, true);
            wp_enqueue_script('fluent-dashboard-vite', $dev_server . '/js/main.js', [], null, true);
        } else {
            $main_js = FLUENT_DASHBOARD_BUILD_PATH . '/main.js';
            $main_css = FLUENT_DASHBOARD_BUILD_PATH . '/main.css';

            $js_version = file_exists($main_js) ? filemtime($main_js) : FLUENT_DASHBOARD_VERSION;
            $css_version = file_exists($main_css) ? filemtime($main_css) : FLUENT_DASHBOARD_VERSION;

            wp_enqueue_script('fluent-dashboard-main', FLUENT_DASHBOARD_BUILD_URL . '/main.js', [], $js_version, true);
            wp_enqueue_style('fluent-dashboard-style', FLUENT_DASHBOARD_BUILD_URL . '/main.css', [], $css_version);
        }

        // Localize script with REST API endpoint
        wp_localize_script($is_dev ? 'fluent-dashboard-vite' : 'fluent-dashboard-main', 'FluentDashboard', [
            'restUrl' => esc_url_raw(rest_url('fluent-dashboard/v1/')),
            'nonce' => wp_create_nonce('wp_rest'),
            'currentMode' => UserPreference::get_mode(),
        ]);

        // Add type="module" for ES modules
        add_filter('script_loader_tag', function ($tag, $handle) use ($is_dev) {
            $handles = $is_dev
                ? ['vite-client', 'fluent-dashboard-vite']
                : ['fluent-dashboard-main'];

            if (in_array($handle, $handles)) {
                $tag = str_replace('<script ', '<script type="module" ', $tag);
            }
            return $tag;
        }, 10, 2);

        // Add inline styles for toggle
        $this->add_inline_styles();
    }

    /**
     * Add inline CSS for toggle button
     */
    private function add_inline_styles() {
        ?>
        <style>
            /* Toggle Container */
            .fluent-dashboard-toggle-container {
                display: flex;
                align-items: center;
                padding: 0;
                margin: 0;
            }

            /* Pill Toggle Container */
            .fluent-toggle-pill {
                position: relative;
                display: flex;
                align-items: center;
                background: #3BD2FC;
                border-radius: 50px;
                padding: 3px;
                cursor: pointer;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                backdrop-filter: blur(10px);
                border: 2px solid rgba(255, 255, 255, 0.2);
                min-height: 36px;
                border-radius: 50px !important;
            }

            .fluent-toggle-pill:hover {
                background: rgba(255, 255, 255, 0.2);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
                border-color: rgba(255, 255, 255, 0.3);
            }

            /* Toggle Options */
            .fluent-toggle-option {
                position: relative;
                padding: 6px 20px;
                border-radius: 50px;
                font-size: 13px;
                font-weight: 600;
                color: rgba(255, 255, 255, 0.7);
                transition: all 0.3s ease;
                z-index: 2;
                cursor: pointer;
                white-space: nowrap;
                min-width: 90px;
                text-align: center;
                border-radius: 50px !important;
            }

            .fluent-toggle-option.active {
                color: #1e293b;
                background: white;
                box-shadow: rgba(0, 0, 0, 0.25) 0px 25px 50px -12px !important;
            }

            .fluent-toggle-option:hover {
                color: rgba(255, 255, 255, 0.95);
            }

            .fluent-toggle-option.active:hover {
                color: #0f172a;
            }

            /* Animated Slider Background */
            .fluent-toggle-slider {
                position: absolute;
                top: 3px;
                left: 3px;
                height: calc(100% - 6px);
                width: calc(50% - 3px);
                background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
                border-radius: 50px;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15),
                0 2px 4px rgba(0, 0, 0, 0.1);
                z-index: 1;
            }

            /* Active State - Fluent */
            .fluent-toggle-pill.active-fluent .fluent-toggle-slider {
                transform: translateX(0);
                background: #3BD2FC;
            }

            /* Active State - WP Default */
            .fluent-toggle-pill.active-wp .fluent-toggle-slider {
                transform: translateX(calc(100% + 3px));
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            }

            /* WordPress Admin Bar Integration */
            #wpadminbar #wp-admin-bar-fluent-dashboard-toggle {
                background: transparent !important;
            }

            #wpadminbar #wp-admin-bar-fluent-dashboard-toggle > .ab-item {
                padding: 0 10px !important;
                background: transparent !important;
                height: auto !important;
                display: flex;
                align-items: center;
            }

            #wpadminbar #wp-admin-bar-fluent-dashboard-toggle:hover > .ab-item {
                background: transparent !important;
            }

            #wpadminbar .fluent-dashboard-toggle-container {
                margin: 0;
            }

            /* Loading State */
            .fluent-toggle-pill.loading {
                pointer-events: none;
                opacity: 0.6;
            }

            .fluent-toggle-pill.loading .fluent-toggle-slider {
                animation: pulse 1.5s ease-in-out infinite;
            }

            @keyframes pulse {
                0%, 100% {
                    opacity: 1;
                }
                50% {
                    opacity: 0.5;
                }
            }

            /* Slide Animation */
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .fluent-toggle-pill {
                animation: slideIn 0.4s ease-out;
            }

            /* Responsive Design */
            @media screen and (max-width: 782px) {
                .fluent-toggle-pill {
                    min-height: 32px;
                    padding: 2px;
                }

                .fluent-toggle-option {
                    padding: 5px 16px;
                    font-size: 12px;
                    min-width: 80px;
                }

                #wpadminbar #wp-admin-bar-fluent-dashboard-toggle > .ab-item {
                    padding: 0 6px !important;
                }
            }

            /* Extra small screens */
            @media screen and (max-width: 600px) {
                .fluent-toggle-option {
                    padding: 4px 12px;
                    font-size: 11px;
                    min-width: 70px;
                }
            }

            /* High contrast for accessibility */
            @media (prefers-contrast: high) {
                .fluent-toggle-pill {
                    border-width: 3px;
                }

                .fluent-toggle-option {
                    font-weight: 700;
                }
            }

            /* Reduced motion for accessibility */
            @media (prefers-reduced-motion: reduce) {
                .fluent-toggle-pill,
                .fluent-toggle-option,
                .fluent-toggle-slider {
                    transition: none;
                }

                .fluent-toggle-pill {
                    animation: none;
                }
            }
        </style>
        <?php
    }
}
