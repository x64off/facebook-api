<?php
namespace x64off\FacebookApi\Webhooks;
use x64off\FacebookApi\Application;
class Validate{
    static function validate(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['hub_verify_token']) && $_GET['hub_verify_token'] === Application::getApiToken() ) {
            echo $_GET['hub_challenge'];
            exit;
        }
    }
}