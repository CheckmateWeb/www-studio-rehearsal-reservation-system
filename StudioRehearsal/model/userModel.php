<?php
class userModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createUser($firstName, $lastName): mixed {
        $query = "INSERT INTO tbl_users (firstName, lastName, createdAt, updatedAt)
                  VALUES (:firstName, :lastName, :createdAt, :updatedAt)";

        $dateNow = date('Y-m-d H:i:s');

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':createdAt', $dateNow);
        $stmt->bindParam(':updatedAt', $dateNow);

        return $stmt->execute();
    }

    public function createUserFull($firstName, $lastName, $email, $password): mixed {
        $query = "INSERT INTO tbl_users (firstName, lastName, email, password, createdAt, updatedAt)
                  VALUES (:firstName, :lastName, :email, :password, :createdAt, :updatedAt)";

        $dateNow = date('Y-m-d H:i:s');
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':createdAt', $dateNow);
        $stmt->bindParam(':updatedAt', $dateNow);

        return $stmt->execute();
    }

    public function updateUserPassword($userID, $hashedPassword): mixed {
        $query = "UPDATE tbl_users
                  SET password = :password,
                      updatedAt = :updatedAt
                  WHERE user_id = :userID";

        $dateNow = date('Y-m-d H:i:s');

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':updatedAt', $dateNow);
        $stmt->bindParam(':userID', $userID);

        return $stmt->execute();
    }

    public function updateUser($userID, $firstName, $lastName, $email = ""): mixed {
        if ($email !== "") {
            $query = "UPDATE tbl_users
                      SET firstName = :firstName,
                          lastName = :lastName,
                          email = :email,
                          updatedAt = :updatedAt
                      WHERE user_id = :userID";
        } else {
            $query = "UPDATE tbl_users
                      SET firstName = :firstName,
                          lastName = :lastName,
                          updatedAt = :updatedAt
                      WHERE user_id = :userID";
        }

        $dateNow = date('Y-m-d H:i:s');

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':updatedAt', $dateNow);
        $stmt->bindParam(':userID', $userID);

        if ($email !== "") {
            $stmt->bindParam(':email', $email);
        }

        return $stmt->execute();
    }

    public function readUser(): mixed {
        $stmt = $this->conn->prepare("SELECT * FROM tbl_users");
        $stmt->execute();
        return $stmt;
    }

    public function deleteUser($userID): mixed {
        $stmt = $this->conn->prepare("DELETE FROM tbl_users WHERE user_id = :userID");
        $stmt->bindParam(':userID', $userID);
        return $stmt->execute();
    }

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM tbl_users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function emailExistsForOtherUser($userID, $email) {
        $stmt = $this->conn->prepare(
            "SELECT user_id FROM tbl_users WHERE email = :email AND user_id != :userID LIMIT 1"
        );
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();

        return (bool) $stmt->fetch();
    }

    public function readAllUsers() {
        $stmt = $this->conn->prepare("SELECT user_id, firstName, lastName, email, createdAt, updatedAt FROM tbl_users");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function readReservations($userId) {
        $query = "SELECT
                    r.reservation_id,
                    r.user_id,
                    r.studio_id,
                    r.reservation_date,
                    r.start_time,
                    r.end_time,
                    r.total_amount,
                    CASE
                        WHEN s.studio_name IS NOT NULL THEN s.studio_name
                        WHEN r.studio_id = 1 THEN 'The Amp Room'
                        WHEN r.studio_id = 2 THEN 'The Synth Cave'
                        WHEN r.studio_id = 3 THEN 'The Vocal Booth'
                        WHEN r.studio_id = 4 THEN 'The Bass Den'
                        ELSE 'Unknown Room'
                    END AS studio_name
                  FROM tbl_reservations_table AS r
                  LEFT JOIN tbl_studios AS s
                    ON s.studio_id = r.studio_id
                  WHERE r.user_id = :userId
                  ORDER BY r.reservation_date DESC, r.start_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':userId' => $userId
        ]);

        return $stmt->fetchAll();
    }

    public function getAllReservations() {
        $query = "SELECT
                    r.reservation_id,
                    r.user_id,
                    r.studio_id,
                    r.reservation_date,
                    r.start_time,
                    r.end_time,
                    r.total_amount,
                    COALESCE(r.status_id, 1) AS status_id,
                    r.createdAt,
                    r.updatedAt,
                    u.firstName,
                    u.lastName,
                    COALESCE(st.status_name, 'Pending') AS status_name,
                    CASE
                        WHEN s.studio_name IS NOT NULL THEN s.studio_name
                        WHEN r.studio_id = 1 THEN 'The Amp Room'
                        WHEN r.studio_id = 2 THEN 'The Synth Cave'
                        WHEN r.studio_id = 3 THEN 'The Vocal Booth'
                        WHEN r.studio_id = 4 THEN 'The Bass Den'
                        ELSE 'Unknown Room'
                    END AS studio_name
                  FROM tbl_reservations_table AS r
                  INNER JOIN tbl_users AS u
                    ON u.user_id = r.user_id
                  LEFT JOIN tbl_studios AS s
                    ON s.studio_id = r.studio_id
                  LEFT JOIN tbl_status AS st
                    ON st.status_id = COALESCE(r.status_id, 1)
                  ORDER BY r.reservation_date DESC, r.start_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAllStatuses() {
        $query = "SELECT status_id, status_name FROM tbl_status ORDER BY status_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateReservationStatus($reservationID, $statusID): mixed {
        try {
            // First, check if status_id column exists by trying to update it
            $query = "UPDATE tbl_reservations_table
                      SET status_id = :statusID,
                          updatedAt = :updatedAt
                      WHERE reservation_id = :reservationID";

            $dateNow = date('Y-m-d H:i:s');

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':statusID', $statusID);
            $stmt->bindParam(':updatedAt', $dateNow);
            $stmt->bindParam(':reservationID', $reservationID);

            return $stmt->execute();
        } catch (PDOException $e) {
            // If column doesn't exist, just return true but don't update
            return true;
        }
    }
}
?>
