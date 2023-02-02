<?php

function brk_tasks_gui_searchtaskinstances()
{
	$args_json = $_REQUEST["args_json"];
	if ($args_json == "")
	{
	echo "no args_json specified";
	?>
	example:<br />
	{"if_this":{"type":"true_if_each_subcondition_is_true","subconditions":[{"type":"true_if_task_has_required_taskid","required_taskid":"1"},{"type":"true_if_inputparameter_has_required_value_for_key","key":"licenseid","required_value":"foo"}]}}
	
	<br />
	<br />
	
	<a target='_blank' href='https://global.nexusthemes.com/?nxs=task-gui&page=searchtaskinstances&args_json={%22if_this%22:{%22type%22:%22true_if_each_subcondition_is_true%22,%22subconditions%22:[{%22type%22:%22true_if_inputparameter_has_required_value_for_key%22,%22key%22:%22licenseid%22,%22required_value%22:%22VALUE_OF_LICENSEID%22}]}}'>To search for taskinstances with a specific licenseid</a><br />
	<a target='_blank' href='https://global.nexusthemes.com/?nxs=task-gui&page=searchtaskinstances&args_json={%22if_this%22:{%22type%22:%22true_if_each_subcondition_is_true%22,%22subconditions%22:[{%22type%22:%22true_if_inputparameter_has_required_value_for_key%22,%22key%22:%22renew_licenseid%22,%22required_value%22:%22VALUE_OF_LICENSEID%22}]}}'>To search for taskinstances with a specific renew_licenseid</a><br />
	<a target='_blank' href='https://global.nexusthemes.com/?nxs=task-gui&page=searchtaskinstances&args_json={%22if_this%22:{%22type%22:%22true_if_each_subcondition_is_true%22,%22subconditions%22:[{%22type%22:%22true_if_inputparameter_has_required_value_for_key%22,%22key%22:%22domain%22,%22required_value%22:%22VALUE_OF_DOMAIN%22}]}}'>To search for taskinstances with a specific domain</a><br />
	<a target='_blank' href='https://global.nexusthemes.com/?nxs=task-gui&page=searchtaskinstances&args_json={%22if_this%22:{%22type%22:%22true_if_each_subcondition_is_true%22,%22subconditions%22:[{%22type%22:%22true_if_inputparameter_has_required_value_for_key%22,%22key%22:%22orderid%22,%22required_value%22:%22VALUE_OF_ORDERID%22}]}}'>To search for taskinstances with a specific orderid</a><br />
	<a target='_blank' href='https://global.nexusthemes.com/?nxs=task-gui&page=searchtaskinstances&args_json={%22if_this%22:{%22type%22:%22true_if_each_subcondition_is_true%22,%22subconditions%22:[{%22type%22:%22true_if_inputparameter_has_required_value_for_key%22,%22key%22:%22hostname%22,%22required_value%22:%22VALUE_OF_HOSTNAME%22}]}}'>To search for taskinstances with a specific hostname</a><br />
	<a target='_blank' href='https://global.nexusthemes.com/?nxs=task-gui&page=searchtaskinstances&args_json={%22if_this%22:{%22type%22:%22true_if_each_subcondition_is_true%22,%22subconditions%22:[{%22type%22:%22true_if_inputparameter_has_required_value_for_key%22,%22key%22:%22sender_email%22,%22required_value%22:%22VALUE_OF_EMAIL%22}]}}'>To search for taskinstances with a specific sender_email</a><br />
	<a target='_blank' href='https://global.nexusthemes.com/?nxs=task-gui&page=searchtaskinstances&args_json={%22if_this%22:{%22type%22:%22true_if_each_subcondition_is_true%22,%22subconditions%22:[{%22type%22:%22true_if_inputparameter_has_required_value_for_key%22,%22key%22:%22toemail%22,%22required_value%22:%22VALUE_OF_EMAIL%22}]}}'>To search for taskinstances with a specific toemail</a><br />
	
	<?php
	die();
	}
	
	$args = json_decode($args_json, true);
	
	echo "search input:<br /><br />";
	echo nxs_prettyprint_array($args);
	
	echo "<br />";
	echo "search results:<br /><br />";
	
	$result = brk_tasks_searchtaskinstances($args);
	
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('task-gui.css',__FILE__ ); ?>" />
	<?php	
	
	echo "<table class='table-oddeven'>";
	$taskinstances = $result["taskinstances"];
	foreach ($taskinstances as $taskinstance)
	{
	$taskid = $taskinstance["taskid"];
	$title = brk_tasks_gettaskstitle($taskid);
	$taskinstanceid = $taskinstance["taskinstanceid"];
	
	$action_url = "https://global.nexusthemes.com/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
	
	$instancemeta = brk_tasks_getinstance($taskid, $taskinstanceid);
	
	$state = $instancemeta["state"];
	
	$inputparameters = $instancemeta["inputparameters"];
	$parameters_html = "";
	foreach ($inputparameters as $inputparameter => $val)
	{
	$parameters_html .= "{$inputparameter} : {$val}<br />";
	}
	
	echo "<tr>";
	echo "<td>{$taskid}</td><td>{$title}</td><td>{$state}</td><td><a target='_blank' href='{$action_url}'>{$taskinstanceid}</a></td><td>$parameters_html</td>";
	echo "</tr>";
	}
	echo "</table>";
	
	echo "<br />";
}