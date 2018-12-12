<?php

	namespace Atiksoftware\Amiral;

	class Tools
	{

		static function UUID(){
			mt_srand((double)microtime()*10000);
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$h = chr(45);
			return substr($charid, 0, 8).$h.substr($charid, 8, 4).$h.substr($charid,12, 4).$h.substr($charid,16, 4).$h.substr($charid,20,12);
		}

		static function GetValue($key = NULL){
			return isset($_POST[$key]) ? Security::cleanXSS($_POST[$key]) : "";
		}
		static function GetParam($key = NULL){
			return isset($_GET[$key]) ? Security::cleanXSS($_GET[$key]) : "";
		}
		static function GetRequest($key = NULL){
			return \Swain\Tools::GetValue($key) ?? \Swain\Tools::GetParam($key) ?? "";
		}
		static function GetHeader($key = NULL){
			return isset($_SERVER[$key]) ? Security::cleanXSS($_SERVER[$key]) : "";
		}
		static function GetFile($key = NULL){
			return isset($_FILES[$key]) ? Security::cleanXSS($_FILES[$key]) : "";
		}
		static function GetJson($url = NULL){
			try{
				return json_decode(file_get_contents($url),true);
			} catch(customException $e) {
				return [];
			}
		}

		static function Redirect($path){
			header("location:".$path);die();
		}

		static function Execute($url){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT,1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
			curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 1);
			$response = curl_exec($ch);
			curl_close($ch);
		}


		static function Request($url = "",$headers = [],$json_decode = false){
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => $headers
			));
			$response = curl_exec($curl);
			if($json_decode){
				$response = (array)@json_decode(@$response,true);
			}
			curl_close($curl);
			return $response;
		}


	}
