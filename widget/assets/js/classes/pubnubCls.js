ignitionChat.pubnubCls = function(){

    // Properties
    var props = {
        translate_api_key : 'AIzaSyBkXpqD13q3J2mHqxhQ78cO86Sm2imnD-k'
    };

    var obj;

    var box;
    var chat = ignitionChat.chat;

    // Construct
    var init = function(){
        
        // init pubnub
        obj = PUBNUB.init({
            publish_key: 'pub-c-610bda8c-a1ac-4794-8daa-f2519991e59b',
            subscribe_key: 'sub-c-095cdb0a-7bee-11e4-8ce0-02ee2ddab7fe',
            uuid: uuid
        });

        //box = document.getElementById('msg_container_base');
    };

    var translate = function(srcLang, targetLang, text, sender){
        var jsonData = jQuery.ajax({
            dataType: 'text',
            type: 'get',
            url: 'https://www.googleapis.com/language/translate/v2?key='+props.translate_api_key+'&source='+srcLang+'&target='+targetLang+'&q='+encodeURIComponent(text)
        }).done(
            function(data) {
                var json = $.parseJSON(data);
                text = json.data.translations[0].translatedText;

                chat.appendMessage(text, sender);
            }
        ).fail(
            function(data) {
                chat.appendMessage('Unable to Translate: ' + text, sender);
            }
        );
    };

    // subscribe
    var subscribe = function(who_is) {

        console.log(who_is);

        var child;

        if(who_is == 'customer') var child = ignitionChat.pubnubCls.customer;
        if(who_is == 'admin') var child = ignitionChat.pubnubCls.admin;

        PUBNUB.subscribe({
            channel : channel,
            message : child.chat_receive,
            connect: child.publish,
            disconnect: disconnect_message,
            reconnect: reconnect_message,
            presence: child.presence_message,
            error: error_message,
            heartbeat: 10,
            state: { type: uuid },
            restore: true
        });
    };
        
    // disconnection message
    var disconnect_message = function() {
        chat.appendMessage("You've been disconnected from chat. Re-connecting...");
    };
        
    // re-connection message
    var reconnect_message = function() {
        chat.appendMessage("You've been re-connected.");
    };
        
    // error message
    var error_message = function(m) {
        chat.appendMessage(m.message);
    };

    var publish = function(sender, language){

        if(typeof(language) == 'undefined')
        {
            language = 'en';
        }

        var msg = $('#input').val();

        if(msg && msg != '') {
            PUBNUB.publish({
                channel : channel,
                message : { user: sender, text: input.value, code: 0, language: language },
                x       : (input.value = '')
            });
        }
    };

    return {
        init : init,
        box : box,
        obj : obj,
        translate: translate,
        subscribe : subscribe,
        error_message : error_message,
        publish : publish
    };

}();