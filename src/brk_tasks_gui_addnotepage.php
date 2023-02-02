<?php

function brk_tasks_gui_addnotepage()
{
    $taskid = $_REQUEST["taskid"];
    $taskinstanceid = $_REQUEST["taskinstanceid"];

    if ($_REQUEST["action"] == "appendnote")
    {
        $text = $_REQUEST["text"];

        $currentemployee_id = brk_tasks_gui_getemployeeidcurrentuser();

        $action_url = "https://tasks.bestwebsitetemplates.net/api/1/prod/add-note-to-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint";
        $action_url = functions::addqueryparametertourl($action_url, "taskid", $taskid, true, true);
        $action_url = functions::addqueryparametertourl($action_url, "taskinstanceid", $taskinstanceid, true, true);
        $action_url = functions::addqueryparametertourl($action_url, "text", $text, true, true);
        $action_url = functions::addqueryparametertourl($action_url, "author_employeeid", $currentemployee_id, true, true);

        $action_string = file_get_contents($action_url);
        $action_result = json_decode($action_string, true);
        if ($action_result["result"] != "OK") { functions::throw_nack("unable to fetch action_url; $action_url"); }

        echo "Comment was added succesfully";
        die();
    }
    $currenturl = functions::geturlcurrentpage();
    brk_tasks_gui_rendernavigation();
    ?>
    <h1>Add note</h1>
    <form action='<?php echo $currenturl; ?>' method='POST' style='margin-left: 100px;'>
    <input type='hidden' name='nxs' value='task-gui' />
    <input type='hidden' name='page' value='addnotepage' />
    <input type='hidden' name='taskid' value='<?php echo $taskid; ?>' />
    <input type='hidden' name='taskinstanceid' value='<?php echo $taskinstanceid; ?>' />
    <input type='hidden' name='action' value='appendnote' />
    <textarea placeholder='Add notes here...' name='text' style='width: 100%; height: 40px;'></textarea>
    <input type='submit' value='Add note' />
    </form>

    <?php
}