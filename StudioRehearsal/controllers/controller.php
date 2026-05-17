<?php
session_start();
require_once __DIR__ . "/../bl/user_manager.php";
require_once __DIR__ . "/../model/databaseCon.php";
require_once __DIR__ . '/../helper/send.php';

$manager = new UserManager();
$action = $_POST['action'] ?? '';

$postString = static function ($key, $default = '') {
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
};

$postInt = static function ($key) {
    return filter_var($_POST[$key] ?? null, FILTER_VALIDATE_INT);
};

$respondText = static function ($message) {
    echo $message;
    exit();
};

$isValidRegistrationPassword = static function ($password) {
    if (!is_string($password)) {
        return false;
    }

    if (strlen($password) < 8 || strlen($password) > 64) {
        return false;
    }

    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        return false;
    }

    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return false;
    }

    return true;
};

if ($action === 'login') {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $email = filter_var($postString('email'), FILTER_VALIDATE_EMAIL);
    $password = (string) ($_POST['password'] ?? '');

    if (!$email || $password === '') {
        echo json_encode([
            "success" => false,
            "message" => "Email and password are required"
        ]);
        exit();
    }

    $manager->loginUserFunc($email, $password);
    exit();
}

if ($action === 'register') {
    ob_clean();

    $f = $postString('fName');
    $l = $postString('lName');
    $e = filter_var($postString('email'), FILTER_VALIDATE_EMAIL);
    $p = (string) ($_POST['password'] ?? '');

    if ($f === '' || $l === '' || !$e || $p === '') {
        $respondText("Invalid input");
    }

    if (!$isValidRegistrationPassword($p)) {
        $respondText("Password must be 8 to 64 characters and include letters, numbers, and at least one special character.");
    }

    if ($manager->getUserByEmail($e)) {
        $respondText("Email already exists");
    }

    $created = $manager->userModel->createUserFull($f, $l, $e, $p);

    if ($created) {
        $body = '
        <div style="margin:0;padding:24px;background-color:#f4f4f7;font-family:Arial,sans-serif;">
            <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.08);border:1px solid #e5e7eb;">
                
                <div style="background:linear-gradient(135deg,#7c3aed,#a855f7);padding:24px 30px;text-align:center;">
                    <h2 style="margin:0;color:#ffffff;font-size:28px;">New User Registration</h2>
                    <p style="margin:8px 0 0;color:#f3e8ff;font-size:14px;">Studio Rehearsal Notification</p>
                </div>

                <div style="padding:30px;">
                    <p style="margin:0 0 20px;font-size:16px;color:#374151;">
                        A new user has successfully registered on your website.
                    </p>

                    <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:20px;">
                        <p style="margin:0 0 12px;font-size:15px;color:#111827;">
                            <strong style="color:#7c3aed;">Full Name:</strong> ' . htmlspecialchars($f . ' ' . $l) . '
                        </p>
                        <p style="margin:0;font-size:15px;color:#111827;">
                            <strong style="color:#7c3aed;">Email Address:</strong> ' . htmlspecialchars($e) . '
                        </p>
                    </div>

                    <div style="margin-top:24px;padding:16px;background:#f3e8ff;border-left:4px solid #7c3aed;border-radius:10px;">
                        <p style="margin:0;font-size:14px;color:#4b5563;">
                            This is an automated message from your Studio Rehearsal system.
                        </p>
                    </div>
                </div>

                <div style="padding:18px 30px;background:#111827;text-align:center;">
                    <p style="margin:0;font-size:13px;color:#d1d5db;">
                        &copy; 2026 Studio Rehearsal. All rights reserved.
                    </p>
                </div>
            </div>
        </div>';

        sendEmail(
            "adventureprofile2@gmail.com",
            "Admin",
            "New User Registration",
            $body
        );

        $respondText("success");
    }

    $respondText("error");
}

if ($action === 'reserve') {
    if (!isset($_SESSION['user_id'])) {
        $respondText("Not logged in");
    }

    $user_id = (int) $_SESSION['user_id'];
    $studio_id = $postInt('studio_id');
    $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);

    if ($studio_id === false || $studio_id < 1 || $price === false || $price < 0) {
        $respondText("Invalid reservation details");
    }

    $dateNow = date('Y-m-d H:i:s');
    $start_time = $dateNow;
    $end_time = date('Y-m-d H:i:s', strtotime($dateNow . ' +1 hour'));

    $database = new DatabaseCon();
    $conn = $database->connectDB();

    if (!$conn) {
        $respondText("Database connection unavailable");
    }

    $query = "INSERT INTO tbl_reservations_table
              (user_id, studio_id, reservation_date, start_time, end_time, total_amount, createdAt, updatedAt)
              VALUES
              (:user_id, :studio_id, :reservation_date, :start_time, :end_time, :total_amount, :createdAt, :updatedAt)";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':studio_id', $studio_id);
    $stmt->bindParam(':reservation_date', $dateNow);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':total_amount', $price);
    $stmt->bindParam(':createdAt', $dateNow);
    $stmt->bindParam(':updatedAt', $dateNow);

    $respondText($stmt->execute() ? "success" : "error");
}

if ($action === 'logout') {
    session_unset();
    session_destroy();
    $respondText("success");
}

if ($action === 'cancelReservation') {
    if (!isset($_SESSION['user_id'])) {
        $respondText("Not logged in");
    }

    $reservationID = $postInt('reservationID');
    if ($reservationID === false || $reservationID < 1) {
        $respondText("Invalid reservation");
    }

    $manager->cancelMyReservation($reservationID);
    exit();
}

if ($action === 'contact') {
    $firstName = $postString("fName");
    $lastName = $postString("lName");
    $fullName = trim($firstName . " " . $lastName);

    $email = filter_var($postString("email"), FILTER_VALIDATE_EMAIL);
    $message = isset($_POST["message"]) ? htmlspecialchars(trim($_POST["message"])) : "";

    if ($fullName === "" || !$email || $message === "") {
        $respondText("Invalid input");
    }

    $body = '
    <div style="margin:0;padding:24px;background-color:#f4f4f7;font-family:Arial,sans-serif;">
        <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.08);border:1px solid #e5e7eb;">
            
            <div style="background:linear-gradient(135deg,#7c3aed,#a855f7);padding:24px 30px;text-align:center;">
                <h2 style="margin:0;color:#ffffff;font-size:28px;">New Contact Message</h2>
                <p style="margin:8px 0 0;color:#f3e8ff;font-size:14px;">Studio Rehearsal Contact Form</p>
            </div>

            <div style="padding:30px;">
                <p style="margin:0 0 20px;font-size:16px;color:#374151;">
                    You received a new message from your website contact form.
                </p>

                <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:20px;">
                    <p style="margin:0 0 12px;font-size:15px;color:#111827;">
                        <strong style="color:#7c3aed;">Full Name:</strong> ' . htmlspecialchars($fullName) . '
                    </p>
                    <p style="margin:0 0 12px;font-size:15px;color:#111827;">
                        <strong style="color:#7c3aed;">Email Address:</strong> ' . htmlspecialchars($email) . '
                    </p>
                    <p style="margin:0;font-size:15px;color:#111827;">
                        <strong style="color:#7c3aed;">Message:</strong><br><br>' . nl2br($message) . '
                    </p>
                </div>

                <div style="margin-top:24px;padding:16px;background:#f3e8ff;border-left:4px solid #7c3aed;border-radius:10px;">
                    <p style="margin:0;font-size:14px;color:#4b5563;">
                        This is an automated message from your Studio Rehearsal system.
                    </p>
                </div>
            </div>

            <div style="padding:18px 30px;background:#111827;text-align:center;">
                <p style="margin:0;font-size:13px;color:#d1d5db;">
                    &copy; 2026 Studio Rehearsal. All rights reserved.
                </p>
            </div>
        </div>
    </div>';

    $result = sendEmail(
        "adventureprofile2@gmail.com",
        "Admin",
        "Contact Form Submission",
        $body
    );

    if ($result === true) {
        $respondText("success");
    }

    $respondText("Unable to send message right now");
}

if (isset($_POST["fName"], $_POST["lName"]) && !isset($_POST["uID"])) {
    $manager->addUserFunc($postString("fName"), $postString("lName"));
    exit;
} elseif (isset($_POST["fName"], $_POST["lName"], $_POST["uID"], $_POST["email"])) {
    $userId = $postInt("uID");
    $email = filter_var($postString("email"), FILTER_VALIDATE_EMAIL);

    if ($userId === false || !$email) {
        $respondText("Invalid user details");
    }

    if ($manager->emailExistsForOtherUser($userId, $email)) {
        $respondText("Email already exists");
    }

    $manager->updateUserFunc(
        $userId,
        $postString("fName"),
        $postString("lName"),
        $email
    );
    exit;
} elseif (isset($_POST["fName"], $_POST["lName"], $_POST["uID"])) {
    $userId = $postInt("uID");
    if ($userId === false) {
        $respondText("Invalid user details");
    }

    $manager->updateUserFunc($userId, $postString("fName"), $postString("lName"), "");
    exit;
} elseif (isset($_POST["dID"])) {
    $userId = $postInt("dID");
    if ($userId === false) {
        $respondText("Invalid user");
    }

    $manager->deleteUserFunc($userId);
    exit;
}

if ($action === 'updateReservationStatus') {
    $reservationID = $postInt('reservationID');
    $statusID = $postInt('statusID');

    if ($reservationID === false || $statusID === false) {
        $respondText("Invalid reservation status");
    }

    $manager->updateReservationStatus($reservationID, $statusID);
    exit;
}

if (isset($_POST['userID']) && $action === 'deleteUser') {
    $userId = $postInt('userID');
    if ($userId === false) {
        $respondText("Invalid user");
    }

    $manager->deleteUserFunc($userId);
    exit;
}
?>
