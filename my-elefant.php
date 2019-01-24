<?php
use myelefant\MyElefant;
new myelefantplugin([['33627391805','Timothy'],['33627391805','Timothy']]);

class myelefantplugin
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
        echo $test->getDate('2019-25-64 22:68');
    }
}
