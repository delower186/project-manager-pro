<?php 
defined('ABSPATH') or die('Hey, What are you doing here? You Silly Man!');
// add meta box
// Add Project Meta Box to Task
function wppm_task_project_metabox() {
    add_meta_box(
        'wppm_task_project',
        'Project',
        'wppm_task_project_callback',
        'wppm_task',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'wppm_task_project_metabox');

function wppm_task_project_callback($post) {
    $projects = get_posts(array('post_type' => 'wppm_project', 'numberposts' => -1));
    $selected = get_post_meta($post->ID, '_wppm_project', true);

    echo '<select name="wppm_project" style="width:100%">';
    echo '<option value="">Select Project</option>';
    foreach ($projects as $project) {
        $sel = $selected == $project->ID ? 'selected' : '';
        echo "<option value='{$project->ID}' $sel>{$project->post_title}</option>";
    }
    echo '</select>';
}

// Save Task Project
function wppm_save_task_project($post_id) {
    if (isset($_POST['wppm_project'])) {
        update_post_meta($post_id, '_wppm_project', intval($_POST['wppm_project']));
    }
}
add_action('save_post_wppm_task', 'wppm_save_task_project');