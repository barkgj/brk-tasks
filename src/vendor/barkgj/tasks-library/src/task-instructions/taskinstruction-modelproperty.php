<?php

function brk_tasks_instance_do_modelproperty($then_that_item, $taskid, $taskinstanceid)
{
	$instancemeta = brk_tasks_getinstance($taskid, $taskinstanceid);
	$inputparameters = $instancemeta["inputparameters"];
	
	$modeluri = $then_that_item["modeluri"];
	$modeluri = nxs_filter_translatesingle($modeluri, "{{", "}}", $inputparameters);
	
	$property = $then_that_item["property"];
	$property = nxs_filter_translatesingle($property, "{{", "}}", $inputparameters);	
	
	$shortcode = "[nxs_string ops=modelproperty modeluri='{$modeluri}' property='{$property}']";
	$value = do_shortcode($shortcode);
	
	//$result["console"][] = "shortcode: $shortcode";
	$result["console"][] = "$value";
	
  $result["result"] = "OK";
  
  return $result;
}