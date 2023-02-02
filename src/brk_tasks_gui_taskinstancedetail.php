<?php

use barkgj\functions;
use barkgj\tasks\tasks;

$loader = require_once __DIR__ . '/vendor/autoload.php';

// todo; fix autoloader of composer so this is not needed...
require_once __DIR__ . '/vendor/barkgj/datasink-library/src/datasink-entity.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/functions.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/filesystem.php';
require_once __DIR__ . '/vendor/barkgj/tasks-library/src/tasks.php';
require_once __DIR__ . '/shortcodes.php';

function nxs_gui_renderinputparametersforinstance($taskid, $taskinstanceid, $instancemeta)
{
	/*
	echo json_encode($instancemeta);
	var_dump($instancemeta["stateparameters"]);
	die();
	*/

	$reflectionmeta = tasks::getreflectionmeta($taskid, $taskinstanceid);
	$required_fields = $reflectionmeta["required_fields"];
	
	?>
	<!-- -->
	<h2>State parameters</h2>
	<div>
		<a href='#' onclick="jQuery('#inputparameterscontainer').toggle();return false;">Show/Hide</a>
	</div>
	<div id='inputparameterscontainer' style='display:none;'>
		<table class='table-oddeven'>
		<?php
		$keystoexclude = array("nxs", "nxs_json_output_format", "createdby_taskid", "createdby_taskinstanceid", "taskid", "original_helpscoutticketid", "reinitiates_taskid", "reinitiates_taskinstanceid", "indent", "workflows_result_json", "incident_taskid", "incident_taskinstanceid", "assigned_to");
		$excludedkeys = array();
		
		$stateparameters = $instancemeta["stateparameters"];
		ksort($stateparameters);
		
		$atleastoneinputparameterrenders = false;
		
		foreach ($stateparameters as $key => $val)
		{
			if (in_array($key, $keystoexclude))
			{
				$excludedkeys[] = $key;
				continue;
			}
			/*
			else if (nxs_stringstartswith($key, "taskinstructionresult_"))
			{
				$excludedkeys[] = $key;
				continue;
			}
			else if (nxs_stringcontains($key, "_result_json_"))
			{
				$excludedkeys[] = $key;
				continue;
			}
			else if (nxs_stringstartswith($key, "cond_wrap_state_"))
			{
				$excludedkeys[] = $key;
				continue;
			}
			*/
		
			$atleastoneinputparameterrenders = true;
			
			$isrequiredfield = in_array($key, $required_fields);
			
			if ($isrequiredfield)
			{
				echo "<td><span title='required input parameter'>&check;</span></td>";
			}
			else
			{
				echo "<td></td>";
			}
			echo "<td>{$key}</td>";
			
			if (false)
			{
			}
			/*
			else if ($key == "plugin_slug")
			{
				$plugin_more_html = "<a target='_blank' href='https://wordpress.org/plugins/{$val}/'>open wordpress.org plugin repository</a>";
				echo "<td>$val<br />{$plugin_more_html}</td>";
			}
			*/
			else
			{
				echo "<td style='display: block; max-height: 300px; overflow-y: scroll;'>" . htmlentities($val) . "</td>";
			}
			
			// copy / edit column
			echo "<td>";
			
			if (functions::stringcontains($val, "'") || functions::stringcontains($val, "\""))
			{
				echo "no&nbsp;copy&nbsp;available";
			}
			else
			{
				$tunedvalue = $val;
				//$tunedvalue = str_replace("\r\n", "CRLF", $tunedvalue);
				
				global $copycounter;
				$copycounter++;
				
				echo "<textarea style='display: none;' id='copycounter{$copycounter}'>$tunedvalue</textarea>";
				
				if ($copycounter == 1)
				{
					?>
					<script>
						function taskguicopyToClipboard(element) 
						{
							var $temp = element;
							$(element).show();
							var html = $(element).html();
							html = html.replace(/<br>/g, "\n"); // or \r\n
							console.log(html);
							$temp.val(html).select();
							document.execCommand("copy");
							$(element).hide();
						}
					</script>
					<?php
				}
				
				echo "<a href='#' onclick=\"var temp = $('#copycounter{$copycounter}'); taskguicopyToClipboard(temp); return false;\">copy</a>";
			}
			
			// allow editing of the field
			$editurl = "https://global.nexusthemes.com/?nxs=task-gui&page=taskinstanceeditfield&taskid={$taskid}&taskinstanceid={$taskinstanceid}&field={$key}";
			echo "&nbsp;|&nbsp;<a href='{$editurl}'>edit&nbsp;field</a>";
			
			// allow deleting of the field
			$deleteurl = "https://global.nexusthemes.com/?nxs=task-gui&page=taskinstancedeletefield&taskid={$taskid}&taskinstanceid={$taskinstanceid}&field={$key}";
			echo "&nbsp;|&nbsp;<a href='{$deleteurl}' target='_blank'>delete&nbsp;field</a>";
			
			echo "</td>";
			
			if (false)
			{
			//
			}
			/*
			else if ($key == "siteid")
			{
				$vpstitle = $inputparameters["vpstitle"];
				$studio = $inputparameters["studio"];
				$siteid = $inputparameters["siteid"];
				if ($studio != "" && $vpstitle != "")
				{
					$action_url = "https://global.nexusthemes.com/api/1/prod/global-site-meta-by-studiositeid/?nxs=hosting-api&nxs_json_output_format=prettyprint&vps_cname={$vpstitle}&studio={$studio}&siteid={$siteid}";
					echo "<td><a target='_blank' href='{$action_url}'>Site meta</a></td>"; 
					
					$action_url = "https://global.nexusthemes.com/?nxs=task-gui&page=authenticate_to_siteid&vpstitle={$vpstitle}&studio={$studio}&siteid={$siteid}";
					echo "<td><a target='_blank' href='{$action_url}'>Login to site</a></td>"; 
				}
			}
			else if (nxs_stringstartswith($key, "workflows_result_json"))
			{
				$action_url = "https://global.nexusthemes.com/api/1/prod/run-workflows-for-taskinstance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
				echo "<td><a target='_blank' href='{$action_url}'>Rerun workflows</a></td>";
			}
			*/
			echo "</tr>";
		}
		
		if (count($excludedkeys) > 0)
		{
			echo "<tr>";
			echo "<td><i>hidden keys</i></td>";
			$pieces = array();
			foreach ($excludedkeys as $excludedkey)
			{
				$show_url = "https://global.nexusthemes.com/?nxs=task-gui&page=showinputparametervalue&taskid={$taskid}&taskinstanceid={$taskinstanceid}&key={$excludedkey}";
				$pieces[] = "<a target='_blank' href='{$show_url}'>{$excludedkey}</a>";
			}
			$excludedkeystring = implode(", ", $pieces);
			
			echo "<td><i>" . $excludedkeystring . "</i></td>";
			echo "</tr>";
		}
		?>
		</table>
		<?php
		if (!$atleastoneinputparameterrenders)
		{
			echo "No input parameters found.";
			// allow user to add a new input parameter
			$addfieldurl = "https://global.nexusthemes.com/?nxs=task-gui&page=taskinstanceeditfield&taskid={$taskid}&taskinstanceid={$taskinstanceid}&field=_new";
			echo " <a href='{$addfieldurl}' target='_blank'>Add first field</a><br />";
		}
		else
		{
			// allow user to add a new input parameter
			$addfieldurl = "https://global.nexusthemes.com/?nxs=task-gui&page=taskinstanceeditfield&taskid={$taskid}&taskinstanceid={$taskinstanceid}&field=_new";
			echo " <a href='{$addfieldurl}' target='_blank'>Add another field</a><br />";
		}
		?>
	</div>
	<?php
}


function brk_tasks_gui_taskinstancedetail()
{
	$homeurl = functions::geturlhome();
	$currenturl = functions::geturlcurrentpage();
	$currentuserid = brk_tasks_gui_getuseridcurrentuser();
	
	brk_tasks_gui_render_head();
	
	$taskid = $_REQUEST["taskid"];
	if ($taskid == "") { functions::throw_nack("taskid not specified"); }
	
	$taskinstanceid = $_REQUEST["taskinstanceid"];
	if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not specified (1)"); }
	
	$instancemeta = tasks::gettaskinstance($taskid, $taskinstanceid);
	$taskmeta = tasks::gettaskmeta($taskid);
	
	$processingtype = $taskmeta["processingtype"];
	
	$action = $_REQUEST["action"];
	if ($action != "")
	{
		if (false)
		{
		}
		else if ($action == "forktaskinstance")
		{
			// reinitiate backto_taskid backto_taskinstanceid
			$createinstance_url = "{$homeurl}/api/1/prod/create-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint";
			
			// keep all the inputparameters
			foreach ($instancemeta["inputparameters"] as $key => $val)
			{
				$should_clone_key = true;
				if (functions::stringstartswith($key, "cloneinputparameterkey_"))
				{
				$should_clone_key = false;
				}
				
				if ($should_clone_key)
				{
					$createinstance_url = functions::addqueryparametertourl($createinstance_url, $key, $val, true, true);
				}
			}
			
			// set the other parameters
			$forktaskid = 211;
			$createinstance_url = functions::addqueryparametertourl($createinstance_url, "taskid", $forktaskid, true, true);
			$createinstance_url = functions::addqueryparametertourl($createinstance_url, "createdby_taskid", $taskid, true, true);
			$createinstance_url = functions::addqueryparametertourl($createinstance_url, "createdby_taskinstanceid", $taskinstanceid, true, true);
			$createinstance_string = file_get_contents($createinstance_url);
			$createinstance_result = json_decode($createinstance_string, true);
			if ($createinstance_result["result"] != "OK") { functions::throw_nack("error fetching createinstance_url; $createinstance_url"); }
			$created_taskinstanceid = $createinstance_result["taskinstanceid"];
			
			// abort "this" taskid taskinstanceid
			$note = "created $forktaskid $created_taskinstanceid ";
			$abort_reason = "fork_task_instance";
			
			$abort_url = "{$homeurl}/api/1/prod/abort-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint";
			$abort_url = functions::addqueryparametertourl($abort_url, "taskid", $taskid, true, true);
			$abort_url = functions::addqueryparametertourl($abort_url, "taskinstanceid", $taskinstanceid, true, true);
			// for some unknown reason we cannot use "note", as "not" is replaced with the mathematical sign for not
			$abort_url = functions::addqueryparametertourl($abort_url, "the_note", $note, true, true);
			$abort_url = functions::addqueryparametertourl($abort_url, "abort_reason", $abort_reason, true, true);
			$abort_url = functions::addqueryparametertourl($abort_url, "aborted_by_userid", brk_tasks_gui_getuseridcurrentuser(), true, true);
			
			$abort_string = file_get_contents($abort_url);
			$abort_result = json_decode($abort_string, true);
			if ($abort_result["result"] != "OK") { functions::throw_nack("error aborting; abort_url; $abort_url"); }
			
			$proceed_url = "{$homeurl}/?nxs=task-gui&page=taskinstancedetail&taskid={$forktaskid}&taskinstanceid={$created_taskinstanceid}";
			echo "Done! <a href='$proceed_url'>Proceed with the task instance to handle the forking</a>";
			
			die();
		}
		else if ($action == "reinitiateprevioustask_stage1")
		{
			$state = tasks::gettaskinstancestate($taskid, $taskinstanceid);
			if ($state == "ENDED")
			{
				// only allowed if the instance does NOT have offspring
				$created_tasks = $instancemeta["created_tasks"];
				if (count($created_tasks) == 0)
				{
					// allowed to re-initiate
				}
				else
				{
					echo "unable to complete reinitiateprevioustask_stage0.5; unexpected state; $state (has offspring)";
					die();
				}
			}
			else if (!($state == "CREATED" || $state == "STARTED" || $state == "ABORTED"))
			{
				echo "unable to complete reinitiateprevioustask_stage1; unexpected state; $state";
				die();
			}
			
			$backto_taskid = $_REQUEST["backto_taskid"];
			if ($backto_taskid == "") { functions::throw_nack("backto_taskid not set?"); }
			$backto_taskinstanceid = $_REQUEST["backto_taskinstanceid"];
			if ($backto_taskinstanceid == "") { functions::throw_nack("backto_taskinstanceid not set?"); }
			
			// check if taskinstance is still there (it could be archived)
			$backtostate = tasks::gettaskinstancestate($backto_taskid, $backto_taskinstanceid);
			if ($backtostate == "")
			{
			?>
			it looks like the instance is archived. Store it first before re-initiating the instance
			<?php
			die();
			}
			
			// render a form that allows user to select which of the properties to keep ("clone")
			
			echo "To reinitiate the task, select which properties you want to keep/clone:<br />";
			echo "<a href='#' onclick='nxs_js_checkall();return false;'>All?</a>";
			echo "<script>";
			echo "function nxs_js_checkall(event) ";
			echo "{ "; 
			echo "  $(':checkbox').each(function() {";
			echo "    this.checked = true;            ";            
			echo "  });";
			echo "}";			
			echo "</script>";
			
			$currenturl = functions::geturlcurrentpage();
			?>
			<form action='<?php echo $currenturl; ?>' method='POST' target='_blank' style='margin-left: 100px;'>
			<input type='hidden' name='nxs' value='task-gui' />
			<input type='hidden' name='taskid' value='<?php echo $taskid; ?>' />
			<input type='hidden' name='taskinstanceid' value='<?php echo $taskinstanceid; ?>' />
			
			<input type='hidden' name='backto_taskid' value='<?php echo $backto_taskid; ?>' />
			<input type='hidden' name='backto_taskinstanceid' value='<?php echo $backto_taskinstanceid; ?>' />
			
			<input type='hidden' name='page' value='taskinstancedetail' />
			<input type='hidden' name='action' value='reinitiateprevioustask_stage2' />			
			<?php
			
			$ancestor_taskmeta = tasks::gettaskinstance($backto_taskid, $backto_taskinstanceid);
			$ancestor_inputparameters = $ancestor_taskmeta["inputparameters"];
			$ancestor_keys_to_ignore = array("indent", "nxs", "nxs_json_output_format", "taskid");
			$ancestor_keys_to_clone_by_default = array("original_helpscoutticketnr");
			foreach ($ancestor_inputparameters as $key => $val)
			{
			if (in_array($key, $ancestor_keys_to_ignore))
			{
			continue;
			}
			if ($val == "")
			{
			// its a bit useless to see empty properties
			continue;
			}
			$checkedatt = "";
			if (in_array($key, $ancestor_keys_to_clone_by_default))
			{
			$checkedatt = "checked";
			}
			
			$preview = "$key ($val)";
			if (strlen($val) > 100)
			{
			$preview = "$key (too longer to make sense)";
			}
			
			echo "<input type='checkbox' name='cloneinputparameterkey_{$key}' {$checkedatt} /> {$preview}<br />";
			}
			?>
			<label>Why?</label><input type='text' style='width: 500px;' name='reinitiate_reason' placeholder='Explain why you want to re-initiate' value='see incident' required /><br />
			<br />
			<input type='submit' value='ReInitiate!' />
			</form>
			<?php
			die();
		}
		else if ($action == "reinitiateprevioustask_stage2")
		{
			functions::throw_nack("not yet implemented");

			/*
			$state = tasks::gettaskinstancestate($taskid, $taskinstanceid);
			if (!($state == "CREATED" || $state == "STARTED" || $state == "ABORTED"))
			{
			echo "unable to complete reinitiateprevioustask_stage2; unexpected state; $state";
			die();
			}
			
			$reinitiate_reason = $_REQUEST["reinitiate_reason"];
			
			$backto_taskid = $_REQUEST["backto_taskid"];
			if ($backto_taskid == "") { functions::throw_nack("backto_taskid not set?"); }
			$backto_taskinstanceid = $_REQUEST["backto_taskinstanceid"];
			if ($backto_taskinstanceid == "") { functions::throw_nack("backto_taskinstanceid not set?"); }
			
			$ancestor_taskmeta = tasks::gettaskinstance($backto_taskid, $backto_taskinstanceid);
			$ancestor_inputparameters = $ancestor_taskmeta["inputparameters"];
			
			$inputparameterstokeep = array();
			
			foreach ($_REQUEST as $possiblekey => $val)
			{
			if (functions::stringstartswith($possiblekey, "cloneinputparameterkey_"))
			{
			$key = str_replace("cloneinputparameterkey_", "", $possiblekey);
			$inputparameterstokeep[$key] = $ancestor_inputparameters[$key];
			}
			}
			
			// reinitiate backto_taskid backto_taskinstanceid
			
			$tospawninputparameters = array();
			
			// clone the inputparameterstokeep
			foreach ($inputparameterstokeep as $key => $val)
			{
			$tospawninputparameters[$key] = $val;
			}
			
			// set the other parameters			
			$tospawninputparameters["taskid"] = $backto_taskid;
			$tospawninputparameters["reinitiates_taskid"] = $backto_taskid;
			$tospawninputparameters["reinitiates_taskinstanceid"] = $backto_taskinstanceid;
			$tospawninputparameters["createdby_taskid"] = $taskid;
			$tospawninputparameters["createdby_taskinstanceid"] = $taskinstanceid;
			$tospawninputparameters["reinitiate_reason"] = $reinitiate_reason;
			
			// do a POST request instead of GET; GET requests can crash (probably too long)
			$create_result = brk_tasks_createtaskinstance_byinvokingapi($backto_taskid, $tospawninputparameters, $taskid, $taskinstanceid);
			if ($create_result["result"] != "OK") { functions::throw_nack("error creating instance"); }
			$createinstance_result = $create_result["action_result"];
			
			//
			$created_taskinstanceid = $createinstance_result["taskinstanceid"];
			
			// abort "this" taskid taskinstanceid
			$note = "created $backto_taskid $created_taskinstanceid (reinitiates previous task instance ($backto_taskid $backto_taskinstanceid)";
			$abort_reason = "reinitiate_previous_task_instance";
			
			$abort_url = "{$homeurl}/api/1/prod/abort-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint";
			$abort_url = functions::addqueryparametertourl($abort_url, "taskid", $taskid, true, true);
			$abort_url = functions::addqueryparametertourl($abort_url, "taskinstanceid", $taskinstanceid, true, true);
			// for some unknown reason we cannot use "note", as "not" is replaced with the mathematical sign for not
			$abort_url = functions::addqueryparametertourl($abort_url, "the_note", $note, true, true);
			$abort_url = functions::addqueryparametertourl($abort_url, "abort_reason", $abort_reason, true, true);
			$abort_url = functions::addqueryparametertourl($abort_url, "aborted_by_userid", brk_tasks_gui_getuseridcurrentuser(), true, true);
			
			$abort_string = file_get_contents($abort_url);
			$abort_result = json_decode($abort_string, true);
			if ($abort_result["result"] != "OK") 
			{
			echo "ERR; aborting ($abort_url) failed; $abort_string<br /><br />"; 
			//functions::throw_nack("error aborting; abort_url; $abort_url"); 
			}
			
			$proceed_url = "{$homeurl}/?nxs=task-gui&page=taskinstancedetail&taskid={$backto_taskid}&taskinstanceid={$created_taskinstanceid}";
			echo "Done! <a href='$proceed_url'>Proceed with the new (reinitiated) instance</a>";
			
			die();
			*/
		}
		else if ($action == "updateparameter")
		{
			$name = $_REQUEST["name"];
			$value = $_REQUEST["value"];
			
			/*
			if (functions::stringcontains($value, "\n"))
			{
			echo "value contains new line char while processing updateparameter action";
			die();
			}
			else
			{
			echo "value does NOT contain a new line char while processing updateparameter action";
			die();
			}
			
			// for arrays (see input_paramet for select dropdownlist with multiple allowed)
			if (is_array($_REQUEST["value"]))
			{
			$value = implode(";", $value);
			}
			*/
			
			$returnurl = $_REQUEST["returnurl"];
			
			$inputparameterstoappend = array
			(
				$name => $value,
			);
			
			/*			
			echo "TESTING:";
			$pimped = $value;
			$pimped = str_replace("\r\n", "X", $pimped);
			var_dump($pimped);
			die();
			*/			

			tasks::appendstateparameters_for_taskinstance($taskid, $taskinstanceid, $inputparameterstoappend);
			
			echo "stored {$name}:{$value}. Please click here; <a href='$returnurl'>$returnurl</a> to proceed";
			?>
			<script>
			window.location='<?php echo $returnurl; ?>';
			</script>
			<?php
			die();
		}
		else if ($action == "updatebulkparameters")
		{
			$multiparameterindex = -1;
			$in_multiparameter_loop = true;
			while ($in_multiparameter_loop)
			{
			$iterator_postfix = "";
			if ($multiparameterindex > -1)
			{
				$iterator_postfix = "_{$multiparameterindex}";	// for example _0, _1, _2, ...
			}
			
			$name = $_REQUEST["name{$iterator_postfix}"];
			
			if (!isset($name))
			{
				if ($multiparameterindex == -1)
				{
					$multiparameterindex++;
					// proceed with the next one
					continue;
				}
				else
				{
					// this means end of the multiparameter loop
					$in_multiparameter_loop = false;
					break;
				}
			}
			
			$value = $_REQUEST["value{$iterator_postfix}"];
			
			$returnurl = $_REQUEST["returnurl"];
			
			$inputparameterstoappend = array
			(
				$name => $value,
			);
			
			tasks::appendstateparameters_for_taskinstance($taskid, $taskinstanceid, $inputparameterstoappend);
			
			echo "stored {$name}:{$value}<br />";
			
			// loop, unless
			$multiparameterindex++;
			if ($multiparameterindex > 1024)
			{
			//
			functions::throw_nack("error bulk updating task instance? (unexpected count? $multiparameterindex)");
			}
			}
			
			echo "please click here; <a href='$returnurl'>$returnurl</a> to proceed";
			?>
			<script>
			window.location='<?php echo $returnurl; ?>';
			</script>
			<?php
			die();
		}
		else if ($action == "clearallparameters")
		{
			$returnurl = $_REQUEST["returnurl"];
			
			$path = "/srv/metamodel/businessprocess.task.instances/{$taskid}.json";
			$string = file_get_contents($path);
			$meta = json_decode($string, true);
			$meta[$taskinstanceid]["inputparameters"] = array();
			$string = json_encode($meta);
			$r = file_put_contents($path, $string);
			
			if ($r === false) { functions::throw_nack("error updating task instance"); }
			
			echo "cleared state parameters. Please click here; <a href='$returnurl'>$returnurl</a> to proceed";
			?>
			<script>
			window.location='<?php echo $returnurl; ?>';
			</script>
			<?php
			
			die();
		}
		else if ($action == "aborttaskinstance")
		{
			$abort_reason = $_REQUEST["abort_reason"];
			if ($abort_reason == "") { functions::throw_nack("error: not aborted; abort_reason is required"); }
			
			$note = $_REQUEST["note"];
			
			if (false)
			{
			}
			else if ($abort_reason == "testing")
			{
				//
			}
			else if ($abort_reason == "not_enough_time")
			{
				//
			}
			else if ($abort_reason == "replaced_by_rfc_internal")
			{
				// 
			}
			else if ($abort_reason == "see_incident_offspring")
			{
				//
			}
			else
			{
				if ($note == "") { functions::throw_nack("error: not aborted; note is required ($abort_reason)"); }
			}
			
			$abort_url = "{$homeurl}/api/1/prod/abort-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint";
			$abort_url = functions::addqueryparametertourl($abort_url, "taskid", $taskid, true, true);
			$abort_url = functions::addqueryparametertourl($abort_url, "taskinstanceid", $taskinstanceid, true, true);
			// for some unknown reason we cannot use "note", as "not" is replaced with the mathematical sign for not
			$abort_url = functions::addqueryparametertourl($abort_url, "the_note", $note, true, true);
			$abort_url = functions::addqueryparametertourl($abort_url, "abort_reason", $abort_reason, true, true);
			$abort_url = functions::addqueryparametertourl($abort_url, "aborted_by_userid", brk_tasks_gui_getuseridcurrentuser(), true, true);
			
			$abort_string = file_get_contents($abort_url);
			$abort_result = json_decode($abort_string, true);
			if ($abort_result["result"] != "OK") { functions::throw_nack("error aborting; abort_url; $abort_url"); }
			
			echo "task instance was aborted :)<br />";
			echo "go to <a href='{$homeurl}/?nxs=task-gui&page=workqueue'>the workqueue</a><br /><br />";
			die();
			//echo "abort_url:" . $abort_url . "<br /><br />";
		}
		else if ($action == "createtaskinstance144andcloseticket")
		{
			// invoke the api to create the task instance
			$number = $_REQUEST["number"];
			
			$createdby_taskid = $_REQUEST["taskid"];
			$createdby_taskinstanceid = $_REQUEST["taskinstanceid"];
			
			$taskid_to_create = 144;
			$createtaskinstance_url = "{$homeurl}/api/1/prod/create-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid_to_create}&original_helpscoutticketnr={$number}&createdby_taskid={$createdby_taskid}&createdby_taskinstanceid={$createdby_taskinstanceid}";
			$createtaskinstance_string = file_get_contents($createtaskinstance_url);
			$createtaskinstance_result = json_decode($createtaskinstance_string, true);
			if ($createtaskinstance_result["result"] != "OK") { functions::throw_nack("unable to invoke createtaskinstance_url; $createtaskinstance_url"); }
			
			
			$created_taskinstanceid = $createtaskinstance_result["taskinstanceid"];
			
			// add note
			$url = "{$homeurl}/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid_to_create}&taskinstanceid={$created_taskinstanceid}";
			$note = "is handled by <a href='{$url}'>taskid $taskid_to_create $created_taskinstanceid</a>";
			$addnote_url = "{$homeurl}/api/1/prod/add-note-to-helpscout-conversation/?nxs=helpscout-api&nxs_json_output_format=prettyprint";
			$addnote_url = functions::addqueryparametertourl($addnote_url, "note", $note, true, true);
			$addnote_url = functions::addqueryparametertourl($addnote_url, "helpscoutnumber", $number, true, true);
			$addnote_string = file_get_contents($addnote_url);
			$addnote_result = json_decode($addnote_string, true);
			if ($addnote_result["result"] != "OK") { functions::throw_nack("unable to invoke addnote_url; $addnote_url"); }
			
			$closeticket_url = "{$homeurl}/api/1/prod/close-ticket-by-number/?nxs=helpscout-api&nxs_json_output_format=prettyprint&helpscoutnumber={$number}";
			$closeticket_string = file_get_contents($closeticket_url);
			$closeticket_result = json_decode($closeticket_string, true);
			if ($closeticket_result["result"] != "OK") { functions::throw_nack("unable to invoke closeticket_url; $closeticket_url"); }
			
			$result = array
			(
			"createtaskinstance_result" => $createtaskinstance_result,
			"addnote_result" => $addnote_result,
			"closeticket_result" => $closeticket_result,
			);
			
			functions::webmethod_return_ok($result);
			
			die();
		}
		else if ($action == "reopeninstance")
		{
			echo "to be implemented (re-implemented)";
			die();
			
			// right now the code opens the existing instance,
			// this is not ok; the improved code should create a NEW instance (as offspring of "this" one),
			// allowing the user to select which properties to keep (similar to how re-initiating works),
			// and then close "this" one
			
			$assignedtouser_id = brk_tasks_gui_getuseridcurrentuser();
			
			$action_url = "{$homeurl}/api/1/prod/re-open-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$taskinstanceid}&assignedtouser_id=$assignedtouser_id";
			$action_string = file_get_contents($action_url);
			$action_result = json_decode($action_string, true);
			if ($action_result["result"] != "OK") { functions::throw_nack("unable to fetch action_url; $action_url"); }
			// task instance is started if we end up here
		}
		else if ($action == "starttaskinstance")
		{
			// 
			$assignedtouser_id = brk_tasks_gui_getuseridcurrentuser();	// todo; get userid
			$startresult = tasks::starttaskinstance($taskid, $taskinstanceid, $currentuserid);
			echo "<div>";
			echo "start result:<br />";
			var_dump($startresult);
			echo "</div>";
			
			// task instance is started if we end up here
		}
		else if ($action == "pick_employee")
		{
			$fetch_url = "{$homeurl}/api/1/prod/search-employees/?nxs=hr-api&nxs_json_output_format=prettyprint&role=support";
			$fetch_string = file_get_contents($fetch_url);
			$fetch_result = json_decode($fetch_string, true);
			foreach ($fetch_result["employees"] as $employee)
			{
			$employees[] = $employee;
			}
			$employees[] = array
			(
			"nxs.hr.employee_id" => "",
			"face_thumb_url" => "noone.png",
			"firstname" => "no one",
			);
			foreach ($employees as $employee)
			{
			$employee_id = $employee["nxs.hr.employee_id"];
			$face_thumb_url = $employee["face_thumb_url"];
			$firstname = $employee["firstname"];
			
			?>
			<form action='<?php echo $currenturl; ?>' method='POST' style='margin-left: 100px;'>
			<input type='hidden' name='nxs' value='task-gui' />
			<input type='hidden' name='taskid' value='<?php echo $taskid; ?>' />
			<input type='hidden' name='taskinstanceid' value='<?php echo $taskinstanceid; ?>' />
			<input type='hidden' name='page' value='taskinstancedetail' />
			<input type='hidden' name='action' value='assigntoemployee' />
			<input type='hidden' name='employee_id' value='<?php echo $employee_id; ?>' />
			<div>
			<img src='<?php echo $face_thumb_url; ?>' alt='<?php echo $employee_id; ?>' title='<?php echo $employee_id; ?>' /><br />
			<input type='submit' value='Assign to <?php echo $firstname; ?>' />
			</div>
			</form>
			<?php
			}
			
			die();
		}
		else if ($action == "assigntoemployee")
		{
			$employee_id = $_REQUEST["employee_id"];
			
			$action_url = "{$homeurl}/api/1/prod/assign-employee-to-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$taskinstanceid}&employeeid={$employee_id}";
			$action_string = file_get_contents($action_url);
			$action_result = json_decode($action_string, true);
			if ($action_result["result"] != "OK") { functions::throw_nack("unable to create task instance; $action_url"); }
			
			echo "assigning done :)";
			$redirect_url = "{$homeurl}/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
			?>
			<script>
			window.location='<?php echo $redirect_url; ?>';
			</script>
			<?php
			
			die();
		}
		else if ($action == "executeonestep")
		{
			$ensure_executionpointer = $_REQUEST["ensure_executionpointer"];
			// todo; check that!
			
			$delegated_execution_url = "{$homeurl}/api/1/prod/run-task-instance-headless/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$taskinstanceid}&executionmode=CURRENT_TASK_INSTRUCTION_ONLY";
			$delegated_execution_result_string = file_get_contents($delegated_execution_url);
			$delegated_execution_result = json_decode($delegated_execution_result_string, true);
			
			?>
			<div class='execution_result'>
				<h1>Execution result:</h1>
				<textarea style='width: 100%; height: 300px;'>
					<?php echo $delegated_execution_result_string; ?>
				</textarea>
			</div>
			<?php
		}
		else if ($action == "setexecutionpointer")
		{
			$executionpointerid = $_REQUEST["executionpointerid"];
			$delegated_result = tasks::setexecutionpointer($taskid, $taskinstanceid, $executionpointerid);
			$delegated_result_string = json_encode($delegated_result);
			?>
			<div class='result'>
				<h1>Result:</h1>
				<textarea style='width: 100%; height: 300px;'>
					<?php echo $delegated_result_string; ?>
				</textarea>
			</div>
			<?php
		}
		else
		{
			echo "this action is not yet supported; $action";
			die();
		}
	}
	
	?>
	<script>function generatepwd(characters, length) { var result = '';  var charactersLength = characters.length; for ( var i = 0; i < length; i++ ) { result += characters.charAt(Math.floor(Math.random() * charactersLength)); } return result; }</script>
	<?php
	
	// ----


	// fetch properties of the ixplatform
	$tasktitle = tasks::gettasktitle($taskid);
	
	// 
	$instancemeta = tasks::gettaskinstance($taskid, $taskinstanceid);
	$taskinstancestate = $instancemeta["state"];
	$lookup = tasks::gettaskinstancelookup($taskid, $taskinstanceid);
	
	$recipe = tasks::gettaskrecipe($taskid);
	$recipe_hash = md5($recipe . $taskid . $taskinstanceid);
	
	$execution_pointer_support = "";
	$taskmeta = tasks::gettaskmeta($taskid);
	$execution_pointers_support = $taskmeta["execution_pointers_support"];
	
	$task_reflectionmeta = tasks::getreflectionmeta($taskid, $taskinstanceid);

	if ($execution_pointers_support == "v1")
	{
		$current_executionpointer = tasks::getexecutionpointer($taskid, $taskinstanceid);
		$isexecutionpointerlegit = tasks::isexecutionpointerlegit($taskid, $current_executionpointer);
		
		$current_task_instruction = $task_reflectionmeta["task_instructions_by_id"][$current_executionpointer];
		$current_instructionnr = $current_task_instruction["instructionnr"];
		$current_linenr = $current_task_instruction["linenr"];
		
		$run_one_step_url = functions::geturlcurrentpage();
		$run_one_step_url = functions::addqueryparametertourl($run_one_step_url, "action", "executeonestep", true, true);
		$run_one_step_url = functions::addqueryparametertourl($run_one_step_url, "ensure_executionpointer", $current_executionpointer, true, true);
		
		$run_remaining_steps_url = "{$homeurl}/api/1/prod/run-task-instance-headless/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$taskinstanceid}&executionmode=ALL_REMAINING_TASK_INSTRUCTIONS";
	}
	else
	{	
		$finished_instruction_pointer = tasks::getfinishedinstructionpointer($taskid, $taskinstanceid);
		$finished_pointer_pieces = explode("_", $finished_instruction_pointer);
		if ($finished_pointer_pieces[0] != $recipe_hash)
		{
			$finished_pointer_pieces = "";
		}
		if ($finished_instruction_pointer != "")
		{
			$has_finished_instruction_pointer = true;
			$finished_instruction_pointer_linenr = $finished_pointer_pieces[1];
		}
	}
	
	//error_log("tidetail:decorate applied recipe");
	
	// decorate applied recipe
	if (true)
	{
		$appliedrecipe = $recipe;
		
		//error_log("tidetail:process tildes");
		
		if (functions::stringcontains($appliedrecipe, "~"))
		{
			$tildeindex = -1;
			$pieces = explode("~", $appliedrecipe);
			$newpieces = array();
			foreach ($pieces as $piece)
			{
				// parse the type of this tilde section (~textarea)
				$type = "";
				$possibletypes = array("textarea", "hidden");
				foreach ($possibletypes as $possibletype)
				{
					if (functions::stringstartswith($piece, $possibletype))
					{
						$type = $possibletype;
						$piece = substr($piece, strlen($possibletype));
					}
				}
				$piece = trim($piece);	// remove white space characters before and after
				
				$tildeindex++;
				if ($tildeindex % 2 == 1)
				{
					if (false)
					{
					}
					else if ($type == "")
					{
					$piece = "<div id='{$guid}' style='width:80vw' class='tilde'>" . $piece;
					$piece = str_replace("\r\n", "\n", $piece);
					$piece = str_replace("\n", "<break />", $piece);
					$piece .= "</div>";
					}
					else if ($type == "textarea")
					{
					$guid = "a" . rand(9999999, 999999999);
					$linecount = substr_count($piece, "\n");
					$pixels = $linecount * 16;
					
					$piece = "<div style='display: inline-block;'><textarea id='{$guid}' style='width:80vw;min-height:{$pixels}px;' class='tilde'>" . $piece;
					// change all concatenate all new lines
					$piece = str_replace("\r\n", "\n", $piece);
					$piece = str_replace("\n", "<newline />", $piece);
					$piece .= "</textarea>";
					
					$piece .= "<br />";					
					$piece .= "<button onclick='nxs_js_copy_{$guid}();return false;'>Copy</button>";
					$piece .= "<script>function nxs_js_copy_{$guid}() { let textarea=document.getElementById(\"{$guid}\");textarea.select();document.execCommand(\"copy\"); }</script>";
					$piece .= "</div>";
					}
					else if ($type == "hidden")
					{
					// absorb
					$piece = "<div class='hidden'></div>";
					}
					else
					{
					$piece .= "ERROR; tilde type not supported; $type<br />";
					}
				}
				$newpieces[] = $piece;
			}
			$appliedrecipe = implode("", $newpieces);
			//$appliedrecipe = str_replace("~", ":):):)", $appliedrecipe);	
		}
		
		
		
		// only replace tabs or *'s when it starts with
		$appliedrecipe = str_replace("\t", " ", $appliedrecipe);
		
		// convert shortcodes
		// $appliedrecipe = do_shortcode($appliedrecipe);
		$appliedrecipe = str_replace("\r\n", "\n", $appliedrecipe);
		$appliedrecipe = str_replace("\r", "\n", $appliedrecipe);
		
		$appliedrecipe = str_replace("�", "\"", $appliedrecipe);
		$appliedrecipe = str_replace("�", "\"", $appliedrecipe);
		
		$lines = explode("\n", $appliedrecipe);
		
		$indentedlines = array();
		global $nxs_gl_recipe_instruction_pointer;
		$nxs_gl_recipe_instruction_pointer["recipe_hash"] = $recipe_hash;
		$nxs_gl_recipe_instruction_pointer["linenr"] = -1;
		foreach ($lines as $line)
		{
			$nxs_gl_recipe_instruction_pointer["linenr"] = $nxs_gl_recipe_instruction_pointer["linenr"] + 1;
			$linenr = $nxs_gl_recipe_instruction_pointer["linenr"];
			
			//error_log("tidetail:process linenr; $linenr");
			
			// parse line for possible taskinstructions
			$lineresult = tasks::parserecipeline($taskid, $linenr, $line);
			
			$linetype = $lineresult["linetype"];
			$type = $lineresult["type"];
			if (false)
			{
				//
			}
			else if ($linetype == "taskinstruction")
			{
				$taskinstruction = $lineresult;
				$nxs_gl_recipe_instruction_pointer["taskinstruction"] = $taskinstruction;
				
				if ($execution_pointers_support == "v1")
				{
					$task_instruction_id = $lineresult["task_instruction_id"];
					if ($current_executionpointer == $task_instruction_id)
					{					
						$inject_before = "";
						$inject_before .= "<div id='nxs-current-execution-pointer' style='background-color: red; padding: 5px; margin: 5px;'>";
						$inject_before .= "<div style='background-color: #eee; padding: 5px; margin: 5px;'>";
						$inject_before .= "taskinstruction; id: $task_instruction_id (type: $type)<br />";
						$inject_before .= "actions;<br />";
						$inject_before .= "- <a target='_blank' href='{$run_one_step_url}'>EXECUTE (one step at a time)</a><br />";
						$inject_before .= "- <a target='_blank' href='{$run_remaining_steps_url}'>RUN (all remaining steps)</a><br />";
						$inject_before .= "<div style='background-color: #ddd; padding: 5px; margin: 5px;'>";
						
						$inject_after = "";
						$inject_after .= "</div>";
						$inject_after .= "</div>";
						$inject_after .= "</div>";
					}
					else
					{
						// $current_instructionnr = $current_task_instruction["instructionnr"];
						// $current_linenr = $current_task_instruction["linenr"];
						
						$state = "notset";
						if (false)
						{
							//
						}
						else if ($linenr < $current_linenr)
						{
							$state = "past";
						}
						else if ($linenr == $current_linenr)
						{
							$state = "current";
						}
						else
						{
							$state = "future";
						}
						
						$reposition_execution_pointer_url = functions::geturlcurrentpage();
						$reposition_execution_pointer_url = functions::addqueryparametertourl($reposition_execution_pointer_url, "action", "setexecutionpointer", true, true);
						$reposition_execution_pointer_url = functions::addqueryparametertourl($reposition_execution_pointer_url, "executionpointerid", $task_instruction_id, true, true);
						
						// "{$homeurl}/api/1/prod/set-task-instance-execution-pointer/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$taskinstanceid}&executionpointerid={$task_instruction_id}";
						
						$inject_before = "";
						$inject_before .= "<div class='nxs-ip nxs-{$state}' style='background-color: #eee; padding: 5px; margin: 5px;'>";
						$inject_before .= "<div style='background-color: #ddd; padding: 5px; margin: 5px;'>";
						$inject_before .= "taskinstruction; id: $task_instruction_id (type: $type)<br />";
						$inject_before .= "actions;<br />";
						$inject_before .= "- <a target='_blank' href='{$reposition_execution_pointer_url}'>REPOSITION EXECUTION POINTER TO THIS INSTRUCTION</a><br />";
						// $inject_before .= "line result:" . json_encode($lineresult);
						$inject_before .= "<div style='background-color: #ccc; padding: 5px; margin: 5px;'>";
						
						$inject_after = "";
						$inject_after .= "</div>";
						$inject_after .= "</div>";
						$inject_after .= "</div>";
					}
					
					
					$line = $inject_before . $line . $inject_after;
				}
			}
			else if ($linetype == "text")
			{
			}
			
			//error_log("tidetail:process shortcode line start '{$type}'");
			
			$renderquickly = false;
			if ($execution_pointers_support == "v1")
			{
				$renderquickly = true;
				//($_REQUEST["renderquickly"] == "true");	
			}
			
			if ($renderquickly == true)
			{
				// to speed things up, we don't evaluate the shortcode;
				// evaluation can be done by executing the steps of the execution pointers
				// $line = htmlentities($line);
			}
			else
			{			
				$line = do_shortcode($line);
			}
			
			//error_log("tidetail:process shortcode line finished");
			
			$line = trim($line);
			
			if ($renderquickly)
			{
			}
			else
			{
				// for slow rendering use manual shortcodes and finished execution pointers
				if ($line != "")
				{
					if ($has_finished_instruction_pointer)
					{
						if (false)
						{
						}
						else if ($linenr < $finished_instruction_pointer_linenr)
						{
							$line = "<div id='instruction_pointer_{$linenr}' class='nxs-ip nxs-past' title='line nr: {$linenr}'>{$line}</div>";		
						}
						else if ($linenr == $finished_instruction_pointer_linenr)
						{
							$line = "<div id='instruction_pointer_{$linenr}' class='nxs-ip nxs-current' title='line nr: {$linenr}'>{$line}</div>";		
						}
						else 
						{
							// one of the upcoming line(s)
							$line = "<div id='instruction_pointer_{$linenr}' class='nxs-ip nxs-future' title='line nr: {$linenr}'>{$line}</div>";		
						}
					}
				}
			}
			
			if ($line != "")
			{
				$indentedlines[] = str_replace("*", "<span class='tab'>&nbsp;</span>", $line);
			}				
		}
	
		// tabs
		$appliedrecipe = implode("\r\n", $indentedlines);
		
		// replace placeholders if any are remaining
		$appliedrecipe = functions::translatesingle($appliedrecipe, "{{", "}}", $lookup);
		
		// convert new lines to html breaks
		$appliedrecipe = nl2br($appliedrecipe);
		
		$appliedrecipe = str_replace("<newline />", "\r\n", $appliedrecipe);
		$appliedrecipe = str_replace("<break />", "<br />", $appliedrecipe);
	}
	
	//error_log("tidetail:decorate task title");
	
	// decorate task title
	if (true)
	{
	$appliedtasktitle = $tasktitle;
	$appliedtasktitle = str_replace("{{", "<span class='placeholder'>{{", $appliedtasktitle);
	$appliedtasktitle = str_replace("}}", "}}</span>", $appliedtasktitle);
	$appliedtasktitle = str_replace("*", "<span class='tab'>&nbsp;</span>", $appliedtasktitle);
	$appliedtasktitle = functions::translatesingle($appliedtasktitle, "{{", "}}", $lookup);
	}
	
	$newtaskinstanceform_url = "{$homeurl}/?nxs=task-gui&page=createnewtaskinstanceform&newtaskid={$taskid}&createdby_taskid={$taskid}&createdby_taskinstanceid={$taskinstanceid}";
	$newbulktaskinstanceform_url = "{$homeurl}/?nxs=task-gui&page=createnewbulktaskinstanceform&newtaskid={$taskid}&createdby_taskid={$taskid}&createdby_taskinstanceid={$taskinstanceid}";
	
	?>
	<div>
	<?php
	
	//error_log("tidetail:brk_tasks_gui_rendernavigation");
	
	brk_tasks_gui_rendernavigation();
	
	// render scroll to current
	if ($execution_pointers_support == "v1")
	{
		
		?>
		<div class='nxs-ip-navigator'>
    	<a style='text-decoration: none;' href='#' onclick='nxs_js_scrolltocurrentexecutionpointer(); return false;'>&#8592; Scroll to current</a><br />
    	<a style='text-decoration: none;' href='<?php echo $run_one_step_url;?>'>&#8617; Execute single step</a><br />
    	<a style='text-decoration: none;' target='_blank' href='<?php echo $run_remaining_steps_url; ?>'>&#8595; Run remaining steps</a><br />
    </div>
    <style>
    	.nxs-ip-navigator
    	{
    		position: fixed;
		    background-color: #eee;
		    padding: 3px;
		    color: white;
		    right: 30px;
		    top:  30px;
		    border: 1px solid black;
    	}
    </style>
		<script>
			function nxs_js_scrolltocurrentexecutionpointer()
			{
				$(document.body).animate({
				    'scrollTop':   $('#nxs-current-execution-pointer').offset().top
				}, 500);
			}
		</script>
		<?php
	}
			
	?>
	<h1 style='font-size: 3em;'>Task <?php echo "{$taskid} - {$appliedtasktitle}"; ?> (<?php echo $processingtype; ?>)</h1>
	<span title='use renderoffspring=true to generate child task instances'>&#9432;</span>
	<br />
	<a target='_blank' href='{$homeurl}/?nxs=task-gui&page=taskinstances&taskid=<?php echo $taskid; ?>'>View all instances of this task</a><br />
	<a target='_blank' href='<?php echo $newtaskinstanceform_url; ?>'>Create new instance</a><br />
	<a target='_blank' href='<?php echo $newbulktaskinstanceform_url; ?>'>Create new instances (bulk)</a><br />
	
	<a target='_blank' href='{$homeurl}/?nxs=task-gui&page=addnotepage&taskid=<?php echo $taskid; ?>&taskinstanceid=<?php echo $taskinstanceid; ?>'>Add note</a><br />
	<a href='#' onclick="jQuery('#panel_discuss').show(); return false;">Discuss</a><br />
	<div id='panel_discuss' style='display: none; background-color: #eee; padding:5px; margin: 5px;'>
	<?php
	
	// echo do_shortcode("[nxs_p001_task_instruction type='create-task-instance' create_taskid='404' render_required_fields=true makeunique='{$taskinstanceid}@{$taskid}']");
	
	?>
	</div>
	<a target='_blank' href='{$homeurl}/?nxs=task-gui&page=createtaskinstanceincident&incident_taskid=<?php echo $taskid; ?>&incident_taskinstanceid=<?php echo $taskinstanceid; ?>'>Create Incident</a><br />
	<?php
	
	if ($_REQUEST["renderoffspring"] == "true")
	{
		echo do_shortcode("[nxs_p001_task_instruction type='conditional_wrapper_begin' title='Need to create offspring task instance?' id='if_needsoffspringintance']");
		$create_offspringtaskinstance_taskid = $lookup["create_offspringtaskinstance_taskid"];
		if ($create_offspringtaskinstance_taskid == "")
		{
		echo do_shortcode("[nxs_p001_task_instruction type='pick_task' field='create_offspringtaskinstance_taskid']");
		}
		else
		{
		echo do_shortcode("[nxs_p001_task_instruction type='pick_task' field='create_offspringtaskinstance_taskid']");
		echo do_shortcode("[nxs_p001_task_instruction type='create-task-instance' create_taskid='{$create_offspringtaskinstance_taskid}']");
		}
		
		echo do_shortcode("[nxs_p001_task_instruction type='end-task-instance']");
		
		echo do_shortcode("[nxs_p001_task_instruction type='conditional_wrapper_end']");
	}
	?>
	<?php
	$currenturl = functions::geturlcurrentpage()
	?>	
	<!-- AAAAA-->
	<div class='container'>
	<a class='toggleable' href='#' onclick="jQuery(this).closest('.container').find('.toggleable').toggle(); return false;">Abort task instance (...)</a>
	<div id='aborttaskinstance' class='toggleable' style='background-color: red; display: none;'>
	<h2>You are about to abort this instance</h2>
	<form action='<?php echo $currenturl; ?>' method='POST' target='_blank' style='margin-left: 100px;'>
	<input type='hidden' name='nxs' value='task-gui' />
	<input type='hidden' name='page' value='taskinstancedetail' />
	<input type='hidden' name='action' value='aborttaskinstance' />
	<label>Reason for aborting?</label><br />
	<select name='abort_reason'>
	<option value=''>SELECT ABORT REASON</option>
	<option value='testing'>Was just testing</option>
	<option value='see_incident_offspring'>Handled by incident</option>
	<option value='replaced_by_rfc_internal'>Replaced by rfc (internal)</option>
	<option value='not_enough_time'>Not enough time</option>
	<option value='user_requested_refund'>User requested refund</option>
	<option value='duplicate'>It is a duplicate</option>
	<option value='misinterpreted_helpscout_interpretation'>Helpscout ticket was misinterpreted</option>
	<option value='was_created_by_accident'>Helpscout ticket was created by accident</option>
	<option value='handled_manually_outside_system'>I resolved this manually outside the system</option>
	</select>
	<br />
	<label>note:</label><br />
	<textarea name='note' style='width: 100%; height: 40px;' placeholder='note (required)'></textarea>
	<br />
	<br />
	<input type='submit' value='Abort task instance' />
	</form>
	<a href='#' onclick="jQuery(this).closest('.container').find('.toggleable').toggle(); return false;">Cancel</a>
	</div>
	</div>
	
	<br />
	
	<h2>Meta</h2>
	<?php
	
	$createtime = $instancemeta["createtime"];
	$rightnow = strtotime('now');
	$delta = $rightnow - $createtime;
	$duration_since_creation_in_days = functions::getsecondstohumanreadable($delta);
	$assignedtouser_id = $instancemeta["assignedtouser_id"];
	
	if ($assignedtouser_id == "")
	{
	echo "Not yet assigned to someone<br />";
	}
	else
	{
		echo "Assigned to:<br />";
		echo "user: {$assignedtouser_id}<br />";
	}
	
	$currenturl = functions::geturlcurrentpage();
	$currentuserid = brk_tasks_gui_getuseridcurrentuser();
	
	$show_assign_to_yourself = false;
	$show_assign_to_someoneelse = false;
	
	if ($assignedtouser_id != "")
	{
		if ($assignedtouser_id != $currentuserid)
		{
		$show_assign_to_yourself = true;
		}
		else
		{
		$show_assign_to_someoneelse = true;
		}
	}
	else
	{
		$show_assign_to_yourself = true;
	}
	
	if ($show_assign_to_yourself)
	{
		?>
		<form action='<?php echo $currenturl; ?>' method='POST' target='_blank' style='margin-left: 100px;'>
		<input type='hidden' name='nxs' value='task-gui' />
		<input type='hidden' name='taskid' value='<?php echo $taskid; ?>' />
		<input type='hidden' name='taskinstanceid' value='<?php echo $taskinstanceid; ?>' />
		<input type='hidden' name='page' value='taskinstancedetail' />
		<input type='hidden' name='action' value='assigntoemployee' />
		<input type='hidden' name='employee_id' value='<?php echo $currentuserid; ?>' />
		<input type='submit' value='Assign to YOURSELF' style='background-color: orange; color: white;' />
		</form>
		<?php
	}
	
	if ($show_assign_to_someoneelse)
	{
	?>
	<form action='<?php echo $currenturl; ?>' method='POST' target='_blank' style='margin-left: 100px;'>
	<input type='hidden' name='nxs' value='task-gui' />
	<input type='hidden' name='taskid' value='<?php echo $taskid; ?>' />
	<input type='hidden' name='taskinstanceid' value='<?php echo $taskinstanceid; ?>' />
	<input type='hidden' name='page' value='taskinstancedetail' />
	<input type='hidden' name='action' value='pick_employee' />
	<input type='submit' value='Assign to someone else ...' />
	</form>
	<?php
	}
		
	$creationdate_human = date("j F Y G:i", $createtime);
	echo "Created; {$creationdate_human}<br />";
	echo "Days since creation; {$duration_since_creation_in_days}<br />";
	
	$starttime = $instancemeta["starttime"];
	if ($starttime != "")
	{
	$delta = $starttime - $createtime;
	$duration_before_started_in_days = functions::getsecondstohumanreadable($delta);
	echo "Days waited till started; {$duration_before_started_in_days}<br />";
	}
	
	//
	if ($finished_instruction_pointer_linenr != "")
	{
		echo "finished_instruction_pointer: <a href='#instruction_pointer_{$finished_instruction_pointer_linenr}'>linenr: {$finished_instruction_pointer_linenr}</a><br />";
		echo "<div style='position: fixed; right: 100px; width: 100px; background-color: blue; padding: 3px;'>";
		echo "<a style='color: white;' href='#instruction_pointer_{$finished_instruction_pointer_linenr}'>linenr: {$finished_instruction_pointer_linenr}</a><br />";
		echo "</div>";
	}
	
	$endtime = $instancemeta["endtime"];
	if ($endtime != "")
	{
	$delta = $endtime - $createtime;
	$duration_handling_in_days = functions::getsecondstohumanreadable($delta);
	echo "End to end time it took to handle (including waits); {$duration_handling_in_days} (creation-end)<br />";
	$delta = $endtime - $starttime;
	$duration_handling_in_days = functions::getsecondstohumanreadable($delta);
	echo "Operational time it took to handle (including waits); {$duration_handling_in_days} (started-end)<br />";
	}
	
	// echo json_encode($instancemeta);
	?>
	
	<h2>Qualification stacktrace</h2>
	<?php
	
	$stacktrace = tasks::getstacktracepreviousgeneration($taskid, $taskinstanceid);
	if (count($stacktrace) > 0)
	{
		echo "Previous generation:<br />";
		foreach ($stacktrace as $frame)
		{
			$parentcreationtime = $frame["createtime"];
			if ($parentcreationtime == "") { $parentcreationtime = strtotime('now'); }
			$rightnow = strtotime('now');
			$delta = $rightnow - $parentcreationtime;
			$duration_humanreadable = functions::getsecondstohumanreadable($delta);
			
			$parenttaskid = $frame["taskid"];
			$parenttaskinstanceid = $frame["taskinstanceid"];
			$parent_url = "{$homeurl}/?nxs=task-gui&page=taskinstancedetail&taskid={$parenttaskid}&taskinstanceid={$parenttaskinstanceid}";
			$tasktitle = tasks::gettasktitle($parenttaskid);
			
			echo "<a target='_blank' href='{$parent_url}'>{$parenttaskid} - {$tasktitle} ({$duration_humanreadable})</a>";
			echo "<br />";
		}
		echo "<br />";
	}
	else
	{
		// no previous generation
	}
	
	echo "Current generation:<br />";
	$stacktrace_args = array();
	$stacktrace = tasks::getstacktrace($taskid, $taskinstanceid, $stacktrace_args);
	if (count($stacktrace) > 0)
	{
		foreach ($stacktrace as $frame)
		{
			if ($frame["isfound"] == false)
			{
				echo "ARCHIVED <a target='_blank' href='{$parent_url}'>{$parenttaskid} - {$tasktitle}</a>";
			}
			else
			{
				$parentcreationtime = $frame["createtime"];
				if ($parentcreationtime == "") { $parentcreationtime = strtotime('now'); }
				$rightnow = strtotime('now');
				$delta = $rightnow - $parentcreationtime;
				$duration_humanreadable = functions::getsecondstohumanreadable($delta);
				$parenttaskid = $frame["taskid"];
				$parenttaskinstanceid = $frame["taskinstanceid"];
				$parent_url = "{$homeurl}/?nxs=task-gui&page=taskinstancedetail&taskid={$parenttaskid}&taskinstanceid={$parenttaskinstanceid}";
				$tasktitle = tasks::gettasktitle($parenttaskid);
				$state = tasks::gettaskinstancestate($parenttaskid, $parenttaskinstanceid);
				
				$currenturl = functions::geturlcurrentpage();
				$travelback_url = $currenturl;
				$travelback_url = functions::addqueryparametertourl($travelback_url, "action", "reinitiateprevioustask_stage1", true, true);
				$travelback_url = functions::addqueryparametertourl($travelback_url, "backto_taskid", $parenttaskid, true, true);
				$travelback_url = functions::addqueryparametertourl($travelback_url, "backto_taskinstanceid", $parenttaskinstanceid, true, true);


				echo "{$state} <a target='_blank' href='{$parent_url}'>{$parenttaskid} - {$tasktitle} ({$duration_humanreadable})</a>";
				
				if (false)
				{
				}
				else if (in_array($taskinstancestate, array("ENDED")))
				{
					// nothing to do here
				}
				else if (in_array($taskinstancestate, array("CREATED", "STARTED")))
				{
					if ($taskid == 73)
					{
						echo " | ";
						echo "<a target='_blank' href='{$travelback_url}'>&#10554; reinitiate (will abort current)</a>";
					}
					else
					{
						echo " | ";
						echo "<a href='#' onclick='window.alert(\"Create an incident first and then re-initiate from there\"); return false;'>reinitiate</a>";
					}
				}
				else
				{
					echo " | unsupported state for re-initing; ($taskinstancestate); ";
				}
			}
			echo "<br />";
		}
	}
	
	// current
	if (true)
	{
		$currenturl = functions::geturlcurrentpage();
		
		$travelback_url = $currenturl;
		$travelback_url = functions::addqueryparametertourl($travelback_url, "action", "reinitiateprevioustask_stage1", true, true);
		$travelback_url = functions::addqueryparametertourl($travelback_url, "backto_taskid", $taskid, true, true);
		$travelback_url = functions::addqueryparametertourl($travelback_url, "backto_taskinstanceid", $taskinstanceid, true, true);
		
		// allow forking of the "current" instance
		$fork_url = $currenturl;
		$fork_url = functions::addqueryparametertourl($fork_url, "action", "forktaskinstance", true, true);
		
		//echo "This task instance has no parent<br />";
		$tasktitle = tasks::gettasktitle($taskid);
		$state = $instancemeta["state"];
		echo "{$state} {$taskid} - {$tasktitle} (current)";
		
		
		if (false)
		{
		}
		else if (in_array($taskinstancestate, array("ENDED")))
		{
			// nothing to do here
		}
		else
		{
			if ($taskid == 73)
			{
				echo " | ";
				echo "<a target='_blank' href='{$travelback_url}'>&#10554; reinitiate (will abort current)</a>";
				//echo " | ";
				//echo "<a target='_blank' onclick=\"return confirm('Proceed to Fork?');\" href='{$fork_url}'>fork (will abort current)</a>";
			}
			else
			{
				echo " | ";
				echo "<a href='#' onclick='window.alert(\"Create an incident first and then re-initiate from there\"); return false;'>reinitiate</a>";
			}
		}
		echo "<br />";
	}
	
	$created_tasks = $instancemeta["created_tasks"];
	if ($created_tasks != null && count($created_tasks) > 0)
	{
		echo "Offspring:<br />";
		foreach ($created_tasks as $created_task)
		{
			$offspring_taskid = $created_task["taskid"];
			$offspring_taskinstanceid = $created_task["taskinstanceid"];
			$offspring_tasktitle = tasks::gettasktitle($offspring_taskid);
			$offspringstate = tasks::gettaskinstancestate($offspring_taskid, $offspring_taskinstanceid);
			$offspring_url = "{$homeurl}/?nxs=task-gui&page=taskinstancedetail&taskid={$offspring_taskid}&taskinstanceid={$offspring_taskinstanceid}";
			
			$offspringtaskmeta = tasks::gettaskmeta($taskid);
			$offspring_processing_type = $offspringtaskmeta["processing_type"];
			
			echo "$offspringstate; <a target='_blank' href='{$offspring_url}'>{$offspring_taskid} - {$offspring_tasktitle} - {$offspring_taskinstanceid}</a> {$offspring_processing_type}<br />";
		}
	}
	else
	{
		echo "No offspring";
	}
	
	?>
	<br />
	<br />
	<?php
	nxs_gui_renderinputparametersforinstance($taskid, $taskinstanceid, $instancemeta);
	?>
	<br />
	<?php
	$notes = $instancemeta["notes"];
	if ($notes != null && count($notes) > 0)
	{
	?>
	<h2>Notes</h2>
	<?php
	foreach ($notes as $note)
	{
	$creationtime = $note["creationtime"];
	$creationtime_human = date("Ymd H:i:s", $creationtime);
	$text = $note["text"];
	$text = str_replace("\r\n", "<br />", $text);
	echo "<span style='background-color: yellow; color: black; padding: 2px; margin: 2px;'>{$creationtime_human} {$text}</span>";
	echo "<br />";
	}
	?>
	<?php
	}
	?>
	
	<?php
	
	$currentuserid = brk_tasks_gui_getuseridcurrentuser();
	
	$assignedtouser_id = $instancemeta["assignedtouser_id"];
	if ($assignedtouser_id != "" && $currentuserid != $assignedtouser_id)
	{
	$visible_for_logged_in_user = false;
	
	if ($_REQUEST["ignoreuserconflicts"] == "true")
	{
	$visible_for_logged_in_user = true;
	echo "<div style='padding: 10px; background-color: red; color: white;'>Please note you are NOT assigned to this task (some other employee is). Normally this content is hidden but since ignoreuserconflict=true is specified you see this</div>";
	}
	}
	else
	{
	$visible_for_logged_in_user = true;
	}
	
	if ($visible_for_logged_in_user)
	{
		if (false)
		{
		}
		else if ($taskinstancestate == "")
		{
			echo "<span style='background-color: red; color: white;'>Instance not found. Most likely its archived (/srv/metamodel/businessprocess.task.instances/archives/)</span><br />";
			$unarchive_url = "{$homeurl}/api/1/prod/create-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&businessprocesstaskid=275&taskid_to_unarchive={$taskid}&taskinstanceid_to_unarchive={$taskinstanceid}";
			echo "If its archived then create a task to unarchive the instance <a target='_blank' href='{$unarchive_url}'>here</a><br />";
		}
		else if ($taskinstancestate == "ENDED")
		{
			$currenturl = functions::geturlcurrentpage();
			
			?>
			<h2>Finished :)</h2><br />
			<br />
			
			<!-- allow to re-open the task instance -->
			<!--
			<form action='<?php echo $currenturl; ?>' method='POST' target='_blank' style='margin-left: 100px;'>
			<input type='hidden' name='nxs' value='task-gui' />
			<input type='hidden' name='taskid' value='<?php echo $taskid; ?>' />
			<input type='hidden' name='taskinstanceid' value='<?php echo $taskinstanceid; ?>' />
			<input type='hidden' name='page' value='taskinstancedetail' />
			<input type='hidden' name='action' value='reopeninstance' />
			<input type='submit' value='Re-open task instance' />
			</form>
			-->
			<?php
			if ($_REQUEST["showappliedrecipe"] == "true")
			{
				$appliedrecipewithoutlinks = $appliedrecipe;
				$appliedrecipewithoutlinks = str_replace("<a ", "<span class='linkremoved' ", $appliedrecipewithoutlinks);
				$appliedrecipewithoutlinks = str_replace("</a>", "</span>", $appliedrecipewithoutlinks);
				?>
				<style>
				.linkremoved { font-style: italic; background-color: #E4E4E4; }
				</style>
				<h2>Applied Recipe</h2><br />
				<?php echo $appliedrecipewithoutlinks; ?>
				<?php
			}
			else
			{
				$showappliedrecipe_url = functions::geturlcurrentpage();
				$showappliedrecipe_url = functions::addqueryparametertourl($showappliedrecipe_url, "showappliedrecipe", "true", true, true);
				?>
				<a href='<?php echo $showappliedrecipe_url; ?>'>Show applied recipe</a>
				<?php
				
			}
		}
		else if ($taskinstancestate == "SLEEPING")
		{
			?>
			This instance is Sleeping<br />
			To wake the instance end all the offspring instances<br />
			todo; render offspring instances here
			<?php
		}
		else if ($taskinstancestate == "ABORTED")
		{
			$abort_reason = $instancemeta["abort_reason"];
			$abort_note = $instancemeta["abort_note"];
			$abortedtime = $instancemeta["abortedtime"];
			$delta = time() - $abortedtime;
			$aborted_time_since = functions::getsecondstohumanreadable($delta);
			$abortedtime_html = date("Ymd H:i:s", $abortedtime);
			$abortedbyip = $instancemeta["abortedbyip"];
			?>
			<h2>Aborted :S</h2><br />
			<div style='background-color: orange; margin: 10px; padding: 10px;'>
			reason : <?php echo $abort_reason; ?><br />
			note : <?php echo $abort_note; ?><br />
			aborted time: <?php echo $abortedtime_html; ?> (by <?php echo $abortedbyip; ?>, <?php echo $aborted_time_since; ?>)<br />
			</div>
			
			<?php
			if ($_REQUEST["showappliedrecipe"] == "true")
			{
				$appliedrecipewithoutlinks = $appliedrecipe;
				$appliedrecipewithoutlinks = str_replace("<a ", "<span class='linkremoved' ", $appliedrecipewithoutlinks);
				$appliedrecipewithoutlinks = str_replace("</a>", "</span>", $appliedrecipewithoutlinks);
				?>
				<style>
				.linkremoved { font-style: italic; background-color: #E4E4E4; }
				</style>
				<h2>Applied Recipe</h2><br />
				<?php echo $appliedrecipewithoutlinks; ?>
				<?php
			}
			else
			{
				$showappliedrecipe_url = functions::geturlcurrentpage();
				$showappliedrecipe_url = functions::addqueryparametertourl($showappliedrecipe_url, "showappliedrecipe", "true", true, true);
				?>
				<a href='<?php echo $showappliedrecipe_url; ?>'>Show applied recipe</a>
				<?php
			}
		}
		else if ($taskinstancestate == "STARTED" || ($taskinstancestate == "CREATED" && $execution_pointers_support == "v1"))
		{
			$availability = "NOTFOUND"; // tasks::getavail brk_tasks_getworkflowsavailabilitystate_for_task($taskid);
			if (false)
			{
			}
			else if ($availability == "AVAILABLE")
			{
				$run_workflows_url = "{$homeurl}/api/1/prod/run-workflows-for-taskinstance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
				echo "<span style='padding: 2px; margin: 2px; background-color: #0f0; color: black;'>to run the workflows manually, <a target='_blank' href='{$run_workflows_url}'>click here</a></span><br /><br />";
			}
			else if ($availability == "BROKEN")
			{
				echo "<span style='padding: 2px; margin: 2px; background-color: #f00; color: white;'>workflows are broken</span><br /><br />";
			}
			else if ($availability == "NOTFOUND")
			{
				echo "<span style='padding: 2px; margin: 2px; background-color: #ddd; color: white;'>no workflows found</span><br /><br />";
			}
			else
			{
				echo "<span style='padding: 2px; margin: 2px; background-color: #ddd; color: white;'>unknown workflows availability state</span><br /><br />";
			}
			
			?>
			<h2>Recipe</h2><br />
			<?php 
			if ($execution_pointers_support == "v1")
			{
				if (!$isexecutionpointerlegit )
				{
					$fixtaskinstance_taskid = 594;
					$fixurl = "{$homeurl}/?nxs=task-gui&page=createnewtaskinstanceform&newtaskid={$fixtaskinstance_taskid}&createdby_taskid={$taskid}&createdby_taskinstanceid={$taskinstanceid}&taskid_to_fix={$taskid}&taskinstanceid_to_fix={$taskinstanceid}";
					?>
					<div style='padding: 5px; margin: 5px; background-color: red; color: white;'>
						Error: invalid execution pointer <a href='<?php echo $fixurl; ?>'>Resolve issue</a>
					</div>
					<?php
				}
			}
			if ($appliedrecipe == "")
			{
				echo "No .txt content found (most likely the .txt file is not yet created)";
			}
			else
			{
				echo $appliedrecipe;
			}
			?>
			<?php
		}
		else if ($taskinstancestate == "CREATED")
		{
			/*
			$workflows_availability_state = brk_tasks_get_workflows_availability_state_for_task($taskid);
			if ($workflows_availability_state == "PRODUCTION" || $workflows_availability_state == "DEVELOPMENT")
			{
				$run_workflows_url = "{$homeurl}/api/1/prod/run-workflows-for-taskinstance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
				echo "<span style='padding: 2px; margin: 2px; background-color: #0f0; color: black;'>to run the workflow manually, <a target='_blank' href='{$run_workflows_url}'>click here</a></span><br /><br />";
			}
			*/
			
			// before user is able to see the steps of -this- task, first it needs to be started
			echo "This task is created, but not yet started. In order to view the steps, start it first!<br /><br />";
			
			$currenturl = functions::geturlcurrentpage();
			?>
			<form action='<?php echo $currenturl; ?>' method='POST' style='margin-left: 100px;'>
			<input type='hidden' name='nxs' value='task-gui' />
			<input type='hidden' name='taskid' value='<?php echo $taskid; ?>' />
			<input type='hidden' name='taskinstanceid' value='<?php echo $taskinstanceid; ?>' />
			<input type='hidden' name='page' value='taskinstancedetail' />
			<input type='hidden' name='action' value='starttaskinstance' />
			<input type='submit' value='Start task instance' />
			</form>
			<?php
			
			if ($_REQUEST["autostart"] == "true")
			{
				echo $appliedrecipe;
			}
		}
		else
		{
			echo "Unexpected state; $taskinstancestate<br />";
		}
	}
	else
	{
		$currenturl = functions::geturlcurrentpage();
		$ignorewarning_url = $currenturl;
		$ignorewarning_url = functions::addqueryparametertourl($ignorewarning_url, "ignoreuserconflicts", "true", true, true);
		?>
		<h2>Recipe</h2><br />
		Task instance is assigned to employee id <?php echo $assignedtouser_id; ?>. You are employee id <?php echo $currentuserid; ?>.
		To avoid having multiple employees work we have disabled the steps. To ignore this warning <a href='<?php echo $ignorewarning_url; ?>'>click here</a>
		<br />
		<?php
	}
	
	//$events_result = tasks::geteve nxs_tracking_getevents($taskid, $taskinstanceid);
	//$events = $events_result["events"];
	$events = array();
	$events_count = count($events);
	?>
	<div class='events' style='background-color: #eee;'>
	<h2><?php echo $events_count; ?> Tracked Events</h2>
	<?php
	foreach ($events as $event)
	{
		var_dump($event);
		echo $event . "<br />";
	}
	?>
	</div>
	<div>
	<h2>Attachments for task <?php echo $taskid; ?></h2>
	<?php
	//$attachmentids = tasks::brk_tasks_gettaskrecipe_attachments($taskid, $taskinstanceid);
	$attachmentids = array();
	if (count($attachmentids) > 0)
	{
		foreach ($attachmentids as $attachmentid)
		{
			echo "attachment $attachmentid<br />";
		}
	}
	else
	{
		echo "No attachments found";
	}
	?>
	</div>
	</div>
	<div style='margin-top: 20px;margin-bottom: 200px;'>:)</div>
	<?php
	die();
}