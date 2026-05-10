<?php

class userModel {

private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createUser($firstName, $lastName): mixed {
        $insertQuery = "INSERT INTO tbl_users (firstName, lastName, createdAt, updatedAt) 
        VALUES (:firstName, :lastName, :createdAt, :updatedAt)";

        $dateNow = (new DateTime())->format('Y-m-d H:i:s');
        $response = $this->conn->prepare($insertQuery);
        $response->bindParam(':firstName', $firstName);
        $response->bindParam(':lastName', $lastName);
        $response->bindParam(':createdAt', $dateNow);
        $response->bindParam(':updatedAt', $dateNow);

        return $response->execute();
        

    }

     public function updateUser($userID, $firstName, $lastName): mixed{
        $query = "UPDATE tbl_users 
                  SET firstName = :firstName, lastName = :lastName, updatedAt = :updatedAt
                  WHERE user_id = :userID";

        $dateNow = (new DateTime())->format('Y-m-d H:i:s');
        $response = $this->conn->prepare($query);
        $response->bindParam(':firstName', $firstName);
        $response->bindParam(':lastName', $lastName);
        $response->bindParam(':updatedAt', $dateNow);
        $response->bindParam(':userID', $userID);

        return $response->execute();
    }

    public function readUser(): mixed {
        $selectQuery = "SELECT * FROM tbl_users";
        $response = $this->conn->prepare($selectQuery);
        $response->execute();

        return $response;
    }

    public function deleteUser($userID): mixed {
        $deleteQuery = "DELETE FROM tbl_users WHERE user_id = :userID";
        $response = $this->conn->prepare($deleteQuery);
        $response->bindParam(':userID', $userID);
        $response->execute();
        return $response;
    }

}





?>