<?php
require_once __DIR__ . "/../model/databaseCon.php";
require_once __DIR__ . "/../model/userModel.php";

   class UserManager {
    private $userModel;

    public function __construct() {
        $database = new DatabaseCon();
        $db = $database->connectDB();
        $this->userModel = new userModel($db);
    }

        public function addUserFunc($firstName, $lastName) {

    try {
        
    if($this->userModel->createUser($firstName, $lastName)) {
        echo "User has been added";
    } else {
        echo "Error is encountered while adding value to the database.";

    }
    

    } catch (InvalidArgumentException $ex) {
        http_response_code(501);
        echo $ex->getMessage();
        exit;
    }
}

  public function updateUserFunc($userID, $firstName, $lastName) {
    try {
        if ($this->userModel->updateUser($userID, $firstName, $lastName)) {
            echo "User has been updated";
        } else {
            echo "Error is encountered while updating value to the database.";
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}
        public function deleteUserFunc($userID) {
        try{
        if ($this->userModel->deleteUser($userID)) {
            echo "User has been deleted";
        } else {
            echo "Error is encountered while deleting value to the database.";
        }

        }catch(PDOException $ex){
            echo $ex->getMessage();
       
        } 

    }

        public function getUser() {
            $response = $this->userModel->readUser();
            return $response->fetchAll(PDO::FETCH_ASSOC);
          
        }
        public function loginUserFunc($fName, $lName) {
            $fNameColumn = array_column($_SESSION["userArray"], "FirstName");
            $fNameResult = array_search($fName, $fNameColumn, true); 

            $lNameColumn = array_column($_SESSION["userArray"], "LastName");
            $lNameResult = array_search($lName, $lNameColumn, true); 

            if($fNameResult !== false && $lNameResult !== false && $fNameResult === $lNameResult){
                echo json_encode(["success" => true, "message" => "User found"]);
            }else {
                echo json_encode(["success" => false, "message" => "User not found"]);
            }
            }
        }
?>