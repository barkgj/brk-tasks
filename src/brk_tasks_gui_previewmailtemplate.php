<?php

function brk_tasks_gui_previewmailtemplate()
{
	$mailtemplate = $_REQUEST["mailtemplate"];
	
	$appliedmail_url = "https://tasks.bestwebsitetemplates.net/api/1/prod/apply-parameters-to-mail-template/?nxs=mail-api&nxs_json_output_format=prettyprint&mailtemplate={$mailtemplate}";
	$appliedmail_string = file_get_contents($appliedmail_url);
	$appliedmail_response = json_decode($appliedmail_string, true);
	if ($appliedmail_response["result"] != "OK") { functions::throw_nack("unable to fetch appliedmail_url; $appliedmail_url"); }
	
	$toemail = $appliedmail_response["applied_result"]["toemail"];
	$ccemail = $appliedmail_response["applied_result"]["ccemail"];
	$bccemail = $appliedmail_response["applied_result"]["bccemail"];
	$subject = $appliedmail_response["applied_result"]["subject"];
	$body = $appliedmail_response["applied_result"]["body"];
	
	echo "<h1>Mail Preview</h1>";
	echo "TO: {$toemail}<br />";
	echo "SUBJECT: {$subject}<br />";
	echo "BODY:<br />";
	echo $body;
	
	die();
}

