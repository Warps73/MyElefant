<?php namespace myelefant;

/**
 * MyElefant plugin
 * To sending sms with myElefant service
 * https://myelefant.com/
 */

use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use DateTime;
use Exception;
use Dotenv\Dotenv;

class MyElefant
{

    /**
     * Error log
     * @var logger
     */
    private $error;

    /**
     * Info log
     * @var logger
     */
    private $info;


    /**
     * Token
     * @var string
     */
    private $token;

    /**
     * Initialize plugin
     * @var array|null $config Plugin's config
     * @throws @Exception
     * @return void
     */
    public function __construct($config)
    {

        if (isset($config['debug']) === true) {
            $this->error = $this->initLogger(
                'error',
                'var/log/error.log'
            );
            $this->info  = $this->initLogger(
                'info',
                'var/log/info.log'
            );
        }

        if (!isset($config['secretKey'])) {
            $this->setLog(
                'critical',
                MyElefantConfig::CRITICAL_MESSAGE_EMPTY_SECRET_KEY
            );
            throw new Exception(MyElefantConfig::CRITICAL_MESSAGE_EMPTY_SECRET_KEY);
        } else {
            $this->token = $this->setAuthentification($config['secretKey']);
        }
    }

    /**
     * Initialize Logger
     *
     * @param string $name Log file's name
     * @param string $path Path to log folder
     * @throws @Exception
     * @return Logger
     */
    private function initLogger($name, $path)
    {
        $logger = new Logger($name);
        $logger->pushHandler(new StreamHandler($path));
        return $logger;
    }

    /**
     * Setting date if not null or current date if null
     *
     * @param string $date format Y-m-d H:i | null
     *
     * @return string
     * @throws @Exception
     */
    private function setDate($date = null)
    {

        $currentDate = new DateTime();
        $currentDate = $currentDate->format('Y-m-d H:i');

        if ($date !== null) {
            if (DateTime::createFromFormat('Y-m-d H:i', $date) !== false) {
                try {
                    $date = new DateTime($date);
                } catch (\Throwable $e) {
                    $this->setLog('critical', $e->getMessage());
                    throw new Exception($e->getMessage());
                }

                $date = $date->format('Y-m-d H:i');
                if ($date > $currentDate) {
                    return $date;
                } else {
                    $this->setLog(
                        'critical',
                        MyElefantConfig::CRITICAL_MESSAGE_DATE
                    );
                    throw new Exception(MyElefantConfig::CRITICAL_MESSAGE_DATE);

                }
            } else {
                $this->setLog(
                    'critical',
                    MyElefantConfig::CRITICAL_MESSAGE_DATE_FORMAT
                );
                throw new Exception(MyElefantConfig::CRITICAL_MESSAGE_DATE_FORMAT);

            }
        }
        return $currentDate;
    }

    /**
     * Setting authentification to MyElefant's api
     *
     * @param string $secretKey MyElefant secret key
     *
     * @return string access_token
     * @throws @Exception
     */
    private function setAuthentification($secretKey)
    {

        $headers = [ 'Authorization' => 'Basic ' . $secretKey ];
        try {
            $client   = new Client();
            $response = $client->request(
                'POST',
                MyElefantConfig::URL_MYELEFANT_API . MyElefantConfig::URL_MYELEFANT_API_AUTHENTIFICATION,
                [ 'headers' => $headers ]
            );
            if ($response->getStatusCode() == 200) {
                $body     = $response->getBody();
                $arr_body = json_decode($body);
                return $arr_body->access_token;
            }
        } catch (\Throwable $e) {
            $this->setLog('critical', $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Sending sms
     *
     * @param string      $campaignId Campaign's id
     * @param string      $campaignName Campaign's name
     * @param array       $contacts Contact's array [['33612233445','John'(Optional),Doe(Optional)],[...]]
     * @param string|null $sendDate Custom date|Current date '2019-01-24 12:00'
     * @param string|null $message Custom message|Template message
     * @param string|null $sender Custom sender|Template sender
     *
     * @throws @Exception
     * @return void
     */
    public function sendSms($campaignId, $campaignName, $contacts, $sendDate = null, $message = null, $sender = null)
    {

        if (!$this->checkFields($message, $sender)) {
            $this->setLog(
                'critical',
                MyElefantConfig::CRITICAL_MESSAGE_EMPTY_MESSAGE
            );
            throw new Exception(MyElefantConfig::CRITICAL_MESSAGE_EMPTY_MESSAGE);
        }
        try {
            $client   = new Client();
            $response = $client->request(
                'POST',
                MyElefantConfig::URL_MYELEFANT_API . MyElefantConfig::URL_MYELEFANT_API_CREATE_CAMPAIGN,
                [
                    'headers' => [
                        'Authorization' => $this->token,
                        'Content-Type'  => 'application/json'
                    ],
                    'json'    => [
                        'logic_param' => $campaignId,
                        'name'        => $campaignName,
                        'contacts'    => $this->setContact($contacts),
                        'send_date'   => $this->setDate($sendDate),
                        'logic'       => 'duplicate',
                        'message'     => $this->setMessage($message),
                        'sender'      => $sender
                    ]
                ]
            );
        } catch (\Throwable $e) {
            $this->setLog('critical', $e->getMessage());
            throw new Exception($e->getMessage());
        }
        if ($response->getStatusCode() == 200) {
            $body     = $response->getBody();
            $arr_body = json_decode($body);
            if ($arr_body->success == true) {
                $this->setLog(
                    'info',
                    MyElefantConfig::SUCCESS_MESSAGE . ', ' . MyElefantConfig::CREDIT_REMAINING . ' ' . $arr_body->solde
                );
            }
        }
    }

    /**
     * Setting message
     *
     * @param string $message Custom message
     *
     * @return string
     * @throws @Exception
     */
    private function setMessage($message)
    {

        if (strlen($message) > MyElefantConfig::MAX_LENGTH_MESSAGE) {
            $this->setLog('warning', MyElefantConfig::WARNING_MESSAGE_LENGTH);
            throw new Exception(MyElefantConfig::WARNING_MESSAGE_LENGTH);
        }
        return $message;
    }

    /**
     * Setting contact
     *
     * @param array $contacts Contact's array
     *
     * @throws @Exception
     * @return array|null
     */
    private function setContact($contacts)
    {

        if (is_array($contacts) && $this->checkContactsFormat($contacts)) {
            foreach ($contacts as $key) {
                if (!$this->checkPhoneNumber($key[0])) {
                    $this->setLog(
                        'critical',
                        MyElefantConfig::CRITICAL_MESSAGE_PHONE_NUMBER_FORMAT
                    );
                    throw new Exception($key[0] . ' ' . MyElefantConfig::CRITICAL_MESSAGE_PHONE_NUMBER_FORMAT);
                }
            }
        } else {
            $this->setLog(
                'critical',
                MyElefantConfig::CRITICAL_MESSAGE_CONTACT_FORMAT
            );
            throw new Exception(MyElefantConfig::CRITICAL_MESSAGE_CONTACT_FORMAT);
        }
        return $contacts;
    }

    /**
     * Checking phone number's format
     *
     * @param string $phoneNumber Phone number
     *
     * @return bool
     */
    private function checkPhoneNumber($phoneNumber)
    {
        if (preg_match(MyElefantConfig::REGEX_PHONE_NUMBER, $phoneNumber)) {
            return true;
        }
        return false;
    }

    /**
     * Setting log
     *
     * @param string $logLevel Log's level
     * @param string $message Message
     *
     * @return void
     */
    private function setLog($logLevel, $message)
    {
        if (isset($this->error) && isset($this->info)) {
            switch ($logLevel) {
                case 'critical':
                    $this->error->critical($message);
                    break;
                case 'warning':
                    $this->error->warning($message);
                    break;
                default:
                    $this->info->info($message);
                    break;
            }
        }
    }

    /**
     * Checking fields
     *
     * @param string|null $message Message
     * @param string|null $sender Sender
     *
     * @return bool
     */
    private function checkFields($message = null, $sender = null)
    {
        if ($sender !== null && $message === null) {
            return false;
        }
        return true;
    }

    /**
     * Checking the format of the contact's array
     *
     * @param array $contacts Contact
     *
     * @return bool
     */
    private function checkContactsFormat($contacts)
    {
        foreach ($contacts as $key) {
            if (!is_array($key)) {
                return false;
            }
        }
        return true;
    }
}
