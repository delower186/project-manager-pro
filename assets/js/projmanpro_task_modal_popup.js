        jQuery(document).ready(function($){
            // Add clickable class to rows
            $('#the-list tr').each(function(){
                var post_id = $(this).attr('id');
                if(post_id){
                    post_id = post_id.replace('post-', '');
                    $(this).attr('data-post-id', post_id).addClass('pmp-task-clickable');
                }
            });

            // Quick view modal
            $('#the-list').on('click', 'tr.pmp-task-clickable', function(e){
                if($(e.target).closest('th, td:first-child, td:nth-child(2)').length) return;

                var post_id = $(this).data('post-id');
                if(!post_id) return;

                $.post(ajaxurl, {
                    action: 'projmanpro_task_quick_view',
                    post_id: post_id,
                    projmanpro_nonce: projmanpro_nonce
                }, function(response){
                    if(response.success){
                        $('<div class="pmp-modal"></div>').html(response.data).dialog({
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
            $(document).on('submit', '#pmp-task-comment-form', function(e){
                e.preventDefault();
                var form = $(this);
                $.post(ajaxurl, form.serialize(), function(response){
                    if(response.success){
                        $('#pmp-task-comments h3').after(response.data.html);
                        form[0].reset();
                    } else {
                        alert(response.data);
                    }
                });
            });
        });