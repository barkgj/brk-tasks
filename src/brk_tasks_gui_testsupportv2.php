<?php
function brk_tasks_gui_testsupportv2()
{
	if ($_REQUEST["action"] == "getmessage")
	{
		$filetobeincluded = "/srv/generic/plugins-available/nxs-p001-shop/shortcodes.php";
		require_once($filetobeincluded);

		$messageid = $_REQUEST["messageid"];
		
		$message_en = do_shortcode("[nxs_string ops=modelproperty modeluri='{$messageid}@nxs.customerservice.message' property='message_en']");
		$handler_taskid = do_shortcode("[nxs_string ops=modelproperty modeluri='{$messageid}@nxs.customerservice.message' property='nxs.p001.businessprocess.task_id']");
		
		if ($handler_taskid == "132")
		{
			$task_input_parameters = do_shortcode("[nxs_string ops=modelproperty modeluri='{$messageid}@nxs.customerservice.message' property='task_input_parameters']");
			// for example question_id=561
			$atts = shortcode_parse_atts($task_input_parameters);
			$question_id = $atts["question_id"];
			if ($question_id != "")
			{
				$html = "";
				
				if (false)
				{
					// todo; apply answer_processors
					
					$html .= "<div style='margin:5px; padding: 5px; background-color: #FFF'>";
					$html .= "<h1>Question {$message_en}</h1>";
					$html .= "<h2>answer</h2>";
					$html .= do_shortcode("[nxs_p001_task type='render-postprocessed-answer' questionid='{$question_id}']");
					$html .= "</div>";
				}
				
				if (true)
				{
					$answer_en = do_shortcode("[nxs_string ops=modelproperty modeluri='{$question_id}@nxs.support.question' property='answer_en']");
					$youtube_url_raw = do_shortcode("[nxs_string ops=modelproperty modeluri='{$question_id}@nxs.support.question' property='youtube_url_raw']");
					
					if ($youtube_url_raw != "")
					{
						if (!nxs_stringcontains_v2($answer_en, $youtube_url_raw, true))
						{
							// watch video:
							$answer_en .= "\r\n\r\n$youtube_url_raw";
						}
					}
					
					$previewurl = "https://global.nexusthemes.com/?nxs=task-gui&page=supportquestionanswerpreview&questionid={$question_id}";
					
					$html .= "<div style='margin:5px; padding: 5px; background-color: #FFF'>";
					$html .= "<a style='float: right;' href='#' onclick=\"jQuery(this).closest('div').remove(); return false;\");return false;\">X - DISMISS</a>";					
					$html .= "<h1>Question</h1>";
					$html .= "{$message_en}";
					$html .= "<h2>Answer</h2>";
					$html .= "<div style='overflow: scroll; height: 300px;'>";
					$html .= htmlentities($answer_en);
					$html .= "</div>";
					// 
					$insertme = "";					
					$insertme .= "\r\n\r\nQuestion:\r\n{$message_en}\r\n";
					$insertme .= "\r\nAnswer:\r\n{$answer_en}\r\n\r\n";
					// tune it
					$insertme = str_replace("\r\n", "NIEUWEREGEL", $insertme);
					$insertme = str_replace("'", "SINGLEQUOTE", $insertme);
					$insertme = str_replace("\"", "DOUBLEQUOTE", $insertme);
					$insertme = htmlentities($insertme);
					$insertme = str_replace("SINGLEQUOTE", "\\'", $insertme);
					$insertme = str_replace("DOUBLEQUOTE", "&quot;", $insertme);
	
					$insertme = str_replace("NIEUWEREGEL", "'+String.fromCharCode(13)+'", $insertme);
					$html .= "<br />";
					$html .= "<a href='#' onclick=\"nxs_js_extend_answer('" . $insertme . "');return false;\">&lt;&lt;&lt; INJECT ANSWER TO RESPONSE</a>";
					$html .= "<br />";
					$html .= "<a href='{$previewurl}' target='_blank'>PREVIEW ANSWER</a>";
					$html .= "<br />";
					$html .= "</div>";
				}
			}
			else
			{
				$html = "question_id is empty? (messageid: $messageid)";
			}
		}
		else
		{
			$html .= "this task is not yet supported; {$handler_taskid}";
			
			// pull task_input_parameters from nxs.customerservice.message
			$task_input_parameters = do_shortcode("[nxs_string ops=modelproperty modeluri='{$messageid}@nxs.customerservice.message' property='task_input_parameters']");

			// convert the string into keyvalues
			// parse_str($task_input_parameters, $parameters);
			$shortcode = "[nxs_p001_task_instruction type='create_task_instance' create_taskid={$handler_taskid} render_required_fields=true {$task_input_parameters}]";
			$html .= do_shortcode($shortcode);
			// happyflow_behaviour=''
			// allow creation of child task
		}
		
		$result = array();
		$result["html"] = $html;
		
		nxs_webmethod_return_raw($result);
	}
	else if ($_REQUEST["action"] == "search")
	{
		$result = array();
		
		global $nxs_g_modelmanager;

		$term = strtolower($_REQUEST["term"]);
				
		// the filters that need to be applied as specified by the user
		$filter_category_ids = array();
		$filter_texts = array();
		
		// the items to return
		$return_category_ids = array();
		$return_message_ids = array();
		
		$term = str_replace(" ", " , ", $term);
		
		$terms = explode(",", $term);
		foreach ($terms as $term)
		{
			$term = trim($term);
			if ($term == "")
			{
				// ignore
				continue;
			}
			
			if (nxs_stringstartswith($term, "["))
			{
				// a semantic term 
				$pieces = explode("_", $term);
				if ($pieces[1] == "cat")
				{
					$filter_category_ids[] = $pieces[2];
				}
			}
			else
			{
				$subterms = explode(" ", $term);
				foreach ($subterms as $subterm)
				{
					$filter_texts[] = $subterm;
				}
			}
		}
		
		$result["debug"]["filter_category_ids"] = $filter_category_ids;
		$result["debug"]["filter_texts"] = $filter_texts;

		//		
		$category_ids = array();
		$message_ids = array();
		
		// search categories
		if (count($filter_category_ids) == 0)
		{
			// find categories nxs.customerservice.category based on searchterm
			$items = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels(array("singularschema" => "nxs.customerservice.category"));
			foreach ($items as $item)
			{
				$category_id = $item["nxs.customerservice.category_id"];
				$title_en = strtolower($item["title_en"]);
				$shouldinclude = true;
				foreach ($filter_texts as $filter_text)
				{
					if (!nxs_stringcontains_v2($title_en, $filter_text, true))
					{
						$shouldinclude = false;
						break;
					}
				}
				
				if ($shouldinclude)
				{
					$return_category_ids[] = $category_id;
				}
			}
		}
		
		// search messages (and related categories)
		if (true)
		{
			$items = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels(array("singularschema" => "nxs.customerservice.message"));
			foreach ($items as $item)
			{
				$shouldinclude = true;
				
				// only include messages in the specified filter_category_ids
				if ($shouldinclude)
				{
					$category_ids_string = $item["nxs.customerservice.category_ids"];
					
					foreach ($filter_category_ids as $filter_category_id)
					{
						if (!nxs_stringcontains($category_ids_string, ";{$filter_category_id};"))
						{
							$shouldinclude = false;
						}
					}
				}
				
				// only include messages with message having text we are searchin for
				if ($shouldinclude)
				{
					$message_en = strtolower($item["message_en"]);
					foreach ($filter_texts as $filter_text)
					{
						if (!nxs_stringcontains_v2($message_en, $filter_text, true))
						{
							$shouldinclude = false;
							break;
						}
					}
				}
				
				if ($shouldinclude)
				{
					$message_id = $item["nxs.customerservice.message_id"];
					$related_category_ids_string = $item["nxs.customerservice.category_ids"];
					// ;23;141;
					$related_category_ids = explode(";", $related_category_ids_string);
					// remove empty items
					$related_category_ids = array_filter($related_category_ids);
					foreach ($related_category_ids as $related_category_id)
					{
						if (!in_array($related_category_id, $filter_category_ids))
						{
							$return_category_ids[]= $related_category_id;
						}
					}
					$return_message_ids[] = $message_id;
				}
			}
		}
		
		// make distinct
		$return_category_ids = array_unique($return_category_ids);
		$return_message_ids = array_unique($return_message_ids);
		
		// build result
		
		// include the categories
		foreach ($return_category_ids as $category_id)
		{
			$title = do_shortcode("[nxs_string ops=modelproperty modeluri='{$category_id}@nxs.customerservice.category' property='title_en']");
			$sanitizedtitle = nxs_stripspecialchars($title);
			$result[] = array
			(
				"label" => "filter category $title",
				"value" => "[filter_cat_{$category_id}_{$sanitizedtitle}]",
				"type" => "cat"
			);
		}
		
		// include the messages
		foreach ($return_message_ids as $message_id)
		{
			// fetch questionid from message_id
			$task_input_parameters = do_shortcode("[nxs_string ops=modelproperty modeluri='{$message_id}@nxs.customerservice.message' property='task_input_parameters']");
			if (nxs_stringstartswith($task_input_parameters, "question_id="))
			{
				$pieces = explode("=", $task_input_parameters);
				$questionid = $pieces[1];
			}
			
			$title = do_shortcode("[nxs_string ops=modelproperty modeluri='{$message_id}@nxs.customerservice.message' property='message_en']");
			$sanitizedtitle = nxs_stripspecialchars($title);
			$result[] = array
			(
				"label" => "msg: $title",
				"value" => "[filter_msg_{$message_id}_qid_{$questionid}_{$sanitizedtitle}]",
				"type" => "msg",
				"id" => $message_id
			);
		}
		
		nxs_webmethod_return_raw($result);
	}
	
	//
	
	brk_tasks_gui_render_head();
	
	$initialanswer = "";
	
	$taskid = $_REQUEST["taskid"];
	$taskinstanceid = $_REQUEST["taskinstanceid"];
	
	if ($taskid != "" && $taskinstanceid != "")
	{
		// get the existing custom reply
		$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
		$initialanswer = $inputparameters["custom_reply"];
		$firstname = $inputparameters["firstname"];
		
		$original_helpscoutticketnr = $inputparameters["original_helpscoutticketnr"];
		
		
		if ($initialanswer == "")
		{
			// if its empty (which it will be initially),
			// then use the message from the input as requested by the user
			$initialanswer = $inputparameters["message"];
			// strip tags
			$initialanswer = strip_tags($initialanswer, '<p><a><img>');
			
			$initialanswer = str_replace("?", "?\r\n\r\n", $initialanswer);
			$initialanswer = str_replace("</p>", "\r\n", $initialanswer);
			$initialanswer = str_replace("<p>", "\r\n", $initialanswer);
			$initialanswer = str_replace(", ", "NXS_KOMMA", $initialanswer);
			$initialanswer = str_replace(",", "NXS_KOMMA", $initialanswer);
			$initialanswer = str_replace("NXS_KOMMA", ",\r\n", $initialanswer);
			
			// get rid of empty lines not adding any value
			$initialanswer = str_replace(" >\r\n", "", $initialanswer);
			$initialanswer = str_replace(" > \r\n", "", $initialanswer);
			
			$initialanswer = str_replace(". ", ".\r\n", $initialanswer);
			
			// get rid of huge gaps
			for ($index = 0; $index < 10; $index++)
			{
				// 3 next lines => 2 next lines
				$initialanswer = str_replace("\r\n\r\n\r\n", "\r\n\r\n", $initialanswer);
			}
			
			$initialanswer = str_replace("\r\n", "\r\n &gt; ", $initialanswer);
			
			$initialanswer = trim($initialanswer);
			
			$support_employee_firstname = brk_tasks_gui_getemployeefirstname();
			
			$support_response_template = "Dear {{firstname}},\r\n
{{quoted_msg}}
\r\n
Sincerely,
{{support_employee_firstname}}";
			$quoted_msg = $initialanswer;
			
			$blended = $support_response_template;
			$blended = str_replace("{{firstname}}", $firstname, $blended);
			$blended = str_replace("{{quoted_msg}}", $quoted_msg, $blended);
			$blended = str_replace("{{support_employee_firstname}}", $support_employee_firstname, $blended);
			
			$initialanswer = $blended;
		}
	}
	else
	{
		for ($index = 0; $index < 100; $index++)
		{
			$initialanswer .= "hello\r\nworld\r\nanotherline\r\n\r\n";
		}
	}
	
	
	// $random = nxs_generaterandomstring(16, 'abcdefghijklmnpqrstuvwxyz');
	$textarea_js_id = "nxs_js_textarea_testsupportv2";
	
	?>
	<script>
		// kudo to https://stackoverflow.com/questions/1064089/inserting-a-text-where-cursor-is-using-javascript-jquery
		function insertAtCaret(areaId, text) {
		  var txtarea = document.getElementById(areaId);
		  if (!txtarea) {
		    return;
		  }
		
		  var scrollPos = txtarea.scrollTop;
		  var strPos = 0;
		  var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
		    "ff" : (document.selection ? "ie" : false));
		  if (br == "ie") {
		    txtarea.focus();
		    var range = document.selection.createRange();
		    range.moveStart('character', -txtarea.value.length);
		    strPos = range.text.length;
		  } else if (br == "ff") {
		    strPos = txtarea.selectionStart;
		  }
		
		  var front = (txtarea.value).substring(0, strPos);
		  var back = (txtarea.value).substring(strPos, txtarea.value.length);
		  txtarea.value = front + text + back;
		  strPos = strPos + text.length;
		  if (br == "ie") {
		    txtarea.focus();
		    var ieRange = document.selection.createRange();
		    ieRange.moveStart('character', -txtarea.value.length);
		    ieRange.moveStart('character', strPos);
		    ieRange.moveEnd('character', 0);
		    ieRange.select();
		  } else if (br == "ff") {
		    txtarea.selectionStart = strPos;
		    txtarea.selectionEnd = strPos;
		    txtarea.focus();
		  }
		
		  txtarea.scrollTop = scrollPos;
		  
		  //
		  $('#<?php echo $textarea_js_id; ?>').trigger('becamedirty');
		}
	  
	  function nxs_js_extend_answer(answer)
	  {
	  	insertAtCaret('nxs_js_textarea_testsupportv2', answer);
	  }
	  

	</script>
	
	<script>
  $( function() {
    function split( val ) {
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }
    
    $("#supportphrase").on("messageselected", 
    	function(event, messageid) 
    	{
		  	console.log("from what we heard a message was selected");
		  	console.log(messageid);
		  	
		  	$.getJSON
        ( 
        	"https://global.nexusthemes.com/", 
          {
          	nxs: 'task-gui',
          	page: 'testsupportv2',
          	action: 'getmessage',
            messageid: messageid,
            taskid: '<?php echo $taskid; ?>',
            taskinstanceid: '<?php echo $taskinstanceid; ?>'
          }
        ).done
        (
        	function( data ) 
        	{
						jQuery('#answercontainer').append(data.html);
						jQuery('#answercontainer').show();
        	}
        );
			}
		);
 
    $("#supportphrase")
      // don't navigate away from the field on tab when selecting an item
      .on( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).autocomplete( "instance" ).menu.active ) {
          event.preventDefault();
        }
      })
      .autocomplete({
        source: function( request, response ) {
          $.getJSON
          ( 
          	"https://global.nexusthemes.com/", 
	          {
	          	nxs: 'task-gui',
	          	page: 'testsupportv2',
	          	action: 'search',
	            term: request.term,
	            taskid: '<?php echo $taskid; ?>',
            	taskinstanceid: '<?php echo $taskinstanceid; ?>'
	          }, 
          	response 
          );
        },
        search: function() {
          // custom minLength
          var term = this.value;
          if ( term.length < 2 ) {
            return false;
          }
        },
        focus: function() {
          // prevent value inserted on focus
          return false;
        },
        select: function( event, ui ) 
        {
          var terms = split( this.value );
          // remove the current input
          terms.pop();
          // add the selected item
          terms.push( ui.item.value );
          
          if (ui.item.type == 'msg')
          {
          	var messageid = ui.item.id;
          	
          	// pull data for the selected msg in the answercontainer
          	$("#supportphrase").trigger("messageselected", messageid);
          }
          
          // add placeholder to get the comma-and-space at the end
          terms.push( "" );
          this.value = terms.join( ", " );
          
          if (ui.item.type == 'cat')
          {
          	$("#supportphrase").trigger("keydown", "fakekeydown_triggeredbycategoryselected");
          }
          
          return false;
        }
      });
  } );
  </script>
 	<?php
 		if ($original_helpscoutticketnr != "")
 		{
 			$helpscout_url = "https://secure.helpscout.net/search/?query={$original_helpscoutticketnr}";
 			
 			?>
 			<a href='<?php echo $helpscout_url; ?>' target='_blank'>Helpscout link</a><br />
 			<?php
 		}
 	?>
 	<?php
 		echo do_shortcode("[nxs_p001_task_instruction type='conditional_wrapper_begin' title='Elementor question no answer yet?' id='if_elementorquestionnoansweryet']");
		$create_offspringtaskinstance_taskid = 535; // 535 = Handle Elementor specific support question that currently has no answer
		echo do_shortcode("[nxs_p001_task_instruction type='create-task-instance' create_taskid='{$create_offspringtaskinstance_taskid}']");
		echo do_shortcode("[nxs_p001_task_instruction type='conditional_wrapper_end']");
 	?>
	Response:<br />
	<textarea id='<?php echo $textarea_js_id; ?>' style='width: 740px; height: 400px; background-color: #eee;'><?php echo $initialanswer; ?></textarea>
	
	
	
	<div id='supportassister' style='width: 600px; background-color: #ddd; position: fixed; top:0; right: 0'>
	  <div class="ui-widget">
	  	<a target='_blank' href='https://docs.google.com/spreadsheets/d/1SbNx6_vcGNBjvx1QdItlEyYpt31PhRC_3YMsFswaj9o/edit#gid=2070580445'>Add a new answer (question)</a>
	  	| 
	  	<a target='_blank' href='https://docs.google.com/spreadsheets/d/1SbNx6_vcGNBjvx1QdItlEyYpt31PhRC_3YMsFswaj9o/edit#gid=242338502'>Add a new message</a> 
			<br />	  	
	  	
		  <label for="supportphrase">Support: </label>
  		<input id="supportphrase" size="50">
  		<div id='answercontainer'  style='display: none; background-color: #777;'>
  		</div>
		</div>
	</div>
	<?php
	
	

	if ($taskid != "" && $taskinstanceid != "")
	{
		$returnurl = "https://global.nexusthemes.com/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid}&taskinstanceid={$taskinstanceid}";
		?>
		<br />Finished with this reply? Saved the response? Then <a href='#' onclick="nxs_js_textarea_push_<?php echo $textarea_js_id; ?>('<?php echo $returnurl; ?>'); return false;">proceed here</a>
		<?php
	}
	
	if ($taskid != "" && $taskinstanceid != "")
	{
		// the field to edit/store
		$name = "custom_reply";
		
		//$html .= "(auto async saving dirty)";
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
		// ignore keypresses for arrow keys
		$html .= "if (e.key === 'ArrowUp' || e.key === 'ArrowDown') return;";
		$html .= "if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') return;";
		$html .= "if (e.key === 'Shift' || e.key === 'Control' || e.key === 'Alt') return;";
		$html .= "console.log(e.key);";
		$html .= "$('#{$textarea_js_id}').trigger('becamedirty');";
		$html .= "});";
		
		$html .= "$('#{$textarea_js_id}').on('becamedirty', function(e){";
		$html .= "var state = $('#{$textarea_js_id}').attr('nxs_state');";			
		$html .= "if (state == 'dirty') { return; }";		
		$html .= "$('#{$textarea_js_id}').attr('nxs_state', 'dirty');";
		$html .= "$('#{$textarea_js_id}_dirty').show();";
		$html .= "setTimeout(function() { nxs_js_textarea_push_{$textarea_js_id}('stay'); }, 5000);";
		$html .= "});";
		
		
		
		$html .= "function nxs_js_textarea_push_{$textarea_js_id}(nextup) {";
		$html .= "var state = $('#{$textarea_js_id}').attr('nxs_state');";
		$html .= "if (state != 'dirty') { ";
		$html .= "console.log('not dirty'); ";
		$html .= "if (nextup == 'stay') { return; }";
		$html .= "window.location.href = nextup;";
		$html .= "}";
		$html .= "$('#{$textarea_js_id}_dirty').hide();";
		$html .= "$('#{$textarea_js_id}_saving').show();";
		$html .= "var url = 'https://global.nexusthemes.com/api/1/prod/set-task-instance-input-parameter/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&taskid={$taskid}&taskinstanceid={$taskinstanceid}&key={$name}';";
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
		$html .= "console.log('nextup:' + nextup);";
		$html .= "$('#{$textarea_js_id}_saving').hide();";
		$html .= "if (nextup != 'stay') { ";
		$html .= "window.location.href = nextup;";
		$html .= "} ";
		$html .= "}).fail(function(data) {;";
		$html .= "console.log(data);";
		$html .= "$('#{$textarea_js_id}_error').show();";
		$html .= "});";
	  $html .= "}";
		
		$html .= "</script>"; 
		
		echo $html;
	}
	
	//
	
	echo "</body>";
	echo "</html>";
	
	die();
}