<?php

use barkgj\functions;

require_once __DIR__ . '/vendor/barkgj/functions-library/src/functions.php';

$api = $_REQUEST["brk"];	// for example wpdiscountprovider-api
$currenturi = functions::geturicurrentpage();
$uripieces = explode("/", $currenturi);
if ($uripieces[1] == "api")
{
	$version = $uripieces[2];
	$env = $uripieces[3];
	$implementationpath = __DIR__ . "/api/{$api}/{$version}/{$env}/{$api}-impl.php";

	if (!file_exists($implementationpath))
	{
		functions::throw_nack("err; api dispatcher; not found; $implementationpath");
	}
	
	require_once($implementationpath);

	// if we reach this stage, the api didn't die
	functions::throw_nack("err; api dispatcher Error #426346 $implementationpath $currenturi");
}
else
{
	functions::throw_nack("err; api dispatcher Error #45677 $currenturi");
}