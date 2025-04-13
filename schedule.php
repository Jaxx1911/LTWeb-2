<?php
require_once 'config.php';

// Handle schedule updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_schedule':
                $stmt = $pdo->prepare("INSERT INTO work_schedule (delivery_personnel_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['personnel_id'], $_POST['day'], $_POST['start_time'], $_POST['end_time']]);
                break;
            case 'update_schedule':
                $stmt = $pdo->prepare("UPDATE work_schedule SET start_time = ?, end_time = ? WHERE id = ?");
                $stmt->execute([$_POST['start_time'], $_POST['end_time'], $_POST['id']]);
                break;
            case 'delete_schedule':
                $stmt = $pdo->prepare("DELETE FROM work_schedule WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                break;
        }
    }
}

// Fetch all delivery personnel
$stmt = $pdo->query("SELECT * FROM delivery_personnel");
$personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all schedules
$stmt = $pdo->query("SELECT ws.*, dp.name FROM work_schedule ws JOIN delivery_personnel dp ON ws.delivery_personnel_id = dp.id");
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Schedule Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1e88e5; /* Blue */
            --secondary-color: #4527a0; /* Deep Purple */
            --light-color: #ffffff;
            --dark-color: #212121;
        }
        
        body {
            background-color: #f5f5f5;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: var(--light-color) !important;
            font-weight: bold;
        }

        .nav-link {
            color: var(--light-color) !important;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: bold;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            border: none;
            margin-bottom: 1rem;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .schedule-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--light-color);
        }

        .day-header {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }

        .content-wrapper {
            margin-top: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">DeliveryManager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house-door"></i> Tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="delivery_personnel.php">
                            <i class="bi bi-people"></i> Người giao hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
                            <i class="bi bi-box"></i> Đơn hàng & Lộ trình
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="schedule.php">
                            <i class="bi bi-calendar"></i> Lịch làm việc
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="bi bi-graph-up"></i> Báo cáo & Thống kê
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="feedback.php">
                            <i class="bi bi-chat-dots"></i> Phản hồi khách hàng
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Lịch làm việc</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                        <i class="bi bi-plus"></i> Thêm lịch làm việc
                    </button>
                </div>

                <div class="row">
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    $vietnamese_days = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
                    
                    foreach ($days as $index => $day) {
                        $day_schedules = array_filter($schedules, function($schedule) use ($day) {
                            return $schedule['day_of_week'] === $day;
                        });
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card schedule-card">
                            <div class="card-body">
                                <h5 class="day-header"><?php echo $vietnamese_days[$index]; ?></h5>
                                <?php foreach ($day_schedules as $schedule): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <strong><?php echo htmlspecialchars($schedule['name']); ?></strong>
                                        <br>
                                        <small><?php echo date('H:i', strtotime($schedule['start_time'])); ?> - <?php echo date('H:i', strtotime($schedule['end_time'])); ?></small>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-warning" onclick="editSchedule(<?php echo htmlspecialchars(json_encode($schedule)); ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteSchedule(<?php echo $schedule['id']; ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div class="modal fade" id="addScheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm lịch làm việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_schedule">
                        <div class="mb-3">
                            <label class="form-label">Nhân viên</label>
                            <select class="form-select" name="personnel_id" required>
                                <?php foreach ($personnel as $person): ?>
                                <option value="<?php echo $person['id']; ?>"><?php echo htmlspecialchars($person['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày trong tuần</label>
                            <select class="form-select" name="day" required>
                                <?php foreach ($days as $index => $day): ?>
                                <option value="<?php echo $day; ?>"><?php echo $vietnamese_days[$index]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thời gian bắt đầu</label>
                            <input type="time" class="form-control" name="start_time" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thời gian kết thúc</label>
                            <input type="time" class="form-control" name="end_time" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Thêm lịch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Schedule Modal -->
    <div class="modal fade" id="editScheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa lịch làm việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_schedule">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Thời gian bắt đầu</label>
                            <input type="time" class="form-control" name="start_time" id="edit_start_time" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thời gian kết thúc</label>
                            <input type="time" class="form-control" name="end_time" id="edit_end_time" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editSchedule(schedule) {
            document.getElementById('edit_id').value = schedule.id;
            document.getElementById('edit_start_time').value = schedule.start_time;
            document.getElementById('edit_end_time').value = schedule.end_time;
            
            new bootstrap.Modal(document.getElementById('editScheduleModal')).show();
        }

        function deleteSchedule(id) {
            if (confirm('Bạn có chắc chắn muốn xóa lịch làm việc này?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_schedule">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> 