ignitionChat.pubnubCls.admin = function(){

    // Properties
    var props = {

    };

    var pubnubCls = ignitionChat.pubnubCls;
    var box = pubnubCls.box;
    var chat = ignitionChat.chat;

    // Construct
    var init = function(){
        pubnubCls.subscribe('support');
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
            if (m.user == 'Support')
                sender = 'Support'
            else if (m.user == 'Customer')
                sender = 'Customer';

            text = m.text;     
        }

        var targetLanguage = $('#language').val();

        if ((m.user == 'Customer' || m.user == '') && m.language != targetLanguage) {
            // Google Translate
            pubnubCls.translate(m.language, targetLanguage, text, sender);
        }
        else
        {
            chat.appendMessage(text, sender);
        }
    };
    
    // publish
    var publish = function() {

        PUBNUB.bind('click', btnsend, function(e) {
            e.preventDefault();

            var targetLanguage = $('#language').val();
            
            pubnubCls.publish('Support', targetLanguage);
        });
    };
                    
    // end chat and unsubscribe all parties
    var unsubscribe = function() {
        
        if(channel == 'demo') { return; }

        var jsonData = jQuery.ajax({
            data: { b: b, unsubscribe: 1 },
            type: 'get',
            url: BASE_URL,
            async: false
        }).done(
            function() {
                PUBNUB.publish({
                    channel : channel,
                    message : { user: 'Support', text: 'Support ended the chat.', code: 1, language: 'en' },
                    x       : (input.value = '')
                });
            }
        );
    };

    // change admin status
    var changeStatus = function(status) {
        var jsonData = jQuery.ajax({
            data: { b: b, change_status: status },
            type: 'get',
            url: BASE_URL
        }).done(
            function(data) {
                PUBNUB.publish({
                    channel : channel,
                    message : { user: '', text: data, code: 0, language: 'en' },
                    x       : (input.value = '')
                });
            }
        );
    };
    
    // change admin language
    var changeLanguage = function(language) {
        var jsonData = jQuery.ajax({
            data: { b: b, change_language: language },
            type: 'get',
            url: BASE_URL
        });
    };
    
    // presence message
    var presence_message = function(m) {
        console.log(m); 
        if (m.action == 'timeout' && m.uuid == 'customer') {
            if (status_message == 'Customer has left the chat.')
                return;
            
            status_message = 'Customer has left the chat.'; 
            chat.appendMessage(status_message);
        }
        if (m.action == 'join' && m.uuid == 'customer') {
            if (status_message == 'Customer has joined the chat.')
                return;
            
            status_message = 'Customer has joined the chat.';
            chat.appendMessage(status_message);
        }
    };

    return {
        init : init,
        publish : publish,
        chat_receive : chat_receive,
        unsubscribe : unsubscribe,
        changeStatus : changeStatus,
        changeLanguage : changeLanguage,
        presence_message : presence_message
    };

}();