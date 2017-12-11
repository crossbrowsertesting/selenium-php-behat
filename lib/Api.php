<?php
/**
 * Drewberry™
 *
 * @author  Wagner Silva <wagner.silva@drewberry.co.uk>
 * @link <http://drewberryinsurance.co.uk>
 * @version 1.0
 */

namespace Drewberry;

require 'vendor/autoload.php';

class Api
{
    protected $user;
    protected $pass;
    protected $url = 'https://crossbrowsertesting.com/api/v3/selenium';

    public function __construct($user, $password)
    {

        $this->setUser($user);
        $this->setPass($password);
    }

    protected function getUser()
    {
        return $this->user;
    }

    protected function getPass()
    {
        return $this->pass;
    }

    protected function setUser($user)
    {
        $this->user = $user;
    }

    protected function setPass($pass)
    {
        $this->pass = $pass;
    }

    protected function call($session_id, $method = 'GET', $params = false)
    {
        $apiResult = new \stdClass();
        $process   = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($process, CURLOPT_POST, 1);
                if ($params) {
                    curl_setopt($process, CURLOPT_POSTFIELDS, $params);
                    curl_setopt($process, CURLOPT_HTTPHEADER, array('User-Agent: php')); //important
                }
                break;
            case "PUT":
                curl_setopt($process, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($params) {
                    curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($params));
                    curl_setopt($process, CURLOPT_HTTPHEADER, array('User-Agent: php')); //important
                }
                break;
            case 'DELETE':
                curl_setopt($process, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                if ($params) {
                    $this->url = sprintf("%s?%s", $this->url, http_build_query($params));
                }
        }
        // Optional Authentication:
        curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($process, CURLOPT_USERPWD, $this->getUser().":".$this->getPass());
        curl_setopt($process, CURLOPT_URL, $this->url.'/'.$session_id);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        $apiResult->content      = curl_exec($process);
        $apiResult->httpResponse = curl_getinfo($process);
        $apiResult->errorMessage = curl_error($process);
        $apiResult->params       = $params;
        curl_close($process);
        $paramsString            = $params ? http_build_query($params) : '';
        $response                = json_decode($apiResult->content);
        if ($apiResult->httpResponse['http_code'] != 200) {
            $message = 'Error calling "'.$apiResult->httpResponse['url'].'" ';
            $message .= (isset($paramsString) ? 'with params "'.$paramsString.'" ' : ' ');
            $message .= '. Returned HTTP status '.$apiResult->httpResponse['http_code'].' ';
            $message .= (isset($apiResult->errorMessage) ? $apiResult->errorMessage : ' ');
            $message .= (isset($response->message) ? $response->message : ' ');
            die($message);
        } else {
            $response = json_decode($apiResult->content);
            if (isset($response->status)) {
                die('Error calling "'.$apiResult->httpResponse['url'].'"'.(isset($paramsString) ? 'with params "'.$paramsString.'"' : '').'". '.$response->message);
            }
        }
        return $response;
    }

    public function score ($session_id, $score){
        return $this->call($session_id, 'PUT', ['score' => $score, 'action' => 'set_score']);
    }
}