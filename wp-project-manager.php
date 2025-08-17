<?php
/**
 * Plugin Name: WP Project Manager
 * Plugin URI:  https://sandalia.com.bd/apps
 * Description: A simple project and task management plugin for WordPress.
 * Version:     1.0
 * Author:      Delower
 * Author URI:  https://sandalia.com.bd/apps
 * License:     GPL2
 * Text Domain: wp-project-manager
 */

defined('ABSPATH') or die('Hey, What are you doing here? You Silly Man!');


define('WPPM_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__));
define('WPPM_PLUGIN_DIR_URL', plugin_dir_url( __FILE__));

// Include custom post types
require_once WPPM_PLUGIN_DIR_PATH . 'inc/cpt.php';
require_once WPPM_PLUGIN_DIR_PATH . 'meta_box/meta_boxes.php';
require_once WPPM_PLUGIN_DIR_PATH . 'inc/custom_menu.php';
