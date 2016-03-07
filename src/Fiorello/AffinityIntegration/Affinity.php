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

    public static function makeCall($function, $data = [])
    {
        $data['IdentityToken'] = self::$token;
        $data['identityToken'] = self::$token;

        var_dump($data);

        echo self::$token;

        $response = self::$SOAPClient->$function($data);

        var_dump($response);

        if($function == "XMLGetProductByID")
        {

            Affinity::logout();
            exit;
        }

        $functionRet = $function . "Result";

        if($response)
        {
            if ($response->$functionRet->ResponseCode > 0) {
                $response->errorCode = $response->$functionRet->ResponseCode;
                $response->errorMessage = $response->$functionRet->ResponseMessage ;
                unset($response->$functionRet);
            } else {
                $response->errorCode = 0;
                $response->errorMessage = null;
                unset($response->$functionRet);

                $responseArr = array_slice(get_object_vars($response),0,1);

                $keys = array_keys($responseArr);

                if(gettype($responseArr[$keys[0]]) == "object") {

                    $object = $responseArr[$keys[0]];
                    $resultKey = ucwords($keys[0]);

                    $response->result = json_encode(simplexml_load_string($object->any)->NewDataSet->$resultKey);
                    //$response->properties = get_object_vars($response->result);
                    $response->properties = array_keys(get_object_vars(simplexml_load_string($object->any)->NewDataSet->$resultKey));
                } else {
                    $response->result = $responseArr[$keys[0]];
                }

                unset($response->$keys[0]);
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

        var_dump($loginResult);
        echo self::$token;
        return $loginResult;
    }

    /*
     * Logout method
     */
    public static function logout() {
        if(!empty(self::$token )) {
            return self::$SOAPClient->IdentityTokenLogout(array('IdentityToken' => self::$token));
        }
        return false;
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

        return self::makeCall('CreateNewTicketOnSiteByID',$data)->ticketID;
    }

    /*
     * Get Site ID
     */
    public static function getSiteId($accountNo) {
        return self::makeCall('GetSiteIDByRef',['siteRef' => $accountNo]);
    }

    /*
     * Get Site Details using Site ID
     */
    public static function getSite($siteID) {
        return self::makeCall('GetSiteByID', ['siteID' => $siteID]);
    }


    public static function getSiteCLIs($subSiteID)
    {
        return self::makeCall('GetSiteUserCLIsByID', ['subSiteID' => $subSiteID]);
    }

    public static function addCLIProduct($productType, $siteID, $cli)
    {

        $xml = null;
        $xml =  "<![CDATA[".$xml."]]>";
        return self::makeCall('XMLAddProduct', ['productType' => $productType, 'productXML' => $xml]);
    }

//    public static function getProduct($productID, $productType)
//    {
//        return self::makeCall('XMLGetProductByID',['productInstanceID'=>$productID, 'productType'=>$productType]);
//    }

    public static function getProduct($productID)
    {
        return self::makeCall('GetProductItemByID',['productItemID'=>$productID]);
    }
}