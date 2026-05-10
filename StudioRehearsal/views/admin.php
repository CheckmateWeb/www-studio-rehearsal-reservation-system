<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['email'] !== "MarivelesAdmin@gmail.com") {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../bl/user_manager.php";
$manager = new UserManager();
$users = $manager->getUserList();
$reservations = $manager->getAllReservations();
$statuses = $manager->getAllStatuses();

// 💡 CALCULATIONS FOR CARDS
$totalUsers = count($users);
$totalReservations = count($reservations);
$totalRevenue = array_sum(array_column($reservations, 'total_amount'));

// 💡 FIND MOST BOOKED ROOM
$roomCounts = [];
foreach ($reservations as $r) {
    $room = $r['studio_name'];
    if (!isset($roomCounts[$room])) {
        $roomCounts[$room] = 0;
    }
    $roomCounts[$room]++;
}

$mostBookedRoom = "No reservations";
if (!empty($roomCounts)) {
    $mostBookedRoom = array_keys($roomCounts, max($roomCounts))[0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Studio Reserve</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../Designs/style.css">
</head>
<body>
    <div class="main-content">
        <nav class="custom-nav">
            <div class="nav-container">
                <h1 class="brand-logo-custom">ADMIN PANEL</h1>
                <div>
                    <a href="dashboard.php" style="color: white; margin-right: 15px;">View Site</a>
                    <a href="javascript:void(0)" onclick="logout()" class="logout-link">Logout</a>
                </div>
            </div>
        </nav>

        <main class="dashboard-container">

            <!-- 🔥 NEW CARDS SECTION -->
            <div class="card-container">
                <div class="card">
                    <h3>Total Users</h3>
                    <p><?= $totalUsers ?></p>
                </div>

                <div class="card">
                    <h3>Total Reservations</h3>
                    <p><?= $totalReservations ?></p>
                </div>

                <div class="card">
                    <h3>Total Revenue</h3>
                    <p>₱<?= number_format($totalRevenue, 2) ?></p>
                </div>

                <div class="card">
                    <h3>Most Booked Room</h3>
                    <p><?= htmlspecialchars($mostBookedRoom) ?></p>
                </div>
            </div>

          <div class="chart-section">
    <h2 class="chart-title">Reservations Per Room</h2>
    <div class="chart-box">
        <div class="chart-inner">
            <canvas id="roomChart"></canvas>
        </div>
    </div>
</div>

            <div class="section-intro">
                <h2 class="main-title">User Management</h2>
                <p class="main-subtitle">Manage registered users.</p>
            </div>

            <table class="reservation-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['user_id']) ?></td>
                                <td><?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['createdAt']) ?></td>
                                <td><?= htmlspecialchars($user['updatedAt']) ?></td>

                                <td class="actions-cell">
                                    <button class="btn action-btn update-btn"
                                        onclick="openUpdateModal(
                                            '<?= htmlspecialchars($user['user_id'], ENT_QUOTES) ?>',
                                            '<?= htmlspecialchars($user['firstName'], ENT_QUOTES) ?>',
                                            '<?= htmlspecialchars($user['lastName'], ENT_QUOTES) ?>',
                                            '<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>'
                                        )">
                                        Update
                                    </button>

                                    <button class="btn action-btn delete-btn"
                                        onclick="deleteFunc('<?= htmlspecialchars($user['user_id'], ENT_QUOTES) ?>')">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="table-section">
                <h2 class="table-title">Room Reservations</h2>

                <table class="reservation-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Room</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($reservations)): ?>
                            <?php foreach ($reservations as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['firstName'] . ' ' . $r['lastName']) ?></td>
                                    <td><?= htmlspecialchars($r['studio_name']) ?></td>
                                    <td><?= date('F j, Y, g:i a', strtotime($r['reservation_date'])) ?></td>
                                    <td class="highlight-text">₱<?= number_format($r['total_amount'], 2) ?></td>
                                    <td>
                                        <select class="status-dropdown" data-reservation-id="<?= htmlspecialchars($r['reservation_id']) ?>" onchange="updateReservationStatus(this)">
                                            <?php foreach ($statuses as $status): ?>
                                                <option value="<?= htmlspecialchars($status['status_id']) ?>" <?= ($r['status_id'] == $status['status_id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($status['status_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="actions-cell">
                                        <button class="btn action-btn update-btn" onclick="viewReservationDetails('<?= htmlspecialchars($r['reservation_id'], ENT_QUOTES) ?>')">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-state">No reservations yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-left">
                <h2>StudioRehearsal</h2>
                <p>Your space. Your sound. Your moment.</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2026 StudioRehearsal. All rights reserved.</p>
        </div>
    </footer>

    <script src="../scripts/service.js"></script>

    <script>
        function updateReservationStatus(selectElement) {
            const reservationID = selectElement.getAttribute('data-reservation-id');
            const statusID = selectElement.value;

            $.post("../controllers/controller.php", {
                action: "updateReservationStatus",
                reservationID: reservationID,
                statusID: statusID
            }, function(res) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Status updated successfully',
                    icon: 'success',
                    background: '#1a1a1a',
                    color: '#fff',
                    confirmButtonColor: '#bb86fc'
                }).then(() => {
                    location.reload();
                });
            }).fail(function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update status',
                    icon: 'error',
                    background: '#1a1a1a',
                    color: '#fff',
                    confirmButtonColor: '#bb86fc'
                });
            });
        }

        function viewReservationDetails(reservationID) {
            Swal.fire({
                title: 'Reservation Details',
                text: 'Reservation ID: ' + reservationID,
                icon: 'info',
                background: '#1a1a1a',
                color: '#fff',
                confirmButtonColor: '#bb86fc'
            });
        }

        function deleteFunc(userID) {
            Swal.fire({
                title: 'Delete User',
                text: 'Are you sure you want to delete this user?',
                background: '#1a1a1a',
                color: '#fff',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ff5252',
                cancelButtonColor: '#888'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("../controllers/controller.php", {
                        userID: userID,
                        action: 'deleteUser'
                    }, function(res) {
                        Swal.fire("Deleted!", res, "success")
                            .then(() => location.reload());
                    });
                }
            });
        }

        function openUpdateModal(userID, firstName, lastName, email) {
            Swal.fire({
                title: 'Update User',
                background: '#1a1a1a',
                color: '#fff',
                html: `
                    <input id="swal-fname" class="swal2-input" value="${firstName}">
                    <input id="swal-lname" class="swal2-input" value="${lastName}">
                    <input id="swal-email" class="swal2-input" value="${email}">
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                confirmButtonColor: '#bb86fc',
                preConfirm: () => {
                    return {
                        fName: document.getElementById('swal-fname').value,
                        lName: document.getElementById('swal-lname').value,
                        email: document.getElementById('swal-email').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("../controllers/controller.php", {
                        uID: userID,
                        fName: result.value.fName,
                        lName: result.value.lName,
                        email: result.value.email
                    }, function(res) {
                        Swal.fire("Updated!", res, "success")
                        .then(() => location.reload());
                    });
                }
            });
        }
    </script>
  
   <script>
    const roomLabels = <?= json_encode(array_keys($roomCounts)) ?>;
    const roomData = <?= json_encode(array_values($roomCounts)) ?>;

    new Chart(document.getElementById('roomChart'), {
        type: 'bar',
        data: {
            labels: roomLabels,
            datasets: [{
                label: 'Reservations',
                data: roomData,
                backgroundColor: [
                    'rgba(187, 134, 252, 0.85)',
                    'rgba(153, 101, 244, 0.85)'
                ],
                borderColor: [
                    '#d7b8ff',
                    '#c9a6ff'
                ],
                borderWidth: 1.5,
                borderRadius: 14,
                borderSkipped: false,
                barThickness: 120,
                maxBarThickness: 140
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1200,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#111',
                    titleColor: '#fff',
                    bodyColor: '#ddd',
                    borderColor: 'rgba(187, 134, 252, 0.35)',
                    borderWidth: 1,
                    cornerRadius: 12,
                    padding: 12,
                    displayColors: false
                }
            },
            layout: {
                padding: {
                    top: 8,
                    right: 10,
                    bottom: 0,
                    left: 10
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: 'rgba(255,255,255,0.82)',
                        stepSize: 1,
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.05)',
                        drawBorder: false
                    },
                    border: {
                        display: false
                    }
                },
                x: {
                    ticks: {
                        color: 'rgba(255,255,255,0.92)',
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    },
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    border: {
                        display: false
                    }
                }
            }
        }
    });
</script>
</body>
</html>