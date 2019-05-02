<?php

namespace App;

use Log;
use App\MockupLogger;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Item;

class Progresswebhook extends Model
{

  private $request_timeout = 10;
  private $connect_timeout = 10;


  public function item() {
    return $this->belongsTo('App\Item');
  }


  public function send($progress) {
    $httpClient = $this->getHttpClient();
    $url = $this->webhookurl;
    $item = [
      'progress'=>$progress
    ];
    $body = json_encode($item);
    try {
      $response = $httpClient->request('POST', $url,[
                                                    'body' => $body,
                                                    'headers' => [ 'Content-Type' => 'application/json' ]
                                                    ]
      );


      if ($response->getStatusCode() == '200') {
        MockupLogger::Item('debug',$this->item,'Progress Webhook Sent:: ' . $url);
      } else {
        MockupLogger::Item('debug',$this->item,'Progress Webhook did not return 200 resonse :: ' . $url);
      }



    } catch (RequestException $e) {
      MockupLogger::Item('debug',$this->item,'Progress Webhook Failed:: ' . $url . ' :: ' . $e->getMessage());
    }
  }



  /**
   * Returns the default guzzle client with the default settings configured
   *
   *
   *
   * @return [type] [description]
   */
  private function getHttpClient() {
      $client = new \GuzzleHttp\Client([
        'timeout'=>$this->request_timeout,
        'connect_timeout'=>$this->connect_timeout
      ]);
      return $client;
  }



}
