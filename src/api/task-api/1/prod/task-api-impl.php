<?php

// TEST URL:
// https://tasks.example.org/api/1/prod/get-task-by-title/?nxs=task-api&nxs_json_output_format=prettyprint&title=TITLE
// https://tasks.example.org/api/1/prod/create-task-instance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=39&foo=bar
// https://tasks.example.org/api/1/prod/start-task-instance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=bp1&instance_context=123
// https://tasks.example.org/api/1/prod/sleep-task-instance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=bp1&instance_context=123
// https://tasks.example.org/api/1/prod/abort-task-instance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=bp1&instance_context=123
// https://tasks.example.org/api/1/prod/end-task-instance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=bp1&instance_context=123
// https://tasks.example.org/api/1/prod/assign-employee-to-task-instance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=&taskinstanceid=&employeeid=1
// https://tasks.example.org/api/1/prod/view-task-stats/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=1
// https://tasks.example.org/api/1/prod/view-tasks-stats/?nxs=task-api&nxs_json_output_format=prettyprint
// https://tasks.example.org/api/1/prod/get-task-instance-meta/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=72&taskinstanceid=...
// https://tasks.example.org/api/1/prod/get-rfcs-for-task/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=89
// https://tasks.example.org/api/1/prod/setup-new-task/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=102
// https://tasks.example.org/api/1/prod/search-task-instances/?nxs=task-api&nxs_json_output_format=prettyprint&args_json=
// https://tasks.example.org/api/1/prod/search-deep-task-instances/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=538&limit=1


// https://tasks.example.org/api/1/prod/list-taskoutcomepredictions/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=138
// https://tasks.example.org/api/1/prod/execute-task-instance-through-rpa/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=39&taskinstanceid=F13BAE28-CA67-7295-2465-CA4B1D03A9C9
// https://tasks.example.org/api/1/prod/add-note-to-task-instance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=&taskinstanceid=&text=Testing 1,2,3&author_employeeid=

// https://tasks.example.org/api/1/prod/set-task-instance-input-parameter/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=&taskinstanceid=&key=testkey&val=testval

// https://tasks.example.org/api/1/prod/run-workflows-for-taskinstance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=&taskinstanceid=
// https://tasks.example.org/api/1/prod/reflect-task/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=
// https://tasks.example.org/api/1/prod/ensure-automated-task-instances-are-being-processed-in-the-background/?nxs=task-api&nxs_json_output_format=prettyprint
// https://tasks.example.org/api/1/prod/get-task-instance-meta-with-recursive-offspring/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=Xtaskinstanceid=Y&maxdepth=2
// https://tasks.example.org/api/1/prod/reflect-task-instance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=593&taskinstanceid=D1532527-CF55-4BEA-6041-0BD1CCE401D6
// https://tasks.example.org/api/1/prod/set-task-instance-execution-pointer/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=593&taskinstanceid=D1532527-CF55-4BEA-6041-0BD1CCE401D6&executionpointerid=6bfb13cbdb1973d8627c843a2aeb5830
// https://tasks.example.org/api/1/prod/run-task-instance-headless/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=593&taskinstanceid=D1532527-CF55-4BEA-6041-0BD1CCE401D6
// https://tasks.example.org/api/1/prod/run-task-instances-batch-headless/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=593&taskinstanceids=D1532527-CF55-4BEA-6041-0BD1CCE401D6;D1532527-CF55-4BEA-6041-0BD1CCE401D5

// https://tasks.example.org/api/1/prod/create-task/?brk=task-api&nxs_json_output_format=prettyprint&taskmeta_id=1&taskrecipe=hello world


use barkgj\functions;
use barkgj\tasks\tasks;



// more than likely values submitted can contains slashes, which would be escaped by the webserver
functions::ensureslashesstripped();

$currenturi = functions::geturicurrentpage();
$method = $_SERVER['REQUEST_METHOD'];
$pieces = explode("?", $currenturi);
$currenturi = $pieces[0];
$pieces = explode("/", $currenturi);
$service = $pieces[4];

if (true)
{
	/*
	$ispubliclyaccessible = false;
	
	if (!$ispubliclyaccessible)
	{
		require_once("/srv/generic/plugins-available/nxs-vps/nxs-vps-logic.php");
		require_once("/srv/generic/libraries-available/nxs-authorization/nxs-authorization.php");
		nxs_authorization_require_OR_operator(array("superadmin", "fromwithininfrastructure", "specialips"));
		require_once("/srv/generic/libraries-available/nxs-domain/nxs-domain.php");
	}
	else
	{
		// public access is allowed
	}
	*/
	
	if (false)
	{
	}
	else if ($service == "set-task-instance-state-parameter")
	{
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not set"); }

		$taskinstanceid = $_REQUEST["taskinstanceid"];
		if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not set"); }

		$key = $_REQUEST["key"];
		if ($key == "") { functions::throw_nack("key not set"); }

		$val = $_REQUEST["val"];
		// if ($val == "") { functions::throw_nack("val not set"); }

		$result = array();

		$instancemeta = tasks::gettaskinstance($taskid, $taskinstanceid);
		$inputparameters = $instancemeta["inputparameters"];
		
		$append_result = tasks::appendstateparameter_for_taskinstance($taskid, $taskinstanceid, $key, $val);
		$result["append_result"] = $append_result;

		// error_log("busprocapi;set key val; $taskid, $taskinstanceid, $key, $val");

		functions::webmethod_return_ok($result);
	}
	else if ($service == "create-task-instance")
	{
		// create state parameters
		if (true)
		{
			$stateparameters = array();
			$keys_to_skip = array("brk", "brk_json_output_format", "taskid", "taskid", "createdby_taskid", "createdby_taskinstanceid", "marker", "startinstance", "endparentinstance", "create_offspringtaskinstance_taskid", "reinitiate_reason");
			foreach ($_REQUEST as $key => $val)
			{
				if (in_array($key, $keys_to_skip))
				{
					continue;
				}
				$stateparameters[$key] = $val;
			}
		}
		
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "")
		{
			// downwards compatibility
			$taskid = $_REQUEST["taskid"];
		}
		
		/*
		$assigned_to = $_REQUEST["assigned_to"];
		if ($assigned_to == "")
		{
			// fallback
			$assigned_to = $_REQUEST["nxs_hr_employee_id"];
		}
		*/
		
		$createdby_taskid = $_REQUEST["createdby_taskid"];
		$createdby_taskinstanceid = $_REQUEST["createdby_taskinstanceid"];
		$mail_assignee = $_REQUEST["mail_assignee"];

		$result = tasks::createtaskinstance($taskid, $assigned_to, $createdby_taskid, $createdby_taskinstanceid, $mail_assignee, $stateparameters);
		
		/*
		if ($_REQUEST["endparentinstance"] == "true")
		{
			// start the instance
			$sub_action_url = "https://tasks.example.org/api/1/prod/end-task-instance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid={$createdby_taskid}&instance_context={$createdby_taskinstanceid}";
			$sub_action_string = file_get_contents($sub_action_url);
			$sub_action_result = json_decode($sub_action_string, true);
			if ($sub_action_result["result"] != "OK") 
			{
				$result = array
				(
					"result" => "NACK",
					"details" => "unable to end parent instance; $sub_action_result",
				);
				return $result;
			}
			$result["endparent_result"] = $sub_action_result;
		}
		*/
		
		/*
		if ($_REQUEST["startinstance"] == "true")
		{
			$child_taskid = $taskid;
			$child_taskinstanceid = $result["taskinstanceid"];
			$assignedtoemployee_id = $assigned_to;
			
			// start the instance
			$sub_action_url = "https://tasks.example.org/api/1/prod/start-task-instance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid={$child_taskid}&instance_context={$child_taskinstanceid}&assignedtoemployee_id={$assignedtoemployee_id}";
			$sub_action_string = file_get_contents($sub_action_url);
			$sub_action_result = json_decode($sub_action_string, true);
			if ($sub_action_result["result"] != "OK") 
			{
				$result = array
				(
					"result" => "NACK",
					"details" => "unable to start child instance; $sub_action_result",
				);
				return $result;
			}
			$result["start_result"] = $sub_action_result;
		}
		*/
		
		functions::webmethod_return_ok($result);
	}
	else if ($service == "create-task")
	{
		$args = array();
		$prefix = "args_";
		foreach ($_REQUEST as $key => $val)
		{
			if (functions::stringstartswith($key, $prefix))
			{
				$args_key = $key;
				$args_key = str_replace($prefix, "", $args_key);
				// 
				$args[$args_key] = $val;
			}
		}

		$taskmeta = array("id" => $_REQUEST["id"]);
		$prefix = "taskmeta_";
		foreach ($_REQUEST as $key => $val)
		{
			if (functions::stringstartswith($key, $prefix))
			{
				$taskmeta_key = $key;
				$taskmeta_key = str_replace($prefix, "", $taskmeta_key);
				// 
				$taskmeta[$taskmeta_key] = $val;
			}
		}
		
		$taskrecipe = $_REQUEST["taskrecipe"];

		if (!isset($taskmeta["id"]))
		{
			functions::throw_nack("taskmeta_id required");
		}

		$createresult = tasks::createtask($args, $taskmeta, $taskrecipe);
		functions::webmethod_return_ok($createresult);
	}

	/*
	// MARKER 674547563354
	else if ($service == "ensure-automated-task-instances-are-being-processed-in-the-background")
	{
	  $result = array();
	  
	  // 
	  // 
	  //
	  $subconditions = array();
		
		// check if project exists
		$subconditions[] = array
		(
			"type" => "true_if_task_has_required_taskid",
			"required_taskid" => "358",
		);
		$subconditions[] = array
		(
			"type" => "true_if_in_any_of_the_required_states",
			"any_of_the_required_states" => array("CREATED", "STARTED")
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
		$numberofactive358s = count($taskinstances);
		
		if ($numberofactive358s >= 1)
		{
			$message = "found {$numberofactive358s} instance(s) of 358 which (still?) process the automated task instance(s), ending the api without doing anything, bye";
			error_log($message);
			
			$result["debug"][] = $message;
			$result["taskinstance"] = $taskinstances[0];
			nxs_webmethod_return_ok($result);
		}
		else
		{ 
			$message = "no 358 instances found; thus no processing of automated task instances took place (yet!), lets check if theres pending automated instances";
			//error_log($message);
			
			$result["debug"][] = $message;
			
			$items_requiring_batch_processing = brk_tasks_get_ordered_task_instances_requiring_batch_processing();
			if (count($items_requiring_batch_processing) == 0)
			{
				$message = "api returns without any further processing as theres no task instances to process, bye";
				error_log($message);
				$result["debug"][] = $message;
				nxs_webmethod_return_ok($result);
			}
			else
			{
				$message = "found at least one automated task instance that requires processing, see field items_requiring_batch_processing";
				error_log($message);
				$result["debug"][] = $message;
				
				$result["items_requiring_batch_processing"] = $items_requiring_batch_processing;
				
				$nextup_taskid = $items_requiring_batch_processing[0]["taskid"];
				$nextup_taskinstanceid = $items_requiring_batch_processing[0]["taskinstanceid"];
				
				$message = "the next one up is $nextup_taskid $nextup_taskinstanceid";
				error_log($message);
				$result["debug"][] = $message;
				
				// sanity check
				if ($nextup_taskid == 358) { functions::throw_nack("doing it wrong; next_up can never be a 358 (358 is the task which runs the other ones)"); }
				
				$taskid_to_create = 358;
				$rpa_hr_employee_id = 3;
				$createdby_taskid = "";					// api's dont have taskids
				$createdby_taskinstanceid = "";	// api's dont have taskinstanceids
				$mail_assignee = "";	// no, dont mail the RPA employee
				
				$inputparameters = array
				(
					"nextup_taskid" => $nextup_taskid,
					"nextup_taskinstanceid" => $nextup_taskinstanceid,
					"runtimeexecutionbehaviour" => "gogogo",	// this property gives 'us' control of the background process (for example if we want to end processing in one way or the other)
					"instances_handled_so_far" => 0,	// max number of instances to handle before ending this instance
					"instances_failed_so_far" => 0,	// max number of instances to handle before ending this instance
					"ttl_seconds" => 60*30,	// this property gives 'us' control of the background process (for example if we want to end processing in one way or the other)
					"lasttouchedtimestamp" => time(),
				);
				
				$createtaskinstance_result = brk_tasks_createtaskinstance($taskid_to_create, $rpa_hr_employee_id, $createdby_taskid, $createdby_taskinstanceid, $mail_assignee, $inputparameters);
				if ($createtaskinstance_result["result"] != "OK") { functions::throw_nack("error creating task instance to handle the background process (358)"); }
				
				$taskid = $createtaskinstance_result["taskid"];
				$taskinstanceid = $createtaskinstance_result["taskinstanceid"];
				
				$result["createtaskinstance_result"][] = $createtaskinstance_result;
				
				$message = "about to return api result to invoker, will proceed to process the next up item in the forked process";
				error_log($message);
				$result["debug"][] = $message;
				
				// return result async so the invoker can proceed doing whatever it wanted to do
				nxs_webmethod_return_raw_async($result);
				
				// the code below is the code that is run in the "forked" process (async from the invoker process)
				error_log("forked php process will now handle 358 instance ... (after api response was given)");
				
				// update rpa_notes
				if (true)
				{
					$message = "starting 358 instance";
					error_log($message);
					$rpa_notes = $inputparameters["rpa_notes"];
					$rpa_notes .= "|{$message}";
					// persist
					brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "rpa_notes", $value);
					// update in mem too!
					$inputparameters["rpa_notes"] = $rpa_notes;
				}
				
				// start the instance
				$instance = brk_tasks_getinstance($taskid, $taskinstanceid);
				$state = $instance["state"];
				if ($state == "CREATED")
				{
					$instance["state"] = "STARTED";
					$instance["starttime"] = time();
					brk_tasks_updateinstance($taskid, $taskinstanceid, $instance);
				}
				else
				{
					//
					functions::throw_nack("background process; 358; unexpected state; $state");
				}
				
				// update rpa_notes
				if (true)
				{
					$message = "358 instance is now started";
					error_log($message);
					$rpa_notes = $inputparameters["rpa_notes"];
					$rpa_notes .= "|{$message}";
					// persist
					brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "rpa_notes", $value);
					// update in mem too!
					$inputparameters["rpa_notes"] = $rpa_notes;
				}
				
				$should_consider_processing_another_one = true;
				while ($should_consider_processing_another_one)
				{
					// TODO: the following should eventually be moved to nxs-tasks
					// or be implemented in the workflow itself as a task instruction (shortcode)
					// its the implementation of task 358
					if (true)
					{
						$instance = brk_tasks_getinstance($taskid, $taskinstanceid);
						$inputparameters = $instance["inputparameters"];
						$runtimeexecutionbehaviour = $inputparameters["runtimeexecutionbehaviour"];
						if ($runtimeexecutionbehaviour == "gogogo")
						{
							$nextup_taskid = $inputparameters["nextup_taskid"];
							$nextup_taskinstanceid = $inputparameters["nextup_taskinstanceid"];
							
							error_log("background; processing forked php process in background (for 358); run-workflows-for-taskinstance | taskid={$nextup_taskid} taskinstanceid={$nextup_taskinstanceid}");
							
							$action_url = "https://tasks.example.org/api/1/prod/run-workflows-for-taskinstance/?nxs=task-api&nxs_json_output_format=prettyprint&taskid={$nextup_taskid}&taskinstanceid={$nextup_taskinstanceid}";
							$action_string = file_get_contents($action_url);
							$action_result = json_decode($action_string, true);
							$result_runworkflows = $action_result["result"];
							
							// update rpa_notes
							if (true)
							{
								$message = "finished executing {$nextup_taskid}&taskinstanceid={$nextup_taskinstanceid} {$result_runworkflows}";
								error_log($message);
								$rpa_notes = $inputparameters["rpa_notes"];
								$rpa_notes .= "|{$message}";
								// persist
								brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "rpa_notes", $value);
								// update in mem too!
								$inputparameters["rpa_notes"] = $rpa_notes;
							}
							
							// success is a numbers game; store the outcome (count fails/successes)
							if ($result_runworkflows == "OK")
							{
								// update the counter
								if (true)
								{
									$instances_handled_so_far = $inputparameters["instances_handled_so_far"];
									$instances_handled_so_far++;
									brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "instances_handled_so_far", $instances_handled_so_far);
									$inputparameters["instances_handled_so_far"] = $instances_handled_so_far;
								}
							}
							else
							{
								// update the counter
								if (true)
								{
									$instances_failed_so_far = $inputparameters["instances_failed_so_far"];
									$instances_failed_so_far++;
									brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "instances_failed_so_far", $instances_failed_so_far);
									$inputparameters["instances_failed_so_far"] = $instances_failed_so_far;
								}
							}
							
							// update lasttouchedtimestamp
							if (true)
							{
								$lasttouchedtimestamp = time();
								brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "lasttouchedtimestamp", $lasttouchedtimestamp);
								$inputparameters["lasttouchedtimestamp"] = $lasttouchedtimestamp;
							}
						}
						else
						{
							$should_consider_processing_another_one = false;
							
							// update rpa_notes
							if (true)
							{
								$message = "ending execution because of runtimeexecutionbehaviour; $runtimeexecutionbehaviour";
								error_log($message);
								$rpa_notes = $inputparameters["rpa_notes"];
								$rpa_notes .= "|{$message}";
								// persist
								brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "rpa_notes", $value);
								// update in mem too!
								$inputparameters["rpa_notes"] = $rpa_notes;
							}
						}
					}
					
					if ($should_consider_processing_another_one)
					{
						// TODO: consider taking into consideration the ttl_seconds	
						// TODO: consider taking into consideration a max number of items to process
					}
					
					if ($should_consider_processing_another_one)
					{
						$items_requiring_batch_processing = brk_tasks_get_ordered_task_instances_requiring_batch_processing();
						if (count($items_requiring_batch_processing) == 0)
						{
							// update rpa_notes
							if (true)
							{
								$message = "no more automated task instances to process, bye";
								error_log($message);
								$rpa_notes = $inputparameters["rpa_notes"];
								$rpa_notes .= "|{$message}";
								// persist
								brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "rpa_notes", $value);
								// update in mem too!
								$inputparameters["rpa_notes"] = $rpa_notes;
							}
							
							$should_consider_processing_another_one = false;
						}
						else
						{
							$nextup_taskid = $items_requiring_batch_processing[0]["taskid"];
							brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "nextup_taskid", $nextup_taskid);
							
							$nextup_taskinstanceid = $items_requiring_batch_processing[0]["taskinstanceid"];
							brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "nextup_taskinstanceid", $nextup_taskinstanceid);
							
							// update rpa_notes
							if (true)
							{
								$message = "at least one more automated task instances to process, $nextup_taskid $nextup_taskinstanceid";
								error_log($message);
								$rpa_notes = $inputparameters["rpa_notes"];
								$rpa_notes .= "|{$message}";
								// persist
								brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "rpa_notes", $value);
								// update in mem too!
								$inputparameters["rpa_notes"] = $rpa_notes;
							}
						}
					}
				}
				
				// update rpa_notes
				if (true)
				{
					$message = "ending 358 instance";
					error_log($message);
					$rpa_notes = $inputparameters["rpa_notes"];
					$rpa_notes .= "|{$message}";
					// persist
					brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "rpa_notes", $value);
					// update in mem too!
					$inputparameters["rpa_notes"] = $rpa_notes;
				}
				
				// ending the 358 instance
				$instance = brk_tasks_getinstance($taskid, $taskinstanceid);
				$state = $instance["state"];
				if ($state == "STARTED")
				{
					$instance["state"] = "ENDED";
					$instance["endtime"] = time();
					$instance["endedbyip"] = $_SERVER['REMOTE_ADDR'];
					brk_tasks_updateinstance($taskid, $taskinstanceid, $instance);
				}
				else
				{
					//
					functions::throw_nack("background process; 358; unable to end;unexpected state; $state");
				}
				
				
				// update rpa_notes
				if (true)
				{
					$message = "ended 358 instance, exit-ing background process";
					error_log($message);
					$rpa_notes = $inputparameters["rpa_notes"];
					$rpa_notes .= "|{$message}";
					// persist
					brk_tasks_appendinputparameter_for_taskinstance($taskid, $taskinstanceid, "rpa_notes", $value);
					// update in mem too!
					$inputparameters["rpa_notes"] = $rpa_notes;
				}
				
				// the following exit line ends the forked process, dont remove it!
				exit();
			}
		}
	  
	  nxs_webmethod_return_ok($result);
	}
	else if ($service == "set-task-instance-execution-pointer")
	{
		$taskid = $_REQUEST["taskid"];
	  $taskinstanceid = $_REQUEST["taskinstanceid"];
	  $executionpointerid = $_REQUEST["executionpointerid"];
	  
	  if ($taskid == "") { functions::throw_nack("taskid not set"); }
	  if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not set"); }
	  if ($executionpointerid == "") { functions::throw_nack("executionpointerid not set"); }
	  
		$delegated_result = brk_tasks_setexecutionpointer($taskid, $taskinstanceid, $executionpointerid);
		
		$result["delegated_result"] = $delegated_result;
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "reflect-task-instance")
	{
		$taskid = $_REQUEST["taskid"];
	  $taskinstanceid = $_REQUEST["taskinstanceid"];
	  
	  if ($taskid == "") { functions::throw_nack("taskid not set"); }
	  if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not set"); }
	  
	  // $finishedinstruction_pointer = brk_tasks_getfinishedinstructionpointer($taskid, $taskinstanceid);	  
	  $taskmeta = brk_tasks_getreflectionmeta($taskid, $taskinstanceid);
	  $execution_pointer = brk_tasks_getexecutionpointer($taskid, $taskinstanceid);
	  $execution_pointer_task_instruction = $taskmeta["task_instructions_by_id"][$execution_pointer];
	  
	  $instance_meta = brk_tasks_getinstance($taskid, $taskinstanceid);
	  $raw_execution_pointer = $instance_meta["execution_pointer"];
	  
	  $state = brk_tasks_getinstancestate($taskid, $taskinstanceid);
	  
		// get current instruction points
		
		$result["taskid"] = $taskid;
		$result["taskinstanceid"] = $taskinstanceid;

		$result["state"] = $state;		
		
		$result["raw_execution_pointer"] = $raw_execution_pointer;
		$result["execution_pointer"] = $execution_pointer;
		$result["execution_pointer_task_instruction"] = $execution_pointer_task_instruction;
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "run-task-instances-batch-headless")
	{
		$taskid = $_REQUEST["taskid"];
		$taskinstanceids = $_REQUEST["taskinstanceids"];
		
		$delegated_result = brk_tasks_execute_batch_headless_from_current_execution_pointer($taskid, $taskinstanceids);
		// since the batch can be enormous and the output per item can be very large, we don't return the output
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "run-task-instance-headless")
	{
		$taskid = $_REQUEST["taskid"];
		$taskinstanceid = $_REQUEST["taskinstanceid"];
		$executionmode = $_REQUEST["executionmode"];
				
		$delegated_result = brk_tasks_execute_headless_from_current_execution_pointer($taskid, $taskinstanceid, $executionmode);
		$result = array
		(
			"delegated_result" => $delegated_result
		);
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "reflect-task")
	{
	  // require input parameters check
	  $taskid = $_REQUEST["taskid"];
	  if ($taskid == "") { functions::throw_nack("taskid not set"); }
	  
	  $taskinstanceid = $_REQUEST["taskinstanceid"];	// can be empty
	  
	  $result = array();
	  
	  $meta = brk_tasks_getreflectionmeta($taskid, $taskinstanceid);
	  $result["meta"] = $meta;
	  
	  nxs_webmethod_return_ok($result);
	}
	else if ($service == "create-bulk-task-instances")
	{
		// require input parameters check
	  $args_json = $_REQUEST["args_json"];
	  if ($args_json == "")
	  {
	  	functions::throw_nack("args_json not filled");
	  }
	  
	  $args = json_decode($args_json, true);
		if ($args == "")
		{
			functions::throw_nack("args_json not filled with valid json? START___{$args_json}___END");
		}
		
		foreach ($args["items"] as $item)
		{
			$itemresult = array();
			$itemresult["item"] = $item;	// so we know what the result is for
			
			// for each item do
			if (true)
			{
				// create inputparameters
				if (true)
				{
					$inputparameters = array();
					$keys_to_skip = array("nxs", "nxs_json_output_format", "taskid", "taskid", "createdby_taskid", "createdby_taskinstanceid", "marker");
					foreach ($item as $key => $val)
					{
						if (in_array($key, $keys_to_skip))
						{
							continue;
						}
						$inputparameters[$key] = $val;
					}
				}
				
				$taskid = $item["taskid"];
				if ($taskid == "") { functions::throw_nack("taskid not set in item"); }
				
				global $nxs_g_modelmanager;
				$a = array("modeluri" => "{$taskid}@nxs.p001.businessprocess.task");
				$properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
				$tasktitle = $properties["title"];
				if ($tasktitle == "") { functions::throw_nack("invalid task (id does not map to a task with a title)"); }
				
				$taskinstanceid = nxs_create_guid();
				if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not set"); }
				
				$path = nxs_bp_gettaskpath($taskid);
				if (!file_exists($path))
				{
					// first time we use this task (no other instances exist)
					$meta = array();
				}
				else
				{
					$string = file_get_contents($path);
					$meta = json_decode($string, true);
				}
				
				if (isset($meta[$taskinstanceid])) { functions::throw_nack("instance was already started"); }
				
				$assigned_to = $item["assigned_to"];
				$createdby_taskid = $item["createdby_taskid"];
				$createdby_taskinstanceid = $item["createdby_taskinstanceid"];
				
				// 
				
				$parentinputparameters = array();
				if ($createdby_taskid != "" && $createdby_taskinstanceid != "")
				{
					$parentinputparameters = brk_tasks_getinstanceinputparameters($createdby_taskid, $createdby_taskinstanceid);
					
					// handle sticky_inputparameters; these will automatically be copied to the offspring,
					// if they have a value, and if not yet "set" manually
					$sticky_inputparameters = brk_tasks_getstickyparameters();
					foreach ($sticky_inputparameters as $key)
					{
						if (!isset($inputparameters[$key]))
						{
							$parentvalue = $parentinputparameters[$key];
							if ($parentvalue != "")
							{
								$inputparameters[$key] = $parentvalue;
							}
							else
							{
								// no need to set empty var
							}
						}
						else
						{
							// if the invoker already explicitly specified a value, use that one and don't use the sticky one
						}
					}
				}
				else
				{
					// if this new instance has no parent, we cannot apply sticky parameters
				}
				
				$meta[$taskinstanceid] = array
				(
					"state" => "CREATED",
					"createtime" => time(),
					"createdbyip" => $_SERVER['REMOTE_ADDR'],
					"createdby_taskid" => $createdby_taskid,
					"createdby_taskinstanceid" => $createdby_taskinstanceid,
					"instance_context" => $taskinstanceid,	// obsolete, but in here for downwards compatibility
					"taskinstanceid" => $taskinstanceid,
					"inputparameters" => $inputparameters,
					"assignedtoemployee_id" => $assigned_to,
				);
				$string = json_encode($meta);
				
				file_put_contents($path, $string, LOCK_EX);
		
				//$itemresult["path"] = $path;
				//$itemresult["length"] = strlen($string);
				$itemresult["taskinstanceid"] = $taskinstanceid;
				
				if ($createdby_taskid != "" && $createdby_taskinstanceid != "")
				{
					brk_tasks_appendcreatedtask_to_taskinstance($createdby_taskid, $createdby_taskinstanceid, $taskid, $taskinstanceid);
				}
				else
				{
					error_log("create-task-instance-api; warning; created $taskid $taskinstanceid - no createdby_taskid and/or createdby_taskinstanceid given");
				}
				
				$mail_assignee = $item["mail_assignee"];
				if ($mail_assignee != "")
				{
					if ($assigned_to != "")
					{
						$mailtemplate = 83;
						$mail_url = "https://tasks.example.org/api/1/prod/send-mail-template-for-employee/?nxs=mail-api&nxs_json_output_format=prettyprint&employeeid={$assigned_to}&mailtemplate={$mailtemplate}";
						
						$mail_url = nxs_addqueryparametertourl_v2($mail_url, "tasktitle", $tasktitle, true, true);
						
						$taskinstance_url = "https://tasks.example.org/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
						$mail_url = nxs_addqueryparametertourl_v2($mail_url, "taskinstance_url", $taskinstance_url, true, true);
						
						$mail_string = file_get_contents($mail_url);
						$mail_result = json_decode($mail_string, true);
						$itemresult["mail_assignee_url"] = $mail_url;
						$itemresult["mail_assignee_result"] = $mail_result;
					}
					else
					{
						error_log("businessprocessapiimpl; mail_assignee set, assigned_to is empty?");
					}
				}
			}
			
			$result["itemresults"][] = $itemresult;
		}
			
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "get-task-by-title")
	{
		// require input parameters check
	  $title = $_REQUEST["title"];
	  if ($title == "") { functions::throw_nack("title not set"); }
	  
	  $result = array();
	  
	  $result["found"] = false;
	  
		// loop over all tasks types
		global $nxs_g_modelmanager;
		$a = array("singularschema" => "nxs.p001.businessprocess.task");
		$alltaskrows = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels($a);
		foreach ($alltaskrows as $props)
		{
			$task_id = $props["nxs.p001.businessprocess.task_id"];
			$currenttitle = $props["title"];  
			if ($currenttitle == $title)
			{
				$result["props"] = $props;
				$result["found"] = true;
			}
	  }
	  
	  nxs_webmethod_return_ok($result);
	}
	else if ($service == "run-workflows-for-taskinstance")
	{
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not set"); }

		$taskinstanceid = $_REQUEST["taskinstanceid"];
		if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not set"); }

		$run_result = nxs_workflow_run($taskid, $taskinstanceid);
		
		$result["run_result"] = $run_result;

		nxs_webmethod_return_ok($result);
	}
	else if ($service == "add-note-to-task-instance")
	{
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not set"); }

		$taskinstanceid = $_REQUEST["taskinstanceid"];
		if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not set"); }

		$text = $_REQUEST["text"];
		if ($text == "") { functions::throw_nack("taskinstanceid not set"); }

		$author_employeeid = $_REQUEST["author_employeeid"];
		if ($author_employeeid == "") { functions::throw_nack("author_employeeid not set"); }
		
		$path = nxs_bp_gettaskpath($taskid);
		if (!file_exists($path)) { functions::throw_nack("not found? (perhaps you forgot to create the task first?)"); }

		$string = file_get_contents($path);
		$meta = json_decode($string, true);
		
		if (!isset($meta[$taskinstanceid])) { functions::throw_nack("instance was not found"); }
		
		$meta[$taskinstanceid]["notes"][] = array
		(
			"author_employeeid" => $author_employeeid,
			"creationtime" => time(),
			"text" => $text
		);
		$string = json_encode($meta);
		
		file_put_contents($path, $string, LOCK_EX);
		
		nxs_webmethod_return_ok($result);
	}
	
	else if ($service == "execute-task-instance-through-rpa")
	{
		// TODO: isnt this obsolete?
		
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not set"); }

		$taskinstanceid = $_REQUEST["taskinstanceid"];
		if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not set"); }
		
		$path = nxs_bp_gettaskpath($taskid);
		if (!file_exists($path)) { functions::throw_nack("not found? (perhaps you forgot to create the task first?)"); }

		$string = file_get_contents($path);
		$meta = json_decode($string, true);
		
		if (!isset($meta[$taskinstanceid])) { functions::throw_nack("unable to start task; instance not found? (create it first)"); }
		
		$action_url = "https://tasks.example.org/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
		$webresponse = file_get_contents($action_url);
		if ($webresponse === false)
		{
			$error = error_get_last();
			var_dump($error);//
			functions::throw_nack("error while executing task instance through rpa");
		}
		
		echo $webresponse;
		die();
		
		//nxs_webmethod_return_ok($result);
	}
	else if ($service == "start-task" || $service == "start-task-instance") // start-task-instance
	{
		$taskid = $_REQUEST["taskid"];
		$instance_context = $_REQUEST["instance_context"];
		$taskinstanceid = $instance_context;
		$assignedtoemployee_id = $_REQUEST["assignedtoemployee_id"];
		
		if ($taskid == "") { functions::throw_nack("taskid not set"); }
		if ($instance_context == "") { functions::throw_nack("instance_context not set"); }
		
		$result = brk_tasks_starttaskinstance($taskid, $taskinstanceid, $assignedtoemployee_id);
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "end-task-instance")
	{
		$taskid = $_REQUEST["taskid"];
		$instance_context = $_REQUEST["instance_context"];
		$taskinstanceid = $instance_context; // nxs_bp_gettaskinstanceid($instance_context);
		
		$result = brk_tasks_endtaskinstance($taskid, $taskinstanceid);
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "assign-employee-to-task-instance")
	{
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not set"); }

		$taskinstanceid = $_REQUEST["taskinstanceid"];
		if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not set"); }
		
		$path = nxs_bp_gettaskpath($taskid);
		if (!file_exists($path)) { functions::throw_nack("not found? (perhaps you forgot to create the task first?)"); }

		$string = file_get_contents($path);
		$meta = json_decode($string, true);
		
		if (!isset($meta[$taskinstanceid])) { functions::throw_nack("instance not found? (create it first)"); }
		
		$assignedtoemployee_id = $_REQUEST["employeeid"];
		$meta[$taskinstanceid]["assignedtoemployee_id"] = $assignedtoemployee_id;
		$string = json_encode($meta);
		
		file_put_contents($path, $string, LOCK_EX);
		
		$result = array
		(
			"path" => $path,
			"length" => strlen($string),
			"duration_secs" => $duration_secs,
			"duration_human" => $duration_human,
		);
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "view-task-stats")
	{
		// https://tasks.example.org/api/1/prod/view-task-stats/?nxs=task-api&nxs_json_output_format=prettyprint&taskid=1
		
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not set"); }

		$path = nxs_bp_gettaskpath($taskid);
		if (!file_exists($path)) { functions::throw_nack("not found?"); }

		$string = file_get_contents($path);
		$meta = json_decode($string, true);
		
		$result = array
		(
			"entries" => $meta,
		);
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "view-tasks-stats")
	{
		$result = array();
		
		// loop over all tasks types
		global $nxs_g_modelmanager;
		$a = array("singularschema" => "nxs.p001.businessprocess.task");
		$alltaskrows = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels($a);
		foreach ($alltaskrows as $alltaskrow)
		{
			$task_id = $alltaskrow["nxs.p001.businessprocess.task_id"];
			$title = $alltaskrow["title"];
			
			echo "{$task_id}<br />";
			
			$path = nxs_bp_gettaskpath($task_id);
			if (!file_exists($path)) 
			{ 
				//functions::throw_nack("task_id not found? $task_id"); }
				$result["errors"][] = "task_id not found? $task_id; $pat";
				continue;
			}
	
			$string = file_get_contents($path);
			
			$allmeta = json_decode($string, true);
			
			foreach ($allmeta as $id => $meta)
			{
				$instance_context = $meta["instance_context"];
				
				$state = $meta["state"];
				
				$starttime = $meta["starttime"];
				$endtime = $meta["endtime"];
				
				$duration_secs = $endtime - $starttime;
				$duration_human = nxs_time_getsecondstohumanreadable($duration_secs);
				
				$result["all"][$title][$state][] = array
				(
					"duration_human" => $duration_human,	
				);
				
				$day = date("Ymd", $starttime);
				$result["byday"][$day][$title][$state][] = array
				(
					"duration_human" => $duration_human,	
				);
			}
		}
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "get-task-instance-meta-with-recursive-offspring")
	{
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not specified"); }

		$taskinstanceid = $_REQUEST["taskinstanceid"];
		if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not specified"); }
		
		$maxdepth = $_REQUEST["maxdepth"];
		if ($maxdepth == "") { functions::throw_nack("maxdepth not specified"); }
		
	  // require input parameters check
	  // $x = $_REQUEST["x"];
	  // if ($x == "") { functions::throw_nack("x not set"); }
	  
	  $result = array();
	  
	  $result["instances"] = brk_tasks_gettaskinstancemetawithrecursiveoffspring($taskid, $taskinstanceid, $maxdepth);
	  
	  nxs_webmethod_return_ok($result);
	}
	else if ($service == "get-task-instance-meta")
	{
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not specified"); }

		$taskinstanceid = $_REQUEST["taskinstanceid"];		
		if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not specified"); }
		
		$path = nxs_bp_gettaskpath($taskid);
		if (!file_exists($path)) { functions::throw_nack("not found? (perhaps you forgot to create the task first?)"); }

		$string = file_get_contents($path);
		$meta = json_decode($string, true);
		
		if (!isset($meta[$taskinstanceid])) { functions::throw_nack("instance not found?"); }
		
		$result["props"] = $meta[$taskinstanceid];
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "get-rfcs-for-task")
	{
		$state = $_REQUEST["state"];
		$states = explode("|", $state);
		
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not set"); }
		
		$rfctaskid = 74;
		$path = nxs_bp_gettaskpath($rfctaskid);
		$string = file_get_contents($path);
		$instances = json_decode($string, true);
		foreach ($instances as $id => $meta)
		{
			$shouldinclude = true;
			
			if ($meta["inputparameters"]["taskid_to_edit"] != $taskid)
			{
				$shouldinclude = false;
			}
			else
			{
				// $result["skipped"][$id] = $meta;
			}
			
			if ($state != "")
			{
				if (!in_array($meta["state"], $states))
				{
					$shouldinclude = false;
				}
			}
			
			if ($shouldinclude)
			{
				$result["rfcs"][$id] = $meta;
			}
		}
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "setup-new-task")
	{
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not set"); }
		
		global $nxs_g_modelmanager;
		$a = array("modeluri" => "{$taskid}@nxs.p001.businessprocess.task");
		$properties = $nxs_g_modelmanager->getmodeltaxonomyproperties($a);
		$tasktitle = $properties["title"];
		if ($tasktitle == "") { functions::throw_nack("invalid task (id does not map to a task with a title)"); }

		// only setup the txt file if its not yet existing
		$path = brk_tasks_gettaskrecipepath($taskid);
		if (file_exists($path))
		{
			functions::throw_nack("already setup? (recipe txt file already exists)");
		}
		
		// clone template
		$frompath = "/srv/generic/templates-available/task-steps-template/steps-template.txt";
		$r = copy($frompath, $path);
		
		if (!$r)
		{
			functions::throw_nack("unable to clone template to recipe for taskid");
		}
		
		$result = array
		(
			"copyresult" => $r,
		);
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "sleep-task-instance")
	{
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not set"); }

		$taskinstanceid = $_REQUEST["taskinstanceid"];
		if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not set"); }
		
		$path = nxs_bp_gettaskpath($taskid);
		if (!file_exists($path)) { functions::throw_nack("not found?"); }

		$string = file_get_contents($path);
		$meta = json_decode($string, true);
		
		if (!isset($meta[$taskinstanceid])) { functions::throw_nack("instance not found?"); }
		
		$oldstate = $meta[$taskinstanceid]["state"];
		if ($oldstate == "SLEEPING") { nxs_webmethod_return_alternativeflow("ALREADYSLEEPING", $meta); }
		
		if ($oldstate != "CREATED" && $oldstate != "STARTED") { functions::throw_nack("unexpected old state; $oldstate"); }
		
		// requirement; sleeping is only allowed if this instance has at least one offspring item
		// that is created or started
		if (true)
		{
			$atleastoneopenoffspringitemfound = false;
			$created_tasks = $meta[$taskinstanceid]["created_tasks"];
			foreach ($created_tasks as $created_task)
			{
				$offspring_taskid = $created_task["taskid"];
				$offspring_taskinstanceid = $created_task["taskinstanceid"];
				$offspring_state = brk_tasks_getinstancestate($offspring_taskid, $offspring_taskinstanceid);
				if ($offspring_state == "CREATED" || $offspring_state == "STARTED")
				{
					$atleastoneopenoffspringitemfound = true;	
				}
			}
			
			if ($atleastoneopenoffspringitemfound == false)
			{
				functions::throw_nack("unable to sleep instance; requires at least one offspring item that is not ended");
			}
		}
		
		$meta[$taskinstanceid]["state"] = "SLEEPING";
		$meta[$taskinstanceid]["sleeptime"] = time();
		$meta[$taskinstanceid]["sleepbyip"] = $_SERVER['REMOTE_ADDR'];
		
		$string = json_encode($meta);
		
		file_put_contents($path, $string, LOCK_EX);
		
		$result = array
		(
			"path" => $path,
			"length" => strlen($string),
			"new_taskinstance_meta" => $meta[$taskinstanceid],
		);
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "abort-task-instance")
	{
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not set"); }

		$taskinstanceid = $_REQUEST["taskinstanceid"];
		if ($taskinstanceid == "") { functions::throw_nack("taskinstanceid not set"); }
		
		$abort_reason = $_REQUEST["abort_reason"];
		if ($abort_reason == "") { functions::throw_nack("abort_reason not set"); }
		
		$aborted_by_employeeid = $_REQUEST["aborted_by_employeeid"];
		
		// we use "the_" prefix, as "note" itself is replaced with NOT mathematical expression
		// for some unknown reason
		$note = $_REQUEST["the_note"];
		
		if (false)
		{
		}
		else if ($abort_reason == "testing")
		{
			// no required note
		}
		else if ($abort_reason == "not_enough_time")
		{
			// no required note
		}
		else if ($abort_reason == "user_requested_refund")
		{
			// no required note
		}
		else if ($abort_reason == "see_incident_offspring")
		{
			// no required note
		}
		else
		{
			// note is required
			if ($note == "") { functions::throw_nack("the_note not set ($abort_reason)"); }
		}

		//
		$instancemeta = brk_tasks_getinstance($taskid, $taskinstanceid);
		if ($instancemeta["isfound"] == false) { functions::throw_nack("instance not found?"); }
		
		$oldstate = $instancemeta["state"];
		if ($oldstate == "ABORTED") { nxs_webmethod_return_alternativeflow("ALREADYABORTED", $instancemeta); }
		
		if ($oldstate != "CREATED" && $oldstate != "STARTED" && $oldstate != "SLEEPING") { functions::throw_nack("unexpected old state; $oldstate"); }
		
		//
		if (false)
		{
			//
		}
		else if ($abort_reason == "misinterpreted_helpscout_interpretation")
		{
			$inputparameters = $instancemeta["inputparameters"];
			$original_helpscoutticketnr = $inputparameters["original_helpscoutticketnr"];
			if ($original_helpscoutticketnr == "")
			{
				functions::throw_nack("error; original_helpscoutticketnr not found as inputparameter?");
			}

			$taskinstanceurl = "https://tasks.example.org/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
			$extended_note = "the <a target='_blank' href='$taskinstanceurl'>task instance</a> to handle this ticket was aborted<br />abort_reason: {$abort_reason}<br />note: {$note}";

			// add a note in helpscout (this will automatically open the ticket in helpscout, which is good as it will be picked up again by task 144 in the next loop)
			$addnote_url = "https://tasks.example.org/api/1/prod/add-note-to-helpscout-conversation/?nxs=helpscout-api&nxs_json_output_format=prettyprint";
			$addnote_url = nxs_addqueryparametertourl_v2($addnote_url, "note", $extended_note, true, true);
			$addnote_url = nxs_addqueryparametertourl_v2($addnote_url, "helpscoutnumber", $original_helpscoutticketnr, true, true);
			$addnote_string = file_get_contents($addnote_url);
			$addnote_result = json_decode($addnote_string, true);
			if ($addnote_result["result"] != "OK") { functions::throw_nack("error adding note; addnote_url; $addnote_url"); }
		}
		else if 
		(
			$abort_reason == "was_created_by_accident" ||
			$abort_reason == "reinitiate_previous_task_instance" || 
			$abort_reason == "handled_manually_outside_system" || 
			$abort_reason == "fork_task_instance" || 
			$abort_reason == "see_incident_offspring" ||
			$abort_reason == "replaced_by_rfc_internal" || 
			$abort_reason == "testing" || 
			$abort_reason == "not_enough_time" || 
			$abort_reason == "duplicate" ||
			$abort_reason == "user_requested_refund" || 
			false
		)
		{
			$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
		}
		else
		{
			functions::throw_nack("error; abort_reason not yet implemented; $abort_reason");
		}
		
		//
		// allow other logic to do things just prior to finishing the task instance
		//
		brk_tasks_handle_event_before_closingtaskinstance($taskid, $taskinstanceid);
		
		//
		//
		//
		$instancemeta["state"] = "ABORTED";
		$instancemeta["abortedtime"] = time();
		$instancemeta["abortedbyip"] = $_SERVER['REMOTE_ADDR'];
		$instancemeta["aborted_by_employeeid"] = $aborted_by_employeeid;
		
		$instancemeta["abort_reason"] = $abort_reason;
		$instancemeta["abort_note"] = $note;

		$duration_secs = $instancemeta["abortedtime"] - $instancemeta["starttime"];
		$duration_human = nxs_time_getsecondstohumanreadable($duration_secs);

		brk_tasks_updateinstance($taskid, $taskinstanceid, $instancemeta);
		
		$result = array
		(
			"path" => $path,
			"length" => strlen($string),
			"duration_secs" => $duration_secs,
			"duration_human" => $duration_human,
			"taskinstanceid" => $taskinstanceid,
			"new_taskinstance_meta" => $instancemeta,
		);
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "search-task-instances")
	{
		// example: {"if_this":{"type":"true_if_each_subcondition_is_true","subconditions":[{"type":"true_if_task_has_required_taskid","required_taskid":"22"},{"type":"true_if_inputparameter_has_required_value_for_key","key":"domain","required_value":"greatpacificfireinvestigations.com"}]}}
		
		$args_json = $_REQUEST["args_json"];
		if ($args_json == "")
		{
			$helpscoutnumber = $_REQUEST["helpscoutnumber"];
			if ($helpscoutnumber != "")
			{
				$args["if_this"] = array
				(
					"type" => "true_if_inputparameter_has_required_value_for_key",
					"key" => "original_helpscoutticketnr",
					"required_value" => $helpscoutnumber,
				);	
			}
			else
			{
				functions::throw_nack("args_json not set");
			}
		}
		else
		{
			$args = json_decode($args_json, true);
			if ($args == "")
			{
				functions::throw_nack("args_json not filled with valid json? START___{$args_json}___END");
			}
		}
		
		$matches = brk_tasks_searchtaskinstances($args);
		$result["matches"] = $matches;
		$result["evaluations"]["count"] = count($matches["taskinstances"]);
			
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "search-deep-task-instances")
	{
		$taskid = $_REQUEST["taskid"];
		$limit = $_REQUEST["limit"];
		$fields_filter = $_REQUEST["fields_filter"];	// aap=1 noot=2
		$state = $_REQUEST["state"];	// STARTED/STOPPED
		
		// validations
		if ($taskid == "") { functions::throw_nack("taskid not specified"); }
		if ($limit == "") { functions::throw_nack("limit not specified"); }
		
		$matches = brk_tasks_deep_searchtaskinstances($taskid, $fields_filter, $limit, $state);
		$result["matches"] = $matches;
		
		nxs_webmethod_return_ok($result);
	}
	else if ($service == "list-taskoutcomepredictions")
	{
		$taskid = $_REQUEST["taskid"];
		if ($taskid == "") { functions::throw_nack("taskid not set"); }		

		// loop over all tasks types
		global $nxs_g_modelmanager;
		$a = array("singularschema" => "nxs.p001.businessprocess.taskoutcomeprediction");
		$allentries = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels($a);
		foreach ($allentries as $entry)
		{
			$taskid_current_entry = $entry["nxs.p001.businessprocess.task_id"];
			if ($taskid == $taskid_current_entry)
			{
				$result["predictions"][] = $entry;
			}
		}

		nxs_webmethod_return_ok($result);		
	}
	// ---
	else
	{
		echo "API call; uri not supported; $currenturi; " . $pieces[4];
	}
	*/
}
else
{
	echo "API call; method not supported; $method";
}