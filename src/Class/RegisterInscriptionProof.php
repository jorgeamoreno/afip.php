<?php
/**
 * SDK for AFIP Register Inscription Proof (ws_sr_constancia_inscripcion)
 **/

class RegisterInscriptionProof extends AfipWebService
{
    public $soap_version 	= SOAP_1_1;
    public $WSDL 			= 'ws_sr_padron_a5-production.wsdl';
    public $URL 			= 'https://aws.afip.gov.ar/sr-padron/webservices/personaServiceA5';
    public $WSDL_TEST 		= 'ws_sr_padron_a5.wsdl';
    public $URL_TEST 		= 'https://awshomo.afip.gov.ar/sr-padron/webservices/personaServiceA5';

    /**
     * Asks to web service for servers status {@see WS
     * Specification item 3.1}
     *
     * @since 1.0
     *
     * @return object { appserver => Web Service status,
     * dbserver => Database status, authserver => Autentication
     * server status}
    **/
    public function GetServerStatus()
    {
        return $this->ExecuteRequest('dummy');
    }

    /**
     * Asks to web service for taxpayer details {@see WS
     * Specification item 3.2}
     *
     * @since 1.0
     *
     * @throws Exception if exists an error in response
     *
     * @return object|null if taxpayer does not exists, return null,
     * if it exists, returns full response {@see
     * WS Specification item 3.2.2}
    **/
    public function GetTaxpayerDetails($identifier)
    {
        $ta = $this->afip->GetServiceTA('ws_sr_constancia_inscripcion');

        $params = array(
            'token' 			=> $ta->token,
            'sign' 				=> $ta->sign,
            'cuitRepresentada' 	=> $this->afip->CUIT,
            'idPersona' 		=> $identifier
        );

        try {
            return $this->ExecuteRequest('getPersona_v2', $params);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'No existe') !== false) {
                return null;
            } else {
                throw $e;
            }
        }
    }

    /**
     * Asks to web service for taxpayers details
     *
     * @throws Exception if exists an error in response
     *
     * @return [object] returns web service full response
    **/
    public function GetTaxpayersDetails($identifiers)
    {
        $ta = $this->afip->GetServiceTA('ws_sr_constancia_inscripcion');

        $params = array(
            'token' 			=> $ta->token,
            'sign' 				=> $ta->sign,
            'cuitRepresentada' 	=> $this->afip->CUIT,
            'idPersona' 		=> $identifiers
        );

        return $this->ExecuteRequest('getPersonaList_v2', $params)->persona;
    }

    /**
     * Sends request to AFIP servers
     *
     * @since 1.0
     *
     * @param string 	$operation 	SOAP operation to do
     * @param array 	$params 	Parameters to send
     *
     * @return mixed Operation results
     **/
    public function ExecuteRequest($operation, $params = array())
    {
        $this->options = array('service' => 'ws_sr_constancia_inscripcion');

        $results = parent::ExecuteRequest($operation, $params);

        return $results->{
            $operation === 'getPersona_v2' ? 'personaReturn' :
                ($operation === 'getPersonaList_v2' ? 'personaListReturn' : 'return')
            };
    }
}
