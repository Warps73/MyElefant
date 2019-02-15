# MyElefant

[![CircleCI](https://circleci.com/gh/digitregroup/php-myelefant-client.svg?style=shield)](https://circleci.com/gh/digitregroup/php-myelefant-client)
[![Latest Stable Version](https://poser.pugx.org/digitregroup/php-myelefant-client/version)](https://packagist.org/packages/digitregroup/php-myelefant-client)
[![Total Downloads](https://poser.pugx.org/digitregroup/php-myelefant-client/downloads)](https://packagist.org/packages/digitregroup/php-myelefant-client)

MyElefant is a service for sending sms / push notifications etc...

https://myelefant.com/

This plugin is for sending sms campaigns with MyElefant's APIs.

https://platform.myelefant.com/api-doc.html

# Configuration

Php ^5.6

# Before use

**Get your secret key :**

Your MyElefant secret Key [here](https://platform.myelefant.com/user/options/)

**Create Campaign on MyElefant Inteface :**

The creation of a campaign is done by duplicating an existing one. You can customize your application on the myElefant interface and then automatically schedule similar campaigns to be sent.

**Get campaign UUID :**

When the campaign is created, a campaignId is created too.
This ID is displayed in the list of your campaigns when you click on "Show IDs".

# Installation

    composer require digitregroup/php-myelefant-client

**Usage**

You can send a campaign with the parameters provided when creating the campaign (message and sender) or by using custom parameters.

To send a new campaign : 

- With your custom's parameters:


        <?php

        use myelefant\MyElefant;

        $client = new MyElefant(['secretKey' => '***SECRET_KEY***'])

        $client->sendSms(
                        'campaignId',
                         'campaignName',
                         [['33612345678',(optional)'Name',(optional)'Surname']],
                         '2019-01-01 12:00',
                         'Your message',
                         'Your sender'
                        );


- With default template's parameters

        <?php

        $client = new MyElefant(['secretKey' => '***SECRET_KEY***'])

        $client->sendSms(
            'campaignId',
            'campaignName',
            [['33612345678',(optional)'Name',(optional)'Surname']],
            '2019-01-01 12:00'
            )

Field's formats :

- Secret Key :

    String

- Campaign Id : 
    
    String

- Campaign name

    String

- Contact :

    Multidimensional array
            
        Example: [['33611223344',(optionnal)'John',(optionnal)'Doe'],[...]]

- Send Date :

    String

        Example : 'Y-m-d H:i' -> '2019-01-25 12:59'

- Message :

    String

- Sender :

   String

# Debug

To activate logging system, add this parameter.


          $client = new MyElefant(['secretKey' => '***SECRET_KEY***', 'debug'=> true])

