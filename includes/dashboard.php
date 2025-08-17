<?php
if (!defined('ABSPATH')) exit;

function wppm_render_dashboard() {
    $projects = wp_count_posts('wppm_project');
    $tasks    = wp_count_posts('wppm_task');

    $tasks_due = new WP_Query(array(
        'post_type' => 'wppm_task',
        'meta_query' => array(
            array(
                'key' => '_wppm_due_date',
                'value' => date('Y-m-d'),
                'compare' => '<=',
                'type' => 'DATE'
            )
        ),
        'posts_per_page' => 5
    ));
    ?>
    <div class="wrap">
        <h1>WP Project Manager Dashboard</h1>
        <p>Quick overview of projects and tasks.</p>

        <h2>Summary</h2>
        <ul>
            <li><strong>Projects:</strong> <?php echo $projects->publish; ?></li>
            <li><strong>Tasks:</strong> <?php echo $tasks->publish; ?></li>
        </ul>

        <h2>Upcoming / Overdue Tasks</h2>
        <ul>
            <?php if ($tasks_due->have_posts()) : 
                while ($tasks_due->have_posts()) : $tasks_due->the_post(); ?>
                    <li><?php the_title(); ?> - Due: <?php echo esc_html(get_post_meta(get_the_ID(), '_wppm_due_date', true)); ?></li>
                <?php endwhile; wp_reset_postdata();
            else : ?>
                <li>No tasks due soon.</li>
            <?php endif; ?>
        </ul>
    </div>
    <?php
}
