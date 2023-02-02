<?php

/*
Plugin Name: brk-tasks plugin
Version: 1.0.0
Plugin URI: http://afnetix.com
Description: tasks gui
Author: Gert-Jan Bark
Author URI: http://afnetix.com
*/

/*
if (!function_exists("functions__override__getsitedatafolder"))
{
    function functions__override__getsitedatafolder()
    {
		$result = "C:\\site1\\";
		echo "overriding to $result :)<br />";
        return $result;
    }
}
*/

error_reporting(E_ERROR | E_PARSE);

$loader = require_once __DIR__ . '/vendor/autoload.php';

// todo; fix autoloader of composer so this is not needed...
require_once __DIR__ . '/vendor/barkgj/datasink-library/src/datasink-entity.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/filesystem.php';
require_once __DIR__ . '/vendor/barkgj/tasks-library/src/tasks.php';

use barkgj\functions;
use barkgj\tasks\tasks;

//$folder = functions::getsitedatafolder();
//echo $folder;
//die();

// {$homeurl}?nxs=task-gui&page=showshortcodes
// {$homeurl}?nxs=task-gui
// {$homeurl}?nxs=task-gui&page=tasklist
// {$homeurl}?nxs=task-gui&taskid=X&taskinstanceid=Y
// {$homeurl}?nxs=task-gui&page=taskinstancedebug&taskinstanceid=&taskid=

// require_once("/srv/generic/libraries-available/nxs-authorization/nxs-authorization.php");
// nxs_authorization_require_OR_operator(array("superadmin", "fromwithininfrastructure", "specialips"));

// require_once("/srv/generic/libraries-available/nxs-workflows/nxs-workflows.php");

if (false)
{
	// wp functions, added to trick the IDE to think these functions exist
	function do_shortcode($x) { return $x; }
	function plugins_url($x) { return ""; }
}

function brk_tasks_getreferencedtaskinstances($message)
{
	$result = array();
	
	/*
	$text = $message;
	
	$identification = "Nexus Mail Delivery [ nxs-global| mail-api | v1.1 | ";
	if (functions::stringcontains($text, $identification))
	{
		$pieces = explode($identification, $text);
		$tail = $pieces[1];	// "88 | EEAE3908-1751-32E3-1E1C-7222FD3DB999 | 53 ]</div>"
		$nuggets = explode(" ", $tail);
		$repliesto_taskid = $nuggets[0];
		$repliesto_taskinstanceid = $nuggets[2];
		$repliesto_mailtemplate = $nuggets[4];
		$result[] = array
		(
			"taskid" => $repliesto_taskid,
			"taskinstanceid" => $repliesto_taskinstanceid,
			"repliesto_mailtemplate" => $repliesto_mailtemplate,
		);
	}
	*/
	
	return $result;
}

function brk_tasks_gui_getsessionkey()
{
	return "brk_tasks_gui";
}

function brk_tasks_gui_getsession()
{
	functions::ensuresessionstarted();
	$result = $_SESSION[brk_tasks_gui_getsessionkey()];
	
	return $result;
}

function brk_tasks_gui_setsessionvar($key, $val)
{
	functions::ensuresessionstarted();
	$_SESSION[brk_tasks_gui_getsessionkey()][$key] = $val;
}

function brk_tasks_gui_getuseridcurrentuser()
{
	$session = brk_tasks_gui_getsession();
	$result = $session["user"]["id"];
	
	return $result;
}

function brk_tasks_gui_getuseremail()
{
	$session = brk_tasks_gui_getsession();
	$result = $session["user"]["email"];
	
	return $result;
}

function brk_tasks_gui_getuserfirstname()
{
	$session = brk_tasks_gui_getsession();
	$result = $session["user"]["firstname"];
	
	return $result;
}

function brk_tasks_gui_getrolescurrentuser()
{
	$session = brk_tasks_gui_getsession();
	$result = $session["user"]["roles"];
	
	return $result;
}

function brk_tasks_gui_render_head()
{
	$homeurl = functions::geturlhome();

	if (brk_tasks_gui_getrolescurrentuser() == "rpa")
	{
		?>
		Hello RPA :)
		<?php
	}
	else
	{
		?>
		<html>
		<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>				
		<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('task-gui.css',__FILE__ ); ?>" />
		<?php
		/*
		<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
		<script>
		window.OneSignal = window.OneSignal || [];
		OneSignal.push(function() {
		OneSignal.init({
		appId: "ca285364-d6d5-49d3-88f9-23feecd8bca9",
		});
		});
		</script>
		*/
		?>
		
		
		</head>
		<body>
		<style>
		.thought
		{
		background-color: #CCFFCC;
		padding: 10px;
		margin: 10px;
		font-style: italic;
		font-family: courier;
		font-size: 10px;
		}
		.grabattention
		{
		background-color: yellow;
		color: black;
		}
		.tasklink
		{
		white-space: nowrap;
		}
		.todo
		{
		background-color: pink;
		}
		.placeholder
		{
		background-color: #0F0;
		}
		.tab
		{
		display:inline-block; 
		width: 50px;
		}
		</style>
		<?php
	}
}

function brk_tasks_gui_rendernavigation()
{
	$homeurl = functions::geturlhome();

	if (brk_tasks_gui_getrolescurrentuser() == "rpa")
	{
		?>
		RPA MENU<br />
		<?php
	}
	else
	{
	global $nxs_g_modelmanager;
	
	$session = brk_tasks_gui_getsession();
	$user = $session["user"];
	$firstname = $user["firstname"];
	$eid = brk_tasks_gui_getuseridcurrentuser();
	
	$date_human = date("j F Y G:i");
	?>
	<script type='text/javascript' src='https://my.nexusthemes.com/wp-content/nexusframework/edge/js/jquery-1.11.1/jquery.min.js?ver=5.3.2'></script>
	<style>
	body 
	{ 
		font-family: Open Sans, sans-serif; 
	}
	.logocontainer,
	.titlecontainer,
	.messagecontainer
	{
		padding: 5px;
		margin: 0 auto;
		width: 100%;
	}
	.logocontainer
	{
		color: white;
	}
	.logocontainer img
	{
		height: 45px;
	}
	.titlecontainer
	{
		background-color: rgb(48, 138, 255);
		color: white;
	}
	.messagecontainer
	{
		background-color: white;
		color: black;
	}
	</style>
	<div id='navigation'>
	<div class='nav-quickbar toggler'>
	<a href='#' onclick="jQuery('.nav-quickbar.toggleable').toggle();">Toggle Quickbar</a>
	</div>
	<div class='nav-quickbar toggleable' style='display: none; background-color: #eee;'>
	<?php
	$options = array
	(
		array
		(
			"taskid" => 1,
		)
	);
	foreach ($options as $option)
	{
		//$title = $option["title"];
		$newtaskid = $option["taskid"];
		
		$title = "Create " . tasks::gettasktitle($newtaskid);
		
		$id = $title;
		$id = strtolower($title);
		$id = str_replace(" ", "_", $title);
		
		$happyflow_behaviour = "";
		if ($current_processingtype == "automated")
		{
			$happyflow_behaviour = "";	// will show link
		}
		else
		{
			$happyflow_behaviour = "start_child_task_instance;redirect_to_child_instance";
		}
		
		?>
		| <a href='#' onclick="jQuery('#<?php echo $id; ?>').toggle();return false;"><?php echo $title; ?></a>
		<div id='<?php echo $id; ?>' style='display: none'>
		<?php
		//echo do_shortcode("[nxs_p001_task_instruction type='create_task_instance' create_taskid={$newtaskid} render_required_fields=true happyflow_behaviour='{$happyflow_behaviour}' allowdaemonchild=true linkparenttochild=false]");
		?>
		</div>
		<?php
	}
	?>
	</div> <!-- quick bar -->
	<br />
	Navigation: 
	<a target='_blank' href='<?php echo $homeurl; ?>?nxs=task-gui&page=workqueue'>Work queue</a> |
	<a target='_blank' href='{$homeurl}?nxs=task-gui&page=searchtaskinstances'>Search task instances</a> |
	<a target='_blank' href='{$homeurl}?nxs=task-gui&page=tasklist'>Tasks</a> | 
	<a target='_blank' href='{$homeurl}?nxs=task-gui&page=showshortcodes'>Shortcodes</a> |
	<a target='_blank' href='{$homeurl}?nxs=task-gui&page=showapiservices'>API services</a> | 
	<a target='_blank' href='{$homeurl}?nxs=task-gui&page=listmailtemplates'>Mail templates</a> | 
	<a target='_blank' href='{$homeurl}?nxs=task-gui&page=bootstrapnewbatch'>Batch processing</a> | 
	<a target='_blank' href='{$homeurl}?nxs=task-gui&page=archive'>Archive</a> | 
	<a target='_blank' href='{$homeurl}?nxs=task-gui&page=whichtaskinstancesdidyouendrecently'>Recent Activities</a> | 
	<a target='_blank' href='{$homeurl}?nxs=task-gui&page=listworkflows'>Workflows</a> | 
	<a target='_blank' href='https://docs.google.com/spreadsheets/d/1ZXVua1soThK87EEXYj1mbD5prWH4rsc4zHii7T0lfjc/edit#gid=119669972'>System URLs</a> | 
	<a target='_blank' href='https://docs.google.com/document/d/103xk7J7Bhlr6WaYxeN-Yr5tJ5HZJbN4je6EU3RPYwM4/edit'>Passwords</a> | 
	<a target='_blank' href='https://docs.google.com/document/d/10I2fAwpmtYI3zQzcsgWvWep3rz99R2Qfmm6Tr2vmCpk/edit'>Tracking</a> | 
	<a target='_blank' href='{$homeurl}?nxs=task-gui&logout=true'>Log out</a>
	<span><?php echo "{$firstname} - {$eid}"; ?></span><br />
	IX Platform:
	<?php
	$pieces = array();
	foreach ($schema_to_url as $schema => $url)
	{
	$hierarchy_pieces = explode(".", $schema);
	$last_hierarchy_piece = end($hierarchy_pieces);
	$pieces[] = "<a target='_blank' href='{$url}'>{$last_hierarchy_piece}</a>";
	}
	$ixplatformrefs_html = implode(" | ", $pieces);
	echo "<a target='_blank' href='https://docs.google.com/spreadsheets/d/1ZXVua1soThK87EEXYj1mbD5prWH4rsc4zHii7T0lfjc/edit#gid=23200529'>TOC</a> | {$ixplatformrefs_html}";
	?>
	<br />
	Marketing:
	<a target='_blank' href='{$homeurl}api/1/prod/peekitems/?nxs=queue-api&nxs_json_output_format=prettyprint&queue_id=5'>Queued newsletter items</a>
	<br />
	<?php
	$kpis = array
	(
		/*
		array
		(
		'type' => 'searchtaskinstances',
		'title' => 'Registered domains',
		'json' => '{"if_this":{"type":"true_if_each_subcondition_is_true","subconditions":[{"type":"true_if_task_has_required_taskid","required_taskid":"92"},{"type":"true_if_in_any_of_the_required_states","any_of_the_required_states":["CREATED","STARTED","ENDED"]}]}}',
		),
		*/
	);
	$kpi_html_pieces = array();
	foreach ($kpis as $kpi)
	{
		$kpi_type = $kpi["type"];
		if ($kpi_type == "searchtaskinstances")
		{
			$kpi_title = $kpi["title"];
			$kpi_json = $kpi["json"];
			$kpi_url = "{$homeurl}?nxs=task-gui&page=searchtaskinstances&args_json={$kpi_json}";
			$kpi_html_pieces[] = "<a target='_blank' href='{$kpi_url}'>{$kpi_title}</a>";
		}
		else if ($kpi_type == "url")
		{
			$kpi_title = $kpi["title"];
			$kpi_url = $kpi["url"];
			$kpi_html_pieces[] = "<a target='_blank' href='{$kpi_url}'>{$kpi_title}</a>";
		}
		else
		{
		// not supported
		}
	}
	$kpi_html = implode(" | ", $kpi_html_pieces);
	?>
	KPI: <?php echo $kpi_html; ?><br />
	<a target='_blank' href='{$homeurl}?nxs=task-gui&page=testsupportv2'>Quick Support</a>
	|
	<a target='_blank' href='{$homeurl}?nxs=tracking-gui&page=go'>Tracking report</a>
	|
	<a target='_blank' href='{$homeurl}?nxs=task-gui&page=stringrepeater'>String repeater</a>
	
	</div>
	<div class="logocontainer">
	<img src="afnetix-logo.png">
	</div>
	<div class="titlecontainer">
	<h1 style="color: white;">List-o-tasks</h1>
	<span style="color: white;"><?php echo $date_human; ?></span>
	</div>
	
	<br />
	<?php
	}
}

function brk_tasks_gui_renderstateparametersforinstance($taskid, $taskinstanceid, $instancemeta)
{
	$reflectionmeta = brk_tasks_getreflectionmeta($taskid, $taskinstanceid);
	$required_fields = $reflectionmeta["required_fields"];
	?>
	<!-- -->
	<h2>State parameters</h2>
	<div>
		<a href='#' onclick="jQuery('#stateparameterscontainer').toggle();return false;">Show/Hide</a>
	</div>
	<div id='stateparameterscontainer' style='display:none;'>
		<table class='table-oddeven'>
		<?php
		$keystoexclude = array("nxs", "nxs_json_output_format", "createdby_taskid", "createdby_taskinstanceid", "taskid", "original_helpscoutticketid", "reinitiates_taskid", "reinitiates_taskinstanceid", "indent", "workflows_result_json", "incident_taskid", "incident_taskinstanceid", "assigned_to");
		$excludedkeys = array();
		
		$stateparameters = $instancemeta["stateparameters"];
		ksort($stateparameters);
		
		$atleastoneinputparameterrenders = false;
		
		foreach ($stateparameters as $key => $val)
		{
			if (in_array($key, $keystoexclude))
			{
				$excludedkeys[] = $key;
				continue;
			}
			else if (functions::stringstartswith($key, "taskinstructionresult_"))
			{
				$excludedkeys[] = $key;
				continue;
			}
			else if (functions::stringcontains($key, "_result_json_"))
			{
				$excludedkeys[] = $key;
				continue;
			}
			else if (functions::stringstartswith($key, "cond_wrap_state_"))
			{
				$excludedkeys[] = $key;
				continue;
			}
		
			$atleastoneinputparameterrenders = true;
			
			$isrequiredfield = in_array($key, $required_fields);
			
			if ($isrequiredfield)
			{
				echo "<td><span title='required input parameter'>&check;</span></td>";
			}
			else
			{
				echo "<td></td>";
			}
			echo "<td>{$key}</td>";
			
			if (false)
			{
			}
			else if ($key == "reported_by_email")
			{
				if ($val == "vanseijen@gmail.com")
				{
					echo "<td>{$val}<br /><img src='https://s3.amazonaws.com/devices.nexusthemes.com/%21authors/author_johan.jpg' /></td>";
				}
				else if ($val == "barkgj@gmail.com")
				{
					echo "<td>{$val}<br /><img src='https://s3.amazonaws.com/devices.nexusthemes.com/%21authors/author_gj.jpg' /></td>";
				}
				else
				{
					echo "<td>{$val}<br />(no img available)</td>";
				}
			}
			else if ($key == "plugin_slug")
			{
				$plugin_more_html = "<a target='_blank' href='https://wordpress.org/plugins/{$val}/'>open wordpress.org plugin repository</a>";
				echo "<td>$val<br />{$plugin_more_html}</td>";
			}
			/*
			else if ($key == "vendor_id")
			{
				$vendor_schema = "nxs.itil.configurationitems.vendor";
				$title = $nxs_g_modelmanager->getcontentmodelproperty("{$val}@{$vendor_schema}", "title");
				echo "<td>";
				echo "{$val}<br />";
				$a = array("modeluri" => "{$vendor_schema}@modelspreadsheet");
				$schema_properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
				$spreadsheet_url = $schema_properties["spreadsheet_url"];
				//
				echo "{$title}<br />";
				echo "model <a target='_blank' href='{$spreadsheet_url}'>{$vendor_schema}</a>";
				echo "</td>";
			}
			*/
			else if ($key == "siteid")
			{
				$vpsid = $stateparameters["vpsid"];
				$studio = $stateparameters["studio"];
				$siteid = $stateparameters[$key];
				if ($siteid != "" && $studio != "" && $vpsid != "")
				{
					$fetch_url = "{$homeurl}api/1/prod/global-site-meta-by-studiositeid/?nxs=hosting-api&nxs_json_output_format=prettyprint&vpsid={$vpsid}&studio={$studio}&siteid={$siteid}";
					$fetch_string = file_get_contents($fetch_url);
					$fetch_result = json_decode($fetch_string, true);
					$scheme = $fetch_result["sourceresult"]["site"]["scheme"];
					$domain = $fetch_result["sourceresult"]["site"]["domain"];
				}
				echo "<td>{$val}<br /><a target='_blank' href='{$scheme}://{$domain}'>{$scheme}://{$domain}</a></td>";
			}
			else if ($key == "facebook_origin_url")
			{
				echo "<td>";
				echo "<a target='_blank' href='{$val}'>{$val}</a>";
				echo "</td>";
			}
			else if ($key == "message")
			{
				echo "<td>";
				echo "<a target='_blank' href='{$homeurl}?nxs=task-gui&page=viewmessage&taskid={$taskid}&taskinstanceid={$taskinstanceid}#message'>View message</a> | ";
				echo "<a target='_blank' href='{$homeurl}?nxs=task-gui&page=viewmessage&taskid={$taskid}&taskinstanceid={$taskinstanceid}&template=2'>View message anonymized</a>";
				
				$references = brk_tasks_getreferencedtaskinstances($val);
				foreach ($references as $reference)
				{
					$ref_taskid = $reference["taskid"];
					$ref_taskinstanceid = $reference["taskinstanceid"];
					$ref_url = "{$homeurl}?nxs=task-gui&page=taskinstancedetail&taskid={$ref_taskid}&taskinstanceid={$ref_taskinstanceid}";
					echo "<br />";
					echo "<a target='_blank' href='$ref_url'>link to task instance to which this message refers</a><br />";
				}
				echo "</td>";
			}
			else if ($key == "non_expired_licenses" || $key == "expiredlicenses")
			{
				$pieces = explode(";", $val);
				$glued = implode("<br />", $pieces);
				echo "<td>" . $glued . "</td>";
			}
			else if (in_array($key, array("orderid", "checkout_orderglobalid")))
			{
				$order_api_url = "{$homeurl}api/1/prod/global-order-detail/?nxs=ecommerce-api&nxs_json_output_format=prettyprint&orderid={$val}";
				echo "<td>";
				echo "{$val}<br />";
				echo "<a target='_blank' href='{$order_api_url}'>Order detail (API)</a>";
				echo "</td>";
			}
			else if (in_array($key, array("attachments_json", "api_stateparameterstoappend_attachments_json")))
			{
				$items = json_decode($val, true);
				
				echo "<td>";
				echo "attachments:<br />";
				foreach ($items as $item)
				{
					$filename = $item["fileName"];
					$mimetype = $item["mimeType"];
					$size = $item["size"];
					$downloadurl = $item["webUrl"];
					echo "&bull; <a target='_blank' href='{$downloadurl}'>{$filename} ({$mimetype}) {$size} bytes</a><br />";
				}
				echo "</td>";
			}
			else if (in_array($key, array("sender_email", "newemail", "oldemail")))
			{
				$action_url = "https://my.nexusthemes.com/?email={$val}";
				$licenses_api_url = "https://license1802.nexusthemes.com/api/1/prod/licenses-by-email/?nxs=licensemeta-api&nxs_json_output_format=prettyprint&email={$val}";
				echo "<td>";
				echo "{$val}<br />";
				echo "<a target='_blank' href='{$action_url}'>Open portal for email</a> ";
				echo "| <a target='_blank' href='{$licenses_api_url}'>Open licenses api for email</a>";
				$ecommerce_api_url = "{$homeurl}api/1/prod/global-order-by-email/?nxs=ecommerce-api&nxs_json_output_format=prettyprint&email={$val}";
				echo "| <a target='_blank' href='{$ecommerce_api_url}'>Orders for email (API)</a>";
				$licenses_api_url = "https://license1802.nexusthemes.com/api/1/prod/licenses-by-email/?nxs=licensemeta-api&nxs_json_output_format=prettyprint&email={$val}";
				echo "| <a target='_blank' href='{$licenses_api_url}'>Licenses for email (API)</a>";
				echo "</td>";
			}
			/*
			else if ($key == "messageid")
			{
				$message_schema = "nxs.customerservice.message";
				$message_en = $nxs_g_modelmanager->getcontentmodelproperty("{$val}@{$message_schema}", "message_en");
				echo "<td>";
				echo "{$val}<br />";
				$a = array("modeluri" => "{$message_schema}@modelspreadsheet");
				$schema_properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
				$spreadsheet_url = $schema_properties["spreadsheet_url"];
				//
				echo "model <a target='_blank' href='{$spreadsheet_url}'>{$message_schema}</a><br />";
				echo "{$message_en}";
				echo "</td>";
			}
			*/
			/*
			else if ($key == "answerid")
			{
				$the_schema = "nxs.support.question";
				$a = array("modeluri" => "{$the_schema}@modelspreadsheet");
				$schema_properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
				$spreadsheet_url = $schema_properties["spreadsheet_url"];
				//
				echo "<td>";
				echo "{$val}<br />";
				echo "model <a target='_blank' href='{$spreadsheet_url}'>{$the_schema}</a><br />";
				echo "</td>";
			}
			*/
			/*
			else if ($key == "bugid")
			{
				$title_en = $nxs_g_modelmanager->getcontentmodelproperty("{$val}@nxs.support.bug", "title_en");
				$description_en = $nxs_g_modelmanager->getcontentmodelproperty("{$val}@nxs.support.bug", "description_en");
				$status = $nxs_g_modelmanager->getcontentmodelproperty("{$val}@nxs.support.bug", "status");
				echo "<td>{$val} ({$status})<br />{$title_en}<br />{$description_en}</td>";
			}
			*/
			else if ($key == "original_helpscoutticketnr" || $key == "helpscout_conversation_nr")
			{
				// note that "helpscout_conversation_nr" is obsolete, it should be original_helpscoutticketnr
				$helpscout_url = "https://secure.helpscout.net/search/?query={$val}";
				$conversationmetaapi_url = "{$homeurl}api/1/prod/get-conversation-meta-by-number/?nxs=helpscout-api&nxs_json_output_format=prettyprint&helpscoutnumber={$val}";
				
				$the_taskid = $stateparameters["taskid"];
				$the_taskinstanceid = $stateparameters["taskinstanceid"];
				$interpretconversation_api_url = "{$homeurl}api/1/prod/interpret-conversation/?nxs=helpscout-api&nxs_json_output_format=prettyprint&helpscoutnumber={$val}&taskid={$the_taskid}&taskinstanceid={$the_taskinstanceid}";
				
				echo "<td>{$val}<br />";
				echo "<a target='_blank' href='{$helpscout_url}'>Open helpscout</a>";
				echo " | ";
				echo "<a target='_blank' href='{$conversationmetaapi_url}'>Open conversation meta (API)</a>";
				
				echo " | ";
				echo "<a target='_blank' href='{$interpretconversation_api_url}'>Get interpreted (API)</a>";
				echo "</td>";
			}
			else if (functions::stringstartswith($key, "send_mail_template_result_json"))
			{
				echo "<td>";
				$val_result = json_decode($val, true);
				$flattened = nxs_array_flattenarray($val_result, $prefix = '', $seperator = "_");
				foreach ($flattened as $subkey => $subval)
				{
					if ($subkey == "mailresult_body")
					{
						$tunedsubval = $subval;
						$tunedsubval = str_replace("<img", "<disabledimg", $tunedsubval);
						echo "$subkey : $tunedsubval <br />";
					}
					else 
					{
						echo "$subkey : $subval <br />";
					}
				}
				echo "</td>";
			}
			else if ($key == "hostname")
			{
				$action_url = "{$homeurl}?nxs=task-gui&page=authenticate_to_hostname&hostname={$val}";
				
				echo "<td>{$val}<br />";
				echo "<a target='_blank' href='http://{$val}'>Open site</a> | ";
				echo "<a target='_blank' href='{$action_url}'>Login to site</a>"; 
				echo "</td>"; 
			}
			else
			{
				echo "<td style='display: block; max-height: 300px; overflow-y: scroll;'>" . htmlentities($val) . "</td>";
			}
			
			// copy / edit column
			echo "<td>";
			
			if (functions::stringcontains($val, "'") || functions::stringcontains($val, "\""))
			{
				echo "no&nbsp;copy&nbsp;available";
			}
			else
			{
				$tunedvalue = $val;
				//$tunedvalue = str_replace("\r\n", "CRLF", $tunedvalue);
				
				global $copycounter;
				$copycounter++;
				
				echo "<textarea style='display: none;' id='copycounter{$copycounter}'>$tunedvalue</textarea>";
				
				if ($copycounter == 1)
				{
					?>
					<script>
						function taskguicopyToClipboard(element) 
						{
							var $temp = element;
							$(element).show();
							var html = $(element).html();
							html = html.replace(/<br>/g, "\n"); // or \r\n
							console.log(html);
							$temp.val(html).select();
							document.execCommand("copy");
							$(element).hide();
						}
					</script>
					<?php
				}
				
				echo "<a href='#' onclick=\"var temp = $('#copycounter{$copycounter}'); taskguicopyToClipboard(temp); return false;\">copy</a>";
			}
			
			// allow editing of the field
			$editurl = "{$homeurl}?nxs=task-gui&page=taskinstanceeditfield&taskid={$taskid}&taskinstanceid={$taskinstanceid}&field={$key}";
			echo "&nbsp;|&nbsp;<a href='{$editurl}'>edit&nbsp;field</a>";
			
			// allow deleting of the field
			$deleteurl = "{$homeurl}?nxs=task-gui&page=taskinstancedeletefield&taskid={$taskid}&taskinstanceid={$taskinstanceid}&field={$key}";
			echo "&nbsp;|&nbsp;<a href='{$deleteurl}' target='_blank'>delete&nbsp;field</a>";
			
			echo "</td>";
			
			if (false)
			{
			//
			}
			else if ($key == "siteid")
			{
				$vpstitle = $stateparameters["vpstitle"];
				$studio = $stateparameters["studio"];
				$siteid = $stateparameters["siteid"];
				if ($studio != "" && $vpstitle != "")
				{
					$action_url = "{$homeurl}api/1/prod/global-site-meta-by-studiositeid/?nxs=hosting-api&nxs_json_output_format=prettyprint&vps_cname={$vpstitle}&studio={$studio}&siteid={$siteid}";
					echo "<td><a target='_blank' href='{$action_url}'>Site meta</a></td>"; 
					
					$action_url = "{$homeurl}?nxs=task-gui&page=authenticate_to_siteid&vpstitle={$vpstitle}&studio={$studio}&siteid={$siteid}";
					echo "<td><a target='_blank' href='{$action_url}'>Login to site</a></td>"; 
				}
			}
			else if (functions::stringstartswith($key, "workflows_result_json"))
			{
				$action_url = "{$homeurl}api/1/prod/run-workflows-for-taskinstance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
				echo "<td><a target='_blank' href='{$action_url}'>Rerun workflows</a></td>";
			}
			
			echo "</tr>";
		}
		
		if (count($excludedkeys) > 0)
		{
			echo "<tr>";
			echo "<td><i>hidden keys</i></td>";
			$pieces = array();
			foreach ($excludedkeys as $excludedkey)
			{
				$show_url = "{$homeurl}?nxs=task-gui&page=showinputparametervalue&taskid={$taskid}&taskinstanceid={$taskinstanceid}&key={$excludedkey}";
				$pieces[] = "<a target='_blank' href='{$show_url}'>{$excludedkey}</a>";
			}
			$excludedkeystring = implode(", ", $pieces);
			
			echo "<td><i>" . $excludedkeystring . "</i></td>";
			echo "</tr>";
		}
		?>
		</table>
		<?php
		if (!$atleastoneinputparameterrenders)
		{
			echo "No input parameters found.";
			// allow user to add a new input parameter
			$addfieldurl = "{$homeurl}?nxs=task-gui&page=taskinstanceeditfield&taskid={$taskid}&taskinstanceid={$taskinstanceid}&field=_new";
			echo " <a href='{$addfieldurl}' target='_blank'>Add first field</a><br />";
		}
		else
		{
			// allow user to add a new input parameter
			$addfieldurl = "{$homeurl}?nxs=task-gui&page=taskinstanceeditfield&taskid={$taskid}&taskinstanceid={$taskinstanceid}&field=_new";
			echo " <a href='{$addfieldurl}' target='_blank'>Add another field</a><br />";
		}
		?>
	</div>
	<?php
}

function brk_tasks_gui_authenticate_to_siteid()
{
	$vpstitle = $_REQUEST["vpstitle"];
	$studio = $_REQUEST["studio"];
	$siteid = $_REQUEST["siteid"];
	
	$fetch_url = "{$homeurl}api/1/prod/global-site-meta-by-studiositeid/?nxs=hosting-api&nxs_json_output_format=prettyprint&vps_cname={$vpstitle}&studio={$studio}&siteid={$siteid}";
	$fetch_string = file_get_contents($fetch_url);
	$fetch_result = json_decode($fetch_string, true);
	$licenseid = $fetch_result["sourceresult"]["licenseid"];
	
	$role = "administrator";
	
	if ($licenseid != "")
	{	
		$createtoken_url = "{$homeurl}api/1/prod/global-create-access-token-for-license/?nxs=hosting-api&nxs_json_output_format=prettyprint&licenseid={$licenseid}&role={$role}";
		$createtoken_string = file_get_contents($createtoken_url);
		$createtoken_data = json_decode($createtoken_string, true);
		$redirect_url = $createtoken_data["authenticationurl"];
		
		$redirectto = $licenseid;
	}
	else
	{
		// use hostname alternative instead
		echo "site is not connected to a license, unable to proceed";
		$hostname = $fetch_result["sourceresult"]["site"]["domain"];
		
		if ($hostname == "")
		{
			functions::throw_nack("not able to derive licenseid nor hostname");
		}
		
		$createtoken_url = "{$homeurl}api/1/prod/global-create-access-token-for-hostname/?nxs=hosting-api&nxs_json_output_format=prettyprint&hostname={$hostname}&role={$role}";
		$createtoken_string = file_get_contents($createtoken_url);
		$createtoken_data = json_decode($createtoken_string, true);
		$redirect_url = $createtoken_data["authenticationurl"];
		
		$redirectto = $hostname;
	}
	
	echo "Redirecting you to <a href='{$redirect_url}'>{$redirectto}</a>. Please be patient ...";
	
	?>
	<script>
	window.location='<?php echo $redirect_url; ?>';
	</script>
	<?php
	die();	
}

function brk_tasks_gui_studio_authenticate_to_license()
{
	$licenseid = $_REQUEST["licenseid"];
	$createtoken_url = "{$homeurl}api/1/prod/global-create-studio-access-token-for-license/?nxs=hosting-api&nxs_json_output_format=prettyprint&licenseid={$licenseid}&role=administrator";
	$createtoken_string = file_get_contents($createtoken_url);
	$createtoken_data = json_decode($createtoken_string, true);
	$redirect_url = $createtoken_data["authenticationurl"];
	echo "<a href='{$redirect_url}'>Redirecting you to {$licenseid} ...</a>";
	?>
	<script>
	window.location='<?php echo $redirect_url; ?>';
	</script>
	<?php
	die();
}

function brk_tasks_gui_authenticate_to_hostname()
{
	$hostname = $_REQUEST["hostname"];
	$role = $_REQUEST["role"];
	$createtoken_url = "{$homeurl}api/1/prod/global-create-access-token-for-hostname/?nxs=hosting-api&nxs_json_output_format=prettyprint&hostname={$hostname}&role={$role}";
	$createtoken_string = file_get_contents($createtoken_url);
	$createtoken_data = json_decode($createtoken_string, true);
	$redirect_url = $createtoken_data["authenticationurl"];
	echo "Redirecting you to  <a href='{$redirect_url}'>{$hostname}</a>. Please be patient ...";

	?>
	<script>
	window.location='<?php echo $redirect_url; ?>';
	</script>
	<?php
	die();
}

function brk_tasks_gui_require_authentication()
{
	$key = "user_id";
	
	if (isset($_REQUEST["logout"]) && $_REQUEST["logout"] == "true")
	{
		$session = brk_tasks_gui_getsession();
		$_SESSION = array();
		
		echo "you are now logged out<br />";
		echo "<a href='{$homeurl}?nxs=task-gui&page=workqueue'>{$homeurl}?nxs=task-gui&page=workqueue</a>";
		exit();
	}
	
	$session = brk_tasks_gui_getsession();
	if ($session[$key] == "")
	{
		$ip = $_SERVER['REMOTE_ADDR'];

		if (functions::stringstartswith($ip, "10.211.55"))
		{
			$userid = 1;
			$userbyip_result = brk_tasks_gui_getuserbyid($userid);

			brk_tasks_gui_setsessionvar($key, $userid);
			brk_tasks_gui_setsessionvar("user", $userbyip_result);
			$firstname = $userbyip_result["firstname"];
			echo "based n your ip we just authenticated you as {$firstname} (id: {$userid})<br />";
		}
		else
		{
			echo "<br /><br />";
			echo json_encode($userbyip_result);
			echo "<br /><br />";
			echo "unable to authenticate you; please add your ip ({$ip}) in the 'ips' column of <a href='https://docs.google.com/spreadsheets/d/1tyt-NCCnPVmIZItugwyC8U6yZumM_M2rVbwIKPTfRIA/edit#gid=0'>this table</a>";
			die();
		}
	}
	else
	{
		//echo "according to your session you are:<br />";
		//var_dump($session);
		//echo "<br />";
	}
}

function brk_tasks_gui_bootstrap()
{
	if ( $GLOBALS['pagenow'] === 'wp-login.php' ) 
	{
    	// We're on the login page!
		return;
	}
	if (is_admin())
	{
		return;
	}

	// escape all slashes that are posted
	functions::ensureslashesstripped();

	brk_tasks_gui_require_authentication();

	
	
	if (isset($_REQUEST["page"]))
	{
		$page = $_REQUEST["page"];
	}
	else
	{
		$page = "root";
	}

	$sanitized_page = "brk_tasks_gui_{$page}";
	// add more sanitization here as needed
	$extension_path = __DIR__ . "/{$sanitized_page}.php";
	if (file_exists($extension_path))
	{
		require_once($extension_path);
	}
	else
	{
		echo "not found; {$extension_path}";
		die();

		//
	}
	
	$functionnametoinvoke = "brk_tasks_gui_{$page}";
	if (function_exists($functionnametoinvoke))
	{
		$args = array();
		$subresult = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		echo "file: $extension_path<br /><br />";
		echo "function not yet implemented;<br /><br />";
		echo "<div style='margin-left: 50px; font-family: courier; background-color: #eee;'>";
		echo "function $functionnametoinvoke()<br />";
		echo "{<br />";
		echo "&nbsp;&nbsp;// ... to be implemented<br />";
		echo "}<br />";
		echo "</div>";
		echo "<br />";
		echo "<br />";
	}
	
	echo ":)";
	die();
}

function brk_tasks_batch_sessiondurationinhours()
{
	return 24;
}

function brk_tasks_batch_setsession_gettransientkey($batchsessionidentifier)
{
	$result = "taskinstance_batch_{$batchsessionidentifier}";
	return $result;
}

function brk_tasks_batch_getsession($batchsessionidentifier)
{
	$transient_key = brk_tasks_batch_setsession_gettransientkey($batchsessionidentifier);
	$result = get_transient($transient_key);
	if ($result == "")
	{
	$result = array();
	}
	return $result;
}

function brk_tasks_batch_getsession_inputparametervalue($batchsessionidentifier, $key)
{
	$session = brk_tasks_batch_getsession($batchsessionidentifier);
	$result = $session[$key];
	return $result;
}

function brk_tasks_batch_setsession_inputparametervalue($batchsessionidentifier, $key, $value)
{
	$transient_key = brk_tasks_batch_setsession_gettransientkey($batchsessionidentifier);
	$batchsession = brk_tasks_batch_getsession($batchsessionidentifier);
	$batchsession[$key] = $value;
	set_transient($transient_key, $batchsession, 60*60*24);	// remains valid for 24 hours
}


function brk_tasks_batch_render_instruction_for_input_of_information($batchsessionidentifier, $configuration)
{
	$instruction = $configuration["instruction"];
	$key = $configuration["key"];
	$options = $configuration["options"];
	
	$value = brk_tasks_batch_getsession_inputparametervalue($batchsessionidentifier, $key);
	if ($value == "")
	{
	echo "<h1>Batch Processing configuration</h1>";
	echo "{$instruction}<br />";
	
	foreach ($options as $option)
	{
	$label = $option["label"];
	$value = $option["value"];
	?>
	<form action='<?php echo functions::geturlcurrentpage(); ?>' method='POST'>
	<input type='hidden' name='batchaction' value='updateproperty' />
	<input type='hidden' name='key' value='<?php echo $key; ?>' />
	<input type='hidden' name='value' value='<?php echo $value; ?>' />
	<input type='submit' value='<?php echo $label; ?>' />
	</form>
	<?php
	}
	echo "<br />:)";
	die();
	// 
	}
}

function brk_tasks_batch_gettaskinstancesforbatchprocessing($taskid, $state, $required_input_parameters = array())
{
	$subconditions = array
	(
	array
	(
	"type" => "true_if_task_has_required_taskid",
	"required_taskid" => $taskid,
	),
	array
	(
	"type" => "true_if_in_any_of_the_required_states",
	"any_of_the_required_states" => array($state),
	),
	array
	(
	"type" => "true_if_taskinstance_notyetassignedtouser",
	),
	);
	
	foreach ($required_input_parameters as $key => $val)
	{
	$subconditions[] = array
	(
	"type" => "true_if_inputparameter_has_required_value_for_key",
	"key" => $key,
	"required_value" => $val,
	);
	}
	
	$search_args = array
	(
		"if_this" => array
		(
			"type" => "true_if_each_subcondition_is_true",
			"subconditions" => $subconditions,
		),
		"return_this" => "details",
	);
	$result = tasks::searchtaskinstances($search_args);
	return $result;
}

function brk_tasks_gui_renderproxyimg()
{
	$url = $_REQUEST["url"];
	$imginfo = getimagesize( $url );
	
	header("Content-type: ".$imginfo['mime']);
	echo readfile( $url );
	die();
}

function brk_tasks_batch_getmaxexecutiontimebeforereturningwebresults($batchsessionidentifier)
{
	$maxexecutiontimebeforereturningwebresults = brk_tasks_batch_getsession_inputparametervalue($batchsessionidentifier, "maxexecutiontimebeforereturningwebresults");
	if ($maxexecutiontimebeforereturningwebresults == "")
	{
	$maxexecutiontimebeforereturningwebresults = 20;
	}
	return $maxexecutiontimebeforereturningwebresults;
}

function brk_tasks_play_alarm_audio_in_browser()
{
	?>
	<script>
	window.onload = function() {
	var context = new AudioContext();
	}	
	</script>
	
	<audio autoplay>
	<source src="https://s3.amazonaws.com/%21resources/audio/alarm.mp3">
	</audio>
	
	<span style='background-color: red; color: white;'>PLAYING ALARM MP3</span>
	
	<!--
	<iframe src="https://s3.amazonaws.com/%21resources/audio/alarm.mp3" allow="autoplay"></iframe><br />
	-->
	<?php
}

function brk_tasks_gui_showinputparametervalue()
{
	brk_tasks_gui_render_head();
	
	$key = $_REQUEST["key"];
	
	$taskid = $_REQUEST["taskid"];
	if ($taskid == "") { functions::throw_nack("taskid not specified"); }
	
	$taskinstanceid = $_REQUEST["taskinstanceid"];
	if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not specified (1)"); }
	
	$stateparameters = tasks::gettaskinstancestateparameters($taskid, $taskinstanceid);
	$value = $stateparameters[$key];
	
	$jsonvalue = json_decode($value, true);
	if ($jsonvalue != "")
	{
	echo "key: {$key}<br />";
	echo "json:<br />";
	echo "<table>";
	//var_dump($jsonvalue);
	foreach ($jsonvalue as $jsonkey => $jsonval)
	{
	echo "<tr>";
	echo "<td>{$jsonkey}</td>";
	echo "<td>";
	if (is_array($jsonval))
	{
	echo "Composite value:<br />";
	echo "<table>";
	foreach ($jsonval as $i => $v)
	{
	echo "<tr>";
	echo "<td>{$i}</td>";
	echo "<td>";
	
	var_dump($v);
	echo "</td>";
	echo "</tr>";
	}
	echo "</table>";
	}
	else
	{
	echo htmlentities($jsonval);
	}
	echo "</td>";
	echo "</tr>";
	}
	echo "</table>";
	}
	else
	{
	echo "key: {$key}<br />";
	echo "value:<br />";
	echo htmlentities($stateparameters[$key]);
	}
	
	echo "<br />";
	echo "<br />";
}

function brk_tasks_gui_getuserbyid($userid)
{
	$result = array
	(
		"id" => $userid,
		"firstname" => "Gert-Jan"
	);
	return $result;
}

function brk_tasks_gui_whichtaskinstancesdidyouendrecently()
{
	brk_tasks_gui_render_head();
	brk_tasks_gui_rendernavigation();	
	
	// hardcodes for now ...
	$users = array
	(
		brk_tasks_gui_getuserbyid(1)
	);

	foreach ($users as $user)
	{
		$currentuserid = $user["id"];
		$currentfirstname = $user["firstname"];
		$currenturl = functions::geturlcurrentpage();
		$action_url = $currenturl;
		$action_url = functions::addqueryparametertourl($action_url, "user_id", $currentuserid, true, true);
		$html_pieces[] = "<a href='{$action_url}'>view {$currentfirstname}</a>";
	}
	echo implode(" | ", $html_pieces);
	echo "<br />";
	
	$html_pieces = array();
	$possiblehours = array(4, 8, 16, 32);
	foreach ($possiblehours as $possiblehour)
	{
	$currenturl = functions::geturlcurrentpage();
	$action_url = $currenturl;
	$action_url = functions::addqueryparametertourl($action_url, "hours", $possiblehour, true, true);
	$html_pieces[] = "<a href='{$action_url}'>view {$possiblehour} hours</a>";
	}
	echo implode(" | ", $html_pieces);
	echo "<br />";
	
	echo "-----------<br />";
	
	$hours = $_REQUEST["hours"];
	if ($hours == "") { $hours = 16; }
	echo "hours: {$hours}<br />";
	
	$user_id = $_REQUEST["user_id"];
	if ($user_id == "") { $user_id = brk_tasks_gui_getuseridcurrentuser(); }
	
	echo "user_id: {$user_id}<br />";
	
	
	
	echo "-------<br />";
	
	// ENDED ONES
	if (true)
	{
	$subconditions = array();
	
	$subconditions[] = array
	(
	"type" => "true_if_in_any_of_the_required_states",
	"any_of_the_required_states" => array("ENDED")
	);
	
	$subconditions[] = array
	(
	"type" => "true_if_assigned_to_any_of_the_required_users",
	"any_of_the_required_users" => array($user_id)
	);
	
	
	$subconditions[] = array
	(
	"type" => "true_if_ended_within_number_of_hours_ago",
	"within_number_of_hours_ago" => $hours
	);
	
	
	$search_args = array
	(
	"if_this" => array
	(
	"type" => "true_if_each_subcondition_is_true",
	"subconditions" => $subconditions,
	),
	);
	
	$taskinstances_wrap = tasks::searchtaskinstances($search_args);
	$taskinstances = $taskinstances_wrap["taskinstances"];
	
	$count = count($taskinstances);
	if ($count > 0)
	{
	echo "In the last {$hours} hours user {$user_id} ended the following {$count} task instances that were assigned to him/her<br />";
	?>
	<?php
	foreach ($taskinstances as $taskinstance)
	{
	$taskid = $taskinstance["taskid"];
	$url = $taskinstance["url"];
	$tasktitle = tasks::gettasktitle($taskid);
	echo "<a target='_blank' href='{$url}'>$taskid - $tasktitle</a><br />";
	}
	}
	else
	{
	echo "None task instances found<br />";
	}
	}
	
	echo "-------<br />";
	
	// ABORTED ONES
	if (true)
	{
	$subconditions = array();
	
	$subconditions[] = array
	(
	"type" => "true_if_in_any_of_the_required_states",
	"any_of_the_required_states" => array("ABORTED")
	);
	
	$subconditions[] = array
	(
	"type" => "true_if_assigned_to_any_of_the_required_users",
	"any_of_the_required_users" => array($user_id)
	);
	
	
	$subconditions[] = array
	(
	"type" => "true_if_aborted_within_number_of_hours_ago",
	"within_number_of_hours_ago" => $hours
	);
	
	
	$search_args = array
	(
	"if_this" => array
	(
	"type" => "true_if_each_subcondition_is_true",
	"subconditions" => $subconditions,
	),
	);
	
	$taskinstances_wrap = brk_tasks_searchtaskinstances($search_args);
	$taskinstances = $taskinstances_wrap["taskinstances"];
	
	$count = count($taskinstances);
	if ($count > 0)
	{
	echo "In the last {$hours} hours user {$user_id} aborted the following {$count} task instances that were assigned to him/her<br />";
	?>
	<?php
	foreach ($taskinstances as $taskinstance)
	{
	$taskid = $taskinstance["taskid"];
	$url = $taskinstance["url"];
	$tasktitle = tasks::gettasktitle($taskid);
	echo "<a target='_blank' href='{$url}'>$taskid - $tasktitle</a><br />";
	}
	}
	else
	{
	echo "None task instances found<br />";
	}
	}
}

function brk_tasks_gui_createoffspringtaskinstance()
{
	$ancestor_taskid = $_REQUEST["ancestor_taskid"];
	if ($ancestor_taskid == "")
	{
	functions::throw_nack("ancestor_taskid not set");
	}
	$ancestor_taskinstanceid = $_REQUEST["ancestor_taskinstanceid"];
	if ($ancestor_taskinstanceid == "")
	{
	functions::throw_nack("ancestor_taskinstanceid not set");
	}
	
	// step 1; create instance of 38
	$taskid = 38;
	$action_url = "{$homeurl}api/1/prod/create-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint";
	
	$action_url = functions::addqueryparametertourl($action_url, "taskid", $taskid, true, true);
	$action_url = functions::addqueryparametertourl($action_url, "createdby_taskid", $ancestor_taskid, true, true);
	$action_url = functions::addqueryparametertourl($action_url, "createdby_taskinstanceid", $ancestor_taskinstanceid, true, true);
	
	$action_string = file_get_contents($action_url);
	$action_result = json_decode($action_string, true);
	if ($action_result["result"] != "OK") { functions::throw_nack("unable to create task instance; $action_url"); }
	
	$newlycreatedtaskinstanceid = $action_result["taskinstanceid"];
	
	$start_instance_url = "{$homeurl}?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$newlycreatedtaskinstanceid}";
	
	?>
	<script>
	window.location='<?php echo $start_instance_url; ?>';
	</script>
	<?php
	
	die();
}

// {$homeurl}?nxs=task-gui&page=tasktoworkflow&taskid=376
function brk_tasks_gui_tasktoworkflow()
{
	brk_tasks_gui_render_head();
	brk_tasks_gui_rendernavigation();	
	
	echo "<h1>tasktoworkflow</h1>";
	// todo; request parameter taskid
	
	$taskid = $_REQUEST["taskid"];
	$txt = tasks::gettaskrecipe($taskid);
	$lines = explode("\n", $txt);
	$linenr = 0;
	$lastknowncomment = "";
	foreach ($lines as $line)
	{
		$linenr++;
		
		if (!functions::stringcontains($line, "nxs_p001_task_instruction"))
		{
			$line = str_replace("*", "", $line);
			$lastknowncomment = trim($line);
		}
		else
		{
			//echo "line: $linenr<br />";
			$line = str_replace("*", "", $line);
			$line = trim($line);
			$line = str_replace("nxs_p001_task_instruction", "nxs_taskinstructiontoworkflow comments=\"{$lastknowncomment}\" ", $line);
			$line = do_shortcode($line);
			echo $line . "<br />";
		}
	}
	
	die();
}

//error_reporting(E_ERROR);
//error_reporting(E_ALL);

add_action( 'init', 'brk_tasks_process_task_request', 1000, 0);
function brk_tasks_process_task_request()
{
	$brk = $_REQUEST["brk"];
	var_dump($brk);
	
	if ($_REQUEST["brk"] != null && functions::stringendswith($_REQUEST["brk"], "-api"))
	{
		require_once("brk-api-dispatcher.php");
		die();
	}
	else
	{
		
	}
	
	brk_tasks_gui_bootstrap();
}


/*
function brk_tasks_gui_authenticate_to_license()
{
	$licenseid = $_REQUEST["licenseid"];
	$createtoken_url = "{$homeurl}api/1/prod/global-create-access-token-for-license/?nxs=hosting-api&nxs_json_output_format=prettyprint&licenseid={$licenseid}&role=administrator";
	$createtoken_string = file_get_contents($createtoken_url);
	$createtoken_data = json_decode($createtoken_string, true);
	$redirect_url = $createtoken_data["authenticationurl"];
	echo "<a href='{$redirect_url}'>Please click here to authenticate</a>";
	?>
	<script>
	window.location='<?php echo $redirect_url; ?>';
	</script>
	<?php
	die();
}
*/