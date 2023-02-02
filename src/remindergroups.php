<?php

function nxs_get_remindergroups_for_themes_OBSOLETE()
{
	// TODO: probably remindergroups can be stored in ixplatform tables...
	$remindergroups = array();
		
	// -----
	$weeks_left = 2;
	$another_reminder = array
	(
		"title" => "1st reminder",
		"requirement_shouldNOTexpireindays" => (7*($weeks_left+0))+1,
		"requirement_hastoexpireindays" => (7*($weeks_left+1)),
		"handled_by_taskid" => 166,
	);
	$remindergroups[] = $another_reminder;
	
	// -----
	$weeks_left = 1;
	$another_reminder = array
	(
		"title" => "2nd reminder",
		"requirement_shouldNOTexpireindays" => (7*($weeks_left+0))+1,
		"requirement_hastoexpireindays" => (7*($weeks_left+1)),
		"handled_by_taskid" => 167,
	);
	$remindergroups[] = $another_reminder;
	
	// -----
	$weeks_left = 0;
	$another_reminder = array
	(
		"title" => "last reminder",
		"requirement_shouldNOTexpireindays" => (7*($weeks_left+0))+1,
		"requirement_hastoexpireindays" => (7*($weeks_left+1)),
		"handled_by_taskid" => 168,
	);
	$remindergroups[] = $another_reminder;
	
	//
	return $remindergroups;
}

