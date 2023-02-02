<?php

function brk_tasks_gui_viewabstracttaskraw()
{
	$taskid = $_REQUEST["taskid"];
	if ($taskid == "") { functions::throw_nack("taskid not specified"); }
	
	$tasktitle = tasks::gettasktitle($taskid);
	
	$recipe = tasks::gettaskrecipe($taskid);
	?>
	<div style='font-family: courier; font-size: 10px;'><?php echo nl2br(htmlspecialchars($recipe)); ?></div>
	<?php
	
	die();
}

