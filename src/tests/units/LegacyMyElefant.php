<?php

namespace tests\units\MyElefant;

require_once __DIR__.'/atoum.phar';
require_once __DIR__.'/../../LegacyMyElefant.php';
require_once __DIR__.'/FakeMyElefant.php';
require_once __DIR__.'/../../../vendor/autoload.php';

use mageekguy\atoum;
use myelefant\LegacyMyElefant as classToTest;
use DateTime;
use Symfony\Component\Yaml\Yaml;
use Exception;
use tests\units\FakeMyElefant;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Dotenv;

class LegacyMyElefant extends atoum\test
{  
    public function testGetDate()
    {
        $this
        ->given(
                $yamlDatas = Yaml::parseFile(__DIR__.'/../../../MyElefant.yaml'),
                $currentDate = new DateTime('now')
                )
            ->if($this->newTestedInstance)

            ->then
                ->string($this->testedInstance->getDate('2019-12-03 12:00'))
                    ->isEqualTo('2019-12-03 12:00')

            ->then
                ->string($this->testedInstance->getDate())
                ->isEqualTo($currentDate->format('Y-m-d H:i'))

            ->then
                ->exception(function(){
                    $this->testedInstance->getDate('asrere');
                })
                ->hasMessage($yamlDatas['CRITICAL_MESSAGE_DATE_FORMAT'])

            ->then
                ->exception(function(){
                    $this->testedInstance->getDate('2018-01-24 12:00');
                })
                ->hasMessage($yamlDatas['CRITICAL_MESSAGE_DATE'])

            ->then
                ->exception(function(){
                    $this->testedInstance->getDate('2018-20-64 12:00');
                })
                ->isInstanceOf('Exception')
            ;
    }
    
    public function testGetMessage()
    {
        $this
            ->if($this->newTestedInstance)
                ->given(
                    $yamlDatas = Yaml::parseFile(__DIR__.'/../../../MyElefant.yaml')
                )
            ->then
                ->string($this->testedInstance->getMessage('I\'m message'))
                    ->isEqualTo('I\'m message')

            ->then
                ->exception(function(){
                    $longText = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vehicula, nulla vitae fringilla dapibus, nunc sem feugiat lorem, gravida aliquet eros lacus id sem viverra fusce.";
                    $this->testedInstance->getMessage($longText);
                })
                ->hasMessage($yamlDatas['WARNING_MESSAGE_LENGTH'])
            ;
    }

    public function testGetContact()
    {
        
        $this
            ->if($this->newTestedInstance)
            ->given(
                $yamlDatas = Yaml::parseFile(__DIR__.'/../../../MyElefant.yaml')
            )
        ->then
            ->array($this->testedInstance->getContact([['33612345618','Timothy']]))
            ->isEqualTo([['33612345618','Timothy']])

        ->then
            ->exception(function(){
                $this->testedInstance->getContact([['3362739','Timothy']]);
            })
            ->hasMessage('3362739 '.$yamlDatas['CRITICAL_MESSAGE_PHONE_NUMBER_FORMAT'])

        ->then
            ->exception(function(){
                $this->testedInstance->getContact([['33612345618','Timothy'],'33612345618','Timothy']);
            })
            ->hasMessage($yamlDatas['CRITICAL_MESSAGE_CONTACT_FORMAT'])

        ->then
            ->exception(function(){
                $this->testedInstance->getContact(['33612345618','Timothy']);
            })
            ->hasMessage($yamlDatas['CRITICAL_MESSAGE_CONTACT_FORMAT'])

        ->then
            ->exception(function(){
                $this->testedInstance->getContact('33612345618');
            })
            ->isInstanceOf('Exception')
        ;
    }

    public function testCheckPhoneNumber()
    {
        $this
            ->if($this->newTestedInstance)

            ->then
                ->variable($this->testedInstance->checkPhoneNumber('33612345618'))
                    ->isIdenticalTo(true)

            ->then
                ->variable($this->testedInstance->checkPhoneNumber('0612345618'))
                    ->isIdenticalTo(false)

            ->then
                ->variable($this->testedInstance->checkPhoneNumber('+33612345618'))
                    ->isIdenticalTo(false)
            ->then
                ->variable($this->testedInstance->checkPhoneNumber('Blabla'))
                    ->isIdenticalTo(false)
            ->then
                ->when(
                    function() {
                        $this->testedInstance->checkPhoneNumber();
                    }
                )
                ->error()
                    ->withType(E_WARNING)
                    ->exists();
    }

    public function TestGetSender(){
        $this
            ->if($this->newTestedInstance)

            ->given(
                $yamlDatas = Yaml::parseFile(__DIR__.'/../../../MyElefant.yaml')
            )

            ->then
                ->variable($this->testedInstance->getSender(null, null))
                ->isIdenticalTo(null)

            ->then
                ->variable($this->testedInstance->getSender(null, 'message'))
                ->isIdenticalTo($yamlDatas['DEFAULT_SENDER'])

            ->then
                ->string($this->testedInstance->getSender('sender', 'message'))
                ->isIdenticalTo('sender')
            ;

    }
    
    public function TestGetAuthentification(){
        $this
            ->given($m = new FakeMyElefant)
		    ->variable($m->getAuthentification('ADN9DSQKNLDSQ1515SDQMPDS'))
            ->isIdenticalTo('TheSecretToken')
        ->then
            ->variable($m->getAuthentification('ASDQDQSDQSS1561'))
            ->isIdenticalTo(false);

    }

    public function TestSendSms(){
        $this
            ->given($m = new FakeMyElefant)
		    ->variable($m->sendSms('validToken',[['validContacts']],'validDate','validMessage','validSender'))
            ->isIdenticalTo('success')
        ->then
            ->variable($m->sendSms('invalidToken',[['validContacts']],'validDate','validMessage','validSender'))
            ->isIdenticalTo('invalid token')
        ;

    }

    public function TestCheckFields(){
        $this
            ->if($this->newTestedInstance)
		    ->variable($this->testedInstance->checkFields('message','sender'))
            ->isIdenticalTo(true)
        ->then
            ->variable($this->testedInstance->checkFields(null,'sender'))
            ->isIdenticalTo(false)
        ;

    }

    public function testCheckContactsFormat(){
        $this
            ->if($this->newTestedInstance)
		    ->variable($this->testedInstance->CheckContactsFormat([['33612345618','Jean'],['33612345617','Alfred']]))
            ->isIdenticalTo(true)

        ->then
		    ->variable($this->testedInstance->CheckContactsFormat([['33612345618','Jean'],'33612345617','Alfred']))
            ->isIdenticalTo(false)
        ->then
            ->when(
                function() {
                    $this->testedInstance->CheckContactsFormat();
                }
            )
            ->error()
                ->withType(E_WARNING)
                ->exists();

    }

    public function testInitLogger(){
        $object = new classToTest;
        $logger = $object->initLogger('name', 'path');
        $this
        ->if($this->newTestedInstance)
            ->object($logger)
            ->isInstanceOf('Monolog\Logger');

    }

}