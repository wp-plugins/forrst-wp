<?php
/* Wrapper class around the Forrst API
 * @author Gerhard Potgieter <info@igeek.co.za>
 * @version 1.0
 */
 
class Forrst {
	var $username = 'kyle';
	var $count = 5;
	var $apiVersion = 'v1';
	var $apiURL = 'http://api.forrst.com/api/';
	
	function Forrst($username,$count) {
		if($username)
			$this->username = $username;
		
		if($count)
			$this->count = $count;
	}
	
	function getPosts() {
		$result = $this->fetch('/users/posts?username='.$this->username);
		$data = json_decode($result);
		return $data;
	}
	
	function fetch($params) {
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL,$this->apiURL.$this->apiVersion.$params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}
?>