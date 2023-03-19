<?php
if(isset($_REQUEST['op'])) { $op = $_REQUEST['op']; } else { $op = ''; }


switch ($op) {
    case "autotheme":
        if(isset($_REQUEST['op'])) { 
		  $_REQUEST['op'] = 'main'; 
		}
        if(!isset($_REQUEST['module'])) { 
		  $_REQUEST['module'] = "AutoTheme";
		}
    default:
    	if (isset($_REQUEST['module']) && $_REQUEST['module'] == "AutoTheme") {
        	include("modules/AutoTheme/admin.php");
        	$func = "AutoTheme_admin_".$_REQUEST['op'];
        	$vars = array_merge($_GET, $_POST);
        	$func($vars);
    	}
    	break;
}
