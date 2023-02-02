<?php

if ($_REQUEST["action"] != "")
{
	$action = $_REQUEST["action"];
	if ($action == "generate")
	{
		$from = $_REQUEST["from"];
		$to = $_REQUEST["to"];
		if ($from > $to) { echo "from>to not allowed"; die(); }
		$format = $_REQUEST["format"];
		
		$placeholder = $_REQUEST["placeholder"];
		$template = $_REQUEST["template"];
		
		$result = array();
		$triesleft = 1999;
		
		if (false)
		{
			//
		}
		else if ($format == "json")
		{			
			for ($index = $from; $index<= $to; $index++)
			{
				$instance_string = $template;
				$instance_string = str_replace($placeholder, $index, $instance_string);
				$instance_json = json_decode($instance_string, true);
				
				$result[] = $instance_json;
				$triesleft--;
				if ($triesleft <= 0)
				{
					echo "endless loop? ($index = $from; $index<= $to)";
					die();
				}
			}
			
			$pretty_string = json_encode($result, JSON_PRETTY_PRINT);
			header('Content-type: text/plain');
	
			echo $pretty_string;
		}
		else if ($format == "text")
		{
			$result = "";
			for ($index = $from; $index<= $to; $index++)
			{
				$instance_string = $template;
				$instance_string = str_replace($placeholder, $index, $instance_string);
				
				$result .= $instance_string . "\r\n";
				$triesleft--;
				if ($triesleft <= 0)
				{
					echo "endless loop? ($index = $from; $index<= $to)";
					die();
				}
			}				
			
			header('Content-type: text/plain');
			// echo "output:";
			echo $result;
		}
		else
		{
			
		}
		die();
	}
	echo "processing request ... (todo)";
	die();
}

?>
<form action='https://global.nexusthemes.com/?nxs=task-gui&page=stringrepeater' method='POST'>
	From index: <br />
	<input name='from' type='text' value='1' /><br />
	To index: <br />
	<input name='to' type='text' value='10' /><br />
	Placeholder: <br />
	<input name='placeholder' type='text' value='1' /><br />
	
	<textarea name='template' style='width: 100%; min-height: 300px;'>
		{
			"i": "(i)",
			"title": "title (i)",
			"static": "static",
			"alt": "{{alt_(i)}}"
		}
	</textarea>
	<input type='hidden' name='action' value='generate' />		
	<select name='format'>
		<option value='json' selected'>JSON</option>
		<option value='text' selected'>plain text</option>
	</select>
	<br /><br />
	<input type='submit' value='Generate' />
</form>
<?php
die();