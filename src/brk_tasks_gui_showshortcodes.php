<?php
function brk_tasks_gui_showshortcodes()
{
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('task-gui.css',__FILE__ ); ?>" />
	<?php
	
	if ($_REQUEST["action"] == "createnewshortcode")
	{
	$requirements = $_REQUEST["requirements"];
	//
	$newtaskid = "99";
	$action_url = "https://global.nexusthemes.com/api/1/prod/create-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&businessprocesstaskid={$newtaskid}&requirements={$requirements}";
	
	$action_string = file_get_contents($action_url);
	$action_result = json_decode($action_string, true);
	if ($action_result["result"] != "OK") { nxs_webmethod_return_nack("unable to create task instance; $action_url"); }
	
	$newlycreatedtaskinstanceid = $action_result["taskinstanceid"];
	
	$start_instance_url = "https://global.nexusthemes.com/?nxs=task-gui&page=taskinstancedetail&taskid={$newtaskid}&taskinstanceid={$newlycreatedtaskinstanceid}";
	echo "task instance created<br />";
	echo "<a href='{$start_instance_url}'>click here to start this new instance</a>";
	
	die();
	}
	
	$currenturl = nxs_geturlcurrentpage();
	brk_tasks_gui_rendernavigation();
	?>
	<H1>Available shortcodes (<a target='_blank' href='https://docs.google.com/spreadsheets/d/1MSQGTfZYVLPE06UChN0Wqa5IjOPt7OFI_mtIdYN7kR0/edit#gid=836986589'>nxs.itil.configurationitems.shortcode</a> and <a target='_blank' href='https://docs.google.com/spreadsheets/d/1MSQGTfZYVLPE06UChN0Wqa5IjOPt7OFI_mtIdYN7kR0/edit#gid=37716066'>nxs.itil.configurationitems.shortcode.type</a>)</H1>
	
	
	<div id='addshortcode' style='background-color: #DDD'>
	<form action='<?php echo $currenturl; ?>' method='POST' target='_blank' style='margin-left: 100px;'>
	<input type='hidden' name='nxs' value='task-gui' />
	<input type='hidden' name='page' value='showshortcodes' />
	<input type='hidden' name='action' value='createnewshortcode' />
	<label>new shortcode:</label><br />
	<textarea name='requirements' style='width: 100%; height: 40px;' placeholder='requirements'></textarea>
	<input type='submit' value='Create Shortcode' />
	</form>
	</div>
	
	<?php
	
	$shortcodes_url = "https://global.nexusthemes.com/api/1/prod/get-shortcodes/?nxs=code-api&nxs_json_output_format=prettyprint";
	$shortcodes_string = file_get_contents($shortcodes_url);
	$shortcodes_result = json_decode($shortcodes_string, true);
	$shortcodes = $shortcodes_result["shortcodes"];
	foreach ($shortcodes as $shortcode)
	{
	$types = $shortcode["types"];
	
	$actualshortcode = $shortcode["shortcode"]["shortcode"];
	
	if (count($types) == 0)
	{ 
	$rows[] = array
	(
	"shortcode_html" => "[{$actualshortcode}]",
	"description_html" => "",
	"actions" => array
	(
	"<a href='#'>Create RFC (edit shortcode)</a>",
	)
	);
	}
	else
	{
	foreach ($types as $type)
	{
	$type_att = $type["type"];
	$description = $type["description"];
	if ($description == "") { $description = "Shortcode type has no description set (<a target='_blank' href='https://docs.google.com/spreadsheets/d/1MSQGTfZYVLPE06UChN0Wqa5IjOPt7OFI_mtIdYN7kR0/edit#gid=37716066'>configure in ixplatform</a>)"; }
	$description = str_replace("\r\n", "<br />", $description);
	
	$rows[] = array
	(
	"shortcode_html" => "[{$actualshortcode} type='{$type_att}']",
	"description_html" => $description,
	"actions" => array
	(
	"<a href='#'>Create RFC (edit shortcode)</a>",
	)
	);
	}
	}
	}
	
	echo "<table class='table-oddeven'>";
	
	foreach ($rows as $row)
	{
	$shortcode_html = $row["shortcode_html"];
	$description_html = $row["description_html"];
	$actions = $row["actions"];
	
	echo "<tr>";
	echo "<td>";
	echo $shortcode_html;
	echo "</td>";
	echo "<td>";
	echo $description_html;
	echo "</td>";
	echo "<td>";
	foreach ($actions as $action)
	{
	echo $action;
	}
	echo "</td>";			
	echo "</tr>";
	}
	
	echo "</table>";
}
