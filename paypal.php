<?php 
class Paypal {

	public $prod = false;

	private $user = "tata_api1.astaen.fr";
	private $pwd = "1406116574";
	private $signature = "AyqwUFUKZ1pDWuLmQwBLRDKvUT3lA70ic95Id5ttzsE.qyRMnP-SsbXT";
	private $endpoint = 'https://api-3t.sandbox.paypal.com/nvp';		

	public $errors = Array();

	public function __construct($user = false, $pwd = false, $signature = false, $prod = false) {
		if($user) {
			$this->user = $user;
		}
		if($pwd) {
			$this->pwd = $pwd;
		}

		if($signature) {
			$this->signature = $signature;
		}

		if($this->prod) {
			$this->user = "contact_api1.harmony-evenements.ovh";
			$this->pwd = "64XDRWDRVMDC3J2S";
			$this->signature = "AFcWxV21C7fd0v3bYYYRCpSSRl31AUCDAMZiZdIykNhZkiDjEPeerK75";
			$this->endpoint = 'https://api-3t.paypal.com/nvp';	
		}
	}

	public function request($method, $params) {

		$params = array_merge($params, array(
			'VERSION' => '115.0',
			'USER' => $this->user,
			'SIGNATURE' => $this->signature,
			'PWD' => $this->pwd,
			'METHOD' => $method
		));

		$params = http_build_query($params); //Création d'une chaine a partir des params Paypal

		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $this->endpoint,
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $params,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false
		));
		$response = curl_exec($curl);
		$responseArray = Array();
		parse_str($response, $responseArray);

		if(curl_errno($curl)) {
			$this->errors = (curl_error($curl));
			curl_close($curl);
			return false;
		} else {
			if($responseArray['ACK'] == 'Success') {
				return $responseArray;
			} else {
				$this->errors = $responseArray;
				curl_close($curl);
				return false;
			}
		}
	}
} 
?>