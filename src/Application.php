<?php

namespace x64off\FacebookApi;

class Application
{
    private static string $apiVersion = '19.0';
    private static ?string $ApiAccessToken = null;
    private static ?int $PageId = null;
    private static ?string $PageAccessToken = null;
    private static array $options = [];

    public static function setApiToken(string $ApiAccessToken)
    {
        self::$ApiAccessToken = $ApiAccessToken;
    }
    public static function setPageToken(string $PageAccessToken)
    {
        self::$PageAccessToken = $PageAccessToken;
    }
    public static function setPageId(int $page_id)
    {
        self::$PageId = $page_id;
    }
    public static function getApiToken(): ?string
    {
        return self::$ApiAccessToken;
    }
    public static function getPageToken(): ?string
    {
        return self::$PageAccessToken;
    }
    public static function getPageId(): ?int
    {
        return self::$PageId;
    }
    public static function setOption(string $option, $value)
    {
        self::$options[$option] = $value;
    }
    public static function getOption(string $option)
    {
        return isset(self::$options[$option]) ? self::$options[$option] : null;
    }
    static function log($filename = null)
    {
        if ($h = fopen((self::$options['log_dir'] ?: __DIR__) . DIRECTORY_SEPARATOR . (is_null($filename) ? 'facebook.api.log' : $filename), 'a')) {
            fwrite($h, '[' . date('c') . ']');
            foreach (func_get_args() as $index => $arg) {
                $index &&
                    fwrite($h, ' ' . (is_scalar($arg) ? $arg : json_encode($arg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
            }
            fwrite($h, PHP_EOL);
            fclose($h);
        }
    }
    static function GetRequest(?string $endpoint, array $params = [], ?string $url = null)
    {
        if($url){
            $url = 'https://graph.facebook.com/v' . self::$apiVersion . '/' . $url . '?access_token=' . self::$PageAccessToken;
        }else{
            $url = 'https://graph.facebook.com/v' . self::$apiVersion . '/' . (self::$PageId ?? 'me') . '/' . $endpoint . '?access_token=' . self::$PageAccessToken;
        }
        
        // Добавление параметров запроса к URL
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        Application::log(null,$url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $result = curl_exec($ch);

        if ($result === false) {
            self::log(null, json_encode(curl_error($ch)));
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        return json_decode($result,true);
    }

    static function PostRequest(string $endpoint, array $data = []): bool
    {
        $url = 'https://graph.facebook.com/v' . self::$apiVersion . '/' . (self::$PageId ?? 'me') . '/' . $endpoint . '?access_token=' . self::$PageAccessToken;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $result = curl_exec($ch);

        if ($result === false) {
            self::log(null, json_encode(curl_error($ch)));
            curl_close($ch);
            return false;
        }
        curl_close($ch);

        return true;
    }
}
