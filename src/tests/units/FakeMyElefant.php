<?php
namespace tests\units;

use mageekguy\atoum;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class FakeMyElefant
{
    private $fakerSecretKey = 'ADN9DSQKNLDSQ1515SDQMPDS';

    public function setAuthentification($secretKey)
    {
        
        if ($secretKey === $this->fakerSecretKey) {
            $mock = new MockHandler([
                new Response(200, ['Authorization' => 'Basic '.$secretKey]),
            ]);
            $handler = HandlerStack::create($mock);
            $client = new Client(['handler' => $handler]);
            return $client->request(
                'POST',
                'https://api.myelefant.com/v1/token',
                ['Authorization' => 'Basic '.$secretKey]
            )->getStatusCode();
        }

        $mock = new MockHandler([
            new Response(401),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        return $client->request(
            'POST',
            'https://api.myelefant.com/v1/token',
            ['Authorization' => 'Basic '.$secretKey]
        )->getStatusCode();
    }

    public function sendSms($campaignLeadId, $campaignName, $secretToken, $contacts, $sendDate = null, $message = null, $sender = null)
    {
        if ($secretToken === 'validToken') {
            $mock = new MockHandler([
                new Response(200),
            ]);
            $handler = HandlerStack::create($mock);
            $client = new Client(['handler' => $handler]);
            return $client->request(
                'POST',
                'https://api.myelefant.com/v1/campaign/create',
                [
                    'headers' =>[
                        'Authorization' =>$secretToken,
                        'Content-Type'=>'application/json'
                    ],
                    'json'=>[
                        'logic_param' =>$campaignLeadId,
                        'name' => $campaignName,
                        'contacts'=> $contacts,
                        'send_date' => $sendDate,
                        'logic' => 'duplicate',
                        'message' => $message,
                        'sender' => $sender
                    ]
                ]
            )->getStatusCode();
        } else {
            $mock = new MockHandler([
                new Response(401),
            ]);
            $handler = HandlerStack::create($mock);
            $client = new Client(['handler' => $handler]);
            return $client->request(
                'POST',
                'https://api.myelefant.com/v1/campaign/create',
                [
                    'headers' =>[
                        'Authorization' =>$secretToken,
                        'Content-Type'=>'application/json'
                    ],
                    'json'=>[
                        'logic_param' =>$campaignLeadId,
                        'name' => $campaignName,
                        'contacts'=> $contacts,
                        'send_date' => $sendDate,
                        'logic' => 'duplicate',
                        'message' => $message,
                        'sender' => $sender
                    ]
                ]
            )->getStatusCode();
        }
    }
}
