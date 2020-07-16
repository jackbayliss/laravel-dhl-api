<?php
namespace jackbayliss\DHLApi\Calls;
use jackbayliss\DHLApi\Api\DHLAbstractAPI;

    class GetRouting extends DHLAbstractAPI
    {
        private $address;

        public function __construct()
        {
            parent::__construct();

        }

        /**
         * Called from DHLAbstractAPI
         *
         * @return [string] XML string; 
         */
        public function toXML():String
        {
            $xml = new \XmlWriter();
            $xml->openMemory();
            $xml->setIndent(TRUE);
            $xml->setIndentString("  ");
            $xml->startDocument('1.0', 'UTF-8');
            $xml->startElement('ns1:RouteRequest');
            $xml->writeAttribute('xmlns:ns1', "http://www.dhl.com");
            $xml->writeAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
            $xml->writeAttribute('xsi:schemaLocation', "http://www.dhl.com routing-global-req.xsd");
            $xml->writeAttribute('schemaVersion', '1.0');
            $xml->startElement('Request');
            $xml->startElement('ServiceHeader');
            $xml->writeElement('MessageTime', date('Y-m-d') . "T" . date('H:i:s') . ".000+02:00");
            $xml->writeElement('MessageReference', $this->reference); 
            $xml->writeElement('SiteID', $this->siteid);
            $xml->writeElement('Password', $this->password);
            $xml->endElement();
            $xml->endElement();
            $xml->writeElement('RegionCode', $this->address->RegionCode);
            $xml->writeElement('RequestType', $this->address->RequestType);
            $xml->writeElement('Address1', $this->address->Address1);
            $xml->writeElement('Address2', $this->address->Address2);
            $xml->writeElement('Address3', $this->address->Address3);
            $xml->writeElement('PostalCode', $this->address->PostalCode);
            $xml->writeElement('City', $this->address->City);
            $xml->writeElement('Division', $this->address->Division);
            $xml->writeElement('CountryCode', $this->address->CountryCode);
            $xml->writeElement('CountryName', $this->address->CountryName);
            $xml->writeElement('OriginCountryCode', $this->address->OriginCountryCode);
            $xml->endElement();
            $xml->endElement();
            $xml->endDocument();
            return $this->xmlRequest = $xml->outputMemory();
        }

        
        public function address($value = NULL):Self
        {
            if (empty($value)) {
                return $this->address;
            }

            $this->address = (object)$value;

            return $this;
        }

        
    }