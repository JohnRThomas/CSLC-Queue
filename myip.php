<?php
    $myFile = fopen("http://www.cmyip.com", "r");

    $searchFor = "My IP Address is";

    //Read until we find the searchfor line
    while (!feof($myFile)) {
	    $line = fgets($myFile);
	    if (strpos($line,$searchFor) > 0) {
		    //We found the line before our ip!
		    //echo "Found it!";
		    break;
	    }
    }

    //The line looks something like this:
    //          <h1 class="page-title">My IP Address is 141.219.209.101</h1>
    //echo $line;
    //Strip away all the extra stuff
    $ip = str_replace("<h1 class=\"page-title\">My IP Address is ","",$line);
    $ip = str_replace("</h1>","",$ip);
    $ip = str_replace(" ","",$ip);	//remove spaces

    echo $ip;
?>