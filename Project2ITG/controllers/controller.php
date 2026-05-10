<?php
session_start();
require_once "../bl/user_manager.php";

$usermanager = new UserManager();

if (isset($_POST["fName"], $_POST["lName"]) && !isset($_POST["uID"])) {
    $usermanager->addUserFunc($_POST["fName"], $_POST["lName"]);
    exit;
} 
else if (isset($_POST["fName"], $_POST["lName"], $_POST["uID"])) {
    $usermanager->updateUserFunc($_POST["uID"], $_POST["fName"], $_POST["lName"]);
    exit;
} 
else if (isset($_POST["dID"])) {
    $usermanager->deleteUserFunc($_POST["dID"]);
    exit;
} 
else if (isset($_POST["lFName"], $_POST["lLName"])) {
    $usermanager->loginUserFunc($_POST["lFName"], $_POST["lLName"]);
    exit;
}
?>