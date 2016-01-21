<?php
namespace Fiorello\AffinityIntegration;

use Illuminate\Support\Facades\Config;
use SoapClient;

class Affinity
{

    private static $SOAPClient;
    // test details, need to change to live prior to rolling out
//    private static $WSDL_uri = 'https://apitest.akjl.co.uk/Global4/AffinityAPIService.svc?WSDL';
//    private static $username = 'Global4-ApiTest';
//    private static $password = 'IU8h3jhfdj';
    // live details
    private static $WSDL_uri = 'https://api.affinity.akjl.co.uk/Global4/AffinityAPIService.svc?WSDL';
    private static $username = 'Global4-API';
    private static $password = 'a98bcB3Gxn';
    private static $appName = 'Router Admin';
    private static $appRef = 'Add Ticket';

    private static $token = 0;

    // initialise device
    public function __construct() {
        self::$WSDL_uri = Config::get('affinity-integration::affinityWSDL');
        self::$username = Config::get('affinity-integration::affinityUserName');
        self::$password = Config::get('affinity-integration::affinityPassword');
        self::$appName  = Config::get('affinity-integration::affinityAppName');
        self::$appRef   = Config::get('affinity-integration::affinityAppRef');

        self::$SOAPClient = new SoapClient(self::$WSDL_uri, array("trace" => false, "exceptions" => true));
    }

    public static function makeCall($function, $data)
    {

        $data['IdentityToken'] = self::$token;
        $response = self::$SOAPClient->$function($data);

        $functionRet = $function . "Result";

        if($response)
        {
            if ($response->$functionRet->ResponseCode > 0) {
                var_dump($response);
            }
        }

        return $response;
    }

    // check if the SOAP extension is loaded
    public static function checkSoapConnection() {
        if (extension_loaded('soap')) {
            return 1;
        }
        return 0;
    }

    /*
     * Login method (returns an identity token)
     */
    public static function login() {
        self::$token = 0;
        $soapData = [
            'UserName' => self::$username,
            'Password' => self::$password,
            'ApplicationName' => self::$appName,
            'ApplicationReference' => self::$appRef
        ];

        $loginResult = self::$SOAPClient->GetIdentityToken($soapData);
        if ($loginResult) {
            if ($loginResult->GetIdentityTokenResult->ResponseCode == 0) {
                self::$token = $loginResult->GetIdentityTokenResult->IdentityToken;
            }
            else {
                var_dump($loginResult);
            }
        }
        return $loginResult;
    }

    /*
     * Logout method
     */
    public static function logout() {
        return self::$SOAPClient->IdentityTokenLogout(array('IdentityToken' => self::$token));
    }

    /*
     * Create ticket
     */
    public static function createNewTicket($siteID, $summary, $detail, $priority, $dateDue, $owner, $category, $subCategory) {
        $data = [
            'siteID' => $siteID,
            'summary' => $summary,
            'details' => $detail,
            'priority' => $priority,
            'dateDue' => $dateDue,
            'owner' => $owner,
            'category' => $category,
            '$subCategory' => $subCategory
        ];

        return self::makeCall('CreateNewTicketOnSIteByID',$data)->ticketID;
    }

    /*
     * Get Site ID
     */
    public static function getSiteId($accountNo) {
        return self::makeCall('GetSiteIDByRef',['siteRef' => $accountNo])->siteID;
    }
}