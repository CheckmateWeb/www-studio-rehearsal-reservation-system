<?php
require_once __DIR__ . "/../model/databaseCon.php";
require_once __DIR__ . "/../model/userModel.php";

class UserManager {
    private const ADMIN_EMAIL = "MarivelesAdmin@gmail.com";
    public $userModel;

    public function __construct() {
        $database = new DatabaseCon();
        $db = $database->connectDB();
        $this->userModel = new userModel($db);
    }

    public function getMyReservations() {
        if (!isset($_SESSION['user_id'])) {
            return [];
        }
        return $this->userModel->readReservations($_SESSION['user_id']);
    }

    public function getAllReservations() {
        return $this->userModel->getAllReservations();
    }

    public function getUserList() {
        return $this->userModel->readAllUsers();
    }

    public function addUserFunc($firstName, $lastName) {
        try {
            echo $this->userModel->createUser($firstName, $lastName)
                ? "User has been added"
                : "Error adding user";
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function updateUserFunc($userID, $firstName, $lastName, $email = "") {
        try {
            echo $this->userModel->updateUser($userID, $firstName, $lastName, $email)
                ? "User updated successfully"
                : "Error updating user";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function deleteUserFunc($userID) {
        try {
            echo $this->userModel->deleteUser($userID)
                ? "User deleted successfully"
                : "Error deleting user";
        } catch(Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function getAllStatuses() {
        return $this->userModel->getAllStatuses();
    }

    public function updateReservationStatus($reservationID, $statusID) {
        try {
            echo $this->userModel->updateReservationStatus($reservationID, $statusID)
                ? "Status updated successfully"
                : "Error updating status";
        } catch(Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function getUser() {
        return $this->userModel->readUser()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email) {
        return $this->userModel->getUserByEmail($email);
    }

    public function emailExistsForOtherUser($userID, $email) {
        return $this->userModel->emailExistsForOtherUser($userID, $email);
    }

    public function isAdminEmail($email) {
        return strcasecmp((string) $email, self::ADMIN_EMAIL) === 0;
    }

    public function loginUserFunc($email, $password) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = $this->userModel->getUserByEmail($email);

        $isValidPassword = false;
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $isValidPassword = true;
            } elseif ($password === $user['password']) {
                // Fallback for existing plaintext passwords; rehash on successful login.
                $isValidPassword = true;
                $newHash = password_hash($password, PASSWORD_BCRYPT);
                $this->userModel->updateUserPassword($user['user_id'], $newHash);
            }
        }

        if ($user && $isValidPassword) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['firstName'];

            echo json_encode([
                "success" => true,
                "role" => ($this->isAdminEmail($user['email']) ? "admin" : "user")
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Invalid email or password"
            ]);
        }
    }
}
?>
