<?php
namespace myelefant;

abstract class MyElefantConfig
{
    const APP_ENV = 'prod';
    const CREDIT_REMAINING = 'remaining credit';
    const CRITICAL_MESSAGE_CONTACT_FORMAT = 'Contact must be a multidimensional array. 
                                             Example [[\'33601020304\', \'John\',\'Doe\']]';
    const CRITICAL_MESSAGE_DATE = 'Date is before the current date';
    const CRITICAL_MESSAGE_DATE_FORMAT = 'Format Date invalid';
    const CRITICAL_MESSAGE_PHONE_NUMBER_FORMAT = 'Format phone number invalid';
    const CRITICAL_MESSAGE_EMPTY_MESSAGE = 'If you specify a sender the message cannot be empty';
    const CRITICAL_MESSAGE_EMPTY_SECRET_KEY = 'You must provide secret key';
    const MAX_LENGTH_MESSAGE = 160;
    const REGEX_PHONE_NUMBER = '/^[0-9]{2}[0-9]{9}$/';
    const SUCCESS_MESSAGE = 'Message sent correctly';
    const URL_MYELEFANT_API = 'https://api.myelefant.com';
    const URL_MYELEFANT_API_AUTHENTIFICATION = '/v1/token';
    const URL_MYELEFANT_API_CREATE_CAMPAIGN = '/v1/campaign/create';
    const WARNING_MESSAGE_LENGTH = 'Your message is more than 160 characters long';
}
