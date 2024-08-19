<?php
namespace Tilko\API;

require_once($BasePath . "/UnitTest/Constant.php");
require_once($BasePath . "/Tilko.API/Models/Models.php");
require_once($BasePath . "/Tilko.API/Encryption/AES.php");
set_include_path(get_include_path() . PATH_SEPARATOR . $BasePath . '/phpseclib1.0.19');
require_once('Crypt/RSA.php');

use Tilko\API\Models, Tilko\API\Encryption;
use UnitTest;

if( !function_exists('random_bytes') ) {
	function random_bytes($length) {
		$cryptoStrong = true; // can be false
		$bytes = openssl_random_pseudo_bytes($length, $cryptoStrong);
		return $bytes;
	}	
}

/**
 * RESTful API 호출을 위한 클래스입니다.
*/
class REST
{
    private $_apiServer = "";
    private $_endPointUrl = "";
    private $_apiKey = "";     // API키
    private $_aesKey = array();     // AES 암호화에 사용할 키
    private $_aesIv = array(0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00, 0x00);  // AES 암호화에 사용할 IV(고정)
    private $_rsaPublicKey = array();
    private $_aes;   // Tilko\Api\Encryption\AES
    private $_headers = array();
    private $_bodies = array();

    /// <summary>
    /// API 호출에 따른 HttpStatusCode
    /// </summary>
    public $HttpStatusCode;

    /// <summary>
    /// 메시지
    /// </summary>
    public $Message;

    /// <summary>
    /// 생성자
    /// </summary>
    /// <param name="ApiKey">API키</param>
    public function __construct($ApiKey)
    {
        $Constant = new \UnitTest\Constant;
        
        $this->_apiServer = $Constant::ApiHost;
        
        if (empty($ApiKey))
        {
            throw new \Exception("API key cannot be null or empty.");
        }

        $this->_apiKey = $ApiKey;
    }

    /// <summary>
    /// API 호출 URL 설정
    /// </summary>
    /// <param name="EndPointUrl">API 호출 URL</param>
    public function SetEndPointUrl($EndPointUrl)
    {
        if (empty($EndPointUrl))
        {
            throw new \Exception("EndPointUrl cannot be null or empty.");
        }
        $this->_endPointUrl = $EndPointUrl;
    }

    /// <summary>
    /// 공개키 획득
    /// </summary>
    public function GetPublicKey()
    {
        $Url = UnitTest\Constant::ApiHost . "api/Auth/GetPublicKey?APIkey=" . $this->_apiKey;

        $CUrl = curl_init();

		curl_setopt_array($CUrl, array(
            CURLOPT_URL => $Url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
		));

		$Response = curl_exec($CUrl);

		curl_close($CUrl);
		return $Response;
    }

    /// <summary>
    /// API BODY에 들어갈 값을 추가합니다.
    /// </summary>
    /// <param name="Key">Body에 삽입할 Key</param>
    /// <param name="Value">Body에 삽입할 Value</param>
    public function AddBody($Key, $Value, $Encrypt=false) {
        try {
            if (array_key_exists($Key, $this->_bodies)) {
                throw new \Exception("There is already the same key in bodies.");
            }

            if ($Encrypt) {
                $this->_bodies[$Key] = base64_encode($this->_aes->Encrypt($Value));
            } else {
                $this->_bodies[$Key] = $Value;
            }
        }
        catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }
    
    /// <summary>
    /// REST를 초기화 합니다.
    /// 전달 받은 API키에 대응되는 RSA 공개키를 서버로부터 수신합니다.
    /// </summary>
    public function Init()
    {
        try
        {
            $this->_headers = array();
            $this->_bodies = array();

            // EndPoint init
            $this->_endPointUrl = "";

            // AES init
            $this->_aesKey = random_bytes(16);
            $this->_aes = new \Tilko\API\Encryption\AES();
            $this->_aes->SetKey($this->_aesKey);
            $this->_aes->SetIv(str_repeat(chr(0), 16));

            // 틸코 인증 서버에 RSA 공개키 요청
            $_pubKey = $this->GetPublicKey();
            $_rsaPubKeyObj = new Models\RsaPublicKey($_pubKey);
            
            // Encrypt AES key
            $_aesCipherKey = array();
            
            $_rsa = new \Crypt_RSA();
            $_rsa->loadKey($_rsaPubKeyObj->GetPublicKey());
            $_rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

            // RSA공개키로 AES키를 암호화
            $_aesCipheredKey = $_rsa->encrypt($this->_aes->GetKey());
            
            $this->_headers = array(
                "Content-Type: application/json; charset=utf-8",
                "API-Key: " . $this->_apiKey,
                "ENC-Key: " . base64_encode($_aesCipheredKey),
            );
        }
        catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    public function Call() {
        $Curl = curl_init();

		curl_setopt_array($Curl, array(
			CURLOPT_URL => $this->_endPointUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($this->_bodies),
			CURLOPT_HTTPHEADER => $this->_headers,
			CURLOPT_VERBOSE => false,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0
		));

		$Response = curl_exec($Curl);

		curl_close($Curl);
		return $Response;
    }
}

?>
