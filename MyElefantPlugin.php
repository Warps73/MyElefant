<?php
use myelefant\MyElefant;

class MyElefantPlugin
{
    /**
     * @param array Contact
     * @param string Date
     * @param string Message (Optionnal only without Sender)
     * @param string Sender (Optionnal)
     */

    public function __construct(array $contacts,string $date=null,string $message=null,string $sender=null){
        require_once __DIR__.'/vendor/autoload.php';
        $test = new MyElefant;
        $test->sendSms($contacts,$date,$message,$sender);
    }
}
