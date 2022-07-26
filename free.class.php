<?php
/**
* Class Free mobile SMS
* Allow you to send SMS to yourself
* user -> login free.fr
* key -> api key that you can get from free.fr
*/
class FREE
{
  const API_URL = 'https://smsapi.free-mobile.fr/sendmsg?user=%s&pass=%s&msg=%s';

  private $apiUser;
  private $apiKey;

  public function __construct($user,$key)
  {
    $this->apiUser = $user;
    $this->apiKey = $key;
  }

  public function sendMessage($msg) {
    $url = sprintf(self::API_URL, $this->apiUser,$this->apiKey, urlencode($msg));
    $res = $this->curlRequestJson($url);
  }

  private function curlRequestJson($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return json_decode($output, true);
  }
}

?>
