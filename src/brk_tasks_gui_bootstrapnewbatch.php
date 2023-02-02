<?php

function brk_tasks_gui_bootstrapnewbatch()
{
	$batchsessionidentifier = brk_tasks_batch_createbatch();
	
	$taskid = brk_tasks_batch_getsession_inputparametervalue($batchsessionidentifier, "taskid");
	$batchsize = brk_tasks_batch_getsession_inputparametervalue($batchsessionidentifier, "batchsize");
	$items_json = brk_tasks_batch_getsession_inputparametervalue($batchsessionidentifier, "items_json");
	
	for ($segment = 0; $segment < $batchsize; $segment++)
	{
	// show links to start the each segment
	$startindex = $segment;
	$action_url = "https://tasks.bestwebsitetemplates.net/?nxs=task-gui&page=batchsegmentexecutor&batchsessionidentifier={$batchsessionidentifier}&index={$startindex}";
	
	$target = "";
	if ($batchsize > 1)
	{
	$target = "_blank";
	}
	
	echo "<a target='{$target}' href='{$action_url}'>Start segment $segment for taskid $taskid</a><br />";	
	}
	
	echo "<br />";
	echo "<br />";
	echo "Task instances processed in this batch are:<br />";
	
	
	// show here which task ids and instances are included in this batch
	/*
	echo "<br /><br />";
	echo json_encode($items);
	echo "<br /><br />";
	*/
	
	echo "<table>";
	$items = json_decode($items_json, true);
	
	
	foreach ($items["taskinstances"] as $item)
	{
	$taskid = $item["taskid"];
	$taskinstanceid = $item["taskinstanceid"];
	echo "<tr><td>{$taskid}</td><td>{$taskinstanceid}</td></tr>";
	}
	echo "</table>";
	
	die();
}


function brk_tasks_gui_batchsegmentexecutor()
{
	//
	$batchsessionidentifier = $_REQUEST["batchsessionidentifier"];
	if ($batchsessionidentifier == "")
	{
	functions::throw_nack("batchsessionidentifier not set");
	}
	
	// HANDLE BATCH ACTIONS (FOR INTERACTING WITH THE META LEVEL OF THE BATCH, LIKE PAUSING, UNPAUSING, ETC.)
	if (true)
	{
	$batchaction = $_REQUEST["batchaction"];
	if ($batchaction != "")
	{
	if (false)
	{
	//
	}
	else if ($batchaction == "pause")
	{
	brk_tasks_batch_setsession_inputparametervalue($batchsessionidentifier, "runtimeexecutionbehaviour", "paused");
	echo "the batch execution is now paused (existing executions will process till finished, but no new executions will execute until you unpause it<br />";
	
	$currenturl = functions::geturlcurrentpage();
	$unpause_url = $currenturl;
	$unpause_url = functions::addqueryparametertourl($unpause_url, "batchaction", "unpause", true, true);
	echo "to proceed with the execution, unpause the runtime execution first by clicking <a target='_blank' href='{$unpause_url}'>here</a><br />";
	
	die();
	}
	else if ($batchaction == "unpause")
	{
	brk_tasks_batch_setsession_inputparametervalue($batchsessionidentifier, "runtimeexecutionbehaviour", "");
	echo "the batch execution is now unpaused<br />";
	
	$currenturl = functions::geturlcurrentpage();
	$pause_url = $currenturl;
	$pause_url = functions::addqueryparametertourl($pause_url, "batchaction", "pause", true, true);
	echo "to pause the execution click <a target='_blank' href='{$pause_url}'>here</a><br /><br />";
	die();
	}
	else
	{
	functions::throw_nack("unsupported batchaction; $batchaction");
	}
	}
	}
	
	$countitemsprocessedinrequest = 0;
	$index = $_REQUEST["index"];
	
	$items_json = brk_tasks_batch_getsession_inputparametervalue($batchsessionidentifier, "items_json");
	$items = json_decode($items_json, true);
	
	$maxexecutiontimebeforereturningwebresults = brk_tasks_batch_getmaxexecutiontimebeforereturningwebresults($batchsessionidentifier);
	echo "Processing index {$index} (or perhaps also upcoming indexes if execution takes less than {$maxexecutiontimebeforereturningwebresults} secs)<br />";
	
	// to reduce the number of web invocations and to speeds things up,
	// we can run multiple indexes with this request before returning the result
	
	$starttime = microtime(true);
	$proceedprocessing = true;
	while ($proceedprocessing)
	{
	$runtimeexecutionbehaviour = brk_tasks_batch_getsession_inputparametervalue($batchsessionidentifier, "runtimeexecutionbehaviour");
	if (false)
	{
	}
	else if ($runtimeexecutionbehaviour == "paused")
	{
	brk_tasks_play_alarm_audio_in_browser();
	
	$currenturl = functions::geturlcurrentpage();
	$unpause_url = $currenturl;
	$unpause_url = functions::addqueryparametertourl($unpause_url, "batchaction", "unpause", true, true);
	echo "unable to proceed<br />";
	echo "runtime execution is paused<br />";
	echo "to proceed with the execution, unpause the runtime execution first by clicking <a target='_blank' href='{$unpause_url}'>here</a><br />";
	
	$currenturl = functions::geturlcurrentpage();
	$action_url = $currenturl;
	$action_url = functions::addqueryparametertourl($action_url, "index", $index, true, true);
	
	echo "to proceed with the current index this link;<br />";
	echo "<a href='{$action_url}'>process next item</a>";
	
	die();
	}
	else if ($runtimeexecutionbehaviour == "")	// running
	{
	$currenturl = functions::geturlcurrentpage();
	$pause_url = $currenturl;
	$pause_url = functions::addqueryparametertourl($pause_url, "batchaction", "pause", true, true);
	echo "runtime execution is active<br />";
	echo "to pause the execution click <a target='_blank' href='{$pause_url}'>here</a><br /><br />";
	// process one item
	if (true)
	{
	$numberofitems = count($items["taskinstances"]);
	if ($index >= $numberofitems)
	{
	brk_tasks_play_alarm_audio_in_browser();
	// 
	echo "entire batch was processed succesfully ($index / $numberofitems)";
	die();
	}
	
	$item = $items["taskinstances"][$index];
	
	// 
	$taskid = $item["taskid"];
	$taskinstanceid = $item["taskinstanceid"];
	
	if ($taskid == "")
	{
	echo "unable to proceed; taskid not set?";
	die();
	}
	if ($taskinstanceid == "")
	{
	echo "unable to proceed; taskinstanceid not set?";
	die();
	}
	
	$stateparameters = tasks::gettaskinstancestateparameters($taskid, $taskinstanceid);
	$workflows_result_json = $stateparameters["workflows_result_json"];
	$didalreadyprocesthisindex = $workflows_result_json != "";
	
	if (true)
	{
	echo "about to execute index $index for batch $batchsessionidentifier<br />";
	echo "taskid $taskid taskinstanceid $taskinstanceid<br />";
	echo "result of applying workflows;<br /><br />";
	$workflow_result = nxs_workflow_run($taskid, $taskinstanceid);
	$workflows_result_json = json_encode($workflow_result);
	
	// store the output in a transient, or perhaps in the task instance? (dependong on how big it is?)
	// probably storing the result should be part of running the workflow
	brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "workflows_result_json", $workflows_result_json);
	
	echo $workflows_result_json;
	
	echo "<br /><br />thats it for this instance<br />----<br /><br />";
	
	$countitemsprocessedinrequest++;
	}
	}
	
	$batchsize = brk_tasks_batch_getsession_inputparametervalue($batchsessionidentifier, "batchsize");
	$nextindex = $index + $batchsize;
	
	$end = microtime(true);
	$delta = $end - $starttime;
	
	$maxexecutiontimebeforereturningwebresults = brk_tasks_batch_getmaxexecutiontimebeforereturningwebresults($batchsessionidentifier);
	
	if ($delta > $maxexecutiontimebeforereturningwebresults)
	{
	// more than X seconds? return content
	$proceedprocessing = false;
	break;
	}
	else
	{
	// proceed with the loop
	$index = $nextindex;
	}
	}
	else
	{
	functions::throw_nack("unsupported runtimeexecutionbehaviour; $runtimeexecutionbehaviour");
	}
	}
	
	echo "<br />";
	echo "Stats:";
	echo "Processing of {$countitemsprocessedinrequest} items took {$delta} secs<br />";
	
	$timeforprocessingoneunit = ceil($delta / $countitemsprocessedinrequest);
	echo "Estimated processing time for one task instance; $timeforprocessingoneunit<br />";
	
	$batchsize = brk_tasks_batch_getsession_inputparametervalue($batchsessionidentifier, "batchsize");
	$maxindex = count($items["taskinstances"]) - 1;
	$itemsleft = $maxindex - (($index / $maxindex) * $maxindex);
	$itemsleftinsegment = $itemsleft / $batchsize;
	$esttimeleftforremainingitemsinsegment = $timeforprocessingoneunit * $itemsleftinsegment;
	$esttimelefthuman = nxs_time_getsecondstohumanreadable($esttimeleftforremainingitemsinsegment);
	
	echo "Estimated processing time left for remaining items in segment of batch; $esttimelefthuman<br />";
	
	echo "<br />";
	echo "<br />";
	
	$currenturl = functions::geturlcurrentpage();
	$action_url = $currenturl;
	$action_url = functions::addqueryparametertourl($action_url, "index", $nextindex, true, true);
	
	$autoproceed = true;
	if (!$autoproceed)
	{
	echo "to proceed with the next segmented item in the batch use this link;<br />";
	echo "<a href='{$action_url}'>process next item</a>";
	}	
	else
	{
	?>
	auto reloading the next one ... hold on ...
	<script>
	window.location = "<?php echo $action_url; ?>";
	</script>
	<?php
	}
	
	die();
}

function brk_tasks_batch_createbatch()
{
	// BOOTSTRAP BATCH SESSION
	if (true)
	{
	$batchsessionidentifier = $_REQUEST["batchsessionidentifier"];
	if ($batchsessionidentifier == "")
	{
	echo "please select an existing batchsession or start a new batchsession<br />";
	$next_url = functions::geturlcurrentpage();
	$next_url = functions::addqueryparametertourl($next_url, "batchsessionidentifier", time(), true, true);
	echo "<div style='color: #999;'>";
	echo "existing batch session list:";
	echo "no existing items found (to be implemented)<br />";
	echo "<div>";
	$hours = brk_tasks_batch_sessiondurationinhours();
	echo "<a href='{$next_url}'>start batch session (remains valid for {$hours} hours)</a><br />";
	echo "<br />so far :)";
	die();
	}
	}
	
	// HANDLE BATCH ACTIONS (FOR CONSTRUCTION THE PROPER INFORMATION IN THE BATCH SESSION)
	if (true)
	{
	$batchaction = $_REQUEST["batchaction"];
	if ($batchaction != "")
	{
	if (false)
	{
	//
	}
	else if ($batchaction == "updateproperty")
	{
	$key = $_REQUEST["key"];
	$value = $_REQUEST["value"];
	
	brk_tasks_batch_setsession_inputparametervalue($batchsessionidentifier, $key, $value);
	}
	else
	{
	functions::throw_nack("unsupported batchaction; $batchaction");
	}
	}
	}
	
	// COMPILE ITEMS FILTER AS DEFINED BY USER
	if (true)
	{
	// TASKID
	if (true)
	{
	$schema = "nxs.p001.businessprocess.task";
	global $nxs_g_modelmanager;
	$a = array
	(
	"singularschema" => $schema,
	);
	$allentries = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels($a);
	
	foreach ($allentries as $entry)
	{
	$taskid = intval($entry["nxs.p001.businessprocess.task_id"]);
	$workflows_availability_state = brk_tasks_get_workflows_availability_state_for_task($taskid);
	$validaccordingtotaskfilter = true;
	if ($_REQUEST["taskid"] != "")
	{
	if ($_REQUEST["taskid"] != $taskid)
	{
	$validaccordingtotaskfilter = false;
	}
	}
	
	if ($validaccordingtotaskfilter)
	{
	if (false)
	{
	//
	}
	else if ($workflows_availability_state == "PRODUCTION")
	{
	$ids_having_workflows_available[] = $taskid;
	$taskmetabyid[$taskid] = $entry["title"];
	}
	else if ($workflows_availability_state == "DEVELOPMENT")
	{
	$ids_indevelopment[] = $taskid;
	}
	}
	}
	
	ksort($taskmetabyid);
	
	$options = array();
	// add the set of all taskids at the top
	if (count($ids_having_workflows_available) > 1)
	{
	$ids_text = implode(", ", $ids_having_workflows_available);
	$ids = implode(";", $ids_having_workflows_available);
	$options[] = array
	(
	"label" => "each task supporting workflows ({$ids_text})",
	"value" => $ids,
	);
	}
	// add each individual taskid
	foreach ($taskmetabyid as $id => $title)
	{
	$options[] = array
	(
	"label" => "{$id} - {$title}",
	"value" => $id,
	);
	}
	
	
	
	$configuration = array
	(
	"key" => "taskid",
	"instruction" => "Select the task for this batch",
	"options" => $options,
	);
	
	if (count($ids_indevelopment) > 0)
	{
	echo "the following ids are in development (set processing_type to 'automated' in nxs.p001.businessprocess.task to enable them);<br />";
	echo implode(", ", $ids_indevelopment);
	echo "<br />";
	}
	
	brk_tasks_batch_render_instruction_for_input_of_information($batchsessionidentifier, $configuration);
	}
	
	// ITEMS
	if (true)
	{
	$options = array();
	
	$taskid = brk_tasks_batch_getsession_inputparametervalue($batchsessionidentifier, "taskid");
	
	// 
	$items = brk_tasks_batch_gettaskinstancesforbatchprocessing($taskid, "CREATED");
	$items_json = json_encode($items);
	$count = count($items["taskinstances"]);
	$options[] = array
	(
	"label" => "Set of task instances having taskid {$taskid} and state CREATED (count: {$count})",
	"value" => "{$items_json}",
	);
	
	// 
	$items = brk_tasks_batch_gettaskinstancesforbatchprocessing($taskid, "STARTED");
	$items_json = json_encode($items);
	$count = count($items["taskinstances"]);
	$options[] = array
	(
	"label" => "Set of task instances having taskid {$taskid} and state STARTED (count: {$count})",
	"value" => "{$items_json}",
	);
	
	$configuration = array
	(
	"key" => "items_json",
	"instruction" => "Select the set of instances for this batch",
	"options" => $options,
	);
	
	brk_tasks_batch_render_instruction_for_input_of_information($batchsessionidentifier, $configuration);
	}
	
	// BATCHSIZE
	if (true)
	{
	$configuration = array
	(
	"key" => "batchsize",
	"instruction" => "Select the batchsize for this batch",
	"options" => array
	(
	array
	(
	"label" => "1",
	"value" => "1",
	),
	array
	(
	"label" => "3",
	"value" => "3",
	),
	)
	);
	
	brk_tasks_batch_render_instruction_for_input_of_information($batchsessionidentifier, $configuration);
	}
	
	// MAXEXECUTIONTIMEBEFORERETURNINGWEBRESULTS
	if (true)
	{
	$configuration = array
	(
	"key" => "maxexecutiontimebeforereturningwebresults",
	"instruction" => "Select the max execution time in secs before the executor should return the webresults",
	"options" => array
	(
	array
	(
	"label" => "10",
	"value" => "10",
	),
	array
	(
	"label" => "30",
	"value" => "30",
	),
	)
	);
	
	brk_tasks_batch_render_instruction_for_input_of_information($batchsessionidentifier, $configuration);
	}
	}
	
	$result = $batchsessionidentifier;
	
	return $result;
}


