<?php
  	session_start();

	# Configure your SQL connection.
	$_ENV["sqlConnector"] = array(
		"dbHost" => "localhost",
		"dbUser" => "root",
		"dbPass" => "",
		"dbName" => "api-flow",
		"dbDriver" => "mysql"
	);

	# Configure your LazyAPI user access.
	$_ENV["apiConnector"] = array(
		"apiUser" => "root",
		"apiPass" => "password"
	);