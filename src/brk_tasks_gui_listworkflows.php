<?php

function brk_tasks_gui_listworkflows()
{
	brk_tasks_gui_render_head();
	brk_tasks_gui_rendernavigation();
	
	$schema = "nxs.workflow.condition.type";
	echo "<h1>workflow types (<a href='https://docs.google.com/spreadsheets/d/1vB7sUn97SW9ZaXA3ASQ3ZdgBFs2tkYq-6BoP41UH_6s/edit#gid=0' target='_blank'>{$schema}</a>)</h1>";
	$currenturl = functions::geturlcurrentpage();
	
	global $nxs_g_modelmanager;
	$a = array
	(
	"singularschema" => $schema,
	);
	$allentries = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels($a);
	
	foreach ($allentries as $entry)
	{
	$entryid = intval($entry["{$schema}_id"]);
	$entrymetabyid[$entryid] = $entry;
	}
	
	ksort($entryid);
	
	$rowindex = -1;
	
	echo "<table class='table-oddeven'>";
	foreach ($entrymetabyid as $entryid => $entry)
	{	
	echo "<tr>";
	$type = $entry["type"];
	$documentation = $entry["documentation"];
	echo "<td>$entryid</td>";
	echo "<td>$type</td>";
	echo "<td>$documentation</td>";
	echo "</tr>";
	}
	echo "</table>";
	
	echo "<div style='padding-bottom: 50px;'>:)</div>";
	
	die();
}