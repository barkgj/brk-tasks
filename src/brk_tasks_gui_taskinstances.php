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

function brk_tasks_gui_taskinstances()
{
	brk_tasks_gui_render_head();
	
	$taskid = $_REQUEST["taskid"];
	$tasktitle = tasks::gettasktitle($taskid);
	
	brk_tasks_gui_rendernavigation();
	
	echo "<h1>Task instances for $taskid - {$tasktitle}</h1>";

	$homeurl = functions::geturlhome();
	$newinstanceurl = $homeurl;
	$newtaskinstanceform_url = "{$homeurl}/?nxs=task-gui&page=createnewtaskinstanceform&newtaskid={$taskid}&createdby_taskid={$taskid}";


	echo "<a href='{$newtaskinstanceform_url}'>New instance</a>";
	
	echo "<table class='table-oddeven'>";
	echo "<tr>";
	echo "<td>id</td>";
	echo "<td>state</td>";
	echo "<td>created days ago</td>";
	echo "<td>details</td>";
	echo "</tr>";
	
	$count = 0;
	
	$taskinstances = tasks::gettaskinstances($taskid);
	foreach ($taskinstances as $instanceid => $instancemeta)
	{
		// var_dump($instancemeta);
		
		$state = $instancemeta["state"];
		$createtime = $instancemeta["createtime"];
		$rightnow = strtotime('now');
		$delta = $rightnow - $createtime;
		$duration_in_days = "todo"; //  nxs_date_gettotaldaysinterval($delta);
		
		$prettyprinted = json_encode($instancemeta, JSON_PRETTY_PRINT);
		
		$details = "<pre>" . $prettyprinted . "</pre>";
		
		if (strlen($details) > 1024)
		{
			if ($_REQUEST["showlong"] == "true")
			{
				//
			}
			else
			{
				$currenturl = functions::geturlcurrentpage();
				$action_url = $currenturl;
				$action_url = functions::addqueryparametertourl($action_url, "showlong", "true", true, true);
				
				$details = "too long to make sense (<a href='{$action_url}'>click here</a>)";
			}
		}
		
		$homeurl = functions::geturlhome();
		$detail_url = "{$homeurl}?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$instanceid}";
		
		echo "<tr>";
		echo "<td><a target='_blank' href='{$detail_url}'>{$instanceid}</a></td>";
		echo "<td>{$state}</td>";
		echo "<td>{$duration_in_days} days ago</td>";
		echo "<td>{$details }</td>";
		echo "</tr>";
		
		$count++;
	}
	
	echo "</table>";
	
	echo "<br />";
	echo "Found: $count<br />";
	echo ":)";
	
	die();
}