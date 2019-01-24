<?php

namespace tests\units\myelefant;

require_once __DIR__.'/atoum.phar';
include_once __DIR__.'/../../MyElefant.php';

use mageekguy\atoum;
use MyElefant\MyElefant as classToTest;
use DateTime;
use Symfony\Component\Yaml\Yaml;
use Exception;

class MyElefant extends atoum\test
{  

    public function testDate()
    {
        $yamlDatas = Yaml::parseFile(__DIR__.'/../../../MyElefant.yaml');
        $currentDate = new DateTime('now');
        $myObject = new classToTest;
        $this
            ->if($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getDate('2019-12-03 12:00'))
                    ->isEqualTo('2019-12-03 12:00')
            ->and
                ->then
                    ->string($this->testedInstance->getDate())
                    ->isEqualTo($currentDate->format('Y-m-d H:i'))
            ->and
                ->then
                    ->string($this->testedInstance->getDate('arsrs'))
                    ->isEqualTo($yamlDatas['TARGET_EXCEPTION'].': '.$yamlDatas['CRITICAL_MESSAGE_DATE_FORMAT'])
            ->and
                ->then
                    ->string($this->testedInstance->getDate('2018-01-24 12:00'))
                    ->isEqualTo($yamlDatas['TARGET_EXCEPTION'].': '.$yamlDatas['CRITICAL_MESSAGE_DATE'])
            ->and
                ->then
                    ->string($this->testedInstance->getDate('2018-20-65 12:00'))
                    ->isEqualTo($yamlDatas['TARGET_EXCEPTION'].': '.$yamlDatas['CRITICAL_MESSAGE_DATE_FORMAT']);
    }
    
    public function testMessage()
    {
        $yamlDatas = Yaml::parseFile(__DIR__.'/../../../MyElefant.yaml');
        $longText = "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nemo, earum distinctio, ea voluptatum ipsum recusandae ipsam molestiae totam maxime error sunt iusto rerum voluptatem voluptas! Iste explicabo hic iusto non.";
        $this
            ->if($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getMessage('I\'m message'))
                    ->isEqualTo('I\'m message')
            ->and
                ->then
                    ->string($this->testedInstance->getMessage($longText))
                    ->isEqualTo($yamlDatas['TARGET_EXCEPTION'].': '.$yamlDatas['WARNING_MESSAGE_LENGTH']);
    }

    public function testContact()
    {
        $myObject = $this->newTestedInstance; 
        $yamlDatas = Yaml::parseFile(__DIR__.'/../../../MyElefant.yaml');
        $this
            ->if($this->newTestedInstance)
            ->then
                ->array($this->testedInstance->getContact([['33627391805','Timothy']]))
                    ->isEqualTo([['33627391805','Timothy']])
            ->and
                ->then
                    ->exception(
                        function() use($myObject) {
                            $myObject->getContact([['33627391','Timothy']]);
                        }
                    )->isIdenticalTo($this->exception)
            ->and
                ->then
                    ->array($this->testedInstance->getContact(['33627391805','Timothy']))
                    ->isIdenticalTo($yamlDatas['TARGET_EXCEPTION'].': '.$yamlDatas['CRITICAL_MESSAGE_CONTACT_FORMAT'])
            ->and
                ->then
                    ->empty($this->testedInstance->getContact())
                    ->isIdenticalTo($yamlDatas['TARGET_EXCEPTION'].': '.$yamlDatas['CRITICAL_MESSAGE_CONTACT_FORMAT']);
    
    }
    
}