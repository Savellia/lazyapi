<?php
	$allowed = true;

	if(isset($_GET["endpoint"]) && !empty($_GET["endpoint"])){
		$endpointName = htmlentities(addslashes($_GET["endpoint"]));
	}else{
		$allowed = false;
	}

	if($allowed){
		require "db.php";
		require "core/engine.php";
		$endpoint = getEndpointByName($endpointName);

		if($endpoint){
			$domain = parse_url($_SERVER["HTTP_ORIGIN"])["host"];
			$domainChecker = domainChecker($domain, $endpoint["domains"]);

			if($domainChecker){

				//echo "<pre>";
				//print_r($domain);
				//echo "</pre>";
				//echo "<pre>";
				//print_r($allowedDomains);
				//echo "</pre>";

				$filePath = $endpoint["pathToFile"];

				if (file_exists($filePath)) {
					$function = $endpoint["functionName"];
					require $filePath;

					if($endpoint["method"] == "GET"){
						$callAnalyzer = callAnalyzer($function,$endpoint["parameters"], $_GET);
					}elseif($endpoint["method"] == "POST"){
						$callAnalyzer = callAnalyzer($function,$endpoint["parameters"], $_POST);
					}else{
						header('HTTP/1.0 405 Method Not Allowed');
						$error = "Method error.";
					}

					if($callAnalyzer){
						print_r(json_encode(eval($callAnalyzer)));
					}else{
						header('HTTP/1.0 403 Forbidden');
						$error = "something wrong with the function or parameters";
					}
				} else {
					header('HTTP/1.0 500 Internal Server Error');
					$error = "Something wrong with the filepath.";
				}
			}else{
				header('HTTP/1.0 403 Forbidden');
				$error = "Domain not allowed.";
			}
		}else{
			header('HTTP/1.0 404 Error');
			$error = "Endpoint doesn't exists.";
		}
	}else{
		header('HTTP/1.0 404 Error');
		$error = "404 error";
	}

	header("Access-Control-Allow-Methods: GET, POST");
	header("Access-Control-Allow-Credentials: true");
	header("Content-Type: application/json; charset=UTF-8");

	if(isset($error)){
		print_r(json_encode($error));
	}