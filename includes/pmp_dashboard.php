<?php
if (!defined('ABSPATH')) exit;
?>

<div class="wrap">
    <h1>Project Manager Pro Dashboard</h1>

    <div style="display:flex; gap:40px; margin-top:20px; flex-wrap:wrap;">
        <!-- Completed Projects -->
        <div style="padding:20px; background:#f0f0f0; border-radius:8px; flex:1 1 250px; text-align:center;">
            <h2>Total Completed Projects</h2>
            <p style="font-size:28px; font-weight:bold; margin:0;">
                <?php
                $completed_projects_count = wp_count_posts('pmp_project')->publish;
                $completed_projects = new WP_Query([
                    'post_type' => 'pmp_project',
                    'meta_key' => '_pmp_project_status',
                    'meta_value' => 'completed',
                    'fields' => 'ids',
                    'posts_per_page' => -1
                ]);
                echo esc_html(count($completed_projects->posts));
                ?>
            </p>
        </div>

        <!-- Completed Tasks -->
        <div style="padding:20px; background:#f0f0f0; border-radius:8px; flex:1 1 250px; text-align:center;">
            <h2>Completed Tasks</h2>
            <p style="font-size:28px; font-weight:bold; margin:0;">
                <?php
                $completed_tasks = new WP_Query([
                    'post_type' => 'pmp_task',
                    'meta_key'  => '_pmp_task_status',
                    'meta_value'=> 'completed',
                    'fields' => 'ids',
                    'posts_per_page' => -1
                ]);
                echo esc_html(count($completed_tasks->posts));
                ?>
            </p>
        </div>

        <!-- Pending Tasks -->
        <div style="padding:20px; background:#f0f0f0; border-radius:8px; flex:1 1 250px; text-align:center;">
            <h2>Pending Tasks</h2>
            <p style="font-size:28px; font-weight:bold; margin:0;">
                <?php
                $pending_tasks = new WP_Query([
                    'post_type' => 'pmp_task',
                    'meta_key'  => '_pmp_task_status',
                    'meta_value'=> 'pending',
                    'fields' => 'ids',
                    'posts_per_page' => -1
                ]);
                echo esc_html(count($pending_tasks->posts));
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
                // 1. Get all running projects
                $running_projects = new WP_Query([
                    'post_type' => 'pmp_project',
                    'meta_key' => '_pmp_project_status',
                    'meta_value' => 'in_progress',
                    'posts_per_page' => -1,
                    'fields' => 'ids'
                ]);

                if (!empty($running_projects->posts)):
                    // 2. Fetch all tasks for these projects in one query
                    $all_tasks_query = new WP_Query([
                        'post_type' => 'pmp_task',
                        'posts_per_page' => -1,
                        'meta_query' => [
                            [
                                'key' => '_pmp_related_project',
                                'value' => $running_projects->posts,
                                'compare' => 'IN'
                            ]
                        ]
                    ]);

                    $tasks_by_project = [];
                    if ($all_tasks_query->have_posts()) {
                        while ($all_tasks_query->have_posts()) {
                            $all_tasks_query->the_post();
                            $proj_id = get_post_meta(get_the_ID(), '_pmp_related_project', true);
                            $tasks_by_project[$proj_id][] = [
                                'id' => get_the_ID(),
                                'status' => get_post_meta(get_the_ID(), '_pmp_task_status', true),
                                'priority' => get_post_meta(get_the_ID(), '_pmp_task_priority', true),
                                'due_date' => get_post_meta(get_the_ID(), '_pmp_task_due_date', true),
                                'assigned' => get_post_meta(get_the_ID(), '_pmp_task_assigned', true),
                                'title' => get_the_title()
                            ];
                        }
                        wp_reset_postdata();
                    }

                    foreach ($running_projects->posts as $proj_id):
                        $proj_status = get_post_meta($proj_id,'_pmp_project_status',true);
                        $proj_priority = get_post_meta($proj_id,'_pmp_project_priority',true);
                        $proj_manager_id = get_post_meta($proj_id,'_pmp_project_assigned',true);
                        $proj_manager = $proj_manager_id ? get_userdata($proj_manager_id) : null;
                        $proj_tasks = $tasks_by_project[$proj_id] ?? [];

                        // Calculate counts
                        $total = count($proj_tasks);
                        $completed = count(array_filter($proj_tasks, fn($t)=> $t['status']==='completed'));
                        $running = count(array_filter($proj_tasks, fn($t)=> $t['status']==='in_progress'));
                        $pending = count(array_filter($proj_tasks, fn($t)=> $t['status']==='pending'));
                        $percent = $total > 0 ? round(($completed/$total)*100) : 0;
                        $due_date = get_post_meta($proj_id,'_pmp_project_due_date',true);
                ?>
                        <tr style="background:#f9f9f9;">
                            <td style="padding:10px; border:1px solid #ddd;">
                                <strong><?php echo esc_html(get_the_title($proj_id)); ?></strong>
                                <span style="font-size:12px; color:#555; margin-left:8px;">(<?php echo esc_html($completed).' / '.esc_html($total); ?>)</span>
                            </td>
                            <td style="padding:10px; border:1px solid #ddd;">
                                <?php echo $proj_manager ? esc_html($proj_manager->display_name) : '—'; ?>
                            </td>
                            <td style="padding:10px; border:1px solid #ddd;">
                                <span class="pmp-badge pmp-status-<?php echo esc_attr($proj_status); ?>"><?php echo esc_html(ucfirst($proj_status)); ?></span>
                            </td>
                            <td style="padding:10px; border:1px solid #ddd;">
                                <span class="pmp-badge pmp-priority-<?php echo esc_attr($proj_priority); ?>"><?php echo esc_html(ucfirst($proj_priority)); ?></span>
                            </td>
                            <td style="padding:10px; border:1px solid #ddd; width:220px;">
                                <div style="background:#ddd; border-radius:6px; overflow:hidden; height:20px;">
                                    <div class="pmp-progress-bar" data-percent="<?php echo esc_attr($percent); ?>" style="width:<?php echo esc_attr($percent); ?>%; height:100%; background:#4caf50; text-align:center; color:white;"><?php echo esc_html($percent); ?>%</div>
                                </div>
                            </td>
                            <td style="padding:10px; border:1px solid #ddd;">
                                <?php echo esc_html($running).' / '.esc_html($completed).' / '.esc_html($pending); ?>
                            </td>
                            <td style="padding:10px; border:1px solid #ddd;">
                                <?php if($due_date): ?>
                                    <span class="pmp-countdown" data-due="<?php echo esc_attr($due_date); ?>" data-status="<?php echo esc_attr($proj_status); ?>"></span>
                                <?php else: ?> — <?php endif; ?>
                            </td>
                        </tr>

                        <?php if ($proj_tasks): ?>
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
                                            <?php foreach ($proj_tasks as $task): 
                                                $assigned_user = $task['assigned'] ? get_userdata($task['assigned']) : null;
                                            ?>
                                                <tr style="background:#fff;">
                                                    <td style="padding:6px; border:1px solid #ddd;"><?php echo esc_html($task['title']); ?></td>
                                                    <td style="padding:6px; border:1px solid #ddd;"><?php echo $assigned_user ? esc_html($assigned_user->display_name) : '—'; ?></td>
                                                    <td style="padding:6px; border:1px solid #ddd;">
                                                        <span class="pmp-badge pmp-status-<?php echo esc_attr($task['status']); ?>"><?php echo esc_html(ucfirst($task['status'])); ?></span>
                                                    </td>
                                                    <td style="padding:6px; border:1px solid #ddd;">
                                                        <span class="pmp-badge pmp-priority-<?php echo esc_attr($task['priority']); ?>"><?php echo esc_html(ucfirst($task['priority'])); ?></span>
                                                    </td>
                                                    <td style="padding:6px; border:1px solid #ddd;"><?php echo $task['due_date'] ? esc_html($task['due_date']) : '—'; ?></td>
                                                    <td style="padding:6px; border:1px solid #ddd;">
                                                        <?php if($task['due_date']): ?>
                                                            <span class="pmp-countdown" data-due="<?php echo esc_attr($task['due_date']); ?>" data-status="<?php echo esc_attr($task['status']); ?>"></span>
                                                        <?php else: ?> — <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        <?php endif; ?>

                    <?php endforeach; 
                else:
                    echo '<tr><td colspan="7" style="padding:8px;">No running projects found.</td></tr>';
                endif; ?>
            </tbody>
        </table>
    </div>
</div>
