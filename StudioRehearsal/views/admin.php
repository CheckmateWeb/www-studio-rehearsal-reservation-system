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

$totalUsers = count($users);
$totalReservations = count($reservations);
$totalRevenue = array_sum(array_column($reservations, 'total_amount'));

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
                <div class="brand-block">
                    <h1 class="brand-logo-custom">Admin Panel</h1>
                    <span class="brand-caption">Users, reservations, and room activity</span>
                </div>
                <div class="nav-actions">
                    <a href="dashboard.php" class="nav-link">View Site</a>
                    <a href="javascript:void(0)" onclick="logout()" class="logout-link" aria-label="Log out of your administrator account">Logout</a>
                </div>
            </div>
        </nav>

        <main class="dashboard-container">
            <section class="hero-panel">
                <div class="hero-grid">
                    <div>
                        <p class="section-kicker">Administration</p>
                        <h2 class="main-title">Monitor the whole studio from one cleaner workspace.</h2>
                        <p class="main-subtitle">Review user accounts, manage reservation statuses, and keep a quick pulse on revenue and room demand.</p>
                    </div>

                    <div class="hero-stats">
                        <div class="mini-stat">
                            <span class="mini-stat-label">Signed in as</span>
                            <span class="mini-stat-value">Administrator</span>
                        </div>
                        <div class="mini-stat">
                            <span class="mini-stat-label">Tracked reservations</span>
                            <span class="mini-stat-value"><?= $totalReservations ?></span>
                        </div>
                        <div class="mini-stat">
                            <span class="mini-stat-label">Most booked room</span>
                            <span class="mini-stat-value"><?= htmlspecialchars($mostBookedRoom) ?></span>
                        </div>
                    </div>
                </div>
            </section>

            <div class="card-container section-block">
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
                    <p>&#8369;<?= number_format($totalRevenue, 2) ?></p>
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

            <div class="section-block">
                <div class="section-title-row">
                    <div class="section-intro">
                        <h2 class="section-title">User Management</h2>
                        <p class="section-note">Update or remove registered accounts while keeping the existing popup confirmations.</p>
                    </div>
                </div>

                <div class="table-panel">
                    <div class="table-wrapper">
                        <table class="reservation-table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Updated At</th>
                                    <th scope="col">Actions</th>
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
                                                <button type="button" class="btn action-btn update-btn"
                                                    onclick="openUpdateModal(
                                                        '<?= htmlspecialchars($user['user_id'], ENT_QUOTES) ?>',
                                                        '<?= htmlspecialchars($user['firstName'], ENT_QUOTES) ?>',
                                                        '<?= htmlspecialchars($user['lastName'], ENT_QUOTES) ?>',
                                                        '<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>'
                                                    )">
                                                    Update
                                                </button>

                                                <button type="button" class="btn action-btn delete-btn"
                                                    onclick="deleteAdminUser('<?= htmlspecialchars($user['user_id'], ENT_QUOTES) ?>')">
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
                    </div>
                </div>
            </div>

            <div class="table-section">
                <div class="section-title-row">
                    <div>
                        <h2 class="table-title">Room Reservations</h2>
                        <p class="section-note">Review bookings and update reservation statuses from the same table.</p>
                    </div>
                </div>

                <div class="table-panel">
                    <div class="table-wrapper">
                        <table class="reservation-table">
                            <thead>
                                <tr>
                                    <th scope="col">User</th>
                                    <th scope="col">Room</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($reservations)): ?>
                                    <?php foreach ($reservations as $r): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($r['firstName'] . ' ' . $r['lastName']) ?></td>
                                            <td><?= htmlspecialchars($r['studio_name']) ?></td>
                                            <td><?= date('F j, Y, g:i a', strtotime($r['reservation_date'])) ?></td>
                                            <td class="highlight-text">&#8369;<?= number_format($r['total_amount'], 2) ?></td>
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
                                                <button
                                                    type="button"
                                                    class="btn action-btn update-btn"
                                                    onclick="viewReservationDetails(
                                                        '<?= htmlspecialchars($r['reservation_id'], ENT_QUOTES) ?>',
                                                        '<?= htmlspecialchars($r['firstName'] . ' ' . $r['lastName'], ENT_QUOTES) ?>',
                                                        '<?= htmlspecialchars($r['studio_name'], ENT_QUOTES) ?>',
                                                        '<?= htmlspecialchars(date('F j, Y, g:i a', strtotime($r['reservation_date'])), ENT_QUOTES) ?>',
                                                        '<?= htmlspecialchars(number_format($r['total_amount'], 2), ENT_QUOTES) ?>',
                                                        '<?= htmlspecialchars($r['status_name'], ENT_QUOTES) ?>'
                                                    )">
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
                </div>
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
            <p>&copy; 2026 StudioRehearsal. All rights reserved.</p>
        </div>
    </footer>

    <script src="../scripts/service.js"></script>

    <script>
        function deleteAdminUser(userID) {
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
                if (!result.isConfirmed) {
                    return;
                }

                $.post("../controllers/controller.php", {
                    userID: userID,
                    action: 'deleteUser'
                }, function(res) {
                    if ((res || '').toString().trim().toLowerCase() === 'user deleted successfully') {
                        Swal.fire("Deleted!", res, "success")
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Error", res, "error");
                    }
                }).fail(function(xhr) {
                    Swal.fire("Error", xhr.responseText || "Unable to delete user", "error");
                });
            });
        }

        function updateReservationStatus(selectElement) {
            const reservationID = selectElement.getAttribute('data-reservation-id');
            const statusID = selectElement.value;

            $.post("../controllers/controller.php", {
                action: "updateReservationStatus",
                reservationID: reservationID,
                statusID: statusID
            }, function(res) {
                if ((res || '').toString().trim().toLowerCase() === 'status updated successfully') {
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
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: res || 'Failed to update status',
                        icon: 'error',
                        background: '#1a1a1a',
                        color: '#fff',
                        confirmButtonColor: '#bb86fc'
                    });
                }
            }).fail(function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: xhr.responseText || 'Failed to update status',
                    icon: 'error',
                    background: '#1a1a1a',
                    color: '#fff',
                    confirmButtonColor: '#bb86fc'
                });
            });
        }

        function viewReservationDetails(reservationID, userName, roomName, reservationDate, totalAmount, statusName) {
            Swal.fire({
                title: 'Reservation Details',
                icon: 'info',
                background: '#1a1a1a',
                color: '#fff',
                confirmButtonColor: '#bb86fc',
                html: `
                    <div class="reservation-detail-grid">
                        <p><strong>ID:</strong> ${reservationID}</p>
                        <p><strong>User:</strong> ${userName}</p>
                        <p><strong>Room:</strong> ${roomName}</p>
                        <p><strong>Date:</strong> ${reservationDate}</p>
                        <p><strong>Total:</strong> &#8369;${totalAmount}</p>
                        <p><strong>Status:</strong> ${statusName}</p>
                    </div>
                `
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
                        'rgba(15, 118, 110, 0.82)',
                        'rgba(245, 158, 11, 0.78)',
                        'rgba(22, 32, 51, 0.72)',
                        'rgba(17, 94, 89, 0.78)'
                    ],
                    borderColor: [
                        '#0f766e',
                        '#f59e0b',
                        '#162033',
                        '#115e59'
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
                        backgroundColor: '#162033',
                        titleColor: '#fff',
                        bodyColor: '#e7edf5',
                        borderColor: 'rgba(15, 118, 110, 0.35)',
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
                            color: '#5d6a7e',
                            stepSize: 1,
                            font: {
                                size: 13,
                                weight: '600'
                            }
                        },
                        grid: {
                            color: 'rgba(22, 32, 51, 0.08)',
                            drawBorder: false
                        },
                        border: {
                            display: false
                        }
                    },
                    x: {
                        ticks: {
                            color: '#162033',
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
