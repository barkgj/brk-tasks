<?php

use barkgj\functions;
use barkgj\tasks\tasks;

$loader = require_once __DIR__ . '/vendor/autoload.php';

// todo; fix autoloader of composer so this is not needed...
require_once __DIR__ . '/vendor/barkgj/datasink-library/src/datasink-entity.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/functions.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/filesystem.php';
require_once __DIR__ . '/vendor/barkgj/tasks-library/src/tasks.php';

if (false)
{
	// fool the IDE in thinking these functions do exist
	function wp_redirect()
	{

	}
}

function brk_tasks_gui_taskinstancelist()
{
	$homeurl = functions::geturlhome();

	global $nxs_g_modelmanager;
	$taskid = $_REQUEST["taskid"];
	
	$bulk_action = $_REQUEST["bulk_action"];
	if ($bulk_action != "")
	{
		echo "bulk action...";
		$taskinstanceids = $_REQUEST["taskinstanceids"];
		if (count($taskinstanceids) == 0)
		{
			echo "No task instances selected?!";
			die();
		}
		
		//
		$start= time();
		
		if (false)
		{
			//
		}
		else if ($bulk_action == "runtaskinstances")
		{
			$delegated_result = tasks::execute_batch_headless_from_current_execution_pointer($taskid, $taskinstanceids);
		}
		else if ($bulk_action == "restarttaskinstances")
		{
			functions::throw_nack("to be implemented");
			/*
			$delegated_result_1 = tasks::reverttorequiredinputparameters($taskid, $taskinstanceids);
			$delegated_result_2 = tasks::batch_resetexecutionpointers($taskid, $taskinstanceids);
			$delegated_result_3 = tasks::execute_batch_headless_from_current_execution_pointer($taskid, $taskinstanceids);
			
			wp_redirect("{$homeurl}/?nxs=task-gui&page=taskinstancelist&taskid={$taskid}");
			die();
			*/
		}
		else if ($bulk_action == "resetexecutionpointers")
		{
			tasks::batch_resetexecutionpointers($taskid, $taskinstanceids);
			
			wp_redirect("{$homeurl}/?nxs=task-gui&page=taskinstancelist&taskid={$taskid}");
			die();
		}
		else if ($bulk_action == "resetnonerequiredinputparameters")
		{
			tasks::batch_reverttorequiredinputparameters($taskid, $taskinstanceids);
			
			wp_redirect("{$homeurl}/?nxs=task-gui&page=taskinstancelist&taskid={$taskid}");
			die();
		}
		else
		{
			echo "unsupported bulk_action";
			die();
		}
		
		$end = time();
		
		$deltasec = $end - $start;
		$secs_per_instance = $deltasec / count($taskinstanceids);
		
		echo "this took $deltasec secs for all instances<br />";
		echo "that is $secs_per_instance secs per instance<br />";

		echo "processing finished<br />";
		echo "Result:<br />";
		var_dump($delegated_result);
		echo "<br />";
		
		echo "Byebye";
		die();
	}
	
	$instances = tasks::gettaskinstances($taskid);
	$count = count($instances);
	
	$title = tasks::gettasktitle($taskid);
	
	$processingtype = tasks::getprocessingtype($taskid);
	
	$taskmeta = tasks::gettaskmeta($taskid);
	
	$execution_pointer_support = $taskmeta["execution_pointer_support"];
		
	ob_start();
	
	brk_tasks_gui_rendernavigation();
	echo "<h1>Open task instances for {$taskid} - {$title} ({$processingtype})</h1>";
	
	$currenturl = functions::geturlcurrentpage();
	$bulkexporturl = $currenturl;
	$bulkexporturl = functions::addqueryparametertourl($bulkexporturl, "bulkaction", "exportcsv", true, true);
	echo "<a href='{$bulkexporturl}' class='nxsbutton'>Export bulk</a><br />";
	$newinstanceurl = "{$homeurl}/?nxs=task-gui&page=createnewtaskinstanceform&newtaskid={$taskid}";
	echo "<a href='{$newinstanceurl}' class='nxsbutton'>Create new instance (form)</a><br />";
	$newbulktaskinstanceform_url = "{$homeurl}/?nxs=task-gui&page=createnewbulktaskinstanceform&newtaskid={$taskid}";
	echo "<a href='{$newbulktaskinstanceform_url}' class='nxsbutton'>Create new instances (bulk)</a><br />";
	
	echo "<a target='_blank' href='{$homeurl}/?nxs=task-gui&page=taskinstances&taskid={$taskid}'>Click here to see ALL instances for this task</a><br />";
	
	// ***
	//$availability = tasks::getworkflowsavailabilitystate_for_task($taskid);
	
	$execution_pointers_support = "-";

	if ($execution_pointers_support == "v1")
	{
		$url = "";
		?>
		
		<script>
			// script for shift click multi select
			$(document).ready
			(
				function() 
				{
					var $chkboxes = $('.nxs-ti');
					var lastChecked = null;
				
					$chkboxes.click
					(
						function(e) 
						{
							console.log("click start");
							
							nxs_js_redraw();
								
							if (!lastChecked) 
							{
								lastChecked = this;
								return;
							}
				
							if (e.shiftKey) 
							{
								var start = $chkboxes.index(this);
								var end = $chkboxes.index(lastChecked);
					
								$chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked);
							}
					
							lastChecked = this;
							
							nxs_js_redraw();
							
							console.log("click end");
						}
					);
				}
			);
		</script>
		
		<script>
			function nxs_js_redraw()
			{
				var count_enabled = $('.nxs-ti:checked').size();
				console.log(count_enabled);
				
				$('#bulk_counter').text(count_enabled);
				if (count_enabled == 0)
				{
					$(".nxs-bulkselect-container").hide();
				}
				else
				{
					$(".nxs-bulkselect-container").show();
				}
			}
			
			function nxs_js_toggle_all()
			{
				$(".nxs-ti").prop("checked", true);
				nxs_js_redraw();
			}
			function nxs_js_toggle_none()
			{
				$(".nxs-ti").prop("checked", false);
				nxs_js_redraw();
			}
		</script>
		
		<!-- begin of bulk form -->
		
		<div>
			Bulk<br />
			<a href='#' onclick='nxs_js_toggle_all();return false;'>All</a>
			<a href='#' onclick='nxs_js_toggle_none();return false;'>None</a>
		</div>		
		
		<form method="POST">
			<input type='hidden' name='nxs' value='task-gui' />
			<input type='hidden' name='page' value='taskinstancelist' />
			<input type='hidden' name='taskid' value='<?php echo $taskid; ?>' />

			<div class='nxs-bulkselect-container' style='background-color: red; padding: 5px; display:none;'>
				<div style='margin: 5px;padding:5px; background-color: white;'>
					Begin batch execute (execution pointers)
					Selected: <label id='bulk_counter'>0</label><br />
					<br />
				</div>
			
				Bulk action <select name='bulk_action' required>
					<option value=''></option>
					<option value='runtaskinstances'>Run selected task instances</option>
					<option value='restarttaskinstances'>Restart selected task instances</option>
					<option value='resetexecutionpointers'>Reset execution pointer task instances</option>
					<option value='resetnonerequiredinputparameters'>Reset none-required input parameters of task instances</option>
				</select>
				
				<input type='submit' value='Go' />
				<br />
			</div>
		<?php
	}
	
	/*
	if (false)
	{
	}
	else if ($availability == "AVAILABLE")
	{
		$bootstrap_batch_url = "{$homeurl}/?nxs=task-gui&page=bootstrapnewbatch";
		$bootstrap_batch_url = nxs_addqueryparametertourl_v2($bootstrap_batch_url, "taskid", $taskid, true, true);
		echo "<span style='background-color: #0F0; color: black;'>Batch (Workflows) processing for task instances is available; </span> <a target='_blank' href='{$bootstrap_batch_url}'>Click here to create new batch</a><br /><br />";
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
	*/
	
	$distinctcolumns = array();
	
	// echo "Instances found: " . count($instances) . "<br />";
	
	echo "<style>";
	echo ".oddeven tr:nth-child(even) {background: #F1F1FF; }";
	echo "</style>";
	
	echo "<table class='oddeven'>";
	echo "<tr><td>Days ago</td><td>State</td><td>ID</td><td>Assigned to</td><td>Actions</td><td>Preview</td></tr>";
	
	foreach ($instances as $instanceid => $instancemeta)
	{
		foreach ($instancemeta as $key => $val)
		{
			if ($key == "inputparameters")
			{
				foreach ($val as $ip_key => $ip_val)
				{
					if (!in_array($ip_key, $distinctcolumns))
					{
						$distinctcolumns[] = $ip_key;
					}
				}
			}
			
			if (!in_array($key, $distinctcolumns))
			{
				$distinctcolumns[] = $key;
			}
		}
		
		$state = $instancemeta["state"];
		$createtime = $instancemeta["createtime"];
		if ($createtime == "") { $createtime = strtotime('now') - 60*60*24*30*3; }
		
		$rightnow = strtotime('now');
		$delta = $rightnow - $createtime;
		$duration_in_days = functions::getsecondstohumanreadable($delta);
		
		$inputparameters = $instancemeta["inputparameters"];
		
		$countbystatus[$state]++;
		
		$include = ($state == "CREATED" || $state == "STARTED" || $state == "SLEEPING" || false);
		
		$instances[$instanceid]["taskgui_include"] = $include;
		
		if ($include)
		{
			$includeindex++;
			
			$instance_url = "{$homeurl}/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$instanceid}";
			
			$action_list = array();
			
			if ($state == "CREATED")
			{
				$autostart_instance_url = "{$homeurl}/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$instanceid}&autostart=true";
				$action_list[]= "<a target='_blank' href='{$autostart_instance_url}'>Start</a>";
			}
			
			$action_list[]= "<a target='_blank' href='{$instance_url}'>View</a>";
			
			if (false)
			{
			}
			else if ($availability == "AVAILABLE")
			{
				$workflow_url = "{$homeurl}/api/1/prod/run-workflows-for-taskinstance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$instanceid}";
				$action_list[]= "<a target='_blank' href='{$workflow_url}'>Run&nbsp;workflow&nbsp;manually</a>";
			}
			else
			{
			}
			
			//			
			$debug_instance_url = "{$homeurl}/?nxs=task-gui&page=taskinstancedebug&taskid={$taskid}&taskinstanceid={$instanceid}";
			$action_list[]= "<a target='_blank' href='{$debug_instance_url}'>Debug</a>";
			
			//
			$actions = implode("&nbsp;|&nbsp;", $action_list);
			
			if ($taskid == 74)
			{
				$taskid_to_edit = $inputparameters["taskid_to_edit"];
				$requirements = $inputparameters["requirements"];
				$preview = "{$taskid_to_edit} - {$requirements}";
				// $preview = json_encode($inputparameters);
			}
			else
			{
				// clone
				$cloned = $inputparameters;
				$ignorekeys = array("nxs", "nxs_json_output_format", "businessprocesstaskid");
				foreach ($ignorekeys as $ignorekey)
				{
					unset($cloned[$ignorekey]);
				}
				$preview = htmlentities(json_encode($cloned));
			}
			
			$assignedtouser_id = $instancemeta["assignedtouser_id"];
			$currentuserid = brk_tasks_gui_getuseridcurrentuser();
			
			$assignedto_html = "";
			if (false)
			{
			}
			else if ($assignedtouser_id == "")
			{
				$assignedto_html = "unassigned";
			}
			else if ($assignedtouser_id == $currentuserid)
			{
				$assignedto_html = "assigned to you";
			}
			else
			{
				$assignedto_html = "assigned to someone else";
			}
			
			echo "<tr>";
			echo "<td><input class='nxs-ti nxs-selectable' type='checkbox' name='taskinstanceids[]' value='{$instanceid}' /></td>";
			echo "<td>{$duration_in_days}</td>";
			echo "<td>{$state}</td>";
			echo "<td>{$instanceid}</td>";
			echo "<td>{$assignedto_html}</td>";
			echo "<td>{$actions}</td>";
			echo "<td>{$preview}</td>";
			echo "</tr>";
		}
	}
	
	echo "</table>";
	
	if ($execution_pointers_support == "v1")
	{
		$url = "";
		echo "</form>";	// end of bulk form
		echo "<div style='background-color: red; padding: 5px;'><div style='margin: 5px;padding:5px; background-color: white;'>End of Batch execute (execution pointers)</div></div>";
	}

	echo "<br />:)";
	
	$output = ob_get_contents();
	
	ob_end_clean();
	
	if ($_REQUEST["bulkaction"] == "exportcsv")
	{
		$title = tasks::gettaskstitle($taskid);
		$rightnow = time();
		$outputname = "{$taskid}_{$title}_{$rightnow}.csv";
		$output = fopen("php://output",'w') or die("Can't open php://output");
		header("Content-Type:application/csv"); 
		header("Content-Disposition:attachment;filename={$outputname}"); 
		fputcsv($output, $distinctcolumns);
		
		
		foreach ($instances as $instanceid => $instancemeta)
		{
			$include = $instancemeta["taskgui_include"];
			if ($include)
			{
				$csvrow = array();
				foreach ($distinctcolumns as $distinctcolumn)
				{
					if ($distinctcolumn == "inputparameters")
					{
						$val = $instancemeta[$distinctcolumn];
						foreach ($val as $ip_key => $ip_val)
						{
						$csvrow[$ip_key] = $ip_val;
						}
						
						// note; we don't include the inputparameters themselves on purpose!
					}
					else
					{
					$csvrow[$distinctcolumn] = $instancemeta[$distinctcolumn];
					}
				}
				fputcsv($output, $csvrow);
			}
		}
		
		//echo "so far :)";
		
		fclose($output);
		
		die();
	}
	
	echo $output;
	
	
	die();
}
