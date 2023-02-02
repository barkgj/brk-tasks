<?php

use barkgj\functions;
use barkgj\tasks\tasks;

$loader = require_once __DIR__ . '/vendor/autoload.php';

// todo; fix autoloader of composer so this is not needed...
require_once __DIR__ . '/vendor/barkgj/datasink-library/src/datasink-entity.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/functions.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/filesystem.php';
require_once __DIR__ . '/vendor/barkgj/tasks-library/src/tasks.php';

// [nxs_p001_task_instruction type="create-task-instance" create_taskid="90" licenseid="" domain=""]
// [nxs_p001_task_instruction type="invoke-api-service" service_id="1" licenseid="{{licenseid}}" domain="{{domain}}"]
// [nxs_p001_task_instruction type="invoke-api-service" service="THESERVICE" licenseid="{{licenseid}}" domain="{{domain}}"]
// [nxs_p001_task_instruction type="open-ixplatform-table" schema="..."]
// [nxs_p001_task_instruction type="commit-to-ixplatform"]


// [nxs_p001_task_instruction type="pull-helpscout-props-by-number" original_helpscoutticketnr="{{original_helpscoutticketnr}}"]
// [nxs_p001_task_instruction type="close-helpscout-ticket-by-number" original_helpscoutticketnr="{{original_helpscoutticketnr}}"]
// [nxs_p001_task_instruction indent='0' type='pick-taskoutcomeprediction']
// [nxs_p001_task_instruction indent='0' type='execute-taskoutcomeprediction-steps']

function nxs_markers_createmarker($markertype)
{
	global $nxs_gl_markertypeusagecount;
	$markertype = "marker_{$markertype}";	
	$nxs_gl_markertypeusagecount[$markertype]++;
	$markertypeusagecount = $nxs_gl_markertypeusagecount[$markertype];
	$marker = "{$markertype}_i{$markertypeusagecount}";
	$marker = str_replace(" ", "_", $marker);
	$marker = str_replace("-", "_", $marker);
	return $marker;
}

function nxs_sc_p001_task_getindentwraphtml_start($atts)
{
	$marker = $atts["marker"];
	
	// $indent = $atts["indent"]; // no more; use "*"'s instead
	//if ($indent == "") { $indent = 0; } 
	$indent = 0;
	$marginleft = $indent * 50;
	
	$result = "<div id='{$marker}' class='INDENTED taskinstruction' style='margin-left: {$marginleft}px; xwidth: 95%'>";
	
	return $result;
}

function nxs_sc_p001_task_getindentwraphtml_end($atts)
{
	$result = "</div>";
	return $result;
}

function nxs_sc_p001_task_getinstanceparameter($name)
{
	global $brk_tasks_instance_lookup;
	if ($brk_tasks_instance_lookup == "")
	{
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not specified"); }
		
		$taskinstanceid = $_REQUEST["taskinstanceid"];
		if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not specified (5)"); }
		
		$instancemeta = tasks::gettaskinstance($taskid, $taskinstanceid);
		
		$brk_tasks_instance_lookup = $instancemeta["inputparameters"];
	}
	$result = $brk_tasks_instance_lookup[$name];
	return $result;
}

function nxs_sc_p001_task_instruction($atts, $content = null, $name='') 
{
	$homeurl = functions::geturlhome();

	$result = "";

	$type = $atts["type"];
	$type_dashconvertedtounderscores = str_replace("-", "_", $type);
	
	$taskid = $_REQUEST["taskid"];
	if ($taskid == "")
	{
		
	}
	
	$taskinstanceid = $_REQUEST["taskinstanceid"];
	$instancemeta = tasks::gettaskinstance($taskid, $taskinstanceid);
	$state = $instancemeta["state"];
	$inputparameters = $instancemeta["inputparameters"];
	
	// apply lookups in content
	$content = functions::translatesingle($content, "{{", "}}", $inputparameters);
	// apply lookups in atts
	foreach ($atts as $key => $val)
	{
		$atts[$key] = functions::translatesingle($val, "{{", "}}", $inputparameters);
	}
	
	$marker = nxs_markers_createmarker("marker_$type");
	$atts["marker"] = $marker;
	
	// generic handling of indents (open)
	$result .= nxs_sc_p001_task_getindentwraphtml_start($atts);
	
	// generic handling of cached  taskinstructionresults
	// $result_of_executed_task_instruction = brk_tasks_getstoredtaskinstructionresult($taskid, $taskinstanceid, $type);
	if (true) // $result_of_executed_task_instruction == "" || $_REQUEST["retry-{$type}"] == "true")
	{
		//
		if (false)
		{
		}
		else if ($type == "")
		{
			return "{error: nxs_p001_task_instruction: type attribassist-to-update-wp-cores-of-each-site-with-outdated-wp-coreute not set}";
		}
		/*
		else if ($type == "update-website-to-latest-wp-core")
		{
			if ($state != "STARTED")
			{
				return "$type; not doing anything as task instance is not started";
			}
		 
		 	if ($state != "STARTED")
			{
				return "$type; not doing anything as task instance is not started";
			}
			
			$vps_id = $inputparameters["vps_id"];
			$studio = $inputparameters["studio"];
			$siteid = $inputparameters["siteid"];
			
			$homeurl = functions::geturlhome();

			$action_url = "{$homeurl}}api/1/prod/global-update-wpcore-for-website-by-vpsid-studio-siteid/?nxs=hosting-api&nxs_json_output_format=prettyprint";
			$action_url = functions::addqueryparametertourl($action_url, "vps_id", $vps_id, true, true);
			$action_url = functions::addqueryparametertourl($action_url, "studio", $studio, true, true);
			$action_url = functions::addqueryparametertourl($action_url, "siteid", $siteid, true, true);
			
			$action_string = file_get_contents($action_url);
		  $action_result = json_decode($action_string, true);
		  if ($action_result["result"] != "OK") { functions::throw_nack("unable to execute; $action_url"); }
		  
		  if ($action_result["action_result"]["evaluation"]["updatedsuccessfully"] == true)
		  {
		  	$result .= "$type; wp core is now up to date<br />";
		  	$verbose = $action_result["action_result"]["evaluation"]["verbose"];
			  if ($verbose != "")
			  {
			  	$result .= "$type; verbose; $verbose<br />";
			  }
		  }
		  else
		  {
		  	$result .= "$type; wp core was not updated successfully?<br />";
		  	$result .= $action_string;
		  }
		}
		*/
		/*
		else if ($type == "assist-to-update-wp-cores-of-each-site-with-outdated-wp-core")
		{
			if ($state != "STARTED")
			{
				return "$type; not doing anything as task instance is not started";
			}
			
			$enabler = "createinstances";
			
			if (tasks::isheadless())
			{
				$doit = true;
			}
			else 
			{
				$doit = false;
		  	if ($_REQUEST["doit"] == $enabler)
		  	{
		  		$doit = true;
		  	}
			}
			
			if ($doit)
	  		{
			  // pre condition; if there's already open task instances for 164, then dont proceed; it means the previous batch is still being processed
			  // TODO: to be implemented
				// -------
				
				$search_args = array
				(
					"if_this" => array
					(
						"type" => "true_if_each_subcondition_is_true",
						"subconditions" => array
						(
							array
							(
								"type" => "true_if_task_has_required_taskid",
								"required_taskid" => 164,
							),
							array
							(
								"type" => "true_if_in_any_of_the_required_states",
								"any_of_the_required_states" => array
								(
									"CREATED",
									"STARTED",
								),
							),
						),
					),
				);
				
				$any_open_ones = tasks::searchtaskinstances($search_args);
				$count_open_ones = count($any_open_ones["taskinstances"]);
				
				if ($count_open_ones > 0)
				{
					$result .= "$type; ERR; unable to start this shortcode; found {$count_open_ones} existing 164(s) still exist (created, started). Please handle those first!";
					$result .= json_encode($any_open_ones);
					return $result;
				}
				
			  $fetch_url = "{$homeurl}api/1/prod/global-list-wp-core-versions-for-websites/?nxs=hosting-api&nxs_json_output_format=prettyprint&versioncompareoperator=ne&versioncompareto=latest-stable";
			  $fetch_string = file_get_contents($fetch_url);
			  $fetch_result = json_decode($fetch_string, true);
			  if ($fetch_result["result"] != "OK") { functions::throw_nack("unable to fetch; $fetch_url"); }
			  
			  $countcreated = 0;
			  
			  foreach ($fetch_result["bylist"] as $outdated_wpcore_item)
			  {
			  	$vps_id = $outdated_wpcore_item["vps_id"];
			  	$studio = $outdated_wpcore_item["studio"];
			  	$siteid = $outdated_wpcore_item["siteid"];
			  	
			  	// 
			  	$action_url = "{$homeurl}api/1/prod/create-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint";
			  	$action_url = functions::addqueryparametertourl($action_url, "businessprocesstaskid", 164, true, true);
			  	$action_url = functions::addqueryparametertourl($action_url, "vps_id", $vps_id, true, true);
			  	$action_url = functions::addqueryparametertourl($action_url, "studio", $studio, true, true);
			  	$action_url = functions::addqueryparametertourl($action_url, "siteid", $siteid, true, true);
			  	
			  	$action_url = functions::addqueryparametertourl($action_url, "createdby_taskid", $atts["taskid"], true, true);
			  	$action_url = functions::addqueryparametertourl($action_url, "createdby_taskinstanceid", $atts["taskinstanceid"], true, true);
			  	
			  	
			  	// do the action
			  	$action_string = file_get_contents($action_url);
				  $action_result = json_decode($action_string, true);
				  if ($action_result["result"] != "OK") { functions::throw_nack("unable to execute action; $action_result"); }
				  
				  $result .= "created task instance 164 to update core for $vps_id $studio $siteid<br />";
			  	
			  	$countcreated++;
			  }
			  
			  // store the fact (as if its a long term memory) that we finished this so next time we wont
			  $subresult = array
			  (
			  	"count_outdated_sites" => count($fetch_result["bylist"]),
			  	"count_task_instances_created" => $countcreated,
			  );
			  tasks::storetaskinstructionresult($taskid, $taskinstanceid, $type, $subresult);
			}
			else
			{
				//
				$currenturl = functions::geturlcurrentpage();
		  		$action_url = $currenturl;
				$action_url = functions::addqueryparametertourl($action_url, "doit", $enabler, true, true);
				$result .= "<span style='background-color: orange; color: white;'>NOTE: procrastinating</span> the creation of the instances the shortcode is invoked by the GUI (not headless through a workflow/batch)<br />";
				$result .= "<a href='{$action_url}#$marker'>Click here to do it</a>";
			}
		}
		*/
		/*
		else if ($type == "render-copy-to-clipboard")
		{
			$label = $atts["label"];
			if ($label == "") { return "ERR; $type; attribute label not set"; }
			
			$value = $atts["value"];
			if (!isset($atts["value"])) { return "ERR; $type; attribute value not set"; }
			
			// replace \r\n's with <br />
			$value = str_replace("\r\n", "<br />", $value);
			$value = str_replace("\n", "<br />", $value);
			$value = str_replace("\r", "<br />", $value);
			
			$clipboardvalue = $value;
			$clipboardvalue = str_replace("&#92;", "\\", $clipboardvalue);
			$clipboardvalue = str_replace("'", "&apos;", $clipboardvalue);
			$clipboardvalue = str_replace("\\", "\\\\", $clipboardvalue);
			
		  $result .= "<a href='#' onclick='navigator.clipboard.writeText(\"{$clipboardvalue}\"); return false;'>copy</a> {$label}: <i style='font-family: courier;'>$value</i>";
		}
		*/
		/*
		else if ($type == "render-working-day-activities-for-employee")
		{
			$nxs_hr_employee_id = $inputparameters["nxs_hr_employee_id"];
			if ($nxs_hr_employee_id == "") { return "ERR; $type; nxs_hr_employee_id not set in task instance"; }
			
			$fetch_url = "{$homeurl}api/1/prod/get-employee-by-id/?nxs=hr-api&nxs_json_output_format=prettyprint&id={$nxs_hr_employee_id}";
			$fetch_string = file_get_contents($fetch_url);
			$fetch_result = json_decode($fetch_string, true);
			if ($fetch_result["result"] != "OK") { functions::throw_nack("unable to fetch url; $fetch_url"); }
			
			if ($fetch_result["found"] == false) { return "ERR; $type; not found?"; }
			
			$result .= $fetch_result["props"]["startup_workingday_activities_html"];
		}
		*/
		/*
		else if ($type == "render-task-instances")
		{
			$args_json = $content;
					
			if ($args_json == "") { functions::throw_nack("err $type; content (json) not specified"); }
			$args = json_decode($args_json, true);
			
			if ($args == "") { functions::throw_nack("err $type; content is not valid json, or empty"); }
			
			//$result .= "Filters:<br />";
			//$result .= nxs_prettyprint_array($args);
			
			require_once("/srv/generic/libraries-available/nxs-tasks/nxs-tasks.php");
			
			$brk_tasks_searchtaskinstances_result = brk_tasks_searchtaskinstances($args);
			
			$result .= "Found: " . count($brk_tasks_searchtaskinstances_result["taskinstances"]);
			
			$result .= "<table>";
			$taskinstances = $brk_tasks_searchtaskinstances_result["taskinstances"];
			foreach ($taskinstances as $taskinstance)
			{
				$taskid = $taskinstance["taskid"];
				$title = brk_tasks_gettaskstitle($taskid);
				$taskinstanceid = $taskinstance["taskinstanceid"];
				
				$action_url = "{$homeurl}?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
				
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
		}
		*/
		/*
		else if ($type == "dequeue-item-from-queue")
		{
			$queueid = $atts["queueid"];
			if ($queueid == "") { functions::throw_nack("queueid not specified"); }
		
			$itemid = $atts["itemid"];
			if ($itemid == "") { functions::throw_nack("itemid not specified"); }
			
			if ($_REQUEST["taskinstructionaction"] == "dequeue")
			{
				$r = check_admin_referer("do_{$marker}");
				
				// if we reach this point it means its a valid action
				$action_url = "{$homeurl}api/1/prod/dequeue/?nxs=queue-api&nxs_json_output_format=prettyprint";
				$action_url = functions::addqueryparametertourl($action_url, "queue_id", $queueid, true, true);
				$action_url = functions::addqueryparametertourl($action_url, "item_id", $itemid, true, true);
				$action_string = file_get_contents($action_url);
				$action_result = json_decode($action_string, true);
				if ($action_result["result"] != "OK") { functions::throw_nack("unable to fetch action_url; $action_url"); }
				
				$result .= "Item {$itemid} was succesfully dequeued from queue $queueid<br />";
			}
			else
			{
				$action_url = functions::geturlcurrentpage();
				$action_url = functions::addqueryparametertourl($action_url, "taskinstructionaction", "dequeue", true, true);
				$action_url = wp_nonce_url($action_url, "do_{$marker}");
				$result .= "Dequeue the item by clicking <a href='{$action_url}'>HERE</a><br />";
			}		
		}
		*/
		/*
		else if ($type == "do-robotic-process-automation-to-qualify-mail")
		{
			// only do this when the task instance is assigned
			
			$assignedtoemployee_id = $instancemeta["assignedtoemployee_id"];
			if ($assignedtoemployee_id == "")
			{
				return "SKIPPED RPA; have not yet done the RPA because the instance is not yet assigned to employee";
			}
			// 
			
			$result_of_executed_task_instruction = brk_tasks_getstoredtaskinstructionresult($taskid, $taskinstanceid, $type);
			if ($result_of_executed_task_instruction == "" || $_REQUEST["retry-{$type}"] == "true")
			{
				$subresult = array();
				
				require_once("/srv/generic/libraries-available/nxs-workflows/nxs-workflows.php");
				
				$workflows = brk_tasks_get_workflows($taskid);
				
				$countworkflows = count($workflows);
				$subresult["console"][] = "{$countworkflows} RPA workflows found";
				
				
				$isdirty = false;
				
				$subresult["console"][] = "applying RPA workflows ...";
				
				// apply workflows
				foreach ($workflows as $workflow)
				{
					$title = $workflow["title"];
					$enabled = true;
					if (isset($workflow["enabled"]))
					{
						$enabled = $workflow["enabled"];
					}
					
					if ($enabled)
					{
						// debugging should be configurable in meta of workflow
						if ($atts["debug"] == "true")
						{
							$subresult["console"][] = "considering workflow; $title ...";
						}
						
						$if_this = $workflow["if_this"];
						
						$evaluation_result = nxs_workflow_evaluate_if_this($if_this, $taskid, $taskinstanceid);
						$conclusion = $evaluation_result["conclusion"];
						if ($conclusion == true)
						{
							$isdirty = true;
							$then_that_items = $workflow["then_that_items"];
							$count = count($then_that_items);
							$subresult["console"][] = "conditions met for {$title}, executing {$count} then_that_items ...";
							$execution_result = nxs_workflow_execute_then_that_items($then_that_items, $taskid, $taskinstanceid);
							foreach ($execution_result["console"] as $console_item)
							{
								$subresult["console"][] = $console_item;
							}
						}
						else
						{
							// $subresult["console"][] = "condition of workflow not met";
						}
					}
					else
					{
						$subresult["console"][] = "warning; workflow $title was not processed (this workflow has enabled=false declaration in {$taskid}.workflow.json file)";
					}
				}
				
				if ($isdirty)
				{
					brk_tasks_storetaskinstructionresult($taskid, $taskinstanceid, $type, $subresult);
				}
					
				foreach ($subresult["console"] as $line)
				{
					$result .= "{$line}<br />";
				}
			}
			else
			{
				$interpreted_result_of_executed_task_instruction = json_decode($result_of_executed_task_instruction, true);
				
				$currenturl = functions::geturlcurrentpage();
				$retry_url = $currenturl;
				$retry_url = functions::addqueryparametertourl($retry_url, "retry-{$type}", "true", true, true);
		
				//
				$result .= "Looks like this piece of code was executed before, will not do it ($type) again to avoid executing code twice<br />";
				$result .= "To retry the execution use <a href='{$retry_url}'>this link</a><br />";
				$result .= "Outcome of the first execution was:<br />-----<br />";
				foreach ($interpreted_result_of_executed_task_instruction["console"] as $line)
				{
					$result .= $line . "<br />";
				}
				$result .= "-----<br />";
			}
		}
		*/
		/*
		else if ($type == "assist-to-update-wpcli-for-vps")
		{
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
			$vpsid = $inputparameters["vpsid"];
		
			global $nxs_g_modelmanager;
			$a = array("modeluri" => "{$vpsid}@nxs.itil.configurationitems.vps");
			$properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
		
			$vps_api_scheme = $properties["vps_api_scheme"];
			$vps_api_hostname = $properties["vps_api_hostname"];
			
			$action_url = "{$vps_api_scheme}://{$vps_api_hostname}/api/1/prod/wpcli-update/?nxs=vps-api&nxs_json_output_format=prettyprint";
			$result .= "<a href='$action_url' target='_blank'>Invoke for $vpsid: $action_url</a>";
		}
		*/
		/*
		else if ($type == "create-tasks-to-update-wp-cli-on-all-vpses")
		{
			$lines = array();
			
			//$wpversion = $inputparameters["wpversion"];
			
			$entries_schema = "nxs.itil.configurationitems.vps";
			global $nxs_g_modelmanager;
			$a = array
			(
				"singularschema" => $entries_schema,
			);
			$allentries = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels($a);
			foreach ($allentries as $entry)
			{
				$entry_id = $entry["{$entries_schema}_id"];
				$currenttitle = $entry["title"]; 
				$vps_api_hostname = $entry["vps_api_hostname"]; 
				
				$lines[]= "[nxs_p001_task_instruction type='create-task-instance' create_taskid=162 vpsid={$entry_id} wpversion={{latest_latest_stable}}]";
			}
			
			foreach ($lines as $line)
			{
				$line = do_shortcode($line);
				$result .= "{$line}\r\n";
			}
		}	
		*/
		/*
		else if ($type == "assist-in-qualifying-vendor-mail")
		{
			$vendor_id = nxs_sc_p001_task_getinstanceparameter("vendor_id");
			if ($vendor_id == "")
			{
				$lines = array
				(
					"* This email address does not qualify as vendor mail",
					"* If this email address DOES belong to a vendor",
					"** Derive the vendor_existing_state (already_exists or does_not_exist)",
					"*** [nxs_p001_task_instruction type='open-ixplatform-table' schema='nxs.itil.configurationitems.vendor' indent=3]",
					"*** If vendor does not exist, the vendor_existing_state is 'does_not_exist'",
					"*** If vendor does exist, the vendor_existing_state is 'already_exists'",
					"*** Write down the 'vendor_existing_state'",
					"*** [nxs_p001_task_instruction indent=3 type='require-parameter' name='vendor_existing_state']",
					"** If vendor_existing_state ({{vendor_existing_state}}) equals 'does_not_exist' then",
					"*** Declare the title of this vendor and write it down",
					"**** [nxs_p001_task_instruction indent=4 type='require-parameter' name='vendor_title']",
					"*** [nxs_p001_task_instruction indent=3 type='create-task-instance' create_taskid=106 sender_email='{{sender_email}}' original_helpscoutticketnr='{{original_helpscoutticketnr}}' vendor_title='{{vendor_title}}']",
					"*** Wait till the vendor is created (to create the vendor, use the task instance you just created)",
					"*** Re-run the workflow such that this ticket will become a handle vendor message",
					"** If vendor_existing_state ({{vendor_existing_state}}) equals 'already_exists' then",
					"*** Escalate to 2nd line (to be implemented; create a new task to append e-mail address to existing vendor)",
				);
			}
			else
			{
				$lines = array
				(
					"* <span class='grabattention'>Note; this email address DOES qualify as a vendor mail</span>",
					"* Handle vendor mail",
					"** [nxs_p001_task_instruction indent=2 type='create-task-instance' create_taskid=86 original_helpscoutticketnr={{original_helpscoutticketnr}} nxs_itil_configurationitems_vendor_id={{vendor_id}}]",
					"** [nxs_p001_task_instruction indent=2 type='end-task-instance']",
				);
			}
		
			foreach ($lines as $line)
			{
				$line = do_shortcode($line);
				$result .= "{$line}\r\n";
			}
		}
		*/
		/*
		else if ($type == "get-input-parameter-value-for-related-task-instance")
		{
			$name = $atts["name"];
			if ($name == "") { functions::throw_nack("name not specified"); }
			
			$relation = $atts["relation"];
			if ($relation == "") { functions::throw_nack("relation not specified"); }
			
			if ($relation == "previousgeneration().lastinstance()")
			{
				// first figure out the previous generation of "this" taskinstance
				$stacktracepreviousgeneration = brk_tasks_getstacktracepreviousgeneration($taskid, $taskinstanceid);
				$frame = end($stacktracepreviousgeneration);
				
				$related_taskid = $frame["taskid"];
				$related_taskinstanceid = $frame["taskinstanceid"];
				
				$related_inputparameters = brk_tasks_getinstanceinputparameters($related_taskid, $related_taskinstanceid);
				$value = $related_inputparameters[$name];
				$result = "$name: <i style='font-family: courier;'>$value</i> <a href='#' onclick='navigator.clipboard.writeText(\"{$value}\"); return false;'>copy</a>";
			}
			else if ($relation == "currentgeneration().closestinstance()")
			{
				// get stacktrace of current generation
				$stacktrace_args = array("reverse" => true);
				$stacktrace = brk_tasks_getstacktrace($taskid, $taskinstanceid, $stacktrace_args);
				foreach ($stacktrace as $frame)
				{
					$frame_taskid = $frame["taskid"];
					$frame_taskinstanceid = $frame["taskinstanceid"];
		
					$frame_inputparameters = brk_tasks_getinstanceinputparameters($frame_taskid, $frame_taskinstanceid);
					$value = $frame_inputparameters[$name];
					if (isset($value))
					{
						$result = "$name: <i style='font-family: courier; background-color: #f3f3f3;'>$value</i> <a href='#' onclick='navigator.clipboard.writeText(\"{$value}\"); return false;'>copy</a>";
						return $result;
					}
				}
				
				return "{err {$type}; unable to proceed; $name not found? }";
			}
			else
			{
				return "{$type}; not supported relation; $relation}";
			}
		}
		*/
		/*
		else if ($type == "execute-taskoutcomeprediction-steps")
		{
			$taskoutcomeprediction_id = nxs_sc_p001_task_getinstanceparameter("nxs_p001_businessprocess_taskoutcomeprediction_id");
		
			if ($taskoutcomeprediction_id != "")
			{
				$taskoutcomeprediction_result = nxs_sc_p001_task_getinstanceparameter("nxs_p001_businessprocess_taskoutcomeprediction_taskoutcomeprediction_result");
				if ($taskoutcomeprediction_result == "")
				{
					global $nxs_g_modelmanager;
					$a = array("modeluri" => "{$taskoutcomeprediction_id}@nxs.p001.businessprocess.taskoutcomeprediction");
					$properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
					$steps_how_to_handle = $properties["steps_how_to_handle"];
					
					if ($steps_how_to_handle == "") { return "{ err; execute-taskoutcomeprediction-steps; no steps_how_to_handle }"; }
					
					
					$result_of_steps = do_shortcode($steps_how_to_handle);
					
					$wrap = array
					(
						"steps_how_to_handle" => $steps_how_to_handle,
						"result_of_steps" => $result_of_steps,
					);
					
					$nxs_p001_businessprocess_taskoutcomeprediction_taskoutcomeprediction_result = json_encode($wrap);
					
					brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "nxs_p001_businessprocess_taskoutcomeprediction_taskoutcomeprediction_result", $nxs_p001_businessprocess_taskoutcomeprediction_taskoutcomeprediction_result);
				}
				else
				{
					$result .= "skipping to execute-taskoutcomeprediction-steps already executed<br />";
				}
			}
			else
			{
				$result .= "execute-taskoutcomeprediction-steps cannot be executed yet (waiting for taskoutcomeprediction_id to be set)<br />";
			}
		}
		*/
		/*
		else if ($type == "pick-taskoutcomeprediction")
		{
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			
			$nxs_p001_businessprocess_taskoutcomeprediction_id = nxs_sc_p001_task_getinstanceparameter("nxs_p001_businessprocess_taskoutcomeprediction_id");
			
			$repliesto_taskid = nxs_sc_p001_task_getinstanceparameter("repliesto_taskid");
			if ($repliesto_taskid == "") { return "{error; expected repliesto_taskid to be set?}"; }
			
			
			if ($nxs_p001_businessprocess_taskoutcomeprediction_id != "") { return "You picked nxs_p001_businessprocess_taskoutcomeprediction_id; $nxs_p001_businessprocess_taskoutcomeprediction_id"; }
			
			$currenturl = functions::getur();
			$returnurl = $currenturl . "#{$marker}";
			
			$repliesto_taskid = nxs_sc_p001_task_getinstanceparameter("repliesto_taskid");
			
			$entries_url = "{$homeurl}api/1/prod/list-taskoutcomepredictions/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$repliesto_taskid}";
			$entries_string = file_get_contents($entries_url);
			$entries_result = json_decode($entries_string, true);
			if ($entries_result["result"] != "OK") { functions::throw_nack("unable to fetch entries_url; $entries_url"); }
			$count = count($entries_result["predictions"]);
			if ($count > 0)
			{
				$result .= "Pick any of the following taskoutcomepredictions:<br />";
				foreach ($entries_result["predictions"] as $entry)
				{
					$id = $entry["nxs.p001.businessprocess.taskoutcomeprediction_id"];
					$text = $entry["outcome_prediction"];
		
					$result .= "<form action='{$homeurl}' method='POST'>";
					$result .= "<input type='hidden' name='nxs' value='task-gui' />";
					$result .= "<input type='hidden' name='action' value='updateparameter' />";
					$result .= "<input type='hidden' name='page' value='taskinstancedetail' />";
					$result .= "<input type='hidden' name='taskid' value='{$taskid}' />";
					$result .= "<input type='hidden' name='taskinstanceid' value='{$taskinstanceid}' />";
					$result .= "<input type='hidden' name='name' value='nxs_p001_businessprocess_taskoutcomeprediction_id' />";
					$result .= "<input type='hidden' name='value' value='{$id}' />";
					$result .= "<input type='hidden' name='returnurl' value='{$returnurl}' />";
					$button_text = nxs_render_html_escape_singlequote("{$id}: {$text}");
					$result .= "<input type='submit' value='{$button_text}' style='background-color: #CCC;'>";
					$result .= "</form>";
					$result .= "------<br />";
				}
			}
			else 
			{
				$result .= "No taskoutcomepredictions are available yet for task $repliesto_taskid<br />";
			}
			
			$result .= "... if the taskoutcomeprediction is not yet available then add it;<br />";
			$result .= do_shortcode("[nxs_p001_task_instruction type='require-parameter' name='new_taskoutcomeprediction' indent=1]");
			
			$new_taskoutcomeprediction = nxs_sc_p001_task_getinstanceparameter("new_taskoutcomeprediction");
			$original_helpscoutticketnr = nxs_sc_p001_task_getinstanceparameter("original_helpscoutticketnr");
			if ($new_taskoutcomeprediction != "" && $original_helpscoutticketnr != "")
			{
				$result .= do_shortcode("[nxs_p001_task_instruction indent=1 type='create-task-instance' create_taskid='152' outcomeprediction_for_taskid='{$repliesto_taskid}' taskoutcomeprediction='{$new_taskoutcomeprediction}']");
			}
			else
			{
				$result .= "<div style='margin-left: 50px;'>before the new taskoutcomeprediction can be created first save the new_taskoutcomeprediction and original_helpscoutticketnr parameter</div>";
			}
		}
		*/
		/*
		else if ($type == "wp-login")
		{
			$hostname = nxs_sc_p001_task_getinstanceparameter("hostname");
			$scheme = nxs_sc_p001_task_getinstanceparameter("scheme");
			$wp_username = nxs_sc_p001_task_getinstanceparameter("wp_username");
			$wp_password = nxs_sc_p001_task_getinstanceparameter("wp_password");
		
			$lines = array();
			$lines[] = "* Login to the site<br />";
			$lines[] = "** Form:";
			$lines[] = "<form method='POST' action='{{scheme}}://{{hostname}}/wp-login.php' target='_blank'>";
			$lines[] = "	<input type='text' name='log' value='{{wp_username}}' />";
			$lines[] = "	<input type='text' name='pwd' value='{{wp_password}}' />";
			$lines[] = "	<input type='submit' name='wp-submit' value='Log In' />";
			$lines[] = "</form><br />";
			$lines[] = "** Alternative flow; login manually by going to: [nxs_p001_task_link url='{{scheme}}://{{hostname}}/wp-admin']<br />";
			$lines[] = "** Alternative flow; if the login is incorrect then send an email to the user<br />";
		
			$result = implode("", $lines);
			
			// apply shortcodes
			$result = do_shortcode($result);
		}
		*/
		/*
		else if ($type == "nxs-infra-license-login")
		{
			$licenseid = nxs_sc_p001_task_getinstanceparameter("licenseid");
			$url = "{$homeurl}?nxs=task-gui&page=authenticate_to_license&licenseid={$licenseid}";
			$result = "<a target='_blank' href='$url'>{$url}</a>";
		}
		*/
		/*
		else if ($type == "nxs-infra-hostname-login")
		{
			$hostname = $atts["hostname"];
			$role = $atts["role"];
			$target = $atts["target"];
			if ($target == "")
			{
				$target = "blank";
			}
			
			if ($role == "") { return "ERR; $type; $role not specified (for example role=administrator or role=studioadmin)"; }
			$url = "{$homeurl}?nxs=task-gui&page=authenticate_to_hostname&hostname={$hostname}&role={$role}";
			
			if (false)
			{
			}
			else if ($target == "blank")
			{
				$lines = array
				(
					"AUTHENTICATE AS {$role} TO {$hostname} (opens in blank window)",
					"----------------------------------------------------------------------------------------------------",
					"[nxs_p001_task_link url='{$url}']",
				);
				
				foreach ($lines as $line)
				{
					$line = do_shortcode($line);
					$result .= "{$line}\r\n";
				}
			}
			else if ($target == "iframe")
			{
				$id = "id". do_shortcode("[nxs_string ops='randomstring' characters='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890']");
				$result .= "<a href='#'onclick='nxs_js_f_{$id}(); return false;'>AUTHENTICATE AS {$role} TO {$hostname} (opens in iframe)</a><br />";
				$result .= "<iframe id='{$id}' style='display: none'></iframe>";
				$result .= "<script>";
				$result .= "function nxs_js_f_{$id}() {";
				// $result .= "console.log('bebabaloba');";
				$result .= "jQuery('#{$id}').show();";
				$result .= "jQuery('#{$id}').attr('src', '{$url}');";
				$result .= "}";
				$result .= "</script>";
			}
			else
			{
				//
				$lines = array
				(
					"ERR; AUTHENTICATE AS {$role} TO {$hostname}; unsupported target (use; iframe or blank)",
					"----------------------------------------------------------------------------------------------------",
				);
				
				foreach ($lines as $line)
				{
					$line = do_shortcode($line);
					$result .= "{$line}\r\n";
				}
			}
		}
		*/
		/*
		else if ($type == "nxs-infra-license-studio-login")
		{
			$licenseid = nxs_sc_p001_task_getinstanceparameter("licenseid");
			$url = "{$homeurl}?nxs=task-gui&page=studio_authenticate_to_license&licenseid={$licenseid}";
			$result .= "<a target='_blank' href='$url'>{$url}</a>";
		}
		*/
		/*
		else if ($type == "invoke-api-service")
		{	
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
			
			// specific parameters for shortcode
			
			$service = $atts["service"];
			$service_id = $atts["service_id"];
			if ($service != "" && $service_id != "") { functions::throw_nack("ambiguous; service and service_id both specified"); }		
			
			if ($service != "")
			{
				// convenience way; input is the service instead of the id
				$service_schema = "nxs.itil.configurationitems.api.service";
				global $nxs_g_modelmanager;
				$a = array
				(
					"singularschema" => $service_schema,
				);
				$allservices = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels($a);
				foreach ($allservices as $entry)
				{
					$entry_id = $entry["nxs.itil.configurationitems.api.service_id"];
					$currentservice = $entry["service"]; 
					if ($service == $currentservice)
					{
						// found
						$service_id = $entry_id;
					}
				}
				
				if ($service_id == "") { return "shortcode error; {$type} service not found ({$service})"; }
			}
			
			if ($service_id == "") { return "shortcode error; {$type} missing attribute; service or service_id"; }
			
			global $nxs_g_modelmanager;
			$a = array("modeluri" => "{$service_id}@nxs.itil.configurationitems.api.service");
			$service_properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
			$api_id = $service_properties["nxs.itil.configurationitems.api_id"];
			$a = array("modeluri" => "{$api_id}@nxs.itil.configurationitems.api");
			
			$api_properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
			
			$api = $api_properties["api"];
			$scheme = $api_properties["scheme"];
			$hostname = $api_properties["hostname"];
			
			$service = $service_properties["service"];
			
			$invoke_api_url = "{$scheme}://{$hostname}/api/1/prod/{$service}/?nxs={$api}-api&nxs_json_output_format=prettyprint";
			
			$parametersforapi = array();
			$exclude_keys = array("service", "service_id", "type", "nxs", "nxs_json_output_format", "indent", "marker", "store_output", "store_output_prefix", "store_output_fields_containing");
			foreach ($atts as $key => $val)
			{
				if (in_array($key, $exclude_keys))	
				{
					// skip
					continue;
				}
				
				// replace placeholders if any are remaining
				$val = nxs_filter_translatesingle($val, "{{", "}}", $inputparameters);
			
				// domain={{domain}}
				
				$invoke_api_url = functions::addqueryparametertourl($invoke_api_url, $key, $val, true, true);
				
				$parametersforapi[$key] = $val;
			}
			
			//
			
			$lines = array
			(
				"Invoke API service for {$service_id} \"{$api} - {{$service}}\"",
				"----------------------------------------------------------------------------------------------------",
			);
			
			foreach ($parametersforapi as $key => $val)
			{
				$lines[] = "* {$key} => {$val}";
			}
			
			$lines[] = "<iframe style='display: none; width: 100%; height: 100px;'></iframe>";
			$lines[] = "* <a href='#' onclick=\"jQuery(this).closest('.taskinstruction').css('opacity', '0.2').find('iframe').show().attr('src', '{$invoke_api_url}'); return false;\">Click HERE to invoke API</a>";
			
			foreach ($lines as $line)
			{
				$line = do_shortcode($line);
				$result .= "{$line}\r\n";
			}
		}
		*/
		/*
		else if ($type == "open-ixplatform-table")
		{	
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);

			// specific parameters for shortcode
			$schema = $atts["schema"];
			if ($schema == "") { return "shortcode error; {$type} schema not set"; }
			
			global $nxs_g_modelmanager;
			$a = array("modeluri" => "{$schema}@modelspreadsheet");
			$schema_properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
			$spreadsheet_url = $schema_properties["spreadsheet_url"];
			$namespace_container = $schema_properties["namespace_container"];
			
			if ($schema == "modelspreadsheet")
			{
				$spreadsheet_url = "https://docs.google.com/spreadsheets/d/1ZXVua1soThK87EEXYj1mbD5prWH4rsc4zHii7T0lfjc/edit#gid=23200529";
			}
			
			if ($spreadsheet_url != "")
			{
				$lines = array
				(
					"Open '<a target='_blank' href='{$spreadsheet_url}'>{$schema}</a>' in Google Spreadsheets",
				);
			}
			else
			{
				$lines = array
				(
					"ERR; Unable to open '<a target='_blank' href='{$spreadsheet_url}'>{$schema}</a>' in Google Spreadsheets (not yet there, or perhaps you need to clear the cache?)",
				);
			}
			
			foreach ($lines as $line)
			{
				$line = do_shortcode($line);
				$result .= "{$line}\r\n";
			}
		}
		*/
		/*
		else if ($type == "commit-to-ixplatform")
		{
			$schema = $atts["schema"];
			$lines = array
			(
				"Commit to ixplatform table {$schema}",
				"* Go to URL: [nxs_p001_task_link url='https://ixplatform.com/?nxs=modelmanagerform&subpage=any&autopilot=true&debugcsv=true']",
			);
			
			foreach ($lines as $line)
			{
				$line = do_shortcode($line);
				$result .= "{$line}\r\n";
			}
		}
		*/
		/*
		else if ($type == "pull-helpscout-props-by-number")
		{
			$currenturl = functions::geturlcurrentpage();
			$returnurl = $currenturl . "#{$marker}";
			
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
		
			$original_helpscoutticketnr = $atts["original_helpscoutticketnr"];
			if ($original_helpscoutticketnr == "")
			{
				$result = "ERROR; unable to proceed original_helpscoutticketnr not set";
			}
			else
			{
				$result .= "PULL HELPSCOUT PROPS<br />";
				$result .= "----------------------------------------------------------------------------------------------------<br />";
				$currenturl = functions::geturlcurrentpage();
				$result .= "<form id='{$marker}' action='{$homeurl}' method='POST'>";
				//$result .= "<label>nxs</label>";
				$result .= "<input type='hidden' name='nxs' value='task-gui' />";
				//$result .= "<br />";
				//$result .= "<label>action</label>";
				$result .= "<input type='hidden' name='action' value='pullhelpscoutpropsbynumber' />";
				$result .= "<input type='hidden' name='page' value='taskinstancedetail' />";
				//$result .= "<br />";
				//$result .= "<label>taskid</label>";
				$result .= "<input type='hidden' name='taskid' value='{$taskid}' />";
				//$result .= "<br />";
				//$result .= "<label>taskinstanceid</label>";
				$result .= "<input type='hidden' name='taskinstanceid' value='{$taskinstanceid}' />";
				//$result .= "<br />";
				//$result .= "<label>{$name}</label>";
				$result .= "<input type='hidden' name='original_helpscoutticketnr' value='{$original_helpscoutticketnr}' />";
				//$result .= "<br />";
				//$result .= "<label>return url</label>";
				$result .= "<input type='hidden' name='returnurl' value='{$returnurl}' />";
				// $result .= "<br />";
				$result .= "<input type='submit' value='Pull context from Helpscout' style='background-color: #CCC;'>";
				$result .= "</form>";
			}
		}
		*/
		/*
		else if ($type == "close-helpscout-ticket-by-number")
		{
			$currenturl = functions::geturlcurrentpage();
			$returnurl = $currenturl . "#{$marker}";
			
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
		
			$original_helpscoutticketnr = $atts["original_helpscoutticketnr"];
			if ($original_helpscoutticketnr == "")
			{
				$result = "ERROR; unable to proceed original_helpscoutticketnr not set";
			}
			else
			{
				$currenturl = functions::geturlcurrentpage();
				$result .= "<form id='{$marker}' action='{$homeurl}' method='POST'>";
				//$result .= "<label>nxs</label>";
				$result .= "<input type='hidden' name='nxs' value='task-gui' />";
				//$result .= "<br />";
				//$result .= "<label>action</label>";
				$result .= "<input type='hidden' name='action' value='closehelpscoutticketbynumber' />";
				//$result .= "<br />";
				//$result .= "<label>taskid</label>";
				$result .= "<input type='hidden' name='taskid' value='{$taskid}' />";
				$result .= "<input type='hidden' name='page' value='taskinstancedetail' />";
				
				
				//$result .= "<br />";
				//$result .= "<label>taskinstanceid</label>";
				$result .= "<input type='hidden' name='taskinstanceid' value='{$taskinstanceid}' />";
				//$result .= "<br />";
				//$result .= "<label>{$name}</label>";
				$result .= "<input type='hidden' name='original_helpscoutticketnr' value='{$original_helpscoutticketnr}' />";
				//$result .= "<br />";
				//$result .= "<label>return url</label>";
				$result .= "<input type='hidden' name='returnurl' value='{$returnurl}' />";
				// $result .= "<br />";
				$result .= "<input type='submit' value='Close ticket #{$original_helpscoutticketnr} in Helpscout' style='background-color: #CCC;'>";
				$result .= "</form>";
			}
		}
		*/
		/*
		else if ($type == "reply-mailtemplate-for-helpscout-conversation")
		{
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
			
			$mailtemplate = $atts["mailtemplate"];
			
			if ($mailtemplate == 57)
			{
				$question_id = $atts["question_id"];
				if ($question_id == "")
				{
					$result = "ERROR; $type; unable to proceed question_id not set";
					return $result;
				}
			}
		
			$currenturl = functions::geturlcurrentpage();
			$returnurl = $currenturl . "#{$marker}";
			
			$helpscout_conversation_nr = $atts["helpscout_conversation_nr"];
			if ($helpscout_conversation_nr == "")
			{
				$helpscout_conversation_nr = $inputparameters["original_helpscoutticketnr"];
			}
			
			if ($helpscout_conversation_nr == "")
			{
				$result = "ERROR; $type; unable to proceed helpscout_conversation_nr not set (1)";
			}
			else
			{
				$action_url = "{$homeurl}api/1/prod/reply-mailtemplate-to-client-in-helpscout-conversation/?nxs=helpscout-api&nxs_json_output_format=prettyprint&helpscoutnumber={$helpscout_conversation_nr}&mailtemplate={$mailtemplate}";
				$previewmailurl = "{$homeurl}?nxs=task-gui&page=mailtemplatepreview&mailtemplateid={$mailtemplate}";
				
				$exclude_keys = array("type", "helpscout_conversation_nr", "mailtemplate");
				foreach ($atts as $key => $val)
				{
					if (in_array($key, $exclude_keys))	
					{
						// skip
						continue;
					}
					
					// replace placeholders if any are remaining
					$val = nxs_filter_translatesingle($val, "{{", "}}", $inputparameters);
					
					if (nxs_stringcontains($val, "{{") || nxs_stringcontains($val, "}}"))
					{
						$result = "ERROR; $type; key {$key} contains unreplaced placeholder; {$val}";
						return $result;
					}
					
					// apply shortcodes (if any)
					$filetobeincluded = "/srv/generic/plugins-available/nxs-p001-shop/shortcodes.php";
					require_once($filetobeincluded);
					//error_log("val before: $val");
					$val = do_shortcode($val);
					//error_log("val after: $val");
					
					// domain={{domain}}
					
					$action_url = functions::addqueryparametertourl($action_url, $key, $val, true, true);
					$previewmailurl  = functions::addqueryparametertourl($previewmailurl, $key, $val, true, true);
				}
				
				$taskid = $_REQUEST["taskid"];
				$taskinstanceid = $_REQUEST["taskinstanceid"];

				$action_url = functions::addqueryparametertourl($action_url, "invokedby_taskid", $taskid, true, true);
				$action_url = functions::addqueryparametertourl($action_url, "invokedby_taskinstanceid", $taskinstanceid, true, true);
				$action_url = functions::addqueryparametertourl($action_url, "taskid", $taskid, true, true);
				$action_url = functions::addqueryparametertourl($action_url, "taskinstanceid", $taskinstanceid, true, true);

				$previewmailurl = functions::addqueryparametertourl($previewmailurl, "invokedby_taskid", $taskid, true, true);
				$previewmailurl = functions::addqueryparametertourl($previewmailurl, "invokedby_taskinstanceid", $taskinstanceid, true, true);
				$previewmailurl = functions::addqueryparametertourl($previewmailurl, "taskid", $taskid, true, true);
				$previewmailurl = functions::addqueryparametertourl($previewmailurl, "taskinstanceid", $taskinstanceid, true, true);
				
				if ($mailtemplate == 57)
				{
					$question_id = $atts["question_id"];
					
					$previewurl = "https://nexusthemes.com/aap-1029/mies-{$question_id}/?taskid={$taskid}&taskinstanceid={$taskinstanceid}&preview=true";
					$improveurl = "https://docs.google.com/spreadsheets/d/1DMUBnvJTlRvmsKR-FQcxj3Km5ehXKvLmZLETSy6HDrk/edit?usp=drive_web&ouid=101834797161834314384";
					
					$lines = array
					(
						"REPLY MAILTEMPLATE FOR HELPSCOUT CONVERSATION (1)",
						"----------------------------------------------------------------------------------------------------",
						"* <div style='display: inline-block; font-size: 70%; font-style: italic;'><a target='_blank' href='{$previewurl}'>(Optional) Preview the question and answer the customer will receive</a></div>",
						"* <div style='display: inline-block; font-size: 70%; font-style: italic;'><a target='_blank' href='{$improveurl}'>(Optional) Improve the question answer the customer will receive</a></div>",
						"<iframe style='display: none; width: 100%; height: 100px;'></iframe>",
						"* <a href='#' onclick=\"jQuery(this).closest('.taskinstruction').css('opacity', '0.2').find('iframe').show().attr('src', '{$action_url}'); return false;\">Reply QA {$question_id} through mailtemplate {$mailtemplate} through Helpscout for conversation {$helpscout_conversation_nr}</a>",
					);
				}
				else
				{
					$lines = array
					(
						"REPLY MAILTEMPLATE FOR HELPSCOUT CONVERSATION (2)",
						"----------------------------------------------------------------------------------------------------",
						"* <div style='opacity: 0.5; display: inline-block; font-size: 70%; font-style: italic;'><a target='_blank' href='{$previewmailurl}'>Preview mail {$mailtemplate}</a></div>",
						"* <div style='opacity: 0.5; display: inline-block; font-size: 70%; font-style: italic;'><a target='_blank' href='https://docs.google.com/spreadsheets/d/1E-mB4yx7NBk3cA4R2In7NE1Qm15EFaO8BOuvPk83NGk/edit#gid=219307078'>Edit mail template {$mailtemplate}</a></div>",
						"<iframe style='display: none; width: 100%; height: 100px;'></iframe>",
						"* <a href='#' onclick=\"jQuery(this).closest('.taskinstruction').css('opacity', '0.2').find('iframe').show().attr('src', '{$action_url}'); return false;\">Reply mail template {$mailtemplate} through Helpscout for conversation {$helpscout_conversation_nr}</a>",
					);
				}
				
				foreach ($lines as $line)
				{
					$line = do_shortcode($line);
					$result .= "{$line}\r\n";
				}
			}
		}
		*/
		/*
		else if ($type == "pick-brush-off")
		{
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			
			$nxs_sales_brushoff_id = nxs_sc_p001_task_getinstanceparameter("nxs_sales_brushoff_id");
			
			if ($nxs_sales_brushoff_id != "") { return "You picked nxs_sales_brushoff_id; $nxs_sales_brushoff_id"; }
			
			$nxs_sales_question_id = nxs_sc_p001_task_getinstanceparameter("nxs_sales_question_id");
			
			$currenturl = functions::geturlcurrentpage();
			$returnurl = $currenturl . "#{$marker}";
			
			if ($nxs_sales_question_id == "") { return "Unable to render brush off; set the nxs_sales_question_id first"; }
			
			$brushoffs_url = "{$homeurl}api/1/prod/get-known-brush-offs-for-sales-question/?nxs=sales-api&nxs_json_output_format=prettyprint&nxs_sales_question_id={$nxs_sales_question_id}";
			$brushoffs_string = file_get_contents($brushoffs_url);
			$brushoffs_result = json_decode($brushoffs_string, true);
			if ($brushoffs_result["result"] != "OK") { functions::throw_nack("unable to fetch brushoffs_url; $brushoffs_url; $brushoffs_string"); }
			
			$result .= "Pick any of the following brush-offs for nxs_sales_question_id {$nxs_sales_question_id}:<br />";
			foreach ($brushoffs_result["rbos"] as $rbo)
			{
				$brushoff_id = $rbo["nxs.sales.brushoff_id"];
				$brushoff_en = $rbo["brushoff_en"];
				$result .= "<form action='{$homeurl}' method='POST'>";
				$result .= "<input type='hidden' name='nxs' value='task-gui' />";
				$result .= "<input type='hidden' name='action' value='updateparameter' />";
				$result .= "<input type='hidden' name='page' value='taskinstancedetail' />";
				$result .= "<input type='hidden' name='taskid' value='{$taskid}' />";
				$result .= "<input type='hidden' name='taskinstanceid' value='{$taskinstanceid}' />";
				$result .= "<input type='hidden' name='name' value='nxs_sales_brushoff_id' />";
				$result .= "<input type='hidden' name='value' value='{$brushoff_id}' />";
				$result .= "<input type='hidden' name='returnurl' value='{$returnurl}' />";
				$button_text = nxs_render_html_escape_singlequote("{$brushoff_id}: {$brushoff_en}");
				$result .= "<input type='submit' value='{$button_text}' style='background-color: #CCC;'>";
				$result .= "</form>";
				$result .= "------<br />";
			}
			
			$result .= "... if the brush-off (rbo) is not yet available then add it;<br />";
			$result .= do_shortcode("[nxs_p001_task_instruction type='require-parameter' name='new_brush_off' indent=1]");
			
			$new_brush_off = nxs_sc_p001_task_getinstanceparameter("new_brush_off");
			
			if ($new_brush_off != "")
			{
				$result .= do_shortcode("[nxs_p001_task_instruction indent=1 type='create-task-instance' create_taskid='139' nxs_sales_question_id='{$nxs_sales_question_id}' brushoff='{$new_brush_off}']");
			}
			else
			{
				$result .= "<div style='margin-left: 50px;'>before the new brush-off can be created first save the new_brush_off parameter</div>";
			}
		}
		*/
		/*
		else if ($type == "open-conversation-in-helpscout")
		{
			$original_helpscoutticketnr = $atts["original_helpscoutticketnr"];
			if ($original_helpscoutticketnr == "")
			{
				$result = "ERROR; unable to proceed original_helpscoutticketnr not set";
			}
			else
			{
				$lines = array
				(
					"OPEN HELPSCOUT TICKET",
					"----------------------------------------------------------------------------------------------------",
					"* Url: [nxs_p001_task_link url='https://secure.helpscout.net/search/?query={{original_helpscoutticketnr}}']",
					"* Click specific ticket",
					"* Click 'open conversation'",
				);
				
				foreach ($lines as $line)
				{
					$line = do_shortcode($line);
					$result .= "{$line}\r\n";
				}
			}
		}
		*/
		/*
		else if ($type == "reply-answer-for-question-through-helpscout-conversation-for-task-instance")
		{
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
			
			$mailtemplate = 57;	// hardcoded
			
			$questionid = $atts["questionid"];
			if ($questionid == "")
			{
				$result = "ERROR; $type; unable to proceed questionid not set";
				return $result;
			}
		
			$currenturl = functions::geturlcurrentpage();
			$returnurl = $currenturl . "#{$marker}";
			
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
		
			$helpscout_conversation_nr = $atts["helpscout_conversation_nr"];
			if ($helpscout_conversation_nr == "")
			{
				$helpscout_conversation_nr = $inputparameters["helpscout_conversation_nr"];
			}
			
			if ($helpscout_conversation_nr == "")
			{
				$result = "ERROR; $type; unable to proceed helpscout_conversation_nr not set (2)";
			}
			else
			{
				$action_url = "{$homeurl}api/1/prod/reply-mailtemplate-to-client-in-helpscout-conversation/?nxs=helpscout-api&nxs_json_output_format=prettyprint&helpscoutnumber={$helpscout_conversation_nr}&mailtemplate={$mailtemplate}&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
				$preview_url = "https://nexusthemes.com/aap-1029/mies-{$questionid}/?taskid={$taskid}&taskinstanceid={$taskinstanceid}&preview=true";
				
				$exclude_keys = array("type", "helpscout_conversation_nr", "mailtemplate");
				foreach ($atts as $key => $val)
				{
					if (in_array($key, $exclude_keys))	
					{
						// skip
						continue;
					}
					
					// replace placeholders if any are remaining
					$val = nxs_filter_translatesingle($val, "{{", "}}", $inputparameters);
				
					// domain={{domain}}
					
					$action_url = functions::addqueryparametertourl($action_url, $key, $val, true, true);
					$preview_url = functions::addqueryparametertourl($preview_url, $key, $val, true, true);
				}
				
				$action_url = functions::addqueryparametertourl($action_url, "question_id", $questionid, true, true);
				$action_url = functions::addqueryparametertourl($action_url, "invokedby_taskid", $taskid, true, true);
				$action_url = functions::addqueryparametertourl($action_url, "invokedby_taskinstanceid", $taskinstanceid, true, true);
				
				$preview_url = functions::addqueryparametertourl($preview_url, "question_id", $questionid, true, true);
				$preview_url = functions::addqueryparametertourl($preview_url, "invokedby_taskid", $taskid, true, true);
				$preview_url = functions::addqueryparametertourl($preview_url, "invokedby_taskinstanceid", $taskinstanceid, true, true);
				
				
				$lines = array
				(
					"REPLY MAILTEMPLATE FOR HELPSCOUT CONVERSATION (1)",
					"----------------------------------------------------------------------------------------------------",
					"* <a target='_blank' href='{$preview_url}'>(Optional) Preview the question and answer the customer will receive</a>",
					"<iframe style='display: none; width: 100%; height: 100px;'></iframe>",
					"* <a href='#' onclick=\"jQuery(this).closest('.taskinstruction').css('opacity', '0.2').find('iframe').show().attr('src', '{$action_url}'); return false;\">Reply QA {$questionid} through mailtemplate {$mailtemplate} through Helpscout for conversation {$helpscout_conversation_nr}</a>",
				);
				
				foreach ($lines as $line)
				{
					$line = do_shortcode($line);
					$result .= "{$line}\r\n";
				}
			}
		}
		*/
		/*
		else if ($type == "reply-support-no-sales-through-helpscout-conversation-for-task-instance")
		{
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
			
			$mailtemplate = 59;	// hardcoded
			
			$questionid = $atts["supportquestionid"];
			if ($questionid == "")
			{
				$result = "ERROR; $type; unable to proceed supportquestionid not set";
				return $result;
			}
		
			$currenturl = functions::geturlcurrentpage();
			$returnurl = $currenturl . "#{$marker}";
			
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
		
			$helpscout_conversation_nr = $atts["helpscout_conversation_nr"];
			if ($helpscout_conversation_nr == "")
			{
				$helpscout_conversation_nr = $inputparameters["helpscout_conversation_nr"];
			}
			
			if ($helpscout_conversation_nr == "")
			{
				$result = "ERROR; $type; unable to proceed helpscout_conversation_nr not set (3)";
			}
			else
			{
				$action_url = "{$homeurl}api/1/prod/reply-mailtemplate-to-client-in-helpscout-conversation/?nxs=helpscout-api&nxs_json_output_format=prettyprint&helpscoutnumber={$helpscout_conversation_nr}&mailtemplate={$mailtemplate}&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
				
				$exclude_keys = array("type", "helpscout_conversation_nr", "mailtemplate");
				foreach ($atts as $key => $val)
				{
					if (in_array($key, $exclude_keys))	
					{
						// skip
						continue;
					}
					
					// replace placeholders if any are remaining
					$val = nxs_filter_translatesingle($val, "{{", "}}", $inputparameters);
				
					// domain={{domain}}
					
					$action_url = functions::addqueryparametertourl($action_url, $key, $val, true, true);
				}
				
				$action_url = functions::addqueryparametertourl($action_url, "question_id", $questionid, true, true);
				$action_url = functions::addqueryparametertourl($action_url, "invokedby_taskid", $taskid, true, true);
				$action_url = functions::addqueryparametertourl($action_url, "invokedby_taskinstanceid", $taskinstanceid, true, true);
				
				$previewurl = "https://nexusthemes.com/support-1030/question-{$questionid}/?preview=true&taskid={$taskid}&taskinstanceid={$taskinstanceid}&questionid={$questionid}";
				
				$lines = array
				(
					"REPLY SUPPORT (NO SALES) ANSWER THROUGH HELPSCOUT IN EXISTING CONVERSATION",
					"----------------------------------------------------------------------------------------------------",
					"* <div style='display: inline-block; font-size: 70%; font-style: italic;'><a target='_blank' href='{$previewurl}'>Preview support and answer</a></div>",
					"** <div style='display: inline-block; font-size: 70%; font-style: italic;'>Alternative flow; if the ANSWER needs to be improved then <a target='_blank' href='https://docs.google.com/spreadsheets/d/1SbNx6_vcGNBjvx1QdItlEyYpt31PhRC_3YMsFswaj9o/edit#gid=2070580445'>edit questionid {$questionid} in ixplatform</a></div>",
					"** <div style='display: inline-block; font-size: 70%; font-style: italic;'>Alternative flow; if the QUESTION needs to be improved then <a target='_blank' href='https://docs.google.com/spreadsheets/d/1SbNx6_vcGNBjvx1QdItlEyYpt31PhRC_3YMsFswaj9o/edit#gid=2070580445'>edit questionid {$questionid} in ixplatform</a></div>",
					"<iframe style='display: none; width: 100%; height: 100px;'></iframe>",
					"* <a href='#' onclick=\"jQuery(this).closest('.taskinstruction').css('opacity', '0.2').find('iframe').show().attr('src', '{$action_url}'); return false;\">Reply QA {$questionid} through mailtemplate {$mailtemplate} through Helpscout for conversation {$helpscout_conversation_nr}</a>",
				);
				
				foreach ($lines as $line)
				{
					$line = do_shortcode($line);
					$result .= "{$line}\r\n";
				}
			}
		}
		*/
		/*
		else if ($type == "reply-support-no-sales-through-email-for-task-instance")
		{
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
			
			$mailtemplate = 59;	// hardcoded
			
			$questionid = $atts["supportquestionid"];
			if ($questionid == "")
			{
				$result = "ERROR; $type; unable to proceed supportquestionid not set";
				return $result;
			}
		
			$currenturl = functions::geturlcurrentpage();
			$returnurl = $currenturl . "#{$marker}";
			
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			
			$result .= do_shortcode("[nxs_p001_task_instruction type='input_parameter' name='licenseid' inputtype='text']");
			$result .= do_shortcode("[nxs_p001_task_instruction type='send-mail-template-to-license-owner' mailtemplate='{$mailtemplate}' supportquestionid='{$questionid}' taskid='{$taskid}' taskinstanceid='{$taskinstanceid}']");
		}
		*/
		/*
		else if ($type == "reply-answer-to-brush-off-for-helpscout-conversation")
		{
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
			
			$mailtemplate = 59;
		
			$question_id = $atts["nxs_sales_brushoff_id"];
			if ($question_id == "")
			{
				$result = "ERROR; $type; unable to proceed nxs_sales_brushoff_id not set";
				return $result;
			}
		
			$currenturl = functions::geturlcurrentpage();
			$returnurl = $currenturl . "#{$marker}";
			
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
		
			$helpscout_conversation_nr = $atts["helpscout_conversation_nr"];
			if ($helpscout_conversation_nr == "")
			{
				$helpscout_conversation_nr = $inputparameters["helpscout_conversation_nr"];
			}
			
			if ($helpscout_conversation_nr == "")
			{
				$result = "ERROR; $type; unable to proceed helpscout_conversation_nr not set (4)";
			}
			else
			{
				$action_url = "{$homeurl}api/1/prod/reply-turnarounscript-for-brushoff-to-client-in-helpscout-conversation/?nxs=helpscout-api&nxs_json_output_format=prettyprint&helpscoutnumber={$helpscout_conversation_nr}&mailtemplate={$mailtemplate}";
				
				$exclude_keys = array("type", "helpscout_conversation_nr", "mailtemplate");
				foreach ($atts as $key => $val)
				{
					if (in_array($key, $exclude_keys))	
					{
						// skip
						continue;
					}
					
					// replace placeholders if any are remaining
					$val = nxs_filter_translatesingle($val, "{{", "}}", $inputparameters);
				
					// domain={{domain}}
					
					$action_url = functions::addqueryparametertourl($action_url, $key, $val, true, true);
				}			
				
				$action_url = functions::addqueryparametertourl($action_url, "question_id", $question_id, true, true);
				
				$result .= "<a target='_blank' href='{$action_url}'>Reply brushoff turnaround script {$question_id} through mailtemplate {$mailtemplate} through Helpscout for conversation {$helpscout_conversation_nr}</a>";
			}
		}
		*/
		/*
		else if ($type == "reference-login-instructions")
		{
			$schema = $atts["schema"];
					
			$lines = array
			(
				"Use the login credentials / instructions",
				"* [nxs_p001_task_link url='https://docs.google.com/document/d/103xk7J7Bhlr6WaYxeN-Yr5tJ5HZJbN4je6EU3RPYwM4/edit']",
			);
			
			foreach ($lines as $line)
			{
				$line = do_shortcode($line);
				$result .= "{$line}\r\n";
			}
		}
		*/
		/*
		else if ($type == "require-parameter")
		{
			$display = $atts["display"];
			if ($display == "none") { return ""; }
			
			$name = $atts["name"];
			if ($name == "") { return "{error: no name attribute set for shortcode nxs_p001_task_requireparameter}"; }
			
			$taskid = $_REQUEST["taskid"];
			if ($taskid == "") { functions::throw_nack("taskid not specified"); }
			
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			//if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not specified (3)"); }
			
			//
			$path = "/srv/metamodel/businessprocess.task.instances/{$taskid}.json";
			$string = file_get_contents($path);
			$meta = json_decode($string, true);
			$instancemeta = $meta[$taskinstanceid];
			
			$lookup = $instancemeta["inputparameters"];
			$value = $lookup[$name];
			
			$currenturl = functions::geturlcurrentpage();
			$returnurl = $currenturl . "#{$marker}";
			
			if (!isset($lookup[$name]))
			{
				$roles = brk_tasks_gui_getrolescurrentuser();
				if ($roles == "rpa")
				{
					functions::throw_nack("rpa reports its unable to proceed; attribute $name is required but not set?");
				}
				else
				{
					$result .= "<form action='{$homeurl}' method='POST'>";
					
					$result .= "<input type='hidden' name='nxs' value='task-gui' />";
					$result .= "<input type='hidden' name='action' value='updateparameter' />";
					$result .= "<input type='hidden' name='page' value='taskinstancedetail' />";
					
					$result .= "<input type='hidden' name='taskid' value='{$taskid}' />";
					
					$result .= "<input type='hidden' name='taskinstanceid' value='{$taskinstanceid}' />";
					$result .= "<br />";
					$result .= "<label>{$name}</label>";
					$result .= "<input type='hidden' name='name' value='{$name}' />";
					
					$result .= "<input style='width: 95%' type='text' name='value' value='' />";
					
					$result .= "<input type='hidden' name='returnurl' value='{$returnurl}' />";
					$result .= "<br />";
					$result .= "<input type='submit' value='Save' style='background-color: #CCC;'>";
					$result .= "</form>";
				}
			}
			else
			{
				$currenturl = functions::geturlcurrentpage();
				
				$result .= "<div class='toggle' style='display: none; background-color: red;'>";
				$result .= "<form action='{$homeurl}' method='POST'>";
				//$result .= "<label>nxs</label>";
				$result .= "<input type='hidden' name='nxs' value='task-gui' />";
				$result .= "<input type='hidden' name='page' value='taskinstancedetail' />";
				//$result .= "<br />";
				//$result .= "<label>action</label>";
				$result .= "<input type='hidden' name='action' value='updateparameter' />";
				//$result .= "<br />";
				//$result .= "<label>taskid</label>";
				$result .= "<input type='hidden' name='taskid' value='{$taskid}' />";
				//$result .= "<br />";
				//$result .= "<label>taskinstanceid</label>";
				$result .= "<input type='hidden' name='taskinstanceid' value='{$taskinstanceid}' />";
				$result .= "<br />";
				$result .= "<label>{$name}</label>";
				$result .= "<input type='hidden' name='name' value='{$name}' />";
				//$result .= "<br />";
				//$result .= "<label>value</label>";
				
				$escapedvalue = nxs_render_html_escape_singlequote($value);
				$result .= "<input style='width: 95%;' type='text' name='value' value='{$escapedvalue}' />";
				//$result .= "<br />";
				//$result .= "<label>return url</label>";
				$result .= "<input type='hidden' name='returnurl' value='{$returnurl}' />";
				$result .= "<br />";
				$result .= "<input type='submit' value='Save' style='background-color: #CCC;' />";
				$result .= "</form>";
				$result .= "<a href='#' onclick='jQuery(this).closest(\".INDENTED\").find(\".toggle\").toggle(); return false;'>cancel</a>";
				$result .= "</div>";
				
				$edittriggerhtml = " <a href='#' onclick='jQuery(this).closest(\".INDENTED\").find(\".toggle\").toggle(); return false;'><span style='display: inline-block; transform: rotateZ(90deg);'>&#9998;</span></a>";
				// copy to clipboard
				$copytoclipboardhtml = " <a href='#' onclick='navigator.clipboard.writeText(\"{$escapedvalue}\"); return false;'>copy</a>";
				$result .= "<div style='display: block;' class='toggle'><label>{$name}</label>: <span>{$value}</span>{$edittriggerhtml} {$copytoclipboardhtml}</div>";		
			}
		}
		*/
		/*
		else if ($type == "open-abstract-steps-for-task")
		{
			$taskid = $atts["taskid"];
			if ($taskid == "") { return "shortcode error; $type; taskid not set"; }
			
			global $nxs_g_modelmanager;
			$a = array("modeluri" => "{$taskid}@nxs.p001.businessprocess.task");
			$properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
			$tasktitle = $properties["title"];
			
			$lines = array
			(
				"OPEN ABSTRACT STEPS FOR {$tasktitle}",
				"----------------------------------------------------------------------------------------------------",
				"[nxs_p001_task_link url='{$homeurl}?nxs=task-gui&page=viewabstracttaskraw&taskid={$taskid}']",
			);
			
			foreach ($lines as $line)
			{
				$line = do_shortcode($line);
				$result .= "{$line}\r\n";
			}
		}
		*/
		/*
		else if ($type == "send-mail-template-to-initiator")
		{
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
			
			$original_helpscoutticketnr = $inputparameters["original_helpscoutticketnr"];
			$licenseid = $inputparameters["licenseid"];
			if ($licenseid == "")
			{
				$licenseid = $atts["licenseid"];
			}
			
			if ($original_helpscoutticketnr != "" && $original_helpscoutticketnr != "{{original_helpscoutticketnr}}")
			{
				$result .= "send-mail-template-to-initiator; detected original_helpscoutticketnr is available (higher precedence over licenseid))<br />";
				
				$sub_atts = $atts;
				$sub_atts["type"] = "reply-mailtemplate-for-helpscout-conversation";
				$sub_atts["indent"] = 0; 
				$sub_result = nxs_sc_p001_task_instruction($sub_atts, $content = null, $name='');
				$result .= $sub_result;
			}
			else if ($licenseid != "" && $licenseid != "{{licenseid}}")
			{
				$result .= "send-mail-template-to-initiator; detected licenseid is available<br />";
				
				$sub_atts = $atts;
				$sub_atts["type"] = "send-mail-template-to-license-owner";
				$sub_atts["indent"] = 0;
				$sub_atts["licenseid"] = $licenseid;
				$sub_result = nxs_sc_p001_task_instruction($sub_atts, $content = null, $name='');
				$result .= $sub_result;
				
			}
			else
			{
				$result .= "send-mail-template-to-initiator; unable to proceed; either original_helpscoutticketnr or licenseid should be set<br />";
			}
		}
		*/
		/*
		else if ($type == "send-mail-template-to-license-owner")
		{
			// atts that are NOT ALLOWED
			if ($atts["firstname"] != "") { return "ERR; $type; dont specify firstname as attribute of shortcode; its derived automatically for you by the shortcode through the licenseid"; }
			if ($atts["toemail"] != "") { return "ERR; $type; dont specify toemail as attribute of shortcode; its derived automatically for you by the shortcode through the licenseid"; }
			
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
			
			$licenseid = $inputparameters["licenseid"];
			if ($licenseid == "")
			{
				$licenseid = $atts["licenseid"];
			}
			
			if ($licenseid == "") { return "ERR; $type; licenseid not set; unable to render shortcode"; } 
		
			// required atts
			$mailtemplate = $atts["mailtemplate"];
			if ($mailtemplate == "") { return "ERR; $type; mailtemplate not specified"; }

			// ---------
			// TODO: move this to the implementation of the task instance to send the mail
			// derive firstname from licenseid
			$licenseinsights_url = "https://license1802.nexusthemes.com/api/1/prod/licenseinsights/?nxs=licensemeta-api&nxs_json_output_format=prettyprint&licensenr={$licenseid}";
			$licenseinsights_string = file_get_contents($licenseinsights_url);
			$licenseinsights_result = json_decode($licenseinsights_string, true);
			if ($licenseinsights_result["result"] != "OK") { return "ERR; $type; invalid license? (unable to fetch licenseinsights_url; $licenseinsights_url)"; }
			// ---------
		
			$action_url = "{$homeurl}api/1/prod/send-mail-template-for-license/?nxs=mail-api&nxs_json_output_format=prettyprint";
			$preview_url = "{$homeurl}?nxs=task-gui&page=previewmailtemplate";
			
			$exclude_keys = array("type", "indent");
			foreach ($atts as $key => $val)
			{
				if (in_array($key, $exclude_keys))	
				{
					// skip
					continue;
				}
				
				// replace placeholders if any are remaining
				$val = nxs_filter_translatesingle($val, "{{", "}}", $inputparameters);
			
				// domain={{domain}}
				
				$action_url = functions::addqueryparametertourl($action_url, $key, $val, true, true);
				$preview_url = functions::addqueryparametertourl($preview_url, $key, $val, true, true);
			}
			
			// add required parameters from taskinstance
			$action_url = functions::addqueryparametertourl($action_url, "licenseid", $licenseid, true, true);
			$action_url = functions::addqueryparametertourl($action_url, "invokedby_taskid", $taskid, true, true);
			$action_url = functions::addqueryparametertourl($action_url, "invokedby_taskinstanceid", $taskinstanceid, true, true);
			
			$preview_url = functions::addqueryparametertourl($preview_url, "licenseid", $licenseid, true, true);
			$preview_url = functions::addqueryparametertourl($preview_url, "invokedby_taskid", $taskid, true, true);
			$preview_url = functions::addqueryparametertourl($preview_url, "invokedby_taskinstanceid", $taskinstanceid, true, true);
			
			$lines = array
			(
				"SEND MAILTEMPLATE TO LICENSE OWNER",
				"----------------------------------------------------------------------------------------------------",
				"<iframe style='display: none; width: 100%; height: 100px;'></iframe>",
				do_shortcode("* <div style='display: inline-block; font-size: 70%; font-style: italic;'>[nxs_p001_task_instruction type='open-ixplatform-table' schema='nxs.mail.mailtemplate' indent=1]</div>"),
				"* <div style='display: inline-block; font-size: 70%; font-style: italic;'><a target='_blank' href='{$preview_url}'>(optional) Preview mail template (b)</a></div>",
				"* <a href='#' onclick=\"jQuery(this).closest('.taskinstruction').css('opacity', '0.2').find('iframe').show().attr('src', '{$action_url}'); return false;\">Send mail template {$mailtemplate} to license owner</a>",
			);
			
			foreach ($lines as $line)
			{
				$line = do_shortcode($line);
				$result .= "{$line}\r\n";
			}
		}
		*/
		/*
		else if ($type == "send-mail-template-to-order-owner")
		{
			$indent = $atts["indent"];
			if ($indent == "") { $indent = 0; } 
			$marginleft = $indent * 50;	
			$result .= "<div id='{$marker}' class='INDENTED taskinstruction' style='margin-left: {$marginleft}px'>";
		
			$taskid = $_REQUEST["taskid"];
			$taskinstanceid = $_REQUEST["taskinstanceid"];
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
			
			$orderid = $inputparameters["orderid"];
			if ($orderid == "")
			{
				// fallback
				$orderid = $inputparameters["order_id"];
			}
			if ($orderid == "") { return "err $type; orderid not found in inputparameters of taskinstanceid"; }
			
			// atts that are NOT ALLOWED
			if ($atts["orderid"] != "") { functions::throw_nack("dont specify orderid as atts (its pulled from input parameters of taskinstance"); }
			if ($atts["order_id"] != "") { functions::throw_nack("dont specify order_id as atts (its pulled from input parameters of taskinstance"); }
			
			// if the orderid is 'just' a number it means something is wrong; we should only accept
			// orderids prefixed with V3 or V4
			if (is_numeric($orderid)) 
			{
				$errmsg = "please specify the entire order, like V3.nexus.en.99999, not just 99999";
				if (brk_tasks_isheadless())
				{
					functions::throw_nack($errmsg);
				}
				else
				{
					return "err $type; {$errmsg}";
				}
			}
			
			// required atts
			$mailtemplate = $atts["mailtemplate"];
			if ($mailtemplate == "") { return "ERR; $type; mailtemplate not specified"; }
			
			$currenturl = functions::geturlcurrentpage();
			$returnurl = $currenturl . "#{$marker}";
		
			$action_url = "{$homeurl}api/1/prod/send-mail-template-for-order/?nxs=mail-api&nxs_json_output_format=prettyprint";
			$preview_url = "{$homeurl}?nxs=task-gui&page=previewmailtemplate";		
		
			$exclude_keys = array("type", "indent");
			foreach ($atts as $key => $val)
			{
				if (in_array($key, $exclude_keys))	
				{
					// skip
					continue;
				}
				
				// replace placeholders if any are remaining
				$val = nxs_filter_translatesingle($val, "{{", "}}", $inputparameters);
			
				// domain={{domain}}
				
				$action_url = functions::addqueryparametertourl($action_url, $key, $val, true, true);
				$preview_url = functions::addqueryparametertourl($preview_url, $key, $val, true, true);
			}
			
			// add required parameters from taskinstance
			$action_url = functions::addqueryparametertourl($action_url, "orderid", $orderid, true, true);
			$action_url = functions::addqueryparametertourl($action_url, "invokedby_taskid", $taskid, true, true);
			$action_url = functions::addqueryparametertourl($action_url, "invokedby_taskinstanceid", $taskinstanceid, true, true);
		
			$preview_url = functions::addqueryparametertourl($preview_url, "licenseid", $licenseid, true, true);
			$preview_url = functions::addqueryparametertourl($preview_url, "invokedby_taskid", $taskid, true, true);
			$preview_url = functions::addqueryparametertourl($preview_url, "invokedby_taskinstanceid", $taskinstanceid, true, true);
			
			$lines = array
			(
				"SEND MAILTEMPLATE TO ORDER OWNER",
				"----------------------------------------------------------------------------------------------------",
				"<iframe style='display: none; width: 100%; height: 100px;'></iframe>",
				"* <div style='display: inline-block; font-style: italic; font-size: 70%; opacity: 0.5;'><a target='_blank' href='{$preview_url}'>Preview mail template $mailtemplate</a></div>",
				do_shortcode("* <div style='display: inline-block; font-style: italic; font-size: 70%; opacity: 0.5;'>[nxs_p001_task_instruction type='open-ixplatform-table' schema='nxs.mail.mailtemplate' indent=1]</div><br />"),
				"* <a href='#' onclick=\"jQuery(this).closest('.taskinstruction').css('opacity', '0.2').find('iframe').show().attr('src', '{$action_url}'); return false;\">Send mail template {$mailtemplate} to order owner</a>",
			);
				
			foreach ($lines as $line)
			{
				$line = do_shortcode($line);
				$result .= "{$line}\r\n";
			}
		}
		*/
		/*
		// MARKER 64354365
		else if ($type == "render-offspring-instances")
		{
		  $created_tasks = $instancemeta["created_tasks"];
			if (count($created_tasks) > 0)
			{
				$result .= "Offspring:<br />";
				foreach ($created_tasks as $created_task)
				{
					$creation_time = $created_task["creation_time"];
					$offspring_taskid = $created_task["taskid"];
					$offspring_tasktitle = brk_tasks_gettaskstitle($offspring_taskid);
					$offspring_taskinstanceid = $created_task["taskinstanceid"];
					$offspring_url = "{$homeurl}?nxs=task-gui&page=taskinstancedetail&taskid={$offspring_taskid}&taskinstanceid={$offspring_taskinstanceid}";
					$result .= "<a target='_blank' href='{$offspring_url}'>{$offspring_taskid} - {$offspring_tasktitle} - {$offspring_taskinstanceid}</a><br />";
				}
			}
			else
			{
				$result .= "No offspring<br />";
			}
		}
		*/
		/*
		else if ($type == "assist-to-fork-task-instance-to-smaller-pieces")
		{
			$seperator = "@@@@FORKED@@@@";
			$helpscoutnr = $inputparameters["original_helpscoutticketnr"];
			
			if ($helpscoutnr == "") 
			{
				$result .= "err; $type; original_helpscoutticketnr not set?";  
			}
			else
			{
				$nxs_fork_action = $_REQUEST["nxs_fork_action"];
				if (false)
				{
					//
				}
				else if ($nxs_fork_action == "")
				{
					$isforkchild = $inputparameters["isforkchild"];
					$helpscoutthreadid = $inputparameters["helpscoutthreadid"];	// available since around 27 aug 2019
					
					$latest_text = $inputparameters["message"];
					 $latest_text = str_replace("<tr", "@NEWLINE@<tr", $latest_text);
					$latest_text = str_replace("\r\n", " ", $latest_text);
					$latest_text = str_replace("\n", " ", $latest_text);
					$latest_text = str_replace("\r", " ", $latest_text);
					
					$latest_text = str_replace("  ", " ", $latest_text);
					$latest_text = str_replace("  ", " ", $latest_text);
					$latest_text = str_replace("  ", " ", $latest_text);
					$latest_text = str_replace("  ", " ", $latest_text);
					$latest_text = strip_tags($latest_text);
					$latest_text = strip_tags($latest_text);
					
					$label = "Seperator";
					$value = $seperator;
					$result .= "{$label}: <i style='font-family: courier;'>$value</i> <a href='#' onclick='navigator.clipboard.writeText(\"{$value}\"); return false;'>copy</a>";
					$result .= "<br />";
					$result .= "Put <span style='background-color: blue; color: white;'>{$seperator}</span> to distinguish one part from another (use as many as you like). For each part the system will create an instance of 144<br /><br />";
					$result .= "<form action='' method='POST'>";
					$result .= "<textarea name='forked_text' style='width: 90%; height: 400px'>";
					$result .= esc_textarea($latest_text);
					$result = str_replace("@NEWLINE@", "<newline />", $result);
					
					//$result .= $latest_text;
					$result .= "</textarea><br />";
					$result .= "<input type='hidden' name='nxs_fork_action' value='prefork'>";
					$result .= "<input type='submit' value='Fork'>";
					$result .= "</form>";
				}
				else if ($nxs_fork_action == "prefork")
				{
					$forked_text = $_REQUEST["forked_text"];
					// var_dump($forked_text);
					$exploded = explode($seperator, $forked_text);
					
					$result .= "Please confirm:<br /><br />";
					
					$result .= "<form action='' method='POST' style='background-color: #BBB; padding: 20px; font-style: italic;'>";
					
					$index = 0;
					foreach ($exploded as $piece)
					{
						$result .= "<input type='hidden' name='part_{$index}' value='" . esc_attr($piece) . "' />";
						$result .= "index: $index<br />";
						$result .= htmlspecialchars($piece);
						$result .= "<br /><br />";
						$index++;
					}
					
					$result .= "<input type='hidden' name='countparts' value='$index' />";
					$result .= "<input type='hidden' name='nxs_fork_action' value='fork' />";
					$result .= "<input type='submit' value='Confirm'>";
					$result .= "</form>";
				}
				else if ($nxs_fork_action == "fork")
				{
					$forkedlist_summary = "";
					
					$countparts = $_REQUEST["countparts"];
					for ($index = 0; $index < $countparts; $index++)
					{
						$nr = $index + 1;
						
						$part = $_REQUEST["part_{$index}"];
						$summarypart = trim(strip_tags($part));
						$forkedlist_summary .= "Question {$nr}:\r\n{$summarypart}\r\n\r\n";
						
						$handled_by_taskid = 131;
						
						$createinstance_url = "{$homeurl}api/1/prod/create-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint";
						$createinstance_url = functions::addqueryparametertourl($createinstance_url, "taskid", $handled_by_taskid, true, true);
						
						// todo; likely other parameters of "this" instance have to be copied
						foreach ($inputparameters as $key => $val)
						{
							$shouldkeep = true;
							if ($shouldkeep)
							{
								$createinstance_url = functions::addqueryparametertourl($createinstance_url, $key, $val, true, true);
							}
						}
						
						$createinstance_url = functions::addqueryparametertourl($createinstance_url, "message", $part, true, true);
						$createinstance_url = functions::addqueryparametertourl($createinstance_url, "messagewasforked", "true", true, true);
						$createinstance_url = functions::addqueryparametertourl($createinstance_url, "forkednr", $nr, true, true);
						$createinstance_url = functions::addqueryparametertourl($createinstance_url, "createdby_taskid", $taskid, true, true);
						$createinstance_url = functions::addqueryparametertourl($createinstance_url, "createdby_taskinstanceid", $taskinstanceid, true, true);
						$createinstance_string = file_get_contents($createinstance_url);
						$createinstance_result = json_decode($createinstance_string, true);
						if ($createinstance_result["result"] != "OK") { functions::throw_nack("error fetching createinstance_url; $createinstance_url"); }
						$created_taskinstanceid = $createinstance_result["taskinstanceid"];
						
						$view_url = "{$homeurl}?nxs=task-gui&page=taskinstancedetail&taskid={$handled_by_taskid}&taskinstanceid={$created_taskinstanceid}";
						$result .= "created instance {$handled_by_taskid} {$created_taskinstanceid} <a target='_blank' href='{$view_url}'>View</a><br />";
					}
					
					// store the forkedlist_summary in "this" instance so we can inform the user
					brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "forkedlist_summary", $forkedlist_summary);
				}
				else
				{
					$result .= "err; $type; nxs_fork_action not supported; $nxs_fork_action";
				}
		  }
		}
		*/
		else 
		{
			$path = __DIR__ . "/vendor/barkgj/tasks-library/src/task-instructions/taskinstruction-{$type_dashconvertedtounderscores}.php";

			if (file_exists($path))
			{
				$type = $type_dashconvertedtounderscores;

				tasks::ensuretaskinstructionloaded($type);

				// create an instance of the class type
				$class = "\\barkgj\\tasks\\taskinstruction\\{$type}";
				echo "about to initiate a class of {$class}<br />";
				
				$instance = new $class();

				// invoke method
				$do_result = $instance->execute($taskid, $taskinstanceid, $atts);
				
				if ($do_result["result"] == "OK")
				{
					// $result .= "<!-- result is OK -->";
					
					foreach ($do_result["console"] as $message)
					{
						$result .= $message;
						$result .= "<br />";
					}
					//$result .= "do_result:<br />";
					//$result .= json_encode($do_result);
					$result .= "<br />";
				}
				else if ($do_result["result"] == "NACK")
				{
					$nack_message = $do_result["nack_message"];
					if ($nack_message == "")
					{
						$nack_message = $do_result["message"];
					}
					error_log("nack;" . json_encode($do_result));
					functions::throw_nack("nack while doing task instruction (type: {$then_that_item_type}); $nack_message", $do_result);
				}
				else if ($do_result == null)
				{
					functions::throw_nack("$functionnametoinvoke should return an object (nothing was returned; hint: did you forget to return the result?)", $do_result);
				}
				else
				{
					functions::throw_nack("execute of type {$type}", $do_result);
				}
		  	}
			else
			{
				return "{error: nxs_p001_task_instruction: type attribute not supported; $type; path not found; $path}";
			}
		}
	}
	else
	{
		/*
		// output was already done before and stored in memory of this task instance
		$result .= "$type; this instruction already executed before (to executed it again, use retry-{$type}=true query parameter); result was:<br />";
		$result .= json_encode($result_of_executed_task_instruction);
		$result .= "<br />";
		*/
	}
	
	// generic handling of indents (close)
	$result .= nxs_sc_p001_task_getindentwraphtml_end($atts);
	
	return $result;
}
add_shortcode("nxs_p001_task_instruction", "nxs_sc_p001_task_instruction", 10, 3);

/*

function nxs_sc_p001_task_link($atts, $content = null, $name='') 
{
	$taskid = $_REQUEST["taskid"];
	if ($taskid == "") { functions::throw_nack("taskid not specified"); }
	
	$taskinstanceid = $_REQUEST["taskinstanceid"];
	// if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not specified (2)"); }
	
	// fetch properties of the ixplatform
	global $nxs_g_modelmanager;
	$a = array("modeluri" => "{$taskid}@nxs.p001.businessprocess.task");
	$properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
	$tasktitle = $properties["title"];
	$recipe = $properties["recipe"];	// 
	
	//
	$path = "/srv/metamodel/businessprocess.task.instances/{$taskid}.json";
	$string = file_get_contents($path);
	$meta = json_decode($string, true);
	$instancemeta = $meta[$taskinstanceid];
	
	$state = $instancemeta["state"];
		
	$lookup = $instancemeta["inputparameters"];
	$lookup["taskinstanceid"] = $_REQUEST["taskinstanceid"];
	$lookup["businessprocesstaskid"] = $_REQUEST["taskid"];
	$lookup["taskid"] = $_REQUEST["taskid"];
		
	$url = $atts["url"];

	$linktext = $url;
	$linktext = str_replace("{{", "<span class='placeholder'>{{", $linktext);
	$linktext = str_replace("}}", "}}</span>", $linktext);
	$linktext = nxs_filter_translatesingle($linktext, "{{", "}}", $lookup);
	
	$url = nxs_filter_translatesingle($url, "{{", "}}", $lookup);
	
	if ($url == "")
	{
		$result = "Error; url attribute not set";
	}
	else
	{	
		$result = "Invoke <a class='tasklink' target='_blank' href='{$url}'>{$linktext}</a>";
	}
	
	return $result;
}
add_shortcode("nxs_p001_task_link", "nxs_sc_p001_task_link");

function nxs_sc_p001_task_todo($atts, $content = null, $name='') 
{
	$todo = $atts["todo"];
	$result = "<span class='todo'>{$todo}</span>";
	
	return $result;
}
add_shortcode("nxs_p001_task_todo", "nxs_sc_p001_task_todo");

function nxs_sc_taskinstructiontoworkflow($atts, $content = null, $name='') 
{
	$result = "";
	
	$result .= "{<br />";
	
	$isfirstrenderedattribute = true;
	foreach ($atts as $key => $val)
	{
		if ($key == "comments" && $val == "")
		{
			// 
			continue;
		}
		
		if ($isfirstrenderedattribute)
		{
			$isfirstrenderedattribute = false;
		}
		else
		{
			$result .= ",<br />";
		}
		
		if (nxs_stringcontains($val, "'"))
		{
			$result .= "  ERR; $val contains single quote<br />";
		}
		
		// replace double quotes with single quotes, to avoid breaking the json
		// (or perhaps consider escaping the double quotes, not sure which is better)
		$val = str_replace("\"", "'", $val);
		$result .= "  \"$key\": \"$val\"";
	}
	
	$result .= "<br />},<br />";
	
	return $result;
}
add_shortcode("nxs_taskinstructiontoworkflow", "nxs_sc_taskinstructiontoworkflow");

*/