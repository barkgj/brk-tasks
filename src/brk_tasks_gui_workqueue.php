<?php

use barkgj\functions;
use barkgj\tasks\tasks;

$loader = require_once __DIR__ . '/vendor/autoload.php';

// todo; fix autoloader of composer so this is not needed...
require_once __DIR__ . '/vendor/barkgj/datasink-library/src/datasink-entity.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/filesystem.php';
require_once __DIR__ . '/vendor/barkgj/tasks-library/src/tasks.php';

function brk_tasks_gui_workqueue()
{
	$homeurl = functions::geturlhome();
	$currentuserid = brk_tasks_gui_getuseridcurrentuser();
	
	brk_tasks_gui_render_head();
	
	brk_tasks_gui_rendernavigation();
	echo "<h1>Work Queue</h1>";
	
	// ----
	
	if (true) // $_REQUEST["test"] == "test")
	{
		$subconditions = array();
		
		$subconditions[] = array
		(
			"type" => "true_if_in_any_of_the_required_states",
			"any_of_the_required_states" => array("CREATED", "STARTED")
		);
		
		$subconditions[] = array
		(
			"type" => "true_if_assigned_to_any_of_the_required_employees",
			"any_of_the_required_employees" => array($currentuserid)
		);
		
		$search_args = array
		(
			"if_this" => array
			(
				"type" => "true_if_each_subcondition_is_true",
				"subconditions" => $subconditions,
			),
			"return_this" => "details",
		);
	
		$taskinstances_wrap = tasks::searchtaskinstances($search_args);
		$taskinstances = $taskinstances_wrap["taskinstances"];
		
		$count = count($taskinstances);
		if ($count > 0)
		{
			echo "Found {$count} created or started tasks instances assigned to you<br />";
			?>
			<div class='container'>
			<a class='toggleable' href='#' onclick="jQuery(this).closest('.container').find('.toggleable').toggle(); return false;">Show</a>
			<div class='toggleable' style='background-color: #DDD; display: none; margin-left: 20px;'>
			<a href='#' onclick="jQuery(this).closest('.container').find('.toggleable').toggle(); return false;">Close</a><br />
			<?php
			foreach ($taskinstances as $taskinstance)
			{
				$taskid = $taskinstance["taskid"];
				$url = $taskinstance["url"];
				$tasktitle = tasks::gettasktitle($taskid);
				echo "<a target='_blank' href='{$url}'>$taskid - $tasktitle</a><br />";
			}
			?>
			</div>
			</div>
			<?php
		}
		else
		{
			echo "No started tasks found that are assigned to you<br />";
		}
	}
	
	// ----
	
	echo "<br />";
	
	// get all tasks
	$tasks = tasks::gettasks();

	$processing_type_by_taskid = array();
	
	foreach ($tasks as $task)
	{
		$taskid = $task["id"];
		$title = $task["title"];
		$handle_prio = $task["handle_prio"];
		$processing_type = $task["processing_type"];
		$processing_type_by_taskid[$taskid] = $processing_type;
	
		$countbystatus = array();
		$instances = tasks::gettaskinstances($taskid);
		foreach ($instances as $instanceid => $instancemeta)
		{
			$state = $instancemeta["state"];
			$countbystatus[$state]++;
		}
	
		$showtask = ($countbystatus["STARTED"] > 0 || $countbystatus["CREATED"] > 0);
		if ($handle_prio == "")
		{
			$handle_prio = 999;	// unknown = highest
		}
	
		if ($showtask)
		{
			$task_abstract_url = "{$homeurl}?nxs=task-gui&taskid={$taskid}&page=viewabstracttask";
	
			// $taskid
			$taskinstancelist_url = "{$homeurl}?nxs=task-gui&page=taskinstancelist&taskid={$taskid}";
	
			if ($handle_prio == 999)
			{
				$priohtml = "(<span><a style='background-color: red; color: white;' href='https://docs.google.com/spreadsheets/d/1Fvf4rdP8nm5bfr8A7PIcdC8F6q_-feRNKFZyT3uyRTA/edit#gid=540158938' target='_blank'>DEFINE PRIO</a></span>)";
			}
			else
			{
				$priohtml = ""; // (prio $handle_prio)";
			}
		
			$handle_prio = intval($handle_prio);
			
			$openinstancesoftask = $countbystatus["CREATED"] + $countbystatus["STARTED"];
			
			$processing_type = $processing_type_by_taskid[$taskid];
			
			$outputbyprio[$handle_prio][] = "Task id: {$taskid} {$processing_type} {$priohtml} ({$openinstancesoftask} open instances) - <a href='{$taskinstancelist_url}' target='_blank'>$title</a> <a href='{$task_abstract_url}' target='_blank'>Abstract steps</a><br />";
			foreach ($countbystatus as $state => $cnt)
			{
				if (in_array($state, array("STARTED", "CREATED")))
				{
					//$outputbyprio[$handle_prio][] = "Task id: {$taskid} ($priohtml} - <a href='{$taskinstancelist_url}' target='_blank'>$title</a> <a href='{$task_abstract_url}' target='_blank'>Task GUI</a><br />";
					//echo "<span style='width: 50px;'></span><span style='display: inline-block; min-width: 100px;'>{$state}</span><span style='display: inline-block; min-width: 100px;'>{$cnt}x</span><br />";
				}
			}
		}
		else
		{
		// $outputbyprio[$handle_prio][] = "<span title='{$taskid}''>...skipped...</span><br />";
		}
	}
	
	$prios_used = array_keys($outputbyprio);
	rsort($prios_used);
	
	foreach ($prios_used as $prio)
	{
		$list = $outputbyprio[$prio];
		
		if ($prio > 0)
		{
			echo "<h1>prio $prio</h1>";
			foreach ($list as $item)
			{
				echo $item;
			}
		}
		else if ($prio == 0)
		{
			echo "<style>";
			echo ".showwhenhovering { opacity: 0.1; } ";
			echo ".showwhenhovering:hover { opacity: 1.0; }";
			echo "</style>";
			echo "<div class='showwhenhovering'>";
			echo "<h1>prio $prio</h1>";
			foreach ($list as $item)
			{
				echo $item;
			}
			echo "</div>";
		}
	}
	
	?>
	<div style='padding-bottom: 200px;'>:)</div>
	<?php
	
	die();
}
