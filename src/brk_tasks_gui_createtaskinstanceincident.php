<?php
function brk_tasks_gui_createtaskinstanceincident()
{
$incident_taskid = $_REQUEST["incident_taskid"];
$incident_taskinstanceid = $_REQUEST["incident_taskinstanceid"];
$tasktitle = tasks::gettasktitle($taskid);

if ($_REQUEST["action"] == "createnewincident")
{
$incident = $_REQUEST["incident"];
$reported_by_email = $_REQUEST["reported_by_email"];
$reported_by_name = $_REQUEST["reported_by_name"];
$assigned_to = $_REQUEST["assigned_to"];
$mail_assignee = $_REQUEST["mail_assignee"];

$incidenttaskid = 73;
//
$action_url = "https://tasks.bestwebsitetemplates.net/api/1/prod/create-task-instance/?nxs=businessprocess-api&nxs_json_output_format=prettyprint";
$action_url = functions::addqueryparametertourl($action_url, "businessprocesstaskid", $incidenttaskid, true, true);
$action_url = functions::addqueryparametertourl($action_url, "incident", $incident, true, true);
$action_url = functions::addqueryparametertourl($action_url, "reported_by_name", $reported_by_name, true, true);
$action_url = functions::addqueryparametertourl($action_url, "reported_by_email", $reported_by_email, true, true);
$action_url = functions::addqueryparametertourl($action_url, "assigned_to", $assigned_to, true, true);
$action_url = functions::addqueryparametertourl($action_url, "mail_assignee", $mail_assignee, true, true);
$action_url = functions::addqueryparametertourl($action_url, "createdby_taskid", $incident_taskid, true, true);
$action_url = functions::addqueryparametertourl($action_url, "createdby_taskinstanceid", $incident_taskinstanceid, true, true);

$action_string = file_get_contents($action_url);
$action_result = json_decode($action_string, true);
if ($action_result["result"] != "OK") { functions::throw_nack("unable to create task instance; $action_url"); }

$newlycreatedtaskinstanceid = $action_result["taskinstanceid"];

$start_instance_url = "https://tasks.bestwebsitetemplates.net/?nxs=task-gui&page=taskinstancedetail&taskid={$incidenttaskid}&taskinstanceid={$newlycreatedtaskinstanceid}";
echo "task instance created<br />";
echo "<a href='{$start_instance_url}'>click here to handle this incident</a>";

die();
}
$currenturl = functions::geturlcurrentpage();
$firstname = brk_tasks_gui_getemployeefirstname();
$email = brk_tasks_gui_getemployeeemail();
brk_tasks_gui_rendernavigation();
?>
<h1>Create incident happening in <?php echo "{$taskid} - $tasktitle"; ?></h1>
<form action='<?php echo $currenturl; ?>' method='POST' style='margin-left: 100px;'>
<input type='hidden' name='nxs' value='task-gui' />
<input type='hidden' name='page' value='createtaskinstanceincident' />
<input type='hidden' name='action' value='createnewincident' />

<label>Reported by name:</label>
<input type='text' name='reported_by_name' value='<?php echo $firstname; ?>' /><br />

<label>Reported by email:</label>
<input type='text' name='reported_by_email' required value='<?php echo $email; ?>' /><br />

<label>Incident:</label>
<textarea placeholder='Incident' name='incident' style='width: 100%; height: 40px;'></textarea>

<label>Assign to (Johan=1, GJ=2, blank=none):</label>
<input type='text' name='assigned_to' value='' /><br />

<label>Send mail to assignee:</label>
<input type='checkbox' name='mail_assignee' checked /><br />

<input type='submit' value='Create Incident' />
</form>
<?php
}