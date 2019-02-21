<?php

namespace tests\units\MyElefant;

require_once __DIR__.'/../../MyElefant.php';
require_once __DIR__.'/../../MyElefantConfig.php';
require_once __DIR__.'/FakeMyElefant.php';

use DateTime;
use tests\units\FakeMyElefant;
use myelefant\MyElefantConfig;
use mageekguy\atoum;

class MyElefant extends atoum\test
{
    public function testSetDate()
    {
        $this->mockGenerator->shunt('__construct');
        $this
        ->given(
            $currentDate = new DateTime('now'),
            $myElefantMock = new \mock\MyElefant\MyElefant([])
        )
            ->then
                ->string($this->invoke($myElefantMock)->setDate('2019-12-03 12:00'))
                ->isEqualTo('2019-12-03 12:00')

            ->then
                ->string($this->invoke($myElefantMock)->setDate())
                ->isEqualTo($currentDate->format('Y-m-d H:i'))

            ->then
                ->exception(
                    function () {
                        $myElefantMock = new \mock\MyElefant\MyElefant([]);
                        $this->invoke($myElefantMock)->setDate('asrere');
                    }
                )
                ->hasMessage(MyElefantConfig::CRITICAL_MESSAGE_DATE_FORMAT)

            ->then
                ->exception(
                    function () {
                        $myElefantMock = new \mock\MyElefant\MyElefant([]);
                        $this->invoke($myElefantMock)->setDate('2018-01-24 12:00');
                    }
                )
                ->hasMessage(MyElefantConfig::CRITICAL_MESSAGE_DATE)

            ->then
                ->exception(
                    function () {
                        $myElefantMock = new \mock\MyElefant\MyElefant([]);
                        $this->invoke($myElefantMock)->setDate('2018-20-64 12:00');
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
            $myElefantMock = new \mock\MyElefant\MyElefant([])
        )
            ->then
                ->string($this->invoke($myElefantMock)->setMessage('I\'m message'))
                    ->isEqualTo('I\'m message')

            ->then
                ->exception(
                    function () {
                        $myElefantMock = new \mock\MyElefant\MyElefant([]);
                        $longText = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vehicula,
                                     nulla vitae fringilla dapibus, nunc sem feugiat lorem,
                                     gravida aliquet eros lacus id sem viverra fusce.";
                        $this->invoke($myElefantMock)->setMessage($longText);
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
            $myElefantMock = new \mock\MyElefant\MyElefant([])
        )
        ->then
            ->array($this->invoke($myElefantMock)->setContact([['33612345618','Timothy']]))
            ->isEqualTo([['33612345618','Timothy']])

        ->then
            ->exception(
                function () {
                    $myElefantMock = new \mock\MyElefant\MyElefant([]);
                    $this->invoke($myElefantMock)->setContact([['3362739','Timothy']]);
                }
            )
            ->hasMessage('3362739 '.MyElefantConfig::CRITICAL_MESSAGE_PHONE_NUMBER_FORMAT)

        ->then
            ->exception(
                function () {
                    $myElefantMock = new \mock\MyElefant\MyElefant([]);
                    $this->invoke($myElefantMock)->setContact([['33612345618','Timothy'],'33612345618','Timothy']);
                }
            )
            ->hasMessage(MyElefantConfig::CRITICAL_MESSAGE_CONTACT_FORMAT)

        ->then
            ->exception(
                function () {
                    $myElefantMock = new \mock\MyElefant\MyElefant([]);
                    $this->invoke($myElefantMock)->setContact(['33612345618','Timothy']);
                }
            )
            ->hasMessage(MyElefantConfig::CRITICAL_MESSAGE_CONTACT_FORMAT)

        ->then
            ->exception(
                function () {
                    $myElefantMock = new \mock\MyElefant\MyElefant([]);
                    $this->invoke($myElefantMock)->setContact('33612345618');
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
            $myElefantMock = new \mock\MyElefant\MyElefant([])
        )
            ->then
                ->variable($this->invoke($myElefantMock)->checkPhoneNumber('33612345618'))
                    ->isIdenticalTo(true)

            ->then
                ->variable($this->invoke($myElefantMock)->checkPhoneNumber('0612345618'))
                    ->isIdenticalTo(false)

            ->then
                ->variable($this->invoke($myElefantMock)->checkPhoneNumber('+33612345618'))
                    ->isIdenticalTo(false)
            ->then
                ->variable($this->invoke($myElefantMock)->checkPhoneNumber('Blabla'))
                    ->isIdenticalTo(false)
                ;
    }

    public function testSetAuthentification()
    {
        $this
        ->given($myElefantFaker = new FakeMyElefant)
        ->variable($this->invoke($myElefantFaker)->setAuthentification('ADN9DSQKNLDSQ1515SDQMPDS'))
        ->isIdenticalTo(200)
        ->then
        ->exception(
            function () {
                $myElefantFaker = new FakeMyElefant;
                $this->invoke($myElefantFaker)->setAuthentification('ASDQDQSDQSS1561');
            }
        )
        ->isInstanceOf('Exception');
    }

    public function testCreateCampaign()
    {
        $this
        ->given($myElefantFaker = new FakeMyElefant)
            ->variable(
                $myElefantFaker->createCampaign(
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
                    $myElefantFaker = new FakeMyElefant;
                    $myElefantFaker->createCampaign(
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
            $myElefantMock = new \mock\MyElefant\MyElefant([])
        )
        ->variable($this->invoke($myElefantMock)->checkFields('message', 'sender'))
        ->isIdenticalTo(true)
        ->then
            ->variable($this->invoke($myElefantMock)->checkFields(null, 'sender'))
            ->isIdenticalTo(false)
        ;
    }

    public function testCheckContactsFormat()
    {
        $this->mockGenerator->shunt('__construct');
        $this
        ->given(
            $myElefantMock = new \mock\MyElefant\MyElefant([])
        )
        ->variable($this->invoke($myElefantMock)->CheckContactsFormat([['33612345618', 'Jean'], ['33612345617', 'Alfred']]))
        ->isIdenticalTo(true)

        ->then
            ->variable($this->invoke($myElefantMock)->CheckContactsFormat([['33612345618', 'Jean'], '33612345617', 'Alfred']))
            ->isIdenticalTo(false)

        ;
    }

    public function testCheckSendSmsContentFormat()
    {
        $this->mockGenerator->shunt('__construct');
        $this
        ->given(
            $myElefantMock = new \mock\MyElefant\MyElefant([])
        )
            ->variable($this->invoke($myElefantMock)->checkSendSmsContentFormat(['33612345618', 'Jean', '33612345617', 'Alfred']))
            ->isIdenticalTo(true)
        ->then
            ->exception(
                function () {
                    $myElefantFaker = new FakeMyElefant;
                    $this->invoke($myElefantFaker)->checkSendSmsContentFormat('33612345618');
                }
            )
            ->isInstanceOf('Exception');

        ;
    }

    public function testInitLogger()
    {
        $this->mockGenerator->shunt('__construct');

        $this
        ->given(
            $myElefantMock = new \mock\MyElefant\MyElefant([]),
            $logger = $this->invoke($myElefantMock)->initLogger('name', 'path')
        )
            ->object($logger)
            ->isInstanceOf('Monolog\Logger');
    }

    public function testSendSms()
    {
        $this
            ->given($myElefantFaker = new FakeMyElefant)
            ->variable(
                $myElefantFaker->sendSms(
                    'campaignId',
                    'campaignName',
                    'validToken'

                )
            )
            ->isIdenticalTo(200)
            ->then
            ->exception(
                function () {
                    $myElefantFaker = new FakeMyElefant;
                    $myElefantFaker->sendSms(
                        'campaignId',
                        'campaignName',
                        'invalid token'
                    );
                }
            )
            ->isInstanceOf('Exception');
    }
}
