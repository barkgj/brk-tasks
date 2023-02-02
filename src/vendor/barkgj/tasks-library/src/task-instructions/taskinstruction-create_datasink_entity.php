<?php

namespace barkgj\tasks\taskinstruction
{
	use barkgj\functions;
	use barkgj\tasks\tasks;
	use barkgj\tasks\itaskinstruction;
	use barkgj\datasink;
	use barkgj\datasink\entity;
	
	class create_datasink_entity implements itaskinstruction
	{
		function execute($taskid, $taskinstanceid, $then_that_item)
		{
			$marker = $then_that_item["marker"];
			$instancemeta = tasks::gettaskinstance($taskid, $taskinstanceid);
			$stateparameters = $instancemeta["stateparameters"];


			// blend stateparameters over then_that_item
			foreach ($then_that_item as $k => $v)
			{
				$then_that_item[$k] = functions::translatesingle($v, "{{", "}}", $stateparameters);
			}
			
			
			$realm = $then_that_item["realm"];
			if ($realm == "")
			{
				$msg = "error: no realm attribute set for shortcode create_datasink_entity";
				$result["console"][] = $msg;
				$result["result"] = "OK";
				return $result;
			}

			$entitytype = $then_that_item["entitytype"];
			if ($entitytype == "")
			{
				$msg = "error: no entity attribute set for shortcode create_datasink_entity";
				$result["console"][] = $msg;
				$result["result"] = "OK";
				return $result;
			}

			$id = $then_that_item["id"];
			if ($id == "")
			{
				$msg = "error: no id attribute set for shortcode create_datasink_entity";
				$result["console"][] = $msg;
				$result["result"] = "OK";
				return $result;
			}

			$title = $then_that_item["title"];
			if ($title == "")
			{
				$msg = "error: no title attribute set for shortcode create_datasink_entity";
				$result["console"][] = $msg;
				$result["result"] = "OK";
				return $result;
			}

			

			$html = "";
			$actiontrigger = "create_datasink_entity_" . md5(json_encode($then_that_item));

			
			if ($_REQUEST["taskinstruction_action"] == $actiontrigger)
			{
				$doit = true;
			}
			else
			{
				$getentitiesrawargs = array
				(
					"datasink_realm" => $realm,
					"datasink_entitytype" => $entitytype,
					"datasink_include_meta" => true
				);
				$entities = entity::getentitiesraw($getentitiesrawargs);
				$count = count(array_keys($entities));
				$html .= "considering {$count} entities<br />";

				$exclude_keys = array("realm", "entitytype", "type", "marker");
				$isexactmatchfound = false;
				foreach ($entities as $entity => $entitymeta)
				{
					$html .= "entity " . json_encode($entitymeta) . "<br />";

					// if at least one entity, we will first assume there is a match
					$allpropsmatch = true;
					foreach ($then_that_item as $k => $v)
					{
						if (in_array($k, $exclude_keys))
						{
							continue;
						}
						
						$looking_for_v = $entitymeta[$k];
						if ($v != $looking_for_v)
						{
							$html .= "mismatch; {$k}; {$v} vs {$looking_for_v}<br />";

							$allpropsmatch = false;
							break;
						}
					}
					if ($allpropsmatch)
					{
						$isexactmatchfound = true;
						break;
					}
				}

				if ($isexactmatchfound)
				{
					$html .= "entity already exists, nothing to do here";
				}
				else
				{
					$html .= "entity not yet found";

					$homeurl = functions::geturlhome();
					$doiturl = "{$homeurl}/?nxs=task-gui&page=taskinstancedetail&taskid=1&taskinstanceid=4599D2E5-D065-4C31-A63E-34DFA0043BAF";
					$doiturl = functions::addqueryparametertourl($doiturl, "taskinstruction_action", $actiontrigger);
	
					$html .= "to create one click <a href='{$doiturl}'>here</a><br />";	
				}

				

				$html .= "to do it, click <a href='{$doiturl}'>here</a><br />";
			}
			
			if ($doit)
			{
				// do it...
				$html .= "doing it...";

				$storeargs = array
				(
					"datasink_invokedbytaskid" => "",
					"datasink_invokedbytaskinstanceid" => "",
			
					"datasink_realm" => $realm,
					"datasink_entitytype" => $entitytype,
					"id" => $id,
					"title" => $title,
					"datasink_alreadyfoundbehaviour" => "SKIP",
					"datasink_accoladesfoundbehaviour" => "THROW_NACK"
				);

				$r = entity::storeentitydata($storeargs);
				$html .= "store result: " . json_encode($r);
			}

			$result["console"][] = $html;
			
			$result["result"] = "OK";
			
			return $result;
		}
	}
}