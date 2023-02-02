<?php

function brk_tasks_gui_viewabstracttask()
{
	$taskid = $_REQUEST["taskid"];
	if ($taskid == "") { functions::throw_nack("taskid not specified"); }
	
	$action = $_REQUEST["action"];
	if (false)
	{
	}
	
	// ----
	$tasktitle = tasks::gettasktitle($taskid);
	$recipe = tasks::gettaskrecipe($taskid);
	
	// decorate applied recipe
	if (true)
	{
	$appliedrecipe = $recipe;
	
	// convert shortcodes
	$appliedrecipe = do_shortcode($appliedrecipe);
	
	// highlights
	$appliedrecipe = str_replace("{{", "<span class='placeholder'>{{", $appliedrecipe);
	$appliedrecipe = str_replace("}}", "}}</span>", $appliedrecipe);
	
	// tabs
	$appliedrecipe = str_replace("*", "<span class='tab'>&nbsp;</span>", $appliedrecipe);
	
	// replace placeholders if any are remaining
	$appliedrecipe = functions::translatesingle($appliedrecipe, "{{", "}}", $lookup);
	$appliedrecipe = nl2br($appliedrecipe);
	}
	
	// decorate task title
	if (true)
	{
	$appliedtasktitle = $tasktitle;
	$appliedtasktitle = str_replace("{{", "<span class='placeholder'>{{", $appliedtasktitle);
	$appliedtasktitle = str_replace("}}", "}}</span>", $appliedtasktitle);
	$appliedtasktitle = str_replace("*", "<span class='tab'>&nbsp;</span>", $appliedtasktitle);
	$appliedtasktitle = functions::translatesingle($appliedtasktitle, "{{", "}}", $lookup);
	}
	?>
	<style>
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
	<div>
	
	<?php
	brk_tasks_gui_rendernavigation();
	?>
	
	<h1>Task <?php echo $appliedtasktitle; ?></h1><br /><br />
	
	<div id='edittaskform' style='background-color: #DDD'>
	<a target='_blank' href='https://tasks.bestwebsitetemplates.net/?nxs=task-gui&page=createrfcinternal&taskid=<?php echo $taskid; ?>'>Create RFC</a>
	</div>
	
	<br />
	
	<div>
	<?php
	
	echo $appliedrecipe;
	?>
	</div>
	<div style='margin-bottom: 200px;'>
	&nbsp;
	</div>
	</div>
	<?php
	die();
}