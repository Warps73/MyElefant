<?php
namespace tests\units;

use mageekguy\atoum;

class FakeMyElefant
{
    private $fakerSecretKey = 'ADN9DSQKNLDSQ1515SDQMPDS';

    public function getAuthentification($secretKey){
        if ($secretKey === $this->fakerSecretKey) {
           return 'TheSecretToken';
        }
        return false;
    }

    public function sendSms($secretToken, $contacts, $sendDate=null, $message = null, $sender = null){
        if ($secretToken === 'validToken') {
            if (isset($contacts, $sendDate, $message, $sender)) {
                return 'success';

            }else {
                return 'Error';
            }
            
        }else {
            return 'invalid token';
        }
    
    }
}
