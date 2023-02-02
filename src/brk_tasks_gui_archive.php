<?php

function brk_tasks_gui_archive()
{
	brk_tasks_gui_render_head();
	brk_tasks_gui_rendernavigation();	
	
	$orderby = $_REQUEST["orderby"];
	if ($orderby == "")
	{
	$orderby = "size";
	}
	
	$order = $_REQUEST["order"];
	if ($order == "ASC")
	{
	$order_actual = SORT_ASC;
	}
	else if ($order == "DESC")
	{
	$order_actual = SORT_DESC;
	}
	else
	{
	$order_actual = SORT_DESC;
	}
	
	// allow user to pick order by
	if (true)
	{
	$config = array
	(
	"field" => "orderby",
	"options" => array
	(
	"nxs.p001.businessprocess.task_id" => "Task id",
	"size" => "Size", 
	"count" => "Count",
	"avgsizeperentry" => "Average size per entry",
	)
	);
	
	if (true)
	{
	$html_pieces = array();
	$field = $config["field"];
	$options = $config["options"];
	foreach ($options as $value => $text)
	{
	$currenturl = functions::geturlcurrentpage();
	$action_url = $currenturl;
	$action_url = functions::addqueryparametertourl($action_url, $field, $value, true, true);
	$html_pieces[] = "<a href='{$action_url}'>{$field} $text</a>";
	}
	echo implode(" | ", $html_pieces);
	echo "<br />";
	}
	}
	
	// allow user to pick order
	if (true)
	{
	$config = array
	(
	"field" => "order",
	"options" => array
	(
	"ASC" => "Ascending", 
	"DESC" => "Descending"
	)
	);
	
	if (true)
	{
	$html_pieces = array();
	$field = $config["field"];
	$options = $config["options"];
	foreach ($options as $value => $text)
	{
	$currenturl = functions::geturlcurrentpage();
	$action_url = $currenturl;
	$action_url = functions::addqueryparametertourl($action_url, $field, $value, true, true);
	$html_pieces[] = "<a href='{$action_url}'>{$field} $text</a>";
	}
	echo implode(" | ", $html_pieces);
	echo "<br />";
	}
	}
	
	// show pending archive task instances
	if (true)
	{
	// following lines are required to prevent the shortcode from throwing
	// an error (makes sense, as task instructions always should be within the
	// context of a task and instance
	$_REQUEST["taskid"] = "TEST";
	$_REQUEST["taskinstanceid"] = "TEST";
	
	echo do_shortcode("[nxs_p001_task_instruction indent='0' type='render_task_instances_v2' f_taskid=241 f_states='STARTED|CREATED']");
	}
	
	$schema = "nxs.p001.businessprocess.task";
	global $nxs_g_modelmanager;
	$a = array
	(
	"singularschema" => $schema,
	);
	$allentries = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels($a);
	
	// enrich
	$items = array();
	foreach ($allentries as $entry)
	{
	$taskid = intval($entry["nxs.p001.businessprocess.task_id"]);
	
	$tasktitle = tasks::gettasktitle($taskid);
	$entry["tasktitle"] = $tasktitle;
	
	$taskmeta = brk_tasks_gettaskmeta($taskid);
	$entry["size"] = $taskmeta["size"];
	$entry["count"] = $taskmeta["count"];
	if ($taskmeta["count"] > 0)
	{
	$entry["avgsizeperentry"] = ceil($taskmeta["size"] / $taskmeta["count"]);
	}
	else
	{
	$entry["avgsizeperentry"] = 0;
	}
	
	
	$items[] = $entry;
	}
	
	//
	array_multisort(array_column($items, $orderby), $order_actual, $items);
	
	echo "<table class='table-oddeven'>";
	echo "<tr>";
	echo "<td>Task id and title</td>";
	echo "<td>Actions</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>Size</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>Count</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>Avg size per entry</td>";
	echo "</tr>";
	
	foreach ($items as $item)
	{
	$taskid = $item["nxs.p001.businessprocess.task_id"];
	$tasktitle = $item["tasktitle"];
	$size = $item["size"];
	$size_human = number_format($size, 0, '.', '.');
	
	$count = $item["count"];
	$avgsizeperentry = $item["avgsizeperentry"];
	
	$actions_pieces = array();
	$actions_pieces[] = "<a target='_blank' href='https://tasks.bestwebsitetemplates.net/api/1/prod/create-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&businessprocesstaskid=241&archive_taskid={$taskid}'>Archive</a>";
	$actions = implode(" | ", $actions_pieces);
	
	echo "<tr>";
	echo "<td>$taskid - $tasktitle</td>";
	echo "<td>$actions</td>";
	echo "<td>&nbsp;</td>";
	echo "<td style='font-family: courier; text-align: right;'>$size_human</td>";
	echo "<td>&nbsp;</td>";
	echo "<td style='font-family: courier; text-align: right;'>$count</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>$avgsizeperentry</td>";
	echo "</tr>";
	}
	echo "</table>";
}