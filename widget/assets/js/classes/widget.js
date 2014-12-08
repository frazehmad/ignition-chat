ignitionChat.widget = function(){

    // Properties
    var props = {
        iframeDivHeightOnVisible : '415px',
        iframeDivHeightOnHidden : '50px',
        iframeHeightOnVisible : '415px',
        iframeHeightOnHidden : '50px'
    };

    // Construct
    var init = function(){
        _bindWidgetActions();
    };

    //
    var _bindWidgetActions = function(){
        $('.chat-window').on('click', '.panel-heading span.icon_minim', function (e) {
            _chatWindowToggle(e);
        });
    };

    // Show/Hide widget
    var _chatWindowToggle = function(e){
        //var iframe = $('#igcIframe', window.parent.document);
        //var iframeDiv = $('#_igc_iframe_container', window.parent.document);

        var $this = $(e.currentTarget);
        if (!$this.hasClass('panel-collapsed')) 
        {
            $this.addClass('panel-collapsed');
            $this.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');

            $this.parents('.panel').find('.panel-body').slideUp("fast", function()
            {
                $this.parents('.panel').find('.panel-footer').slideUp('fast', function(){
                    //iframe.css('height', props.iframeHeightOnHidden);
                    //iframeDiv.css('height', props.iframeDivHeightOnHidden);
                });
            });
        } 
        else 
        {
            $this.parents('.panel').find('.panel-body').slideDown();
            $this.removeClass('panel-collapsed');
            $this.removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');

            $this.parents('.panel').find('.panel-footer').slideDown();

            //iframe.css('height', props.iframeHeightOnVisible);
            //iframeDiv.css('height', props.iframeDivHeightOnVisible);

            $('#input').focus();
        }
    };

    return {
        init : init
    };

}();