<?php
  // db info
  include('db.inc.php');

  $langs = '{"data":{"languages":[{"language":"af","name":"Afrikaans"},{"language":"sq","name":"Albanian"},{"language":"ar","name":"Arabic"},{"language":"hy","name":"Armenian"},{"language":"az","name":"Azerbaijani"},{"language":"eu","name":"Basque"},{"language":"be","name":"Belarusian"},{"language":"bn","name":"Bengali"},{"language":"bs","name":"Bosnian"},{"language":"bg","name":"Bulgarian"},{"language":"ca","name":"Catalan"},{"language":"ceb","name":"Cebuano"},{"language":"zh","name":"Chinese (Simplified)"},{"language":"zh-TW","name":"Chinese (Traditional)"},{"language":"hr","name":"Croatian"},{"language":"cs","name":"Czech"},{"language":"da","name":"Danish"},{"language":"nl","name":"Dutch"},{"language":"en","name":"English"},{"language":"eo","name":"Esperanto"},{"language":"et","name":"Estonian"},{"language":"tl","name":"Filipino"},{"language":"fi","name":"Finnish"},{"language":"fr","name":"French"},{"language":"gl","name":"Galician"},{"language":"ka","name":"Georgian"},{"language":"de","name":"German"},{"language":"el","name":"Greek"},{"language":"gu","name":"Gujarati"},{"language":"ht","name":"Haitian Creole"},{"language":"ha","name":"Hausa"},{"language":"iw","name":"Hebrew"},{"language":"hi","name":"Hindi"},{"language":"hmn","name":"Hmong"},{"language":"hu","name":"Hungarian"},{"language":"is","name":"Icelandic"},{"language":"ig","name":"Igbo"},{"language":"id","name":"Indonesian"},{"language":"ga","name":"Irish"},{"language":"it","name":"Italian"},{"language":"ja","name":"Japanese"},{"language":"jw","name":"Javanese"},{"language":"kn","name":"Kannada"},{"language":"km","name":"Khmer"},{"language":"ko","name":"Korean"},{"language":"lo","name":"Lao"},{"language":"la","name":"Latin"},{"language":"lv","name":"Latvian"},{"language":"lt","name":"Lithuanian"},{"language":"mk","name":"Macedonian"},{"language":"ms","name":"Malay"},{"language":"mt","name":"Maltese"},{"language":"mi","name":"Maori"},{"language":"mr","name":"Marathi"},{"language":"mn","name":"Mongolian"},{"language":"ne","name":"Nepali"},{"language":"no","name":"Norwegian"},{"language":"fa","name":"Persian"},{"language":"pl","name":"Polish"},{"language":"pt","name":"Portuguese"},{"language":"pa","name":"Punjabi"},{"language":"ro","name":"Romanian"},{"language":"ru","name":"Russian"},{"language":"sr","name":"Serbian"},{"language":"sk","name":"Slovak"},{"language":"sl","name":"Slovenian"},{"language":"so","name":"Somali"},{"language":"es","name":"Spanish"},{"language":"sw","name":"Swahili"},{"language":"sv","name":"Swedish"},{"language":"ta","name":"Tamil"},{"language":"te","name":"Telugu"},{"language":"th","name":"Thai"},{"language":"tr","name":"Turkish"},{"language":"uk","name":"Ukrainian"},{"language":"ur","name":"Urdu"},{"language":"vi","name":"Vietnamese"},{"language":"cy","name":"Welsh"},{"language":"yi","name":"Yiddish"},{"language":"yo","name":"Yoruba"},{"language":"zu","name":"Zulu"}]}}';
  $langs = json_decode($langs);

  // variable initialization
  $error_message = '';
  $lang_code = 'en';
    
  // iframe parameters for the frontend
  if (isset($_GET['f'])) {
    $f = $_GET['f'];
    
    try {
      // check if the support agent exists
      $stmt = $db->prepare("SELECT * FROM users WHERE front_password=?");
      $stmt->execute(array($f));
            
      if (!$stmt->rowCount()) {
        $error_message = 'User doesn\'t exist';
      }
      else {
        // check if the support agent is online and is available
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $row['id'];        
        $stmt = $db->prepare("SELECT * FROM channels WHERE user_id = ? AND status = 1");
        $stmt->execute(array($user_id));
        
        if (!$stmt->rowCount()) {
          $error_message = 'Support agent is not available.';
        }
        else {
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $channel = $row['channel'];
        }
      }
    }
    catch (Exception $e) {
      $error_message = $e->getMessage();
    }
  }
  // iframe parameters for the backend
  else if (isset($_GET['b'])) {
    $b = $_GET['b'];
    
    try {
      // check if the support agent exists
      $stmt = $db->prepare("SELECT * FROM users WHERE back_password=?");
      $stmt->execute(array($b));
      
      if (!$stmt->rowCount()) {
        $error_message = 'User doesn\'t exist';
      }
      else {
        // check if unsubscribing
        if (isset($_GET['unsubscribe'])) {
          $stmt = $db->prepare("DELETE FROM channels WHERE user_id = (SELECT id FROM users WHERE back_password = ?)");
          $stmt->execute(array($b));
          exit;
        }
        
        // check if changing status
        if (isset($_GET['change_status'])) {
          $stmt = $db->prepare("UPDATE channels SET status = ? WHERE user_id = (SELECT id FROM users WHERE back_password = ?)");
          $stmt->execute(array($_GET['change_status'], $b));
          
          if ($_GET['change_status'])
            echo 'Support has come back.';
          else
            echo 'Support has gone away.';
          exit;
        }
        
        // check if changing language
        if (isset($_GET['change_language'])) {
          $stmt = $db->prepare("UPDATE users SET language = ? WHERE back_password = ?");
          $stmt->execute(array($_GET['change_language'], $b));
          exit;
        }
        
        // check if the support agent is online and is available
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $row['id'];
        $lang_code = $row['language'] ? $row['language'] : 'en';      
        $stmt = $db->prepare("SELECT * FROM channels WHERE user_id = ?");
        $stmt->execute(array($user_id));
        
        if (!$stmt->rowCount()) {
          $stmt = $db->prepare("INSERT INTO channels (user_id) VALUES (?)");
          $stmt->execute(array($user_id));
          $channel = uniqid();
          $db->query("UPDATE channels SET channel = '$channel' WHERE user_id = '$user_id'");
        }
        else {
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $channel = $row['channel'];
          $channel_status = $row['status'];
        }
      }
    }
    catch (Exception $e) {
      $error_message = $e->getMessage();
    }
  }
  else {
    $error_message = 'Invalid Parameters.';
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IgnitionChat</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-theme.min.css" rel="stylesheet">
    <link href="assets/css/widget.css" rel="stylesheet">

    <script>
    var channel = '<?php echo !empty($channel) ? $channel : NULL; ?>';
    var status_message = '';
    var uuid = '<?php if (isset($f)) echo 'customer'; else if (isset($b)) echo 'support'; ?>';
    var b = '<?php echo (!empty($b) ? $b : NULL); ?>';
    var BASE_URL = '<?php echo 'http://' . $_SERVER['SERVER_NAME'] . '/widget/'; ?>';
    </script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
        <div class="row chat-window col-xs-16 col-md-12" id="chat_window_1" style="margin-left:10px;">
            <div class="col-xs-12 col-md-12">
              <div class="panel panel-default">
                    <div class="panel-heading top-bar">
                        <div class="col-md-8 col-xs-8">
                            <h3 class="panel-title">Support </h3>
                        </div>
                        <div class="col-md-4 col-xs-4" style="text-align: right;">
                            <a href="#"><span id="minim_chat_window" class="glyphicon glyphicon-chevron-down icon_minim _panel-collapsed"></span></a>
                        </div>
                    </div>

                    <?php if(!empty($error_message)): ?>
                      
                      <div class="panel-body msg_container_base _hide-panel">
                        <div class="chat-error"><?php echo $error_message; ?></div>
                      </div>
                    
                    <?php else: ?>

                    <div class="panel-body msg_container_base _hide-panel">
                       
                    </div>

                    <div class="panel-footer _hide-panel">
                      <form class="form-inline" role="form" onsubmit="return false">
                        <div class="form-group">
                          <div class="input-group">
                              <select id="language">
                                <?php foreach ($langs->data->languages as $lang): ?>
                                  <option value="<?php echo $lang->language; ?>" <?php echo ($lang_code == $lang->language) ? 'selected="selected"' : NULL; ?>><?php echo $lang->name; ?></option>
                                <?php endforeach; ?>
                              </select> Your Language
                          </div>
                        </div>

                        <div class="input-group">
                            <input id="input" type="text" class="form-control input-sm chat_input" placeholder="Write your message here..." />
                            <span class="input-group-btn">
                            <button class="btn btn-primary btn-sm" id="btnsend">Send</button>
                            </span>
                        </div>

                        <?php if(!empty($b)): ?>
                          <br>
                          <div class="checkbox">
                            <label>
                              <input type="checkbox" id="notAvailable" <?php echo ((!$channel_status) ? 'checked="checked"' : NULL); ?> /> Not Available
                            </label>
                            <span class="pull-right"><button type="button" class="btn btn-danger btn-sm" id="endChat">End Chat</button></span>
                          </div>
                        <?php endif ?>
                      </form>
                    </div>

                  <?php endif; ?>

            </div>
          </div>
      </div>
  </div>

  <script id="sent-template" type="text/x-handlebars-template">
    <div class="row msg_container base_sent" id="{{id}}">
        <div class="col-md-10 col-xs-10">
            <div class="messages msg_sent">
                <p>{{message}}</p>
                <time>{{who}}</time>
            </div>
        </div>
        <div class="col-md-2 col-xs-2">&nbsp;</div>
    </div>
  </script>

  <script id="receive-template" type="text/x-handlebars-template">
    <div class="row msg_container base_receive" id="{{id}}">
        <div class="col-md-2 col-xs-2">&nbsp;</div>
        <div class="col-md-10 col-xs-10">
            <div class="messages msg_receive">
                <p>{{message}}</p>
                <time>{{who}}</time>
            </div>
        </div>
    </div>
  </script>

  <script id="notification-template" type="text/x-handlebars-template">
    <div class="row msg_container base_receive" id="{{id}}">
        <div class="col-md-12 col-xs-12">
            <div class="messages msg_notification">
                <p>{{message}}</p>
            </div>
        </div>
    </div>
  </script>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="http://cdn.pubnub.com/pubnub.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="assets/js/bootstrap.min.js"></script>

    <script src="assets/js/handlebars.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/classes/widget.js"></script>
    <script src="assets/js/classes/chat.js"></script>
    <script src="assets/js/classes/pubnubCls.js"></script>
    <?php if(empty($b)): ?>
      <script src="assets/js/classes/pubnubCls.customer.js"></script>
    <?php else: ?>
      <script src="assets/js/classes/pubnubCls.admin.js"></script>
    <?php endif; ?>

    <script>
      // Initialize the class
      (function(){
          ignitionChat.widget.init();
          ignitionChat.chat.init();
          ignitionChat.pubnubCls.init();

          <?php if(empty($b)): ?>
            <?php if(isset($channel_status) AND $channel_status == 1): ?>
              ignitionChat.pubnubCls.customer.init();
            <?php endif; ?>
          <?php else: ?>
            ignitionChat.pubnubCls.admin.init();
          <?php endif; ?>
      }());
    </script>
  </body>
</html>