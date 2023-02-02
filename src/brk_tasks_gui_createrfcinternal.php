<?php

function brk_tasks_gui_createrfcinternal()
{
    $taskid = $_REQUEST["taskid"];
    $tasktitle = tasks::gettasktitle($taskid);

    if ($_REQUEST["action"] == "createnewtaskrfc")
    {
    $requirements = $_REQUEST["requirements"];
    //
    $taskid_to_edittask = "74";
    $action_url = "https://tasks.bestwebsitetemplates.net/api/1/prod/create-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint&businessprocesstaskid={$taskid_to_edittask}&taskid_to_edit={$taskid}&requirements={$requirements}";

    $action_string = file_get_contents($action_url);
    $action_result = json_decode($action_string, true);
    if ($action_result["result"] != "OK") { functions::throw_nack("unable to create task instance; $action_url"); }

    $newlycreatedtaskinstanceid = $action_result["taskinstanceid"];

    $start_instance_url = "https://tasks.bestwebsitetemplates.net/?nxs=task-gui&page=taskinstancedetail&taskid={$taskid_to_edittask}&taskinstanceid={$newlycreatedtaskinstanceid}";
    echo "task instance created<br />";
    echo "<a href='{$start_instance_url}'>click here to start this new instance</a>";

    die();
    }
    $currenturl = functions::geturlcurrentpage();
    brk_tasks_gui_rendernavigation();
    ?>
    <h1>Create rfc for <?php echo "{$taskid} - $tasktitle"; ?></h1>
    <form action='<?php echo $currenturl; ?>' method='POST' style='margin-left: 100px;'>
    <input type='hidden' name='nxs' value='task-gui' />
    <input type='hidden' name='page' value='createrfcinternal' />
    <input type='hidden' name='action' value='createnewtaskrfc' />
    <textarea placeholder='Requirements for RFC' name='requirements' style='width: 100%; height: 40px;'></textarea>
    <input type='submit' value='Create RFC' />
    </form>

    <?php
}