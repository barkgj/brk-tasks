<?php

use barkgj\functions;
use barkgj\tasks\tasks;

$loader = require_once __DIR__ . '/vendor/autoload.php';

// todo; fix autoloader of composer so this is not needed...
require_once __DIR__ . '/vendor/barkgj/datasink-library/src/datasink-entity.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/functions.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/filesystem.php';
require_once __DIR__ . '/vendor/barkgj/tasks-library/src/tasks.php';

function brk_tasks_gui_createnewtaskinstanceform()
{	
	$homeurl = functions::geturlhome();
	$newtaskid = $_REQUEST["newtaskid"];
	if ($newtaskid == "") { functions::throw_nack("newtaskid not set?"); }
	if (!tasks::taskexists($newtaskid)) { functions::throw_nack("task not found? $newtaskid"); }

	$createdby_taskid = $_REQUEST["createdby_taskid"];
	$createdby_taskinstanceid = $_REQUEST["createdby_taskinstanceid"];

	//if ($createdby_taskid == "") { functions::throw_nack("createdby_taskid not set?"); }
	//if ($createdby_taskinstanceid == "") { functions::throw_nack("createdby_taskinstanceid not set?"); }
	
	$meta = tasks::getreflectionmeta($newtaskid, "");

	$required_fields = $meta["required_fields"];
	
	// handle possible actions
	$action = $_REQUEST["action"];
	if ($action == "createinstance")
	{		
		$assigned_to = "";
		$mail_assignee = false;
		$stateparameters = array();
		foreach ($required_fields as $required_field)
		{
			$stateparameters[$required_field] = $_REQUEST[$required_field];
		}
		
		$delegated_result = tasks::createtaskinstance($newtaskid, $assigned_to, $createdby_taskid, $createdby_taskinstanceid, $mail_assignee, $stateparameters);
		
		if ($delegated_result["result"] != "OK") { functions::throw_nack("error creating task instance;" . json_encode($delegated_result)); }
		
		$taskid = $delegated_result["taskid"];
		$taskinstanceid = $delegated_result["taskinstanceid"];
		
		// determine how to continue from here; either redirect to the new instance (makes most sense)
		// or do something else?
		$next_url = "{$homeurl}?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
		wp_redirect($next_url, 301);
		exit;
	}
	

	$task_title = ucwords(tasks::gettasktitle($newtaskid));
	
	$currenturl = "{$homeurl}?nxs=task-gui&page=createnewtaskinstanceform";
	
	brk_tasks_gui_render_head();
	brk_tasks_gui_rendernavigation();
	?>
	<h1>New task instance - Form - <?php echo $task_title; ?></h1>
	<br />
	Required fields:<br /><br />
	<form id='newtaskinstanceform' action='<?php echo $currenturl; ?>' method='POST' style='margin-left: 100px;'>
		<input type='submit' value='Create task instance' /><br /><br />
		<input type='hidden' name='nxs' value='task-gui' />
		<input type='hidden' name='page' value='createnewtaskinstanceform' />
		<input type='hidden' name='action' value='createinstance' />
		
		<?php 
		foreach ($required_fields as $required_field)
		{
			$fieldvalue = $_REQUEST[$required_field];
			$escaped_fieldvalue = $fieldvalue;
			$escaped_fieldvalue = str_replace("\"", "&quot;", $escaped_fieldvalue);
			echo "<label for='{$required_field}'>{$required_field}</label><br />";
			echo "<input id='{$required_field}' required name='{$required_field}' type='input' value=\"{$escaped_fieldvalue}\" style='width:100%' /><br /><br />";
		}
		
		echo "<input type='hidden' name='newtaskid' value='{$newtaskid}' />";
		echo "<input type='hidden' name='createdby_taskid' value='{$createdby_taskid}' />";
		echo "<input type='hidden' name='createdby_taskinstanceid' value='{$createdby_taskinstanceid}' />";
		?>
		<input type='submit' value='Create task instance' />
		<div style='height: 100px;'>&nbsp;</div>
	</form>
	<?php
	
	// attempt to auto submit
	?>
	<script>
		//jQuery('#newtaskinstanceform').submit();
	</script>
	<?php
	
	die();
}