<?php

function brk_tasks_gui_taskinstancedeletefield()
{
	$taskid = $_REQUEST["taskid"];
	if ($taskid == "") { functions::throw_nack("taskid not specified"); }
	
	$taskinstanceid = $_REQUEST["taskinstanceid"];
	if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not specified"); }
	
	$field = $_REQUEST["field"];
	if ($field == "") { functions::throw_nack("field not specified"); }
	if (in_array($field, array("", "nxs", "page", "taskid", "taskinstanceid", "field"))) { functions::throw_nack("unsupported field; $field"); }
	
	$action = $_REQUEST["action"];
	if ($action == "")
	{
		// ok, its a get request
	}
	else if ($action == "deletefieldvalue")
	{
		tasks::deleteinput_for_taskinstance($taskid, $taskinstanceid, $field);
		
		echo "deleted value for field $field to $val";
		$nexturl = "https://tasks.bestwebsitetemplates.net/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
		echo "<a href='{$nexturl}'>click here to proceed with the task instance</a>";
		die();
	}
	else
	{
		echo "sorry, unsupported action; $action";
		die();
	}
	
	$stateparameters = tasks::gettaskinstancestateparameters($taskid, $taskinstanceid);
	$val = $stateparameters[$field];
	
	$currenturl = functions::geturlcurrentpage();
	?>
	<form action='<?php echo $currenturl; ?>' method='POST' target='_blank' style='margin-left: 100px;'>
	<input type='hidden' name='nxs' value='task-gui' />
	<input type='hidden' name='taskid' value='<?php echo $taskid; ?>' />
	<input type='hidden' name='taskinstanceid' value='<?php echo $taskinstanceid; ?>' />
	
	<input type='hidden' name='page' value='taskinstancedeletefield' />
	<input type='hidden' name='action' value='deletefieldvalue' />
	<input type='hidden' name='field' value='<?php echo $field; ?>' />
	field: <?php echo $field; ?><br />
	
	<input type='submit' value='Delete!' />
	<?php
	?>
	
	<?php
	echo "<br />:)";
	die();
}