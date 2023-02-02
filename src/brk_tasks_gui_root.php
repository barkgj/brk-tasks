<?php

use barkgj;
use barkgj\functions;
use barkgj\tasks;

//$loader = require_once __DIR__ . '/vendor/autoload.php';

// todo; fix autoloader of composer so this is not needed...
require_once __DIR__ . '/vendor/barkgj/datasink-library/src/datasink-entity.php';
require_once __DIR__ . '/vendor/barkgj/functions-library/src/filesystem.php';
require_once __DIR__ . '/vendor/barkgj/tasks-library/src/tasks.php';


function brk_tasks_gui_root()
{
    echo "ROOT OF TASKS SYSTEM :)<br /><br />";

    $currenturl = functions::geturlcurrentpage();
    $action_url = $currenturl;
    $workqueue_url = functions::addqueryparametertourl($action_url, "page", "workqueue", true, true);
    echo "<a href='{$workqueue_url}'>workqueue</a>";    

    echo phpinfo();

    die();
}