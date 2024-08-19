<?php
namespace Tilko\API\Models;

/**
 * API Response의 기본 Object
 */
class BaseModel
{
	private $Status;
	private $Message;

	public function GetStatus()
	{
		return $this->Status;
	}

	public function SetStatus($Status)
	{
		$this->Status = $Status;

		return $this;
	}

	public function GetMessage()
	{
		return $this->Message;
	}

	public function SetMessage($Message)
	{
		$this->Message = $Message;

		return $this;
	}
}
/**
 * 건강보험공단 인증결과 데이터 모델
 */
class AuthResponse extends BaseModel
{
	private $AuthCode;

	function __construct($JsonStr)
    {
		$JsonArray = json_decode($JsonStr);
		foreach($JsonArray as $Key=>$Value){
		   $this->$Key = $Value;
		}

	}

	public function GetAuthCode()
	{
		return $this->AuthCode;
	}

	public function SetAuthCode($AuthCode)
	{
		$this->AuthCode = $AuthCode;

		return $this;
	}

}

/**
 * RSA 공개키 요청 결과 데이터 모델
 */
class RsaPublicKey extends BaseModel
{
	private $PublicKey;	//API 키에 매칭되는 RSA 공개키
	private $ApiKey;	//전달한 API 키(검증 용)

	function __construct($JsonStr)
    {
		$JsonArray = json_decode($JsonStr);
		foreach($JsonArray as $Key=>$Value){
		   $this->$Key = $Value;
		}

	}
	public function GetPublicKey()
	{
		return $this->PublicKey;
	}

	public function SetPublicKey($PublicKey)
	{
		$this->PublicKey = $PublicKey;

		return $this;
	}

	public function GetApiKey()
	{
		return $this->ApiKey;
	}

	public function SetApiKey($ApiKey)
	{
		$this->ApiKey = $ApiKey;

		return $this;
	}
}
?>
