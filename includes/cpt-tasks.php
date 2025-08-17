<?php
// Register Tasks CPT
function wppm_register_task_cpt() {
    $labels = [
        'name' => 'Tasks',
        'singular_name' => 'Task',
    ];
    $args = [
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'menu_position' => 7,
        'menu_icon' => 'dashicons-list-view',
        'supports' => ['title', 'editor'],
    ];
    register_post_type('wppm_task', $args);
}
add_action('init', 'wppm_register_task_cpt');

// Add Meta Boxes for Tasks
function wppm_task_meta_boxes() {
    add_meta_box('wppm_task_details', 'Task Details', 'wppm_task_meta_callback', 'wppm_task', 'normal', 'default');
}
add_action('add_meta_boxes', 'wppm_task_meta_boxes');

function wppm_task_meta_callback($post) {
    $status   = get_post_meta($post->ID, '_wppm_task_status', true);
    $priority = get_post_meta($post->ID, '_wppm_task_priority', true);
    $due_date = get_post_meta($post->ID, '_wppm_task_due_date', true);
    $assigned = get_post_meta($post->ID, '_wppm_task_assigned', true);
    $related_project = get_post_meta($post->ID, '_wppm_related_project', true);

    // Fetch all users
    $users = get_users();
    // Fetch all projects
    $projects = get_posts(['post_type' => 'wppm_project', 'numberposts' => -1]);
    ?>
    <p>
        <label>Status:</label><br>
        <select name="wppm_task_status">
            <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
            <option value="in_progress" <?php selected($status, 'in_progress'); ?>>In Progress</option>
            <option value="completed" <?php selected($status, 'completed'); ?>>Completed</option>
        </select>
    </p>
    <p>
        <label>Priority:</label><br>
        <select name="wppm_task_priority">
            <option value="low" <?php selected($priority, 'low'); ?>>Low</option>
            <option value="medium" <?php selected($priority, 'medium'); ?>>Medium</option>
            <option value="high" <?php selected($priority, 'high'); ?>>High</option>
        </select>
    </p>
    <p>
        <label>Due Date:</label><br>
        <input type="date" name="wppm_task_due_date" value="<?php echo esc_attr($due_date); ?>">
    </p>
    <p>
        <label>Assigned User:</label><br>
        <select name="wppm_task_assigned">
            <option value="">-- Select User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($assigned, $user->ID); ?>>
                    <?php echo esc_html($user->display_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label>Related Project:</label><br>
        <select name="wppm_related_project">
            <option value="">-- Select Project --</option>
            <?php foreach ($projects as $project): ?>
                <option value="<?php echo esc_attr($project->ID); ?>" <?php selected($related_project, $project->ID); ?>>
                    <?php echo esc_html($project->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php
}

function wppm_save_task_meta($post_id) {
    if (array_key_exists('wppm_task_status', $_POST)) {
        update_post_meta($post_id, '_wppm_task_status', sanitize_text_field($_POST['wppm_task_status']));
    }
    if (array_key_exists('wppm_task_priority', $_POST)) {
        update_post_meta($post_id, '_wppm_task_priority', sanitize_text_field($_POST['wppm_task_priority']));
    }
    if (array_key_exists('wppm_task_due_date', $_POST)) {
        update_post_meta($post_id, '_wppm_task_due_date', sanitize_text_field($_POST['wppm_task_due_date']));
    }
    if (array_key_exists('wppm_task_assigned', $_POST)) {
        update_post_meta($post_id, '_wppm_task_assigned', intval($_POST['wppm_task_assigned']));
    }
    if (array_key_exists('wppm_related_project', $_POST)) {
        update_post_meta($post_id, '_wppm_related_project', intval($_POST['wppm_related_project']));
    }
}
add_action('save_post', 'wppm_save_task_meta');


// custom columns
// Add columns to Tasks list
function wppm_task_columns($columns) {
    $columns['status']   = 'Status';
    $columns['priority'] = 'Priority';
    $columns['due_date'] = 'Due Date';
    $columns['assigned'] = 'Assigned To';
    $columns['related']  = 'Related Project';
    $columns['countdown'] = 'Time Left';
    return $columns;
}
add_filter('manage_wppm_task_posts_columns', 'wppm_task_columns');

// Render column values for Tasks
function wppm_task_column_content($column, $post_id) {
    switch ($column) {
        case 'status':
            echo esc_html(get_post_meta($post_id, '_wppm_task_status', true));
            break;
        case 'priority':
            echo esc_html(get_post_meta($post_id, '_wppm_task_priority', true));
            break;
        case 'due_date':
            echo esc_html(get_post_meta($post_id, '_wppm_task_due_date', true));
            break;
        case 'assigned':
            $user_id = get_post_meta($post_id, '_wppm_task_assigned', true); // ✅ corrected key
            $user    = $user_id ? get_userdata($user_id) : null;
            echo $user ? esc_html($user->display_name) : '—';
            break;
        case 'related':
            $proj_id = get_post_meta($post_id, '_wppm_related_project', true); // ✅ corrected key
            $proj    = $proj_id ? get_post($proj_id) : null;
            echo $proj ? esc_html($proj->post_title) : '—';
            break;
        case 'countdown':
            $due_date = get_post_meta($post_id, '_wppm_task_due_date', true);
            if ($due_date) {
                echo '<span class="wppm-countdown" data-due="' . esc_attr($due_date) . '"></span>';
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_wppm_task_posts_custom_column', 'wppm_task_column_content', 10, 2);

// Make columns sortable in Tasks table
// Make columns sortable in Tasks table
function wppm_task_sortable_columns($columns) {
    $columns['status']   = 'status';
    $columns['priority'] = 'priority';
    $columns['due_date'] = 'due_date';
    $columns['assigned'] = 'assigned';
    $columns['related']  = 'related';
    return $columns;
}
add_filter('manage_edit-wppm_task_sortable_columns', 'wppm_task_sortable_columns');

// Handle sorting by meta
function wppm_task_orderby($query) {
    if(!is_admin()) return;

    $orderby = $query->get('orderby');

    if($orderby == 'status') {
        $query->set('meta_key', '_wppm_task_status');
        $query->set('orderby', 'meta_value');
    }
    elseif($orderby == 'priority') {
        $query->set('meta_key', '_wppm_task_priority');
        $query->set('orderby', 'meta_value');
    }
    elseif($orderby == 'due_date') {
        $query->set('meta_key', '_wppm_task_due_date');
        $query->set('orderby', 'meta_value');
    }
    elseif($orderby == 'assigned') {
        $query->set('meta_key', '_wppm_task_assigned');
        $query->set('orderby', 'meta_value_num');
    }
    elseif($orderby == 'related') {
        $query->set('meta_key', '_wppm_related_project');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'wppm_task_orderby');

// Add filters above Tasks table
function wppm_task_filters() {
    global $typenow;
    if ($typenow !== 'wppm_task') return;

    // Status filter
    $statuses = ['pending'=>'Pending','in_progress'=>'In Progress','completed'=>'Completed'];
    $current_status = isset($_GET['_wppm_task_status']) ? $_GET['_wppm_task_status'] : '';
    echo '<select name="_wppm_task_status"><option value="">All Statuses</option>';
    foreach($statuses as $key => $label) {
        printf('<option value="%s"%s>%s</option>', $key, selected($current_status, $key, false), $label);
    }
    echo '</select>';

    // Assigned User filter
    $users = get_users();
    $current_user = isset($_GET['_wppm_task_assigned']) ? $_GET['_wppm_task_assigned'] : '';
    echo '<select name="_wppm_task_assigned"><option value="">All Users</option>';
    foreach($users as $user) {
        printf('<option value="%d"%s>%s</option>', $user->ID, selected($current_user, $user->ID, false), $user->display_name);
    }
    echo '</select>';

    // Related Project filter
    $projects = get_posts(['post_type'=>'wppm_project','numberposts'=>-1]);
    $current_proj = isset($_GET['_wppm_related_project']) ? $_GET['_wppm_related_project'] : '';
    echo '<select name="_wppm_related_project"><option value="">All Projects</option>';
    foreach($projects as $proj) {
        printf('<option value="%d"%s>%s</option>', $proj->ID, selected($current_proj, $proj->ID, false), $proj->post_title);
    }
    echo '</select>';
}
add_action('restrict_manage_posts', 'wppm_task_filters');

// Filter Tasks query
function wppm_task_filter_query($query) {
    global $pagenow, $typenow;
    if ($typenow === 'wppm_task' && $pagenow === 'edit.php' && $query->is_main_query()) {
        $meta_query = [];

        if(!empty($_GET['_wppm_task_status'])) {
            $meta_query[] = [
                'key' => '_wppm_task_status',
                'value' => $_GET['_wppm_task_status'],
                'compare' => '='
            ];
        }
        if(!empty($_GET['_wppm_task_assigned'])) {
            $meta_query[] = [
                'key' => '_wppm_task_assigned',
                'value' => intval($_GET['_wppm_task_assigned']),
                'compare' => '='
            ];
        }
        if(!empty($_GET['_wppm_related_project'])) {
            $meta_query[] = [
                'key' => '_wppm_related_project',
                'value' => intval($_GET['_wppm_related_project']),
                'compare' => '='
            ];
        }

        if(!empty($meta_query)) $query->set('meta_query', $meta_query);
    }
}
add_action('pre_get_posts', 'wppm_task_filter_query');
