<?php

namespace tests\units\MyElefant;

require_once __DIR__.'/atoum.phar';
require_once __DIR__.'/../../MyElefant.php';
require_once __DIR__.'/../../MyElefantConfig.php';
require_once __DIR__.'/FakeMyElefant.php';
require_once __DIR__.'/../../../vendor/autoload.php';

use mageekguy\atoum;
use MyElefant\MyElefant as classToTest;
use DateTime;
use Exception;
use tests\units\FakeMyElefant;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use myelefant\MyElefantConfig;

class MyElefant extends atoum\test
{

    
    public function testSetDate()
    {
        $this->mockGenerator->shunt('__construct');
        $this
        ->given(
            $currentDate = new DateTime('now'),
            $myElefantMock = new \mock\MyElefant\MyElefant
        )
            ->then
                ->string($myElefantMock->setDate('2019-12-03 12:00'))
                    ->isEqualTo('2019-12-03 12:00')

            ->then
                ->string($myElefantMock->setDate())
                ->isEqualTo($currentDate->format('Y-m-d H:i'))

            ->then
                ->exception(
                    function () {
                        $myElefantMock = new \mock\MyElefant\MyElefant;
                        $myElefantMock->setDate('asrere');
                    }
                )
                ->hasMessage(MyElefantConfig::CRITICAL_MESSAGE_DATE_FORMAT)

            ->then
                ->exception(
                    function () {
                        $myElefantMock = new \mock\MyElefant\MyElefant;
                        $myElefantMock->setDate('2018-01-24 12:00');
                    }
                )
                ->hasMessage(MyElefantConfig::CRITICAL_MESSAGE_DATE)

            ->then
                ->exception(
                    function () {
                        $myElefantMock = new \mock\MyElefant\MyElefant;
                        $myElefantMock->setDate('2018-20-64 12:00');
                    }
                )
                ->isInstanceOf('Exception')
            ;
    }
    
    public function testSetMessage()
    {
        $this->mockGenerator->shunt('__construct');
        $this
        ->given(
            $myElefantMock = new \mock\MyElefant\MyElefant
        )
            ->then
                ->string($myElefantMock->setMessage('I\'m message'))
                    ->isEqualTo('I\'m message')

            ->then
                ->exception(
                    function () {
                        $myElefantMock = new \mock\MyElefant\MyElefant;
                        $longText = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vehicula, 
                                     nulla vitae fringilla dapibus, nunc sem feugiat lorem, 
                                     gravida aliquet eros lacus id sem viverra fusce.";
                        $myElefantMock->setMessage($longText);
                    }
                )
                ->hasMessage(MyElefantConfig::WARNING_MESSAGE_LENGTH)
            ;
    }

    public function testSetContact()
    {
        $this->mockGenerator->shunt('__construct');
        $this
        ->given(
            $myElefantMock = new \mock\MyElefant\MyElefant
        )
        ->then
            ->array($myElefantMock->setContact([['33612345618','Timothy']]))
            ->isEqualTo([['33612345618','Timothy']])

        ->then
            ->exception(
                function () {
                    $myElefantMock = new \mock\MyElefant\MyElefant;
                    $myElefantMock->setContact([['3362739','Timothy']]);
                }
            )
            ->hasMessage('3362739 '.MyElefantConfig::CRITICAL_MESSAGE_PHONE_NUMBER_FORMAT)

        ->then
            ->exception(
                function () {
                    $myElefantMock = new \mock\MyElefant\MyElefant;
                    $myElefantMock->setContact([['33612345618','Timothy'],'33612345618','Timothy']);
                }
            )
            ->hasMessage(MyElefantConfig::CRITICAL_MESSAGE_CONTACT_FORMAT)

        ->then
            ->exception(
                function () {
                    $myElefantMock = new \mock\MyElefant\MyElefant;
                    $myElefantMock->setContact(['33612345618','Timothy']);
                }
            )
            ->hasMessage(MyElefantConfig::CRITICAL_MESSAGE_CONTACT_FORMAT)

        ->then
            ->exception(
                function () {
                    $myElefantMock = new \mock\MyElefant\MyElefant;
                    $myElefantMock->setContact('33612345618');
                }
            )
            ->hasMessage(MyElefantConfig::CRITICAL_MESSAGE_CONTACT_FORMAT)
        ;
    }

    public function testCheckPhoneNumber()
    {
        $this->mockGenerator->shunt('__construct');
        $this
        ->given(
            $myElefantMock = new \mock\MyElefant\MyElefant
        )
            ->then
                ->variable($myElefantMock->checkPhoneNumber('33612345618'))
                    ->isIdenticalTo(true)

            ->then
                ->variable($myElefantMock->checkPhoneNumber('0612345618'))
                    ->isIdenticalTo(false)

            ->then
                ->variable($myElefantMock->checkPhoneNumber('+33612345618'))
                    ->isIdenticalTo(false)
            ->then
                ->variable($myElefantMock->checkPhoneNumber('Blabla'))
                    ->isIdenticalTo(false)
                ;
    }
    
    public function testSetAuthentification()
    {
        $this
        ->given($MyElefantFaker = new FakeMyElefant)
        ->variable($MyElefantFaker->setAuthentification('ADN9DSQKNLDSQ1515SDQMPDS'))
        ->isIdenticalTo(200)
        ->then
        ->exception(
            function () {
                $MyElefantFaker = new FakeMyElefant;
                $MyElefantFaker->setAuthentification('ASDQDQSDQSS1561');
            }
        )
        ->isInstanceOf('Exception');
    }

    public function testSendSms()
    {
        $this
        ->given($MyElefantFaker = new FakeMyElefant)
            ->variable(
                $MyElefantFaker->sendSms(
                    'campaignId',
                    'campaignName',
                    'validToken',
                    [['validContacts']],
                    'validDate',
                    'validMessage',
                    'validSender'
                )
            )
            ->isIdenticalTo(200)
        ->then
            ->exception(
                function () {
                    $MyElefantFaker = new FakeMyElefant;
                    $MyElefantFaker->sendSms(
                        'campaignId',
                        'campaignName',
                        'invalidToken',
                        [['validContacts']],
                        'validDate',
                        'validMessage',
                        'validSender'
                    );
                }
            )
        ->isInstanceOf('Exception');
    }

    public function testCheckFields()
    {
        $this->mockGenerator->shunt('__construct');
        $this
        ->given(
            $myElefantMock = new \mock\MyElefant\MyElefant
        )
        ->variable($myElefantMock->checkFields('message', 'sender'))
        ->isIdenticalTo(true)
        ->then
            ->variable($myElefantMock->checkFields(null, 'sender'))
            ->isIdenticalTo(false)
        ;
    }

    public function testCheckContactsFormat()
    {
        $this->mockGenerator->shunt('__construct');
        $this
        ->given(
            $myElefantMock = new \mock\MyElefant\MyElefant
        )
        ->variable($myElefantMock->CheckContactsFormat([['33612345618', 'Jean'], ['33612345617', 'Alfred']]))
        ->isIdenticalTo(true)

        ->then
            ->variable($myElefantMock->CheckContactsFormat([['33612345618', 'Jean'], '33612345617', 'Alfred']))
            ->isIdenticalTo(false)

        ;
    }

    public function testInitLogger()
    {
        $this->mockGenerator->shunt('__construct');
        
        $this
        ->given(
            $myElefantMock = new \mock\MyElefant\MyElefant,
            $logger = $myElefantMock->initLogger('name', 'path')
        )
            ->object($logger)
            ->isInstanceOf('Monolog\Logger');
    }
}
