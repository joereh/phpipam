<?php

/** 
 * Edit custom IP field
 ************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* checks */
if($_POST['action'] == "delete") {
	# no cehcks
}
else {
	# remove spaces
	$_POST['name'] = trim($_POST['name']);
	
	# length > 4 and < 12
	if( (strlen($_POST['name']) < 4) || (strlen($_POST['name']) > 24) ) 	{ $errors[] = 'Name must be between 4 and 24 characters!'; }
	
	/* validate HTML */
	
	# must not start with number
	if(is_numeric(substr($_POST['name'], 0, 1))) 							{ $errors[] = 'Name must not start with number'; }		

	# only alphanumeric and _ are allowed
	if(!preg_match('!^[\w_ ]*$!', $_POST['name'])) 							{ $errors[] = 'Only alphanumeric, spaces and underscore characters are allowed'; }
}



/* die if errors otherwise execute */
if(sizeof($errors) != 0) {
	print '<div class="alert alert-error">Please correct the following errors:'. "\n";
	print '<ul>'. "\n";
	foreach($errors as $error) {
		print '<li style="text-align:left">'. $error .'</li>'. "\n";
	}
	print '</ul>'. "\n";
	print '</div>'. "\n";
}
else {
	if(!updateCustomVLANField($_POST)) 		{ print '<div class="alert alert-error">Failed to '. $_POST['action'] .' field!</div>';}
	else 									{ print '<div class="alert alert-success">Field '. $_POST['action'] .' success!</div>';}
}

?>