<?php
function brk_tasks_gui_viewmessage()
{
$taskid = $_REQUEST["taskid"];
$taskinstanceid = $_REQUEST["taskinstanceid"];

$instancemeta = brk_tasks_getinstance($taskid, $taskinstanceid);
$createtime = $instancemeta["createtime"];
$creationdate_human = date("j F Y G:i", $createtime);

$inputparameters = brk_tasks_getinstanceinputparameters($taskid, $taskinstanceid);
$message = $inputparameters["message"];

$template = $_REQUEST["template"];

if (false)
{
}
else if ($template == "")
{
	
brk_tasks_gui_rendernavigation();

nxs_gui_renderinputparametersforinstance($taskid, $taskinstanceid, $instancemeta);

?>
<h1 id='message'>Message</h1>
<?php	
	
echo $message;

require_once("/srv/generic/libraries-available/nxs-mail/nxs_mail_logic.php");
$parsed = nxs_mail_parse_message($message);
var_dump($parsed);
$related_taskinstance = $parsed["relates_to_taskinstance"];
if (isset($related_taskinstance))
{
$rel_taskid = $related_taskinstance["taskid"];
$rel_taskinstanceid = $related_taskinstance["taskinstanceid"];

$url = "https://global.nexusthemes.com/?nxs=task-gui&page=taskinstancedetail&taskid={$rel_taskid}&taskinstanceid={$rel_taskinstanceid}";
// 
echo "<div style='background-color: #ccc; padding: 25px;'>";
echo "Found a related task instance in this email. <a target='_blank' href='$url'>Open related task instance</a>";
echo "</div>";
}

$currenturl = nxs_geturlcurrentpage();
$url = $currenturl;
$url = nxs_addqueryparametertourl_v2($url, "template", 2, true, true);
echo "<div style='background-color: #ccc; padding: 25px;'>";
echo "Alternative rendering options:<br />";
echo "<a href='$url'>Template 2 (marketing purposes)</a>";
echo "</div>";
}
else if ($template == "2")
{
$manualtextstoberemoved_string = $_POST["manualtextstoberemoved_string"];
$manualtextstoberemoved_string = str_replace("\r\n", "|", $manualtextstoberemoved_string);
$manualtextstoberemoved = explode("|", $manualtextstoberemoved_string);

$firstname = $inputparameters["firstname"];
$lastname = $inputparameters["lastname"];
$licenseid = $inputparameters["licenseid"];
$sender_email = $inputparameters["sender_email"];

if ($licenseid != "")
{
$license_url = "https://license1802.nexusthemes.com/api/1/prod/licenseinsights/?nxs=licensemeta-api&nxs_json_output_format=prettyprint&licensenr={$licenseid}";
$license_string = file_get_contents($license_url);
$license_meta = json_decode($license_string, true);
$license_lastname = $license_meta["customer_data"]["lastname"];
}

$hidden = "<span style='color: #DDD; opacity: 0.5;text-shadow: 0 0 5px rgba(0,0,0,0.5);'>[HIDDEN]</span>";

// remove email addresses
$pattern = "/[^@\s]*@[^@\s]*\.[^@\s]*/";
$message = preg_replace($pattern, $hidden, $message);

// remove phone
$message = preg_replace('/([0-9][0-9][0-9]+[\- ]?[0-9][0-9]+)/', $hidden, $message);

// remove phone
$message = preg_replace('/([0-9][0-9][0-9]\.[0-9][0-9][0-9]\.[0-9][0-9][0-9][0-9])/', $hidden, $message);

$itemstoberemoved = array($license_lastname, $licenseid, $firstname, $lastname, $sender_email);
foreach ($itemstoberemoved as $itemtoberemoved)
{
$message = str_ireplace($itemtoberemoved, $hidden, $message);
}

foreach ($manualtextstoberemoved as $itemtoberemoved)
{
$message = str_ireplace($itemtoberemoved, $hidden, $message);
}

// remove @handles
$pattern = '/@([a-z0-9])+/i';
$message = preg_replace($pattern, $hidden, $message);

?>
<style>
body 
{ 
font-family: Open Sans, sans-serif; 
}
.logocontainer,
.titlecontainer,
.messagecontainer
{
padding: 5px;
margin: 0 auto;
width: 30%;
}
.logocontainer
{
color: white;
}
.logocontainer img
{
height: 45px;
}
.titlecontainer
{
background-color: rgb(48, 138, 255);
color: white;
}
.messagecontainer
{
background-color: white;
color: black;
}
</style>
<div style=''>
<div class='logocontainer'>
<img src='' />
</div>
<div class='titlecontainer'>
<h1 style='color: white;'>Client message</h1>
<span style='color: white;'><?php echo $creationdate_human; ?></span>
</div>
<div class='messagecontainer'>
<?php
echo $message;
?>
</div>
</div>
<script type='text/javascript' src='https://my.nexusthemes.com/wp-content/nexusframework/edge/js/jquery-1.11.1/jquery.min.js?ver=5.3.2'></script>
<a style='text-decoration: none' href='#' onclick="jQuery('#tune').toggle(); return false;">:)</a>
<div id='tune' style='background-color: red; display: none;'>
To anonimize particular words in the message, add them here (seperate by pipline | or add each one on a new line)
<form method='POST'>
<?php
$skip = array("manualtextstoberemoved_string");
foreach ($_REQUEST as $key => $val)
{
if (!in_array($key, $skip))
{
echo "<input type='hidden' name='$key' value='$val' />";
}
}
?>

<textarea name='manualtextstoberemoved_string'><?php echo $manualtextstoberemoved_string; ?></textarea>
<input type='submit' />
</form>
To anonimize the task gui, invoke the task gui like so;
<a target='_blank' href='https://my.nexusthemes.com/?demo=on'>TASK GUI (anonimized mode)</a>
</div>

<?php
}

die();
}
