ignitionChat.pubnubCls.customer = function(){

    // Properties
    var props = {

    };

    var pubnubCls = ignitionChat.pubnubCls;
    var box = pubnubCls.box;
    var chat = ignitionChat.chat;

    // Construct
    var init = function(){
        where_now();
    };

    // user channels
    var where_now = function() {
        PUBNUB.where_now({
            callback: check_status,
            error: pubnubCls.error_message
        });
    };
    
    // check user status
    var check_status = function(m) {
        var result = '';
        for (var p in m) {
            if(m.hasOwnProperty(p)) {
                if (result != '')
                    result += ',';
                result += m[p];
            }
        }
         
        if (result.indexOf(channel) >= 0) {
            // user already chatting with the support agent.
            pubnubCls.subscribe('customer');
        }
        else {
            // check if the support agent is free
            here_now();
        }
    };
    
    // number of users in the channel
    var here_now = function() {
        PUBNUB.here_now({
            channel: channel,
            callback: allow_chat,
            state: true,
            error: pubnubCls.error_message
        });
    };
    
    // start chat
    var allow_chat = function(m) {
        // is support person available?
        var support = 0;
        for (p in m.uuids) {
            if(m.uuids.hasOwnProperty(p)) {
                if (m.uuids[p].state.type == 'support')
                    support = 1;
            }
        }
        
        if (support == 0) {
            $('.msg_container_base').html('Support agent is not available. Click <a href="javascript:this.location = \'\';">here</a> to refresh.');
        }
        else if (m.occupancy == 2) {
            $('.msg_container_base').html('Support agent is busy. Click <a href="javascript:this.location = \'\';">here</a> to refresh.');
        }
        else {
            // connect to channel
            pubnubCls.subscribe('customer');
        }                   
    };
    
    // publish
    var publish = function() {

        PUBNUB.bind('click', btnsend, function(e) {
            e.preventDefault();

            var targetLanguage = $('#language').val();
            
            pubnubCls.publish('Customer', targetLanguage);
        });
    };
    
    // receive chat
    var chat_receive = function(m) {

        var text = '';
        var sender = '';
        
        if (m.code == 1) {
            PUBNUB.unsubscribe({
                channel: channel
            });
                            
            text = m.text;
        }
        else {
            if (m.user == 'Customer')
                sender = 'Customer';
            else if (m.user == 'Support')
                sender = 'Support'

            text = m.text;
        }

        var targetLanguage = $('#language').val();

        if ((m.user == 'Support' || m.user == '') && m.language != targetLanguage)  {
            // Google Translate
            pubnubCls.translate(m.language, targetLanguage, text, sender);
        }
        else
        {
            chat.appendMessage(text, sender);
        }
    };

    // presence message
    var presence_message = function(m) {                   
        if (m.action == 'timeout' && m.uuid == 'support') {
            if (status_message == 'Support has left the chat.')
                return;
                
            status_message = 'Support has left the chat.';
            chat.appendMessage(status_message);
        }
        if (m.action == 'join' && m.uuid == 'support') {
            if (status_message == 'Support has joined the chat.')
                return;
                
            status_message = 'Support has joined the chat.';
            chat.appendMessage(status_message);
        }
    };

    return {
        init : init,
        publish : publish,
        chat_receive : chat_receive,
        presence_message : presence_message
    };

}();