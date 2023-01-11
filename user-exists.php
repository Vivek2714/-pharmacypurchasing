<?php

$userEmail = $_POST['email'];
// print_r($_POST);
if(email_exists($userEmail)){
    return true;
}
return false;