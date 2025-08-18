<?php
if (!defined('ABSPATH')) exit;
?>

    <div class="wrap">
        <h1>WP Project Manager Dashboard</h1>

        <div style="display:flex; gap:40px; margin-top:20px; flex-wrap:wrap;">
            <!-- Completed Projects -->
            <div style="padding:20px; background:#f0f0f0; border-radius:8px; flex:1 1 250px; text-align:center;">
                <h2>Total Completed Projects</h2>
                <p style="font-size:28px; font-weight:bold; margin:0;">
                    <?php
                    $completed_projects = new WP_Query([
                        'post_type' => 'wppm_project',
                        'meta_key'  => '_wppm_project_status',
                        'meta_value'=> 'completed',
                        'posts_per_page' => -1
                    ]);
                    echo $completed_projects->found_posts;
                    ?>
                </p>
            </div>

            <!-- Completed Tasks -->
            <div style="padding:20px; background:#f0f0f0; border-radius:8px; flex:1 1 250px; text-align:center;">
                <h2>Completed Tasks</h2>
                <p style="font-size:28px; font-weight:bold; margin:0;">
                    <?php
                    $completed_tasks = new WP_Query([
                        'post_type' => 'wppm_task',
                        'meta_key'  => '_wppm_task_status',
                        'meta_value'=> 'completed',
                        'posts_per_page' => -1
                    ]);
                    echo $completed_tasks->found_posts;
                    ?>
                </p>
            </div>

            <!-- Pending Tasks -->
            <div style="padding:20px; background:#f0f0f0; border-radius:8px; flex:1 1 250px; text-align:center;">
                <h2>Pending Tasks</h2>
                <p style="font-size:28px; font-weight:bold; margin:0;">
                    <?php
                    $pending_tasks = new WP_Query([
                        'post_type' => 'wppm_task',
                        'meta_key'  => '_wppm_task_status',
                        'meta_value'=> 'pending',
                        'posts_per_page' => -1
                    ]);
                    echo $pending_tasks->found_posts;
                    ?>
                </p>
            </div>
        </div>

        <!-- Running Projects Table -->
        <div style="padding:20px; background:#ffffff; border-radius:8px; margin-top:30px; box-shadow:0 0 15px rgba(0,0,0,0.05);">
            <h2 style="margin-bottom:20px;">Currently Running Projects & Tasks</h2>
            <table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif;">
                <thead>
                    <tr style="background:#0073aa; color:#fff; text-align:left;">
                        <th style="padding:10px; border:1px solid #ddd;">Project</th>
                        <th style="padding:10px; border:1px solid #ddd;">Manager</th>
                        <th style="padding:10px; border:1px solid #ddd;">Status</th>
                        <th style="padding:10px; border:1px solid #ddd;">Priority</th>
                        <th style="padding:10px; border:1px solid #ddd;">Progress</th>
                        <th style="padding:10px; border:1px solid #ddd;">Tasks (Running / Completed / Pending)</th>
                        <th style="padding:10px; border:1px solid #ddd;">Time Left</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $running_projects = new WP_Query([
                        'post_type' => 'wppm_project',
                        'meta_key'  => '_wppm_project_status',
                        'meta_value'=> 'in_progress',
                        'posts_per_page' => -1
                    ]);

                    if($running_projects->have_posts()):
                        while($running_projects->have_posts()): $running_projects->the_post();
                            $proj_id = get_the_ID();

                            // Tasks queries
                            $tasks_total = new WP_Query([
                                'post_type' => 'wppm_task',
                                'meta_key' => '_wppm_related_project',
                                'meta_value'=> $proj_id,
                                'posts_per_page'=> -1
                            ]);
                            $tasks_completed = new WP_Query([
                                'post_type'=>'wppm_task',
                                'meta_key'=>'_wppm_related_project',
                                'meta_value'=>$proj_id,
                                'meta_query'=>[['key'=>'_wppm_task_status','value'=>'completed','compare'=>'=']],
                                'posts_per_page'=>-1
                            ]);
                            $tasks_running = new WP_Query([
                                'post_type'=>'wppm_task',
                                'meta_key'=>'_wppm_related_project',
                                'meta_value'=>$proj_id,
                                'meta_query'=>[['key'=>'_wppm_task_status','value'=>'in_progress','compare'=>'=']],
                                'posts_per_page'=>-1
                            ]);
                            $tasks_pending = new WP_Query([
                                'post_type'=>'wppm_task',
                                'meta_key'=>'_wppm_related_project',
                                'meta_value'=>$proj_id,
                                'meta_query'=>[['key'=>'_wppm_task_status','value'=>'pending','compare'=>'=']],
                                'posts_per_page'=>-1
                            ]);

                            $total = $tasks_total->found_posts;
                            $completed = $tasks_completed->found_posts;
                            $percent = $total > 0 ? round(($completed/$total)*100) : 0;
                            $due_date = get_post_meta($proj_id,'_wppm_project_due_date',true);
                            $proj_status = get_post_meta($proj_id,'_wppm_project_status',true);
                            $proj_priority = get_post_meta($proj_id,'_wppm_project_priority',true);

                            // Project Manager
                            $proj_manager_id = get_post_meta($proj_id,'_wppm_project_assigned',true);
                            $proj_manager = $proj_manager_id ? get_userdata($proj_manager_id) : null;
                    ?>
                            <tr style="background:#f9f9f9;">
                                <td style="padding:10px; border:1px solid #ddd;">
                                    <strong><?php the_title(); ?></strong>
                                    <!-- Completed / Total tasks counter -->
                                    <span style="font-size:12px; color:#555; margin-left:8px;">(<?php echo $completed.' / '.$total; ?>)</span>
                                </td>
                                <td style="padding:10px; border:1px solid #ddd;">
                                    <?php echo $proj_manager ? esc_html($proj_manager->display_name) : '—'; ?>
                                </td>
                                <td style="padding:10px; border:1px solid #ddd;">
                                    <span class="wppm-badge wppm-status-<?php echo esc_attr($proj_status); ?>"><?php echo ucfirst($proj_status); ?></span>
                                </td>
                                <td style="padding:10px; border:1px solid #ddd;">
                                    <span class="wppm-badge wppm-priority-<?php echo esc_attr($proj_priority); ?>"><?php echo ucfirst($proj_priority); ?></span>
                                </td>
                                <td style="padding:10px; border:1px solid #ddd; width:220px;">
                                    <div style="background:#ddd; border-radius:6px; overflow:hidden; height:20px;">
                                        <div class="wppm-progress-bar" data-percent="<?php echo $percent; ?>" style="width:<?php echo $percent; ?>%; height:100%; background:#4caf50; text-align:center; color:white;"><?php echo $percent; ?>%</div>
                                    </div>
                                </td>
                                <td style="padding:10px; border:1px solid #ddd;">
                                    <?php echo $tasks_running->found_posts.' / '.$tasks_completed->found_posts.' / '.$tasks_pending->found_posts; ?>
                                </td>
                                <td style="padding:10px; border:1px solid #ddd;">
                                    <?php if($due_date): ?>
                                        <span class="wppm-countdown" data-due="<?php echo esc_attr($due_date); ?>" data-status="<?php echo esc_attr($proj_status); ?>"></span>
                                    <?php else: ?> — <?php endif; ?>
                                </td>
                            </tr>

                            <!-- Nested Task Table (Running & Pending tasks only) -->
                            <?php
                            $all_tasks = new WP_Query([
                                'post_type'=>'wppm_task',
                                'meta_key'=>'_wppm_related_project',
                                'meta_value'=>$proj_id,
                                'meta_query'=>[['key'=>'_wppm_task_status','value'=>['in_progress','pending'],'compare'=>'IN']],
                                'posts_per_page'=>-1
                            ]);
                            if($all_tasks->have_posts()): ?>
                                <tr>
                                    <td colspan="7" style="padding:0; background:#f4f4f4;">
                                        <table style="width:95%; margin:10px 20px; border-collapse:collapse; font-size:14px;">
                                            <thead>
                                                <tr style="background:#0073aa; color:#fff;">
                                                    <th style="padding:6px; border:1px solid #ddd;">Task</th>
                                                    <th style="padding:6px; border:1px solid #ddd;">Assigned To</th>
                                                    <th style="padding:6px; border:1px solid #ddd;">Status</th>
                                                    <th style="padding:6px; border:1px solid #ddd;">Priority</th>
                                                    <th style="padding:6px; border:1px solid #ddd;">Due</th>
                                                    <th style="padding:6px; border:1px solid #ddd;">Countdown</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($all_tasks->have_posts()): $all_tasks->the_post();
                                                    $task_id = get_the_ID();
                                                    $assigned_id = get_post_meta($task_id,'_wppm_task_assigned',true);
                                                    $assigned_user = $assigned_id ? get_userdata($assigned_id) : null;
                                                    $task_due = get_post_meta($task_id,'_wppm_task_due_date',true);
                                                    $task_status = get_post_meta($task_id,'_wppm_task_status',true);
                                                    $task_priority = get_post_meta($task_id,'_wppm_task_priority',true);
                                                ?>
                                                    <tr style="background:#fff;">
                                                        <td style="padding:6px; border:1px solid #ddd;"><?php the_title(); ?></td>
                                                        <td style="padding:6px; border:1px solid #ddd;"><?php echo $assigned_user ? $assigned_user->display_name : '—'; ?></td>
                                                        <td style="padding:6px; border:1px solid #ddd;">
                                                            <span class="wppm-badge wppm-status-<?php echo esc_attr($task_status); ?>"><?php echo ucfirst($task_status); ?></span>
                                                        </td>
                                                        <td style="padding:6px; border:1px solid #ddd;">
                                                            <span class="wppm-badge wppm-priority-<?php echo esc_attr($task_priority); ?>"><?php echo ucfirst($task_priority); ?></span>
                                                        </td>
                                                        <td style="padding:6px; border:1px solid #ddd;"><?php echo $task_due ? esc_html($task_due) : '—'; ?></td>
                                                        <td style="padding:6px; border:1px solid #ddd;">
                                                            <?php if($task_due): ?>
                                                                <span class="wppm-countdown" data-due="<?php echo esc_attr($task_due); ?>" data-status="<?php echo esc_attr($task_status); ?>"></span>
                                                            <?php else: ?> — <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; wp_reset_postdata(); ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php endif; ?>

                    <?php wp_reset_postdata();
                        endwhile;
                    else:
                        echo '<tr><td colspan="7" style="padding:8px;">No running projects found.</td></tr>';
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
