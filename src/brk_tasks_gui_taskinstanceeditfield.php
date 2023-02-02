<?php
function brk_tasks_gui_taskinstanceeditfield()
{
	$taskid = $_REQUEST["taskid"];
	if ($taskid == "") { nxs_webmethod_return_nack("taskid not specified"); }
	
	$taskinstanceid = $_REQUEST["taskinstanceid"];
	if ($taskinstanceid == "") { nxs_webmethod_return_nack("taskinstanceid not specified"); }
	
	$field = $_REQUEST["field"];
	if ($field == "") { nxs_webmethod_return_nack("field not specified"); }
	if (in_array($field, array("", "nxs", "page", "taskid", "taskinstanceid", "field"))) { nxs_webmethod_return_nack("unsupported field; $field"); }
	
	if ($field == "_new")
	{
	
	}
	
	$action = $_REQUEST["action"];
	if ($action == "")
	{
	// ok, its a get request
	}
	else if ($action == "updatefieldvalue")
	{
		$val = $_REQUEST["val"];
		brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, $field, $val);
		echo "updated value for field $field to $val<br />";
		$nexturl = "https://global.nexusthemes.com/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
		
		echo "<a href='{$nexturl}'>you will be redfirected to {$nexturl}</a>";
		wp_redirect($nexturl);
		die();
	}
	else
	{
		echo "sorry, unsupported action; $action";
		die();
	}
	
	$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
	//var_dump($inputparameters);
	$val = $inputparameters[$field];
	
	$currenturl = nxs_geturlcurrentpage();
	?>
	<form action='<?php echo $currenturl; ?>' method='POST' style='margin-left: 100px;'>
	<input type='hidden' name='nxs' value='task-gui' />
	<input type='hidden' name='taskid' value='<?php echo $taskid; ?>' />
	<input type='hidden' name='taskinstanceid' value='<?php echo $taskinstanceid; ?>' />
	
	<input type='hidden' name='page' value='taskinstanceeditfield' />
	<input type='hidden' name='action' value='updatefieldvalue' />
	<input type='hidden' name='field' value='<?php echo $field; ?>' />
	field: <?php echo $field; ?><br />
	value: <input type='text' style='width: 100%;' name='val' value='<?php echo $val; ?>' />
	<input type='submit' value='Update!' />
	<?php
	?>
	
	<?php
	echo "<br />:)";
	die();
}