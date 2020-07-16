<?php

    namespace jackbayliss\DHLApi\Api;
    use Illuminate\Support\Facades\Log;

    /**
     * DHL Abstract API.
     */
    abstract class DHLAbstractAPI
    {
        protected $_stagingUrl = 'https://xmlpitest-ea.dhl.com/XMLShippingServlet?isUTF8Support=true';
        protected $_productionUrl = 'https://xmlpi-ea.dhl.com/XMLShippingServlet?isUTF8Support=true';
        protected $xmlRequest;
        protected $response;
        protected $rawresponse;
        protected $siteid;
        protected $password;
        protected $accountNumber;
        protected $_mode;
        protected $currency;
        protected $reference;
        
        public function __construct()
        {
            $this->siteid = config('dhlapiconfig.siteid')?: getenv("DHL_SITEID");
            $this->password = config('dhlapiconfig.password')?: getenv("DHL_PASSWORD");
            $this->_mode = getenv('APP_ENV') ?: config('app.env');
            $this->accountNumber = config('dhlapiconfig.account_number')?: getenv("DHL_ACCOUNT_NUMBER");
        }

        public function getResponse(): Object
        {
            if ($this->_mode == "production") {
                $ch = curl_init($this->_productionUrl);
            } else {
                $ch = curl_init($this->_stagingUrl);
            }

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_PORT, 443);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xmlRequest());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

            $response = curl_exec($ch);

            curl_close($ch);

            $this->rawresponse = $response;

            try {
                $this->response = simplexml_load_string($response);
            } catch (\Exception $exception) {
                return FALSE;
            }
            if(isset($this->response->Response->Status->ActionStatus) && $this->response->Response->Status->ActionStatus=="Error"){
                // If you're seeing this the API has gave you an error, it's usually to do with the data you've passed in.
                if($this->_mode=="local"){
                    throw new \Exception($this->response->Response->Status->Condition->ConditionData);
                }else{
                    Log::error("DHL API ERROR: " . $this->response->Response->Status->Condition->ConditionData);
                    abort(500,$this->response->Response->Status->Condition->ConditionData);
                }
            }else{  
                return $this->response;
            }
        }

   

        public function mode($value = NULL):Self
        {
            if (empty($value)) {
                return $this->_mode;
            }

            $this->_mode = $value;

            return $this;
        }
        
        public function xmlRequest():String
        {
            if (!isset($this->xmlRequest)) {
                $this->toXML();
            }

            return $this->xmlRequest;
        }

        public function getRawResponse()
        {
            if (empty($this->rawresponse)) {
                $this->getResponse();
            }

            return $this->rawresponse;
        }

        public function currency($value = NULL):Self
        {
            if (empty($value)) {
                return $this->currency;
            }

            $this->currency = $value;

            return $this;
        }
        public function reference($value = NULL):Self
        {
            if (empty($value)) {
                return $this->reference;
            }

            $this->reference = $value;

            return $this;
        }
        
    }