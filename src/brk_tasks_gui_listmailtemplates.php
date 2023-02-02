<?php

function brk_tasks_gui_listmailtemplates()
{
	brk_tasks_gui_render_head();
	brk_tasks_gui_rendernavigation();
	
	
	echo "<h1>mail templates (<a href='https://docs.google.com/spreadsheets/d/1E-mB4yx7NBk3cA4R2In7NE1Qm15EFaO8BOuvPk83NGk/edit#gid=219307078' target='_blank'>nxs.mail.mailtemplate</a>)</h1>";
	$currenturl = functions::geturlcurrentpage();
	
	
	$schema = "nxs.mail.mailtemplate";
	global $nxs_g_modelmanager;
	$a = array
	(
		"singularschema" => $schema,
	);
	$allentries = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels($a);
	
	foreach ($allentries as $entry)
	{
		$entryid = intval($entry["nxs.mail.mailtemplate_id"]);
		$entrymetabyid[$entryid] = $entry;
	}
	
	ksort($entryid);
	
	$rowindex = -1;
	
	echo "<table class='table-oddeven'>";
	foreach ($entrymetabyid as $entryid => $entry)
	{	
		echo "<tr>";
		$title = $entry["subject"];
		echo "<td>$entryid</td>";
		echo "<td>$title</td>";
		echo "<td><a target='_blank' href='{$rfc_url}'>create rfc</a></td>";
		echo "</tr>";
	}
	echo "</table>";
	
	echo "<div style='padding-bottom: 50px;'>:)</div>";
	
	die();
}