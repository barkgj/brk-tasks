<?php

function brk_tasks_gui_createnewbulktaskinstanceform()
{	
	$newtaskid = $_REQUEST["newtaskid"];
	$createdby_taskid = $_REQUEST["createdby_taskid"];
	$createdby_taskinstanceid = $_REQUEST["createdby_taskinstanceid"];

	if (!brk_tasks_taskexists($newtaskid)) { nxs_webmethod_return_nack("task not found? $newtaskid"); }
	
	//if ($createdby_taskid == "") { nxs_webmethod_return_nack("createdby_taskid not set?"); }
	//if ($createdby_taskinstanceid == "") { nxs_webmethod_return_nack("createdby_taskinstanceid not set?"); }
	
	$meta = brk_tasks_getreflectionmeta($newtaskid, "");
	$required_fields = $meta["required_fields"];
	
	// handle possible actions
	$action = $_REQUEST["action"];
	if ($action == "createinstances")
	{
		// parse all data submitted
		if (true)
		{
			// get rid of escaped slashes
			$bulkdata = stripslashes_deep($_REQUEST["bulkdata"]);
			
			require_once("/srv/generic/libraries-available/nxs-parser/nxs-parser.php");
			$args = array
			(
				"csvcontents" => $bulkdata,
				"columnseperator" => "\t",
			);
			$rows = nxs_parser_parsecsv_data($args);
			
			echo "<div class='technical'>";
			echo "parser found " . count($rows) . " rows<br />";
			echo "</div>";
			echo "<h2>Found the following records:</h2>";
			echo "<table>";
			foreach ($rows as $row)
			{
				// check if all required fields are set, if so, use the row to create an instance
				$allrequiredfieldsset = true;
				foreach ($required_fields as $required_field)
				{
					if ($row[$required_field] == "")
					{
						$allrequiredfieldsset = false;
					}
				}
				
				if ($allrequiredfieldsset)
				{
					$included[] = $row;
					
					echo "<tr>";
					foreach ($row as $key => $val)
					{
						echo "<td>";
						echo "INCLUDED";
						echo "</td>";
						echo "<td>";
						echo "$key - $val";
						echo "</td>";
					}
					echo "</tr>";
					
					
					
				}
				else
				{
					$excluded[] = $row;
					
					echo "<tr>";
					foreach ($row as $key => $val)
					{
						echo "<td>";
						echo "EXCLUDED (required fields missing)";
						echo "</td>";
						echo "<td>";
						echo "$key - $val";
						echo "</td>";
					}
					echo "</tr>";
				}
				
				
				
			}
			echo "</table>";
			
			//echo "todo: invoke bulk api";
			//var_dump($included);
			
			foreach ($included as $taskinstance_props_tobecreated)
			{
				$assigned_to = "";
				$createdby_taskid = "";
				$createdby_taskinstanceid = "";
				$mail_assignee = false;
				$inputparameters = $taskinstance_props_tobecreated;
				$delegated_result = brk_tasks_createtaskinstance($newtaskid, $assigned_to, $createdby_taskid, $createdby_taskinstanceid, $mail_assignee, $inputparameters);
				if ($delegated_result["result"] != "OK")
				{
					echo "ERR creating instance?!";
					die();
				}
				echo "created instance for taskid $newtaskid<br />";
			}			
		}
		
		echo "thats it";
		die();
		
	}
	
	$task_title = do_shortcode("[nxs_string ops=modelproperty modeluri='{$newtaskid}@nxs.p001.businessprocess.task' property='title']");
	$currenturl = "https://global.nexusthemes.com/?nxs=task-gui&page=createnewbulktaskinstanceform";
	
	brk_tasks_gui_render_head();
	brk_tasks_gui_rendernavigation();
	?>
	<h1>Create new <?php echo $task_title; ?> instance BULK Form</h1>
	<?php
	foreach ($required_fields as $required_field)
	{
		$fieldvalue = $_REQUEST[$required_field];
		echo "Required field; {$required_field}<br />";
	}
	?>
	<form action='<?php echo $currenturl; ?>' method='POST' style='margin-left: 100px;'>
		<input type='hidden' name='nxs' value='task-gui' />
		<input type='hidden' name='page' value='createnewbulktaskinstanceform' />
		<input type='hidden' name='action' value='createinstances' />
		
		<textarea id='bulkdata' name='bulkdata' style='width: 100%; min-height: 400px;'></textarea>
		<?php
		echo "<input type='hidden' name='newtaskid' value='{$newtaskid}' />";
		echo "<input type='hidden' name='createdby_taskid' value='{$createdby_taskid}' />";
		echo "<input type='hidden' name='createdby_taskinstanceid' value='{$createdby_taskinstanceid}' />";
		?>
		<input type='submit' value='Create task instances (bulk)' />
	</form>
	<div style='padding-bottom: 100px;'>&nbsp;</div>
	<?php
	die();
}