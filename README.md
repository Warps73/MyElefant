# MyElefant

MyElefant is a service for send sms / push notification etc...

https://myelefant.com/

This plugin is for send sms campaign with MyElefant's APIs.

https://platform.myelefant.com/api-doc.html

# Configuration

Php ^7.0

# Before use

**Get your secret key :**

Your MyElefant secret Key [here](https://platform.myelefant.com/user/options/)

**Create Campaign on MyElefant Inteface :**

The creation of a campaign is done by duplicating an existing one. You can customize your application on the myElefant interface and then automatically schedule similar campaigns to be sent.

**Get campaign UUID :**

When the campaign is created, a campaignId is created too.
This ID is displayed in the list of your campaigns when you click on "Show IDs".

# Installation

    composer require digitregroup/myelefant

# Usage

In .env file provide your campaign uuid and your secret key.

    CAMPAIGN_LEAD_ID = your_campaign_uuid
    SECRET_KEY = your_secret_key

**Default usage**

For send a new campaign you have to provide many parameters : 

- Contact :

    It's a required field, you can provide one or many contact 
            
        Example: [['33611223344',(optionnal)'John',(optionnal)'Doe'],[...]]

- Send Date :

    The send date is an optional parameter, if empty it takes a current date value.

        Example : 'Y-m-d H:i' | '2019-01-25 12:59'
        /!\Send Date must be a string

- Message :

    Message must be a string

- Sender :

    Sender must be a string


**For Message and Sender, you have 3 usages possible**

Using default values :

- Message : The default message provided in MyElefant application

- Sender : The default sender provided in MyElefant application

Your custom values :

- Message : Your custom message.

- Sender : Your custom sender.

Your can also provide a default sender in MyElefant.yaml

- Message : Your custom message

- Sender : The sender provided in MyElefant.yaml


Great ! Now you can call MyElefantPlugin.php file for sending your first campaign :

/!\ Respect options order : /!\



    new MyElefantPlugin(Your custom parameters);



# Debug

For activate log system change this parameter in MyElefant.yaml

    APP_ENV = prod 
        to
    APP_ENV = dev

