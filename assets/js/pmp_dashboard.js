jQuery(document).ready(function($){
    // Function to update countdowns
    function updateCountdown($elem) {
        var due = new Date($elem.data('due'));
        var now = new Date();
        var diff = due - now;

        var status = $elem.data('status');
        var emoji = '⏳'; // Default running emoji

        if(diff <= 0){
            $elem.text('Expired ❌').css('color','red');
            return;
        }

        var days = Math.floor(diff / (1000*60*60*24));
        var hours = Math.floor((diff/(1000*60*60)) % 24);
        var minutes = Math.floor((diff/(1000*60)) % 60);
        var seconds = Math.floor((diff/1000) % 60);

        $elem.text(days+'d '+hours+'h '+minutes+'m '+seconds+'s');

        var totalHours = diff / (1000*60*60);

        if(totalHours <= 1){
            $elem.css('color','red');
            emoji = '⚠️';
        } else if(totalHours <= 24){
            $elem.css('color','orange');
            emoji = '⏳';
        } else {
            $elem.css('color','green');
            emoji = '✅';
        }

        $elem.prepend(emoji + ' ');
    }

    // Initialize countdowns
    $('.pmp-countdown').each(function(){
        var $this = $(this);
        updateCountdown($this);
        setInterval(function(){ updateCountdown($this); }, 1000);
    });

    // Animate progress bars
    $('.pmp-progress-bar').each(function(){
        var $bar = $(this);
        var percent = $bar.data('percent') || 0;
        $bar.css('width','0');
        $bar.animate({width: percent+'%'}, 1500);
    });

    // Color badges for status and priority
    $('.pmp-badge').each(function(){
        var $badge = $(this);
        var classes = $badge.attr('class').split(/\s+/);
        classes.forEach(function(cl){
            if(cl.indexOf('pmp-status-') === 0){
                switch(cl.replace('pmp-status-','')){
                    case 'in_progress': $badge.css({'background':'#2196f3','color':'#fff','padding':'2px 6px','border-radius':'4px'}); break;
                    case 'completed': $badge.css({'background':'#4caf50','color':'#fff','padding':'2px 6px','border-radius':'4px'}); break;
                    case 'pending': $badge.css({'background':'#ff9800','color':'#fff','padding':'2px 6px','border-radius':'4px'}); break;
                    default: $badge.css({'background':'#777','color':'#fff','padding':'2px 6px','border-radius':'4px'});
                }
            }
            if(cl.indexOf('pmp-priority-') === 0){
                switch(cl.replace('pmp-priority-','')){
                    case 'high': $badge.css({'background':'#f44336','color':'#fff','padding':'2px 6px','border-radius':'4px'}); break;
                    case 'medium': $badge.css({'background':'#ff9800','color':'#fff','padding':'2px 6px','border-radius':'4px'}); break;
                    case 'low': $badge.css({'background':'#4caf50','color':'#fff','padding':'2px 6px','border-radius':'4px'}); break;
                    default: $badge.css({'background':'#777','color':'#fff','padding':'2px 6px','border-radius':'4px'});
                }
            }
        });
    });
});
