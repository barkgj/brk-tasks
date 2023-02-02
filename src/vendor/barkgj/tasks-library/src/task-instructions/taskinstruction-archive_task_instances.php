<?php

function brk_tasks_instance_do_archive_task_instances($then_that_item, $taskid, $taskinstanceid)
{
	$instancemeta = brk_tasks_getinstance($taskid, $taskinstanceid);
	$inputparameters = $instancemeta["inputparameters"];
	
  $result = array();
  
  $archive_taskid = $inputparameters["archive_taskid"];
  if ($archive_taskid == "")
  {
  	$result["result"] = "NACK";
  	$result["nack_details"] = "archive_taskid not set";
  	return $result;
  }
  
  $result["console"][] = "About to archive {$archive_taskid}";
  
  $archive_result = brk_tasks_archive_taskinstances($archive_taskid);
  $count_processed_archived_items = $archive_result["count_processed_archived_items"];
  
	$result["console"][] = "Archived {$count_processed_archived_items} taskinstances";
  
  //
  //
  //
  
  $result["result"] = "OK";
  
  return $result;
}