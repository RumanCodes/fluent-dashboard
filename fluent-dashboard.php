<?php

if ( ! defined('ABSPATH')) {
    exit;
}
/**
 * WordPress - Fluent Dashboard
 *
 * Plugin Name:         Fluent Dashboard
 * Plugin URI:          https://wordpress.org/plugins/fluent-dashboard
 * Description:         Fluent plugins dashboard
 * Version:             1.1.1
 * Requires at least:   5.2
 * Requires PHP:        7.2
 * Contributor:         Contributor according to the WordPress.org
 * Author:              WPManageNinja LLC
 * Author URI:          https://suitepress.org/fluent-dashboard
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         fluent-dashboard
 * Domain Path:         /languages
 */
require_once __DIR__ . '/vendor/autoload.php';

use FluentDashboard\App;

if ( class_exists( 'FluentDashboard\App' ) ) {
    $app = new App();
}
