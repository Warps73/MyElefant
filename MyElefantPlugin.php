<?php
use myelefant\MyElefant;

new MyElefantPlugin([['0627391805', 'Timothy']],'29-02-2019');

class MyElefantPlugin
{
    /**
     * @param array Contact
     * @param string Date (null = now)
     * @param string Message (Optionnal only without Sender)
     * @param string Sender (Optionnal) 
     */

    public function __construct(array $contacts,string $date=null,string $message=null,string $sender=null){
        require_once __DIR__.'/vendor/autoload.php';
        $test = new MyElefant;
        $test->sendSms($contacts,$date,$message,$sender);
    }
}
