<?php
/**
 * Plugin Name: WP Project Manager
 * Description: A lightweight Project & Task Management plugin for WordPress.
 * Version: 1.0.0
 * Author: Your Name
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define('WPPM_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__));
define('WPPM_PLUGIN_DIR_URL', plugin_dir_url( __FILE__));

// Include CPTs
require_once WPPM_PLUGIN_DIR_PATH . 'includes/wppm_enqueue.php';
require_once WPPM_PLUGIN_DIR_PATH . 'project/cpt-projects.php';
require_once WPPM_PLUGIN_DIR_PATH . 'task/cpt-tasks.php';
require_once WPPM_PLUGIN_DIR_PATH . 'includes/menu.php';
require_once WPPM_PLUGIN_DIR_PATH . 'project/project_view.php';
require_once WPPM_PLUGIN_DIR_PATH . 'task/task_view.php';