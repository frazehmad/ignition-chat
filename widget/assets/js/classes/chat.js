ignitionChat.chat = function(){

    // Properties
    var props = {
        
    };

    var msgObj = $('#input');

    // Construct
    var init = function(){
        _bindChatActions();
    };

    // Bind Actions
    var _bindChatActions = function(){

        $("#input").bind( 'keydown', function(e) {
            if ((e.keyCode || e.charCode) !== 13) return true;
            $("#btnsend").click();
            return false;
        });

        $('#endChat').bind('click', function(){
            ignitionChat.pubnubCls.admin.unsubscribe();
        });

        $('#notAvailable').bind('click', function(e){
            var status = (($(e.currentTarget).is(":checked")) ? '0' : '1');
            ignitionChat.pubnubCls.admin.changeStatus(status);
        });

        $('#language').bind('change', function(e){
            console.log('b :' + b);
            if(b == '' || b == null)
            {
                
            }
            else
            {
                var lang = $(e.currentTarget).val();
                ignitionChat.pubnubCls.admin.changeLanguage(lang);
            }
        });
    };

    var appendMessage = function(msg, sender){
        
        var templateID = '#notification-template';

        if(sender == 'Support') 
        {
            templateID = '#receive-template';
        } 
        else if(sender == 'Customer') 
        {
            templateID = '#sent-template';
        }

        // Generate Random ID
        var randomID = 'msg_' + new Date().getTime();

        var source   = $(templateID).html();
        var template = Handlebars.compile(source);

        var context = {message: msg, who: sender, id: randomID}
        var html    = template(context);

        $( ".msg_container_base" ).append( html );

        // Scroll to last message
        $(".msg_container_base").animate({ scrollTop: $('.msg_container_base').prop('scrollHeight') }, "slow");

        // Clear message and focus on inout again
        msgObj.val('');
        msgObj.focus();
    };

    return {
        init : init,
        appendMessage : appendMessage
    };

}();