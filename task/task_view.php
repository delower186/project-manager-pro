<?php
/**
 * =============================
 * AJAX: Quick View Modal (Tasks)
 * =============================
 */
add_action('wp_ajax_wppm_task_quick_view', function() {
    // Sanitize and validate nonce
    $nonce = isset($_POST['wppm_nonce']) ? sanitize_text_field(wp_unslash($_POST['wppm_nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'wppm_action')) {
        wp_send_json_error(['message' => 'Security check failed'], 400);
    }

    // Sanitize post_id
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id) wp_send_json_error(['message' => 'Invalid task ID'], 400);

    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'wppm_task') {
        wp_send_json_error(['message' => 'Task not found'], 404);
    }

    // Meta fields
    $status       = get_post_meta($post_id, '_wppm_task_status', true) ?: 'Pending';
    $priority     = get_post_meta($post_id, '_wppm_task_priority', true) ?: 'Normal';
    $due_date     = get_post_meta($post_id, '_wppm_task_due_date', true) ?: '—';
    $assignee_id  = get_post_meta($post_id, '_wppm_task_assigned', true);
    $assignee_name= $assignee_id ? get_the_author_meta('display_name', $assignee_id) : '—';
    $content      = apply_filters('the_content', $post->post_content);

    // Deadline calculation
    $time_left = '—';
    if ($due_date && $due_date !== '—') {
        $now = new DateTime();
        $deadline_dt = new DateTime($due_date);
        $interval = $now->diff($deadline_dt);
        $time_left = ($now > $deadline_dt) ? 'Deadline passed' : $interval->days.'d '.$interval->h.'h '.$interval->i.'m left';
    }

    if ($status === 'completed') $time_left = "🎉";
    elseif ($status === 'cancelled') $time_left = "😢";

    ob_start(); ?>
    <div>
        <h2><?php echo esc_html($post->post_title); ?></h2>
        <p><strong>Assignee:</strong> <?php echo esc_html($assignee_name); ?></p>
        <p><strong>Status:</strong> <?php echo esc_html(ucfirst($status)); ?></p>
        <p><strong>Priority:</strong> <?php echo esc_html(ucfirst($priority)); ?></p>
        <p><strong>Deadline:</strong> <?php echo esc_html($due_date); ?></p>
        <p><strong>Time Left:</strong> <?php echo esc_html($time_left); ?></p>
        <div><?php echo wp_kses_post($content); ?></div>

        <div id="wppm-task-comments">
            <h3>Comments</h3>
            <?php
            $comments = get_comments(['post_id' => $post_id]);
            foreach ($comments as $comment) {
                echo '<div class="wppm-comment" id="comment-' . esc_attr($comment->comment_ID) . '">';
                echo '<strong>' . esc_html($comment->comment_author) . ':</strong> ';
                echo '<p>' . esc_html($comment->comment_content) . '</p>';
                echo '</div>';
            }
            ?>
            <form id="wppm-task-comment-form">
                <textarea name="comment" rows="3" style="width:100%;" required></textarea>
                <input type="hidden" name="action" value="wppm_task_add_comment" />
                <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>" />
                <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('wppm_action')); ?>" />
                <button type="submit" class="button button-primary">Post Comment</button>
            </form>
        </div>
    </div>
    <?php
    wp_send_json_success(ob_get_clean());
});


/**
 * =============================
 * Admin Footer JS (Tasks list screen)
 * =============================
 */
add_action('admin_footer-edit.php', function() {
    $screen = get_current_screen();
    if ($screen->post_type !== 'wppm_task') return;
    ?>
    <script>
    jQuery(document).ready(function($){
        // Add clickable class to rows
        $('#the-list tr').each(function(){
            var post_id = $(this).attr('id');
            if(post_id){
                post_id = post_id.replace('post-', '');
                $(this).attr('data-post-id', post_id).addClass('wppm-task-clickable');
            }
        });

        // Quick view modal
        $('#the-list').on('click', 'tr.wppm-task-clickable', function(e){
            if($(e.target).closest('th, td:first-child, td:nth-child(2)').length) return;

            var post_id = $(this).data('post-id');
            if(!post_id) return;

            $.post(ajaxurl, {
                action: 'wppm_task_quick_view',
                post_id: post_id,
                wppm_nonce: '<?php echo wp_create_nonce('wppm_action'); ?>'
            }, function(response){
                if(response.success){
                    $('<div class="wppm-modal"></div>').html(response.data).dialog({
                        modal: true,
                        width: 700,
                        title: 'Task Details',
                        open: function () {
                            var maxH = Math.floor(window.innerHeight * 0.8);
                            $(this).css({ maxHeight: maxH + 'px', overflowY: 'auto' });
                        },
                        close: function() { $(this).dialog('destroy').remove(); }
                    });
                } else {
                    alert('Failed to load task details.');
                }
            });
        });

        // Handle AJAX comment submit
        $(document).on('submit', '#wppm-task-comment-form', function(e){
            e.preventDefault();
            var form = $(this);
            $.post(ajaxurl, form.serialize(), function(response){
                if(response.success){
                    $('#wppm-task-comments h3').after(response.data.html);
                    form[0].reset();
                } else {
                    alert(response.data);
                }
            });
        });
    });
    </script>
    <?php
});


/**
 * =============================
 * AJAX: Add Task Comment
 * =============================
 */
add_action('wp_ajax_wppm_task_add_comment', function() {
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'wppm_action')) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id) wp_send_json_error(['message' => 'Invalid task ID']);

    $comment_content = isset($_POST['comment']) ? sanitize_textarea_field(wp_unslash($_POST['comment'])) : '';
    if (!$comment_content) wp_send_json_error(['message' => 'Comment cannot be empty']);

    $current_user = wp_get_current_user();
    $comment_id = wp_new_comment([
        'comment_post_ID'      => $post_id,
        'comment_content'      => $comment_content,
        'user_id'              => $current_user->ID,
        'comment_author'       => $current_user->display_name,
        'comment_author_email' => $current_user->user_email,
        'comment_approved'     => 1,
    ]);

    if ($comment_id) {
        $comment = get_comment($comment_id);
        ob_start();
        ?>
        <div class="wppm-comment" id="comment-<?php echo esc_attr($comment->comment_ID); ?>">
            <strong><?php echo esc_html($comment->comment_author); ?>:</strong>
            <p><?php echo esc_html($comment->comment_content); ?></p>
        </div>
        <?php
        wp_send_json_success(['html' => ob_get_clean()]);
    } else {
        wp_send_json_error(['message' => 'Failed to add comment']);
    }
});


/**
 * =============================
 * Disable moderation for wppm_task CPT comments
 * =============================
 */
add_filter('pre_comment_approved', function($approved, $commentdata) {
    if (isset($commentdata['comment_post_ID'])) {
        $post_type = get_post_type($commentdata['comment_post_ID']);
        if ($post_type === 'wppm_task') {
            return 1;
        }
    }
    return $approved;
}, 10, 2);
