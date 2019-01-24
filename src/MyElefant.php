<?php
namespace myelefant;
use Dotenv;
use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Symfony\Component\Yaml\Yaml;
use DateTime;
use Exception;
class MyElefant
{

    /**
     * @var logger
     */
    private $error;

    /**
     * @var logger
     */
    private $info;

    /**
     * @var string
     */
    private $token;

    /**
     * @var array
     */
    private $dotenv;

    /**
     * @var array
     */
    private $yamlDatas;

    /**
     * @var array
     */
    private $contacts;

    public function __construct(){
        $this->yamlDatas = Yaml::parseFile(__DIR__.'/../MyElefant.yaml');
        $this->dotenv = $this->initDotEnv();
        $this->token = $this->getAuthentification(getenv('SECRET_KEY'));

        if ($this->yamlDatas['APP_ENV'] == 'dev' ) {
            $this->error = $this->initLogger('error',__DIR__.'/../log/error.log');
            $this->info = $this->initLogger('info',__DIR__.'/../log/info.log');
        }

    }
    
    /**
     * Initialize Dotenv
     * @return array
     */

    public function initDotEnv(){
        $dotenv = Dotenv\Dotenv::create(__DIR__.'/..');
        return $dotenv->load();

    }

     /**
     * Initialize Logger
     * @return self
     */

    public function initLogger(string $name,string $path){
        $logger = new Logger($name);
        $logger->pushHandler(new StreamHandler($path));
        return $logger;
        
    }

    /**
     * @param DateTime format Y-m-d H:i | null
     * @return Exception|DateTime
     */

    public function getDate($date = null){
        try {
            $currentDate = new DateTime();
            $currentDate = $currentDate->format('Y-m-d H:i');

            if ($date != null ){
                if (DateTime::createFromFormat('Y-m-d H:i', $date) !== false) {
                    try {
                        $date = new DateTime($date);

                    } catch (Exception $e) {
                        $this->setLog('critical',$this->yamlDatas['CRITICAL_MESSAGE_DATE_FORMAT']);
                        return $this->yamlDatas['TARGET_EXCEPTION'].': '.$this->yamlDatas['CRITICAL_MESSAGE_DATE_FORMAT'];
                    }

                    $date = $date->format('Y-m-d H:i');
                    if ($date > $currentDate){
                        return $date;
    
                    }else {
                        $this->setLog('critical',$this->yamlDatas['CRITICAL_MESSAGE_DATE']);
                        throw new Exception($this->yamlDatas['CRITICAL_MESSAGE_DATE']);
    
                    }               
                }else{
                    $this->setLog('critical',$this->yamlDatas['CRITICAL_MESSAGE_DATE_FORMAT']);
                    throw new Exception($this->yamlDatas['CRITICAL_MESSAGE_DATE_FORMAT']);
                }
            }
        } catch (Exception $e){
            return ($this->yamlDatas['TARGET_EXCEPTION'].': '.$e->getMessage());

        }
        return $currentDate;
    }

    /**
     * @param string privateKey MyElefant secret key
     * @return string access_token 
     */

    public function getAuthentification($secretKey){
        try {
            $headers = ['Authorization' => 'Basic '.$secretKey];
            $client = new Client();
            $response = $client->request('POST', $this->yamlDatas['URL_MYELEFANT_API'].$this->yamlDatas['URL_MYELEFANT_API_AUTHENTIFICATION'],['headers' => $headers]); 
            if($response->getStatusCode() == 200) {
                $body = $response->getBody();
                $arr_body = json_decode($body);
                return $arr_body->access_token;

            }
        } catch(Exception $e) {
            $this->setLog($e->getMessage());
        }
    } 

    /**
     * @param array contact [[33612345678]]
     * @param string|null Send date '2019-01-20 12:00' if null Date = now
     * @param string|null Message to send
     * @param string|null Sender
     * @return void
     */

    public function sendSms(array $contacts, $sendDate=null, $message = null, $sender = null){
        

        // Debug
        // var_dump(getenv('CAMPAIGN_LEAD_ID'));
        // var_dump($this->yamlDatas['CAMPAIGN_NAME']);
        // var_dump($this->getContact($contacts));
        // var_dump($this->getDate($sendDate));
        // var_dump($this->yamlDatas['LOGIC']);
        // var_dump($this->getMessage($message));
        // var_dump($this->yamlDatas['DEFAULT_SENDER']); 

        try {
            if (!$this->checkFields($message,$sender)) {
                $this->setLog('critical',$this->yamlDatas['CRITICAL_MESSAGE_EMPTY_MESSAGE']);
                throw new Exception($this->yamlDatas['CRITICAL_MESSAGE_EMPTY_MESSAGE']);
            }

            $client = new Client();
            $response = $client->request('POST', $this->yamlDatas['URL_MYELEFANT_API'].$this->yamlDatas['URL_MYELEFANT_API_CREATE_CAMPAIGN'],
                [ 
                'headers' =>['Authorization' => $this->token, 'Content-Type'=>'application/json'],
                'json'=>[
                    'logic_param' =>getenv('CAMPAIGN_LEAD_ID'),
                    'name' => $this->yamlDatas['CAMPAIGN_NAME'],
                    'contacts'=> $this->getContact($contacts),
                    'send_date' => $this->getDate($sendDate),
                    'logic' =>$this->yamlDatas['LOGIC'],
                    'message' => $this->getMessage($message),
                    'sender' => $this->getSender($sender, $message)
                ]                    
            ]); 
            if($response->getStatusCode() == 200) {
                $body = $response->getBody();
                $arr_body = json_decode($body);
                if ($arr_body->success == true) {
                    $this->setLog('info',$this->yamlDatas['SUCCESS_MESSAGE'].', '.$this->yamlDatas['CREDIT_REMAINING'].' '.$arr_body->solde);

                }
            }
        } catch(Exception $e) {
            $this->setLog('critical',$e->getMessage());
        }
    } 

    /**
     * @param string
     * @return string|Exception
     */

    public function getMessage($message){
        try {
            if(strlen($message) > $this->yamlDatas['MAX_LENGTH_MESSAGE'] ){
                $this->setLog('warning',$this->yamlDatas['WARNING_MESSAGE_LENGTH']);
                throw new Exception($this->yamlDatas['WARNING_MESSAGE_LENGTH']);

            }
        } catch (Exception $e) {
            return ($this->yamlDatas['TARGET_EXCEPTION'].': '.$e->getMessage());

        }   
        return $message;
    }

    /**
     * @param array
     * @return array|null
     */

    public function getContact(array $contacts = null ){
        try {
            if (is_array($contacts[0])) {
                foreach ($contacts as $key) {
                    if(!$this->getPhoneNumber($key[0])){
                        $this->setLog('critical',$this->yamlDatas['CRITICAL_MESSAGE_PHONE_NUMBER_FORMAT']);
                        return new Exception($key[0].' '. $this->yamlDatas['CRITICAL_MESSAGE_PHONE_NUMBER_FORMAT']);

                    }
                }                
            }else {
                $this->setLog('critical',$this->yamlDatas['CRITICAL_MESSAGE_CONTACT_FORMAT']);
                throw new Exception($this->yamlDatas['CRITICAL_MESSAGE_CONTACT_FORMAT']);

            }
        } catch (Exception $e) {
            return ($this->yamlDatas['TARGET_EXCEPTION'].': '.$e->getMessage());

        }   
        return $contacts;
    }

    /**
     * @param string
     * @return bool 
     */

    private function getPhoneNumber(string $phoneNumber){
        if(preg_match($this->yamlDatas['REGEX_PHONE_NUMBER'],$phoneNumber)){
            return true;

        }
        return false;
    }

    /**
     * @param string $log level
     * @param string $message
     * @return void
     */

    private function setLog(string $logLevel, string $message){
        if ( isset($this->error) && isset($this->info) ) {
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
     * @param string|null
     * @param string|null
     * @return string|void
     */

    public function getSender($sender,$message){
        try {
            if ($sender == null && $message == null) {
                return;

            }elseif ($sender == null && $message != null) {
                return $this->yamlDatas['DEFAULT_SENDER'];

            }
            return $sender;
        } catch (Exception $e) {
            
        }
    }

    /**
     * @param string|null
     * @param string|null
     * @return bool
     */

    public function checkFields($message, $sender){
        if ($sender != null && $message == null) {
            return false;
        }
        return true;
    }
}





