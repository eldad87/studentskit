<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sivan
 * Date: 10/9/12
 * Time: 2:54 PM
 * To change this template use File | Settings | File Templates.
 */

class WatchitooHelper extends AppHelper {

    public function __construct() {

        Configure::load('watchitoo');

    }

    public function initJS( $meetingId, $initParams=array() ) {
        return '


        function OnWatchitooPlayerEvent(playerID,eventName,eventParams) {

            switch(eventName) {
                case "ready":
                    Init();
                    break;
                case "error":
                    switch(eventParams.errorCode) {
                        case 1003:
                            alert("Invalid vendorID/accessToken Credentials!");
                            break;

                        default:
                            alert(eventParams);
                            break;

                    }
                    break;
            }
        }

        function Init() {
            var initializeParams = {
                userID: "'.$initParams['user_id'].'",
                watchitooUserID : "'.$initParams['watchitoo_user_id'].'",
                displayName : "'.$initParams['display_name'].'",
                isModerator : '.$initParams['is_moderator'].'
            };

            var ws=document.getElementById("WatchitooPlayer");

            var result=ws.Initialize('.Configure::read('Watchitoo.vendorId').', '.Configure::read('Watchitoo.accessToken').', '.$meetingId.', initializeParams );
            if(result!=null&&result.result)
            {
                if(result.result=="fail"&&result.errorString)
                    alert(result.errorString);
            }

        }

';
    }

    public function embedMeetingJS() {
        return '<script type="text/javascript" src="http://www.watchitoo.com/swf/watchitooShowEmbed.php?playerid=WatchitooPlayer&tp=1&scale=false&width=960&height=530&layout=11"></script>';
    }
}