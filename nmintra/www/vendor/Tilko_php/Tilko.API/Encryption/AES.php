<?php
namespace Tilko\API\Encryption;

/**
 * AES 암호화 클래스 (AES-128-CBC)
 */
class AES{
	prIvate $Key;	// AES KEY
	prIvate $Iv;	// AES IV


	public function GetKey()
	{
		return $this->Key;
	}

	public function SetKey($Key)
	{
		$this->Key = $Key;
		return $this;
	}

	public function GetIv()
	{
		return $this->Iv;
	}

	public function SetIv($Iv)
	{
		$this->Iv = $Iv;
		return $this;
	}

	public function Encrypt($PlainText)
	{
		return openssl_encrypt($PlainText, 'AES-128-CBC', $this->Key, OPENSSL_RAW_DATA, $this->Iv);	//default padding은 PKCS7 padding
	}

	public function Decrypt($EncText)
	{
		return openssl_decrypt($EncText, 'AES-128-CBC', $this->Key, OPENSSL_RAW_DATA, $this->Iv);	//default padding은 PKCS7 padding
	}
}

?>