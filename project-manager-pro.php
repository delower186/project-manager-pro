<?php
/*
Plugin Name: Project Manager Pro
Plugin URI: https://sandalia.com.bd/apps/project-manager-pro
Description: Project Manager Pro helps teams manage projects, tasks, and deadlines directly from WordPress, enabling seamless collaboration and productivity.
Version:1.0.2
Author: Delower
Author URI: https://github.com/delower186
License: GPLv2 or later
Text Domain: project-manager-pro
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

define('PMP_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__));
define('PMP_PLUGIN_DIR_URL', plugin_dir_url( __FILE__));

// Include CPTs
require_once PMP_PLUGIN_DIR_PATH . 'includes/pmp_enqueue.php';
require_once PMP_PLUGIN_DIR_PATH . 'project/pmp_cpt_projects.php';
require_once PMP_PLUGIN_DIR_PATH . 'task/pmp_cpt_tasks.php';
require_once PMP_PLUGIN_DIR_PATH . 'includes/pmp_menu.php';
require_once PMP_PLUGIN_DIR_PATH . 'project/pmp_project_view.php';
require_once PMP_PLUGIN_DIR_PATH . 'task/pmp_task_view.php';