<?php

use barkgj\functions;
use barkgj\tasks\tasks;

$loader = require_once __DIR__ . '/vendor/autoload.php';

// todo; fix autoloader of composer so this is not needed...
require_once __DIR__ . '/vendor/barkgj/datasink-library/src/datasink-entity.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/functions.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/filesystem.php';
require_once __DIR__ . '/vendor/barkgj/tasks-library/src/tasks.php';

function brk_tasks_gui_taskinstancedebug()
{
	$taskid = $_REQUEST["taskid"];
	$taskinstanceid = $_REQUEST["taskinstanceid"];
	echo "<h1>Debug</h1>";

	$instancemeta = tasks::gettaskinstance($taskid, $taskinstanceid);
	echo json_encode($instancemeta);

	if ($_REQUEST["action"] == "end")
	{
		echo "ending instance";
		$sub_action_url = "https://tasks.bestwebsitetemplates.net/api/1/prod/end-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&businessprocesstaskid={$taskid}&instance_context={$taskinstanceid}";
		$sub_action_string = file_get_contents($sub_action_url);
		echo $sub_action_string;
	}
	else
	{
		echo "<br /><br />";
		$currenturl = functions::geturlcurrentpage();
		$url = $currenturl;
		$url = functions::addqueryparametertourl($url, "action", "end", true, true);
		echo "<a href='$url'>End instance (FORCED)</a>";
	}

	die();
}