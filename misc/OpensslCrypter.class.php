<?php

	if(! defined('OPENSSL_CRYPTER'))
	{
		define('OPENSSL_CRYPTER', 1) ;
		
		class OpensslCrypter {
			public $cipher = "AES-256-CFB" ;
			public $key = "I2DKS434EM" ;
			public $hmac = "sha256" ;
			public $useHmac = true ;
			protected $cryptMode = 1 ;
			public function __construct($cipher='', $key='', $hmac='') {
				if($cipher != '') {
					$this->cipher = $cipher ;
				}
				if($key != '') {
					$this->key = $key ;
				}
				if($hmac != '') {
					$this->hmac = $hmac ;
				}
			}
			public function encode($value) {
				$ivlen = openssl_cipher_iv_length($this->cipher);
				$iv = openssl_random_pseudo_bytes($ivlen);
				$ciphertext_raw = openssl_encrypt($value, $this->cipher, $this->key, $this->cryptMode, $iv);
				if(! $this->useHmac) {
					return base64_encode($ciphertext_raw) ;
				}
				$hmac = hash_hmac($this->hmac, $ciphertext_raw, $this->key, true);
				return base64_encode( $iv.$hmac.$ciphertext_raw );
			}
			public function decode($value) {
				$c = base64_decode($value);
				$ivlen = openssl_cipher_iv_length($this->cipher);
				$iv = substr($c, 0, $ivlen);
				if(! $this->useHmac) {
					return openssl_decrypt($c, $this->cipher, $this->key, $this->cryptMode, $iv) ;
				}
				$sha2len = 32 ;
				$hmac = substr($c, $ivlen, $sha2len);
				$ciphertext_raw = substr($c, $ivlen+$sha2len);
				return openssl_decrypt($ciphertext_raw, $this->cipher, $this->key, $this->cryptMode, $iv);
			}
		}
	}

