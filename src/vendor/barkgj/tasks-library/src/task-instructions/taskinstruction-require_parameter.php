<?php

namespace barkgj\tasks\taskinstruction
{
	use barkgj\functions;
	use barkgj\tasks\tasks;
	use barkgj\tasks\itaskinstruction;
	
	class require_parameter implements itaskinstruction
	{
		function execute($taskid, $taskinstanceid, $then_that_item)
		{
			$marker = $then_that_item["marker"];
			$instancemeta = tasks::gettaskinstance($taskid, $taskinstanceid);
			$stateparameters = $instancemeta["stateparameters"];
			
			$name = $then_that_item["name"];
			if ($name == "")
			{
				$msg = "error: no name attribute set for shortcode nxs_p001_task_requireparameter";
				$result["console"][] = $msg;
				$result["result"] = "OK";
				return $result;
			}

			$val = $stateparameters[$name];
			
			$currenturl =functions::geturlcurrentpage();
			$returnurl = $currenturl . "#{$marker}";
			
			$random = 'a' . rand(999999999, 9999999999);
			
			$textarea_js_id = "nxs_js_textarea_{$random}";
			
			$render_expanded_view = true;
			if ($val != "")
			{
				if ($val != "" && $then_that_item["render_expanded_view_when_not_empty"] == "false")
				{
					$render_expanded_view = false;
				}
			}
			
			$escapedvalue = $val;
			$escapedvalue = str_replace("'", "&apos;", $escapedvalue);
			$escapedvalue = str_replace("\"", "&quot;", $escapedvalue);
			$escapedvalue = str_replace("\r\n", "<br />", $escapedvalue);
			$escapedvalue = str_replace("\r", "<br />", $escapedvalue);
			$escapedvalue = str_replace("\n", "<br />", $escapedvalue);
			$escapedvalue = str_replace("<br />", "&#13;&#10;", $escapedvalue);

			$html = "";

			$homeurl = functions::geturlhome();

			if ($render_expanded_view)
			{
				$html.= "render_expanded_view: true<br />";
			}
			else
			{
				$html.= "render_expanded_view: false<br />";
			}
			
			if ($render_expanded_view)
			{
				$html .= "<form action='{$homeurl}' method='POST'>";
				
				$html .= "<input type='hidden' name='nxs' value='task-gui' />";
				$html .= "<input type='hidden' name='action' value='updateparameter' />";
				$html .= "<input type='hidden' name='page' value='taskinstancedetail' />";
				
				$html .= "<input type='hidden' name='taskid' value='{$taskid}' />";
				
				$html .= "<input type='hidden' name='taskinstanceid' value='{$taskinstanceid}' />";
				$html .= "<br />";
				$html .= "<label>{$name}</label><br />";
				$html .= "<input type='hidden' name='name' value='{$name}' />";
				
				//$html .= "<input style='width: 95%' type='text' name='value' value='' />";
				$html .= "<textarea id='{$textarea_js_id}' style='width: 80vw; height: 300px;' placeholder='{$name}' name='value'>{$escapedvalue}</textarea>";
				
				$html .= "<input type='hidden' name='returnurl' value='{$returnurl}' />";
				$html .= "<br />";
				$html .= "<input type='submit' value='Save' style='background-color: #CCC;'>";
				$html .= "</form>";
			}
			else
			{
				$currenturl = functions::geturlcurrentpage();
				
				$html .= "<div class='toggle' style='display: none; background-color: red;'>";
				$html .= "<form action='{$homeurl}' method='POST'>";
				$html .= "<input type='hidden' name='nxs' value='task-gui' />";
				$html .= "<input type='hidden' name='page' value='taskinstancedetail' />";
				$html .= "<input type='hidden' name='action' value='updateparameter' />";
				$html .= "<input type='hidden' name='taskid' value='{$taskid}' />";
				$html .= "<input type='hidden' name='taskinstanceid' value='{$taskinstanceid}' />";
				$html .= "<br />";
				$html .= "<label>{$name}</label>";
				$html .= "<input type='hidden' name='name' value='{$name}' />";
				
				$html .= "<textarea id='{$textarea_js_id}' style='width: 95%; margin-left: 10px; height: 300px;' placeholder='{$name}' name='value'>{$escapedvalue}</textarea>";
				
				$html .= "<input type='hidden' name='returnurl' value='{$returnurl}' />";
				$html .= "<br />";
				$html .= "<input type='submit' value='Save' style='background-color: #CCC;' />";
				$html .= "</form>";
				$html .= "<a href='#' onclick='jQuery(this).closest(\".INDENTED\").find(\".toggle\").toggle(); return false;'>cancel</a>";
				$html .= "</div>";
				
				$edittriggerhtml = " <a href='#' onclick='jQuery(this).closest(\".INDENTED\").find(\".toggle\").toggle(); return false;'><span style='display: inline-block; transform: rotateZ(90deg);'>&#9998;</span></a>";
				
				$html .= "<div style='display: block;' class='toggle'><label>{$name}</label><br /><div style='background-color: #eee; max-height: 300px; overflow-y: scroll; margin-left: 50px;'><span style='font-style: italic;'>{$value}</span></div>{$edittriggerhtml}</div>";		
			}
			
			$auto_async_save_dirty = true;
			if ($then_that_item["auto_async_save_dirty"] == "false")
			{
				$auto_async_save_dirty = false;
			}
			
			if ($auto_async_save_dirty)
			{
				//
				$html .= "(auto async saving dirty)";
				$html .= "<style>";
				$html .= ".ajaxindicator { padding: 10px; width: 150px; }";
				$html .= "</style>";
				$html .= "<div class='ajaxindicator'>";
				$html .= "<div style='display: none; background-color: green; color: white;' id='{$textarea_js_id}_saving'>saving ...</div>";
				$html .= "<div style='display: none; background-color: orange; color: white;' id='{$textarea_js_id}_dirty'>dirty</div>";	
				$html .= "<div style='display: none; background-color: red; color: white;' id='{$textarea_js_id}_error'>error</div><br />";
				$html .= "</div>";
				$html .= "<script>";
				$html .= "$('#{$textarea_js_id}').on('keydown', function(e){";
				$html .= "var state = $('#{$textarea_js_id}').attr('nxs_state');";			
				$html .= "if (state == 'dirty') { return; }";		
				$html .= "$('#{$textarea_js_id}').attr('nxs_state', 'dirty');";
				$html .= "$('#{$textarea_js_id}_dirty').show();";
				$html .= "setTimeout(function() { nxs_js_textarea_push_{$textarea_js_id}(); }, 5000);";
				$html .= "});";
				
				$html .= "function nxs_js_textarea_push_{$textarea_js_id}() {";
				$html .= "var state = $('#{$textarea_js_id}').attr('nxs_state');";
				$html .= "if (state != 'dirty') { console.log('not dirty'); return; }";
				$html .= "$('#{$textarea_js_id}_dirty').hide();";
				$html .= "$('#{$textarea_js_id}_saving').show();";
				$html .= "var url = '{$homeurl}api/1/prod/set-task-instance-state-parameter/?brk=task-api&brk_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$taskinstanceid}&key={$name}';";
				$html .= "var val = $('#{$textarea_js_id}').val();";
				$html .= "console.log('dirty: ' + val);";
				$html .= "$('#{$textarea_js_id}').attr('nxs_state', '');";
				$html .= "var request = {";
				$html .= "  val: val";
				$html .= "};";
				$html .= "$.ajax({";
				$html .= "  url: url,";
				$html .= "  data: request,";
				$html .= "  dataType: 'json',";
				$html .= "  type: 'POST'";
				$html .= "}).done(function(data) {";
				$html .= "console.log(data);";
				$html .= "$('#{$textarea_js_id}_saving').hide();";
				$html .= "}).fail(function(data) {;";
				$html .= "console.log(data);";
				$html .= "$('#{$textarea_js_id}_error').show();";
				$html .= "});";
				$html .= "}";
				
				$html .= "</script>"; 
			}

			$renderclipboard = true;
			if ($renderclipboard)
			{
				// copy to clipboard
				$html .= " <a href='#' onclick='navigator.clipboard.writeText(\"{$escapedvalue}\"); return false;'>copy</a>";
			}

			
			$result["console"][] = $html;
			
			$result["result"] = "OK";
			
			return $result;
		}
	}
}