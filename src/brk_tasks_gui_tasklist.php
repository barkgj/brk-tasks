<?php

use barkgj\functions;
use barkgj\tasks\tasks;

$loader = require_once __DIR__ . '/vendor/autoload.php';

// todo; fix autoloader of composer so this is not needed...
require_once __DIR__ . '/vendor/barkgj/datasink-library/src/datasink-entity.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/functions.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/filesystem.php';
require_once __DIR__ . '/vendor/barkgj/tasks-library/src/tasks.php';

function brk_tasks_gui_tasklist()
{
	$homeurl = functions::geturlhome();

	if ($_REQUEST["action"] == "createnewtask")
	{
		$recursatregularinterval = $_REQUEST["recursatregularinterval"];
		$title = $_REQUEST["title"];
		$requirements = $_REQUEST["requirements"];
		//
		$newtaskid = "1";
		$action_url = "{$homeurl}api/1/prod/create-task-instance/?brk=task-api&brk_json_output_format=prettyprint&taskid={$newtaskid}&title={$title}&requirements={$requirements}&recursatregularinterval={$recursatregularinterval}";
		
		$action_string = file_get_contents($action_url);
		$action_result = json_decode($action_string, true);
		if ($action_result["result"] != "OK") { functions::throw_nack("unable to create task instance; $action_url"); }
		
		$newlycreatedtaskinstanceid = $action_result["taskinstanceid"];
		
		$start_instance_url = "{$homeurl}?nxs=task-gui&page=taskinstancedetail&taskid={$newtaskid}&taskinstanceid={$newlycreatedtaskinstanceid}";
		echo "task instance created<br />";
		echo "<a href='{$start_instance_url}'>click here to start this new instance</a>";
		
		die();
	}
	
	brk_tasks_gui_render_head();
	brk_tasks_gui_rendernavigation();
	
	echo "<h1>list o tasks (<a target='_blank' href='todo'>todo add link to datasink entity gui</a>)</h1>";
	$newtaskurl = $homeurl;
	$newtaskurl = functions::addqueryparametertourl($newtaskurl, "page", "createnewtaskinstanceform");
	$newtaskurl = functions::addqueryparametertourl($newtaskurl, "newtaskid", "1");
	?>
	<div>
		<a target='_blank' href='<?php echo $newtaskurl; ?>'>Add task</a>
	</div>
	<?php
	// fetch all tasks
	$tasks = tasks::gettasks();
	
	$rowindex = -1;
	
	?>
	<style>
		tr:hover { background-color: white !important; }
	</style>
	<?php
	
	echo "<table class='table-oddeven'>";
	foreach ($tasks as $taskid => $entry)
	{	
		echo "<tr>";
		$title = $entry["title"];
		$title_shortened = $title;	// do_shortcode("[nxs_string ops='ellipsify' length='100']{$title}[/nxs_string]");
		$handle_prio = $entry["handle_prio"];
		$instances_url = "{$homeurl}?nxs=task-gui&page=taskinstances&taskid={$taskid}";
		$abstractsteps_url = "{$homeurl}?nxs=task-gui&page=viewabstracttask&taskid={$taskid}";
		$abstractsteps_txt_url = "{$homeurl}?nxs=task-gui&page=viewabstracttaskraw&taskid={$taskid}";
		$rfc_url = "{$homeurl}?nxs=task-gui&page=createrfcinternal&taskid={$taskid}";
		$newinstance_url = "{$homeurl}?nxs=task-gui&page=createnewtaskinstanceform&newtaskid={$taskid}";
		echo "<td>$taskid</td>";
		echo "<td><span title='{$title}'>{$title_shortened}</span></td>";
		echo "<td>$handle_prio</td>";
		echo "<td><a target='_blank' href='{$instances_url}'>instances</a></td>";
		echo "<td><a target='_blank' href='{$abstractsteps_url}'>abstract steps</a></td>";
		echo "<td><a target='_blank' href='{$abstractsteps_txt_url}'>abstract steps as txt</a></td>";
		echo "<td><a target='_blank' href='{$rfc_url}'>create rfc</a></td>";
		echo "<td><a target='_blank' href='{$newinstance_url}'>create instance</a></td>";
		/*
		echo "<td class='toggle_container'>";
		echo "<a target='_blank' href='#' onclick=\"jQuery(this).closest('.toggle_container').find('.togglable').toggle(); return false;\">create instance</a>";
		echo "<div class='togglable' style='display: none'>";
		
		$happyflow_behaviours = array();
		$processingtype = brk_tasks_getprocessingtype($taskid);
		if ($processingtype != "automated")
		{
		$happyflow_behaviours[] = "start_child_task_instance";
		$happyflow_behaviours[] = "redirect_to_child_instance";
		}
		$happyflow_behaviour = implode(";", $happyflow_behaviours);
		
		echo do_shortcode("[nxs_p001_task_instruction type='create_task_instance' create_taskid={$taskid} linkparenttochild=false allowdaemonchild=true happyflow_behaviour='{$happyflow_behaviour}']");
		
		// 
		echo "</div>";
		echo "</td>";
		*/
		echo "</tr>";
	}
	echo "</table>";
	
	echo "<div style='padding-bottom: 50px;'>:)</div>";
	
	die();
}