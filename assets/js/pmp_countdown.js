jQuery(document).ready(function($){
    function updateCountdown($elem) {
        var status = $elem.data('status');
        if(status === 'completed'){
            $elem.text('✅ Completed').css('color','green');
            return; // stop countdown
        }

        var due = new Date($elem.data('due'));
        var now = new Date();
        var diff = due - now;

        if(diff <= 0){
            $elem.text('⏰ Expired').css('color', 'red');
            return;
        }

        var days = Math.floor(diff / (1000*60*60*24));
        var hours = Math.floor((diff/(1000*60*60)) % 24);
        var minutes = Math.floor((diff/(1000*60)) % 60);
        var seconds = Math.floor((diff/1000) % 60);

        $elem.text(days+'d '+hours+'h '+minutes+'m '+seconds+'s');

        var totalHours = diff / (1000*60*60);
        if(totalHours <= 24) $elem.css({'background':'#ff9800','color':'#fff','padding':'2px 6px','border-radius':'4px'});
        if(totalHours <= 1) $elem.css({'background':'#f44336','color':'#fff','padding':'2px 6px','border-radius':'4px'});
    }

    $('.pmp-countdown').each(function(){
        var $this = $(this);
        updateCountdown($this);
        setInterval(function(){ updateCountdown($this); }, 1000);
    });
});

