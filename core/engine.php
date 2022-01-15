<?php
	$_ENV['dbAccess'] = connect();

	function connect(){
		if(isset($_ENV['sqlConnector'])){
			try{
				$pdo = new PDO($_ENV['sqlConnector']["dbDriver"].':host='.$_ENV['sqlConnector']["dbHost"].';dbname='.$_ENV['sqlConnector']["dbName"], $_ENV['sqlConnector']["dbUser"], $_ENV['sqlConnector']["dbPass"], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_general_ci'));
			}catch(Exception $e){
				return $e->getMessage();
			}
			return $pdo;
		}else{
			return false;
		}
	}

	function request($request){
		if($_ENV['dbAccess']){
			$pdo_request = $_ENV['dbAccess']->prepare($request);

			if($pdo_request->execute() && strtolower(substr($request, 0, 6))  == "select"){
				$resultData = $pdo_request->fetchAll(PDO::FETCH_NAMED);
				return $resultData;
			}else{
				if($pdo_request->errorCode() != "00000"){
					return false;
				}else{
					return true;
				}
			}
		}else{
			return "Database connection error.";
		}
	}

	function getEndpoints(){
		return request("SELECT * FROM endpoints");
	}

	function accessChecker(){
		return $_SESSION['access'];
	}

	function installSQLChecker(){
		$state = true;
		if(!request("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'endpoints'")){
			$state = false;
		}
		if(!request("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'domains'")){
			$state = false;
		}
		if(!request("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'parameters'")){
			$state = false;
		}

		return $state;
	}

	function userAPIAccessChecker(){
		$state = true;
		if(empty($_ENV["apiConnector"]["apiUser"]) || empty($_ENV["apiConnector"]["apiPass"])){
			$state = false;
		}

		return $state;
	}

	function insertEndpoint($name, $description, $method, $path, $function, $state, $parametersName, $parametersType, $domainsName){
		$endpoint = request("INSERT INTO endpoints (name, description, method, pathToFile, functionName, available) VALUES ('$name', '$description', '$method', '$path', '$function', '$state')");

		if($endpoint){
			$idEndpoint = request("SELECT LAST_INSERT_ID() as id")[0]["id"];
			if(isset($parametersName) && isset($parametersType) && $parametersName != false && $parametersType != false){
				foreach ($parametersName as $key => $value) {
					request("INSERT INTO parameters (idEndpoint, parameter, type) VALUES ('$idEndpoint', '".$parametersName[$key]."', '".$parametersType[$key]."')");
				}
			}
			if(isset($domainsName) && $domainsName != false){
				foreach ($domainsName as $key => $value) {
					request("INSERT INTO domains (idEndpoint, domain) VALUES ('$idEndpoint', '".$domainsName[$key]."')");
				}
			}
			return true;
		}else{
			return false;
		}
	}

	function updateEndpoint($id, $name, $description, $method, $path, $function, $state, $parametersName, $parametersType, $domainsName){
		$endpoint = request("UPDATE endpoints SET name='$name', description='$description', method='$method', pathToFile='$path', functionName='$function', available='$state' WHERE id = $id");

		if($endpoint){
			request("DELETE FROM `parameters` WHERE idEndpoint = $id");
			if(isset($parametersName) && isset($parametersType) && $parametersName != false && $parametersType != false){
				foreach ($parametersName as $key => $value) {
					request("INSERT INTO parameters (idEndpoint, parameter, type) VALUES ('$id', '".$parametersName[$key]."', '".$parametersType[$key]."')");
				}
			}
			request("DELETE FROM `domains` WHERE idEndpoint = $id");
			if(isset($domainsName) && $domainsName != false){
				foreach ($domainsName as $key => $value) {
					request("INSERT INTO domains (idEndpoint, domain) VALUES ('$id', '".$domainsName[$key]."')");
				}
			}
			return true;
		}else{
			return false;
		}
	}

	function deleteEndpoint($id){
		if(request("DELETE FROM domains WHERE idEndpoint = $id")){
			if(request("DELETE FROM parameters WHERE idEndpoint = $id")){
				return request("DELETE FROM endpoints WHERE id = $id");
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	function getEndpointByName($name){
		$endpoint =  request("SELECT * FROM endpoints WHERE name = '$name'");
		if(count($endpoint) == 1){
			$endpoint =  $endpoint[0];
			if($endpoint["available"]){
				$endpoint["domains"] = request("SELECT domain FROM domains WHERE idEndpoint = '".$endpoint["id"]."' ORDER BY id ASC");
				$endpoint["parameters"] = request("SELECT parameter, type FROM parameters WHERE idEndpoint = '".$endpoint["id"]."' ORDER BY id ASC");
				return $endpoint;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	function getEndpointById($id){
		$endpoint =  request("SELECT * FROM endpoints WHERE id = $id");
		if(count($endpoint) == 1){
			$endpoint =  $endpoint[0];
			$endpoint["domains"] = request("SELECT domain FROM domains WHERE idEndpoint = $id  ORDER BY id ASC");
			$endpoint["parameters"] = request("SELECT parameter, type FROM parameters WHERE idEndpoint = $id  ORDER BY id ASC");
			return $endpoint;
		}else{
			return false;
		}

	}

	function typeChecker($type, $parametersUrl){
		if($type == "int"){
			return is_int($parametersUrl);
		}elseif($type == "numeric"){
			return is_numeric($parametersUrl);
		}elseif($type == "string"){
			return is_string($parametersUrl);
		}elseif($type == "object"){
			return is_object($parametersUrl);
		}elseif($type == "float"){
			return is_float($parametersUrl);
		}elseif($type == "array"){
			return is_array($parametersUrl);
		}elseif($type == "bool"){
			return is_bool($parametersUrl);
		}else{
			return $type == "unsecured";
		}		
	}

	function callAnalyzer($function,$parametersDatabase, $parametersUrl){
		$parameterChecker = parameterChecker($parametersDatabase, $parametersUrl);
		if($parameterChecker["result"]){
			$parameters = $parameterChecker["parameters"];
			if(str_contains($function, '::')){
				// static function in class
				$tmp = explode("::", $function);
				$class = $tmp[0];
				$function = $tmp[1];
				return "\$x = new {$class}(); return \$x::{$function}({$parameters});";
			}elseif(str_contains($function, '->')){
				// normal function in class
				$tmp = explode("->", $function);
				$class = $tmp[0];
				$function = $tmp[1];
				return "\$x = new {$class}(); return \$x->{$function}({$parameters});";
			}elseif(!str_contains($function, '->') && !str_contains($function, '::')){
				return "return {$function}({$parameters});";
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	function parameterChecker($parametersDatabase, $parametersUrl){
		$data["parameters"] = "";
		foreach ($parametersDatabase as $key => $value) {
			$parameter = $parametersDatabase[$key]["parameter"];
			$type = $parametersDatabase[$key]["type"];
			if(array_key_exists($parameter, $parametersUrl)){
				if(typeChecker($type,$parametersUrl[$parameter])){
					if(is_string($parametersUrl[$parameter]) || is_numeric($parametersUrl[$parameter])){
						$data["parameters"] .= "'".$parametersUrl[$parameter]."',";
					}else{
						$data["parameters"] .= $parametersUrl[$parameter].",";
					}
				}else{
					$data["result"] = false;
					return $data;
				}
			}else{
				$data["result"] = false;
				return $data;
			}
		}
		rtrim($data["parameters"],',');
		$data["result"] = true;
		return $data;
	}

	function domainChecker($allowedDomains){
		if(!isset($_SERVER["HTTP_ORIGIN"])){
			header('HTTP/1.0 403 Forbidden');
			return false;
		}else{
			$domain = parse_url($_SERVER["HTTP_ORIGIN"])["host"];
		}

		if(count($allowedDomains) > 0){
			foreach ($allowedDomains as $key => $value) {
				$tmp[] = $allowedDomains[$key]["domain"];
			}
			$allowedDomains = $tmp;
			header("Access-Control-Allow-Origin: ".$_SERVER["HTTP_ORIGIN"]);
			return in_array($domain, $allowedDomains,true);
		}elseif (count($allowedDomains) == 0) {
			header("Access-Control-Allow-Origin: *");
			return true;
		}else{
			return false;
		}
	}