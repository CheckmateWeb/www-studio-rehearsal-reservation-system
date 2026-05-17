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
$roomRevenue = [];
foreach ($reservations as $r) {
    $room = $r['studio_name'];
    if (!isset($roomCounts[$room])) {
        $roomCounts[$room] = 0;
    }
    $roomCounts[$room]++;

    if (!isset($roomRevenue[$room])) {
        $roomRevenue[$room] = 0;
    }
    $roomRevenue[$room] += (float) $r['total_amount'];
}

$mostBookedRoom = "No reservations";
if (!empty($roomCounts)) {
    $mostBookedRoom = array_keys($roomCounts, max($roomCounts))[0];
}

$weeklyTrendMap = [];
for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
    $dateKey = date('Y-m-d', strtotime("-{$daysAgo} days"));
    $weeklyTrendMap[$dateKey] = 0;
}

foreach ($reservations as $r) {
    $reservationDay = date('Y-m-d', strtotime($r['reservation_date']));
    if (array_key_exists($reservationDay, $weeklyTrendMap)) {
        $weeklyTrendMap[$reservationDay]++;
    }
}

$weeklyTrendLabels = [];
foreach (array_keys($weeklyTrendMap) as $dateKey) {
    $weeklyTrendLabels[] = date('M j', strtotime($dateKey));
}

$weeklyTrendData = array_values($weeklyTrendMap);
$revenueLabels = array_keys($roomRevenue);
$revenueData = array_values($roomRevenue);

$reservationDurationsInHours = [];
foreach ($reservations as $r) {
    $startTimestamp = strtotime((string) $r['start_time']);
    $endTimestamp = strtotime((string) $r['end_time']);

    if ($startTimestamp && $endTimestamp && $endTimestamp > $startTimestamp) {
        $reservationDurationsInHours[] = ($endTimestamp - $startTimestamp) / 3600;
    }
}

$averageBookingDuration = !empty($reservationDurationsInHours)
    ? array_sum($reservationDurationsInHours) / count($reservationDurationsInHours)
    : 0;

$weeklyReservationCount = array_sum($weeklyTrendData);
$totalWeeklyCapacityHours = 4 * 24 * 7;
$occupancyRate = $totalWeeklyCapacityHours > 0
    ? ($weeklyReservationCount / $totalWeeklyCapacityHours) * 100
    : 0;
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

            <div class="section-block">
                <div class="section-title-row">
                    <div class="section-intro">
                        <h2 class="section-title">KPI Cards</h2>
                        <p class="section-note">Choose which summary cards appear at the top of the admin dashboard.</p>
                    </div>
                </div>

                <div class="kpi-toggle-panel">
                    <label class="kpi-toggle">
                        <input type="checkbox" class="kpi-toggle-input" data-card-toggle="total-users" checked>
                        <span>Total Users</span>
                    </label>
                    <label class="kpi-toggle">
                        <input type="checkbox" class="kpi-toggle-input" data-card-toggle="total-reservations" checked>
                        <span>Total Reservations</span>
                    </label>
                    <label class="kpi-toggle">
                        <input type="checkbox" class="kpi-toggle-input" data-card-toggle="total-revenue" checked>
                        <span>Total Revenue</span>
                    </label>
                    <label class="kpi-toggle">
                        <input type="checkbox" class="kpi-toggle-input" data-card-toggle="most-booked-room" checked>
                        <span>Most Booked Room</span>
                    </label>
                    <label class="kpi-toggle">
                        <input type="checkbox" class="kpi-toggle-input" data-card-toggle="occupancy-rate">
                        <span>Occupancy Rate</span>
                    </label>
                    <label class="kpi-toggle">
                        <input type="checkbox" class="kpi-toggle-input" data-card-toggle="avg-booking-duration">
                        <span>Average Booking Duration</span>
                    </label>
                </div>
            </div>

            <div class="card-container section-block">
                <div class="card" data-card-key="total-users">
                    <h3>Total Users</h3>
                    <p><?= $totalUsers ?></p>
                </div>

                <div class="card" data-card-key="total-reservations">
                    <h3>Total Reservations</h3>
                    <p><?= $totalReservations ?></p>
                </div>

                <div class="card" data-card-key="total-revenue">
                    <h3>Total Revenue</h3>
                    <p>&#8369;<?= number_format($totalRevenue, 2) ?></p>
                </div>

                <div class="card" data-card-key="most-booked-room">
                    <h3>Most Booked Room</h3>
                    <p><?= htmlspecialchars($mostBookedRoom) ?></p>
                </div>

                <div class="card" data-card-key="occupancy-rate">
                    <h3>Occupancy Rate</h3>
                    <p><?= number_format($occupancyRate, 1) ?>%</p>
                </div>

                <div class="card" data-card-key="avg-booking-duration">
                    <h3>Average Booking Duration</h3>
                    <p><?= number_format($averageBookingDuration, 1) ?> hrs</p>
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

            <div class="chart-grid section-block">
                <div class="chart-section">
                    <h2 class="chart-title">Weekly Booking Trend</h2>
                    <div class="chart-box">
                        <div class="chart-inner">
                            <canvas id="weeklyTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="chart-section">
                    <h2 class="chart-title">Revenue Breakdown</h2>
                    <div class="chart-box">
                        <div class="chart-inner chart-inner-donut">
                            <canvas id="revenueDonutChart"></canvas>
                        </div>
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
        const kpiStorageKey = 'studio-admin-visible-kpis';

        function applyKpiVisibility(visibleCardKeys) {
            document.querySelectorAll('[data-card-key]').forEach((card) => {
                const shouldShow = visibleCardKeys.indexOf(card.dataset.cardKey) !== -1;
                card.style.display = shouldShow ? '' : 'none';
            });

            document.querySelectorAll('.kpi-toggle-input').forEach((toggle) => {
                toggle.checked = visibleCardKeys.indexOf(toggle.dataset.cardToggle) !== -1;
            });
        }

        function getDefaultVisibleCards() {
            return [
                'total-users',
                'total-reservations',
                'total-revenue',
                'most-booked-room'
            ];
        }

        function getStoredVisibleCards() {
            try {
                const stored = localStorage.getItem(kpiStorageKey);
                if (!stored) {
                    return getDefaultVisibleCards();
                }

                const parsed = JSON.parse(stored);
                return Array.isArray(parsed) && parsed.length ? parsed : getDefaultVisibleCards();
            } catch (error) {
                return getDefaultVisibleCards();
            }
        }

        function saveVisibleCards(cardKeys) {
            localStorage.setItem(kpiStorageKey, JSON.stringify(cardKeys));
        }

        function initializeKpiToggles() {
            const toggles = Array.from(document.querySelectorAll('.kpi-toggle-input'));
            let visibleCards = getStoredVisibleCards();
            applyKpiVisibility(visibleCards);

            toggles.forEach((toggle) => {
                toggle.addEventListener('change', function () {
                    const activeKeys = toggles
                        .filter((item) => item.checked)
                        .map((item) => item.dataset.cardToggle);

                    visibleCards = activeKeys.length ? activeKeys : getDefaultVisibleCards();
                    applyKpiVisibility(visibleCards);
                    saveVisibleCards(visibleCards);
                });
            });
        }

        initializeKpiToggles();

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
        const weeklyTrendLabels = <?= json_encode($weeklyTrendLabels) ?>;
        const weeklyTrendData = <?= json_encode($weeklyTrendData) ?>;
        const revenueLabels = <?= json_encode($revenueLabels) ?>;
        const revenueData = <?= json_encode($revenueData) ?>;
        const chartPalette = [
            '#0f766e',
            '#f59e0b',
            '#162033',
            '#115e59',
            '#0ea5e9',
            '#ef4444'
        ];

        new Chart(document.getElementById('roomChart'), {
            type: 'bar',
            data: {
                labels: roomLabels,
                datasets: [{
                    label: 'Reservations',
                    data: roomData,
                    backgroundColor: chartPalette.map((color) => color + 'd1'),
                    borderColor: chartPalette,
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

        new Chart(document.getElementById('weeklyTrendChart'), {
            type: 'line',
            data: {
                labels: weeklyTrendLabels,
                datasets: [{
                    label: 'Bookings',
                    data: weeklyTrendData,
                    borderColor: '#0f766e',
                    backgroundColor: 'rgba(15, 118, 110, 0.14)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#0f766e',
                    pointBorderWidth: 2
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
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
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

        new Chart(document.getElementById('revenueDonutChart'), {
            type: 'doughnut',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueData,
                    backgroundColor: chartPalette.map((color) => color + 'd6'),
                    borderColor: '#f8fbff',
                    borderWidth: 3,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                cutout: '64%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#162033',
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 18,
                            font: {
                                size: 12,
                                weight: '700'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#162033',
                        titleColor: '#fff',
                        bodyColor: '#e7edf5',
                        borderColor: 'rgba(15, 118, 110, 0.35)',
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const value = Number(context.raw || 0).toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                return context.label + ': PHP ' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
