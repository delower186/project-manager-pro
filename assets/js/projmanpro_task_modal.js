jQuery(document).ready(function($){

    // Open modal on task row click
    $(document).on('click', 'tr.pmp-task-clickable', function(e){
        if($(e.target).closest('th, td:first-child, td:nth-child(2)').length) return;

        var post_id = $(this).data('post-id');
        if(!post_id) return;

        var $modal = $('#pmp-task-single-modal');

        // Close previous dialog and unbind previous handlers
        if($modal.hasClass('ui-dialog-content')) {
            $modal.dialog('close');
            $modal.off('submit', '#pmp-task-comment-form'); // remove previous submit
        }

        $modal.html('Loading...');

        $.post(ajaxurl, {
            action: 'projmanpro_task_quick_view',
            post_id: post_id,
            projmanpro_nonce: projmanpro_ajax.nonce
        }, function(response){
            if(response.success){
                $modal.html(response.data);
                $modal.dialog({
                    modal: true,
                    width: 700,
                    title: 'Task Details',
                    close: function(){
                        $modal.html('');
                        $modal.off('submit', '#pmp-task-comment-form'); // remove handler on close
                    }
                });

                // Attach submit handler once per modal open
                $modal.on('submit', '#pmp-task-comment-form', function(e){
                    e.preventDefault();
                    var $form = $(this);
                    var $container = $form.closest('#pmp-task-comments');

                    $.post(ajaxurl, $form.serialize(), function(resp){
                        if(resp.success && resp.data.html){
                            $container.append(resp.data.html); // append new comment
                            $form[0].reset();
                        } else {
                            alert(resp.data && resp.data.message ? resp.data.message : 'Failed to add comment');
                        }
                    });
                });

            } else {
                alert(response.data && response.data.message ? response.data.message : 'Failed to load task details.');
            }
        });
    });

});
