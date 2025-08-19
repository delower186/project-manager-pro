<?php
/*
Plugin Name: WP Project Manager
Plugin URI: https://sandalia.com.bd/apps
Description: WP Project Manager helps teams manage projects, tasks, and deadlines directly from WordPress, enabling seamless collaboration and productivity.
Version:1.0.0
Author: Delower
Author URI: https://sandalia.com.bd/apps
License: GPLv2 or later
Text Domain: wp-project-manager
*/
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright (C) 2025  delower.

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