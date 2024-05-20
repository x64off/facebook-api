<?php
namespace x64off\FacebookApi\Webhooks;

use x64off\FacebookApi\Application;

class Messages{
    public static function getMessage($input = null ,$callback = null){
        //
        $input = $input ?: json_decode(file_get_contents('php://input'), true);
        //
        //
        if (!empty($input['entry'][0]['messaging'])) {
            foreach ($input['entry'][0]['messaging'] as $event) {
                if (!empty($event['message'])) {
                    if (Application::getOption('DEBUG')){
                        Application::log(null, json_encode(["type"=>"message", "sender" => $event['sender']['id'], "text" => $event['message']['text'] , "timestamp" => $event['timestamp']]));
                    }
                    if($callback){
                        return $callback( $event['sender']['id'], $event['message']['text'], $event['timestamp']);
                    }else{
                        return [ "sender" => $event['sender']['id'], "text" => $event['message']['text'] , "timestamp" => $event['timestamp']];
                    }
                }
            }
        }
    }
    public static function getUserInfo($recipientId){
        return Application::GetRequest(null,['fields'=>'name'],$recipientId);
    }
    public static function sendMessage($recipientId, $messageText) {
        //
        $result = Application::PostRequest('messages',[
            'recipient' => ['id' => $recipientId],
            'message' => ['text' => $messageText],
            'messaging_type' => 'RESPONSE',
        ]);
        //
        if (Application::getOption('DEBUG')){
            Application::log(null, json_encode(["type"=>"message","success" => $result, "recipient" => $recipientId, "message" => $messageText]));
        }
        //
        return $result;
    }
}