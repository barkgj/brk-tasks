<?php
function brk_tasks_gui_showapiservices()
{
	if ($_REQUEST["action"] == "createnewapiservice")
	{
	$service = $_REQUEST["service"];
	$requirements = $_REQUEST["requirements"];
	//
	$newtaskid = "97";
	$action_url = "https://global.nexusthemes.com/api/1/prod/create-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint";
	$action_url = nxs_addqueryparametertourl_v2($action_url, "businessprocesstaskid", $newtaskid, true, true);
	$action_url = nxs_addqueryparametertourl_v2($action_url, "service", $service, true, true);
	$action_url = nxs_addqueryparametertourl_v2($action_url, "requirements", $requirements, true, true);
	
	$action_string = file_get_contents($action_url);
	$action_result = json_decode($action_string, true);
	if ($action_result["result"] != "OK") { nxs_webmethod_return_nack("unable to create task instance; $action_url"); }
	
	$newlycreatedtaskinstanceid = $action_result["taskinstanceid"];
	
	$start_instance_url = "https://global.nexusthemes.com/?nxs=task-gui&page=taskinstancedetail&taskid={$newtaskid}&taskinstanceid={$newlycreatedtaskinstanceid}";
	echo "task instance created<br />";
	echo "<a href='{$start_instance_url}'>click here to start this new instance</a>";
	
	die();
	}
	
	$currenturl = nxs_geturlcurrentpage();
	brk_tasks_gui_rendernavigation();
	?>
	<H1>Available API services (<a href='https://docs.google.com/spreadsheets/d/1MSQGTfZYVLPE06UChN0Wqa5IjOPt7OFI_mtIdYN7kR0/edit#gid=75631176' target='_blank'>nxs.itil.configurationitems.api</a> and <a href='https://docs.google.com/spreadsheets/d/1MSQGTfZYVLPE06UChN0Wqa5IjOPt7OFI_mtIdYN7kR0/edit#gid=178141240' target='_blank'>nxs.itil.configurationitems.api.service</a></H1>
	
	<div id='addapiservice' style='background-color: #DDD'>
	<form action='<?php echo $currenturl; ?>' method='POST' target='_blank' style='margin-left: 100px;'>
	<input type='hidden' name='nxs' value='task-gui' />
	<input type='hidden' name='page' value='showapiservices' />
	<input type='hidden' name='action' value='createnewapiservice' />
	<label>new api service:</label><br />
	<input type='text' name='service' value='' placeholder='service' style='width: 30vw;' /><br />
	<textarea name='requirements' style='width: 100%; height: 40px;' placeholder='requirements'></textarea>
	<input type='submit' value='Create API Service' />
	</form>
	</div>
	
	
	<div>
	<h2>Pending API services (identified, scheduled to be created)</h2>
	<br />
	<?php
	$render_args_json = '{"if_this":{"type":"true_if_each_subcondition_is_true","subconditions":[{"type":"true_if_task_has_required_taskid","required_taskid":"97"},{"type":"true_if_in_any_of_the_required_states","any_of_the_required_states":["CREATED","STARTED"]}]}}';
	$render_args = json_decode($render_args_json, true);
	require_once("/srv/generic/libraries-available/nxs-tasks/nxs-tasks.php");
	
	$brk_tasks_searchtaskinstances_result = brk_tasks_searchtaskinstances($render_args);
	
	$result .= "Found: " . count($brk_tasks_searchtaskinstances_result["taskinstances"]);
	
	$result .= "<table>";
	$taskinstances = $brk_tasks_searchtaskinstances_result["taskinstances"];
	foreach ($taskinstances as $taskinstance)
	{
	$taskid = $taskinstance["taskid"];
	$title = brk_tasks_gettaskstitle($taskid);
	$taskinstanceid = $taskinstance["taskinstanceid"];
	
	$action_url = "https://global.nexusthemes.com/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
	
	$instancemeta = brk_tasks_getinstance($taskid, $taskinstanceid);
	
	$inputparameters = $instancemeta["inputparameters"];
	$parameters_html = "";
	foreach ($inputparameters as $inputparameter => $val)
	{
	$parameters_html .= "{$inputparameter} : {$val}<br />";
	}
	
	$result .= "<tr>";
	$result .= "<td>{$taskid}</td><td>{$title}</td><td><a target='_blank' href='{$action_url}'>{$taskinstanceid}</a></td><td>$parameters_html</td>";
	$result .= "</tr>";
	}
	$result .= "</table>";
	echo $result;
	?>
	
	</div>
	
	<?php
	
	$apiservices_url = "https://global.nexusthemes.com/api/1/prod/get-api-services/?nxs=code-api&nxs_json_output_format=prettyprint";
	$apiservices_string = file_get_contents($apiservices_url);
	$apiservices_result = json_decode($apiservices_string, true);
	$apis = $apiservices_result["apis"];
	$service_index = -1;
			
	foreach ($apis as $apimeta)
	{
		$scheme = $apimeta["api"]["scheme"];
		$hostname = $apimeta["api"]["hostname"];
		$api = $apimeta["api"]["api"];
		$api_title = $apimeta["api"]["api_title"];
		
		$services = $apimeta["services"];
		$servicescount = count($services);
		
		if ($servicescount == 0)
		{ 
			$rows[] = array
			(
			"api_html" => "API: {$api}-api {$api_title} - no services yet",
			"description_html" => "",
			"actions" => array
			(
			"no actions",
			)
			);
		}
		else
		{
			$rows[] = array
			(
			"api_html" => "API: {$api}-api {$api_title} - {$servicescount} services found",
			"description_html" => "",
			"actions" => array
			(
			// "no actions",
			)
			);
			
			foreach ($services as $servicemeta)
			{
				$service_index++;
				
				$service = $servicemeta["service"];
				$service_description = $servicemeta["description"];
				
				// /api/1/prod/resolves-to-our-infra/?nxs=dns-api&nxs_json_output_format=prettyprint&domain=nexusthemes.com
				$description = "";
				$description .= "Invoke: {$scheme}://{$hostname}/api/1/prod/{$service}/?nxs={$api}-api&nxs_json_output_format=prettyprint";
				
				$copy = "[nxs_p001_task_instruction type='invoke_api' service='{$service}' store_output='true' store_output_prefix='api_' store_output_fields_containing='*']";
				$escaped_copy = htmlspecialchars($copy);
				//$escaped_copy = str_replace("'", "\\'", $escaped_copy);
				//$escaped_copy = str_replace("\"", "\\\"", $escaped_copy);
				$shortcode_html = "";
				$shortcode_html .= "<a href='#' onclick=\"nxs_js_copydelegate_{$service_index}();return false;\">copy</a>";
				$shortcode_html .= "<script>";
				$shortcode_html .= "function nxs_js_copydelegate_{$service_index}(){nxs_js_copy_to_clip(\"{$escaped_copy}\");}";
				$shortcode_html .= "</script>";
				$shortcode_html .= "{$copy}<br />";
				$shortcode_html .= "$service_description";
				
				
				$api_html = "";
				
				$rows[] = array
				(
					"api_html" => $api_html,
					"description_html" => $description,
					"shortcode_html" => $shortcode_html,
					"actions" => array
					(
						"todo",
					)
				);
			}
		}
	}
	
	echo "<table>";
	
	foreach ($rows as $row)
	{
		$api_html = $row["api_html"];
		$description_html = $row["description_html"];
		$shortcode_html = $row["shortcode_html"];
		
		$actions = $row["actions"];
		
		echo "<tr>";
		echo "<td>";
		echo $api_html;
		echo "</td>";
		echo "<td>";
		echo $description_html;
		echo "</td>";
		echo "<td>";
		echo $shortcode_html;
		echo "</td>";
		echo "<td>";
		foreach ($actions as $action)
		{
			echo $action;
		}
		echo "</td>";			
		echo "</tr>";
	}
	
	echo "</table>";
	?>
	<script>
	function nxs_js_copy_to_clip(text) {
    var input = document.createElement('input');
    input.setAttribute('value', text);
    document.body.appendChild(input);
    input.select();
    var result = document.execCommand('copy');
    document.body.removeChild(input);
    return result;
 	}
 	</script>
 	<?php
}