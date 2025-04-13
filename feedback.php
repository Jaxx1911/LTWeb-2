<?php
require_once 'config.php';

// Handle feedback status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $stmt = $pdo->prepare("UPDATE feedback SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $_POST['id']]);
    }
}

// Fetch feedback with filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$query = "SELECT * FROM feedback";
if ($status_filter !== 'all') {
    $query .= " WHERE status = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$status_filter]);
} else {
    $stmt = $pdo->query($query);
}
$feedback_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Feedback Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .nav-link {
            color: white;
        }
        .nav-link:hover {
            background-color: #495057;
        }
        .rating {
            color: #ffc107;
        }
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        .status-processed {
            background-color: #28a745;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <h3 class="text-white mb-4">Delivery System</h3>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="delivery_personnel.php">
                            <i class="bi bi-people"></i> Delivery Personnel
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
                            <i class="bi bi-box"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="bi bi-graph-up"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="feedback.php">
                            <i class="bi bi-chat-dots"></i> Customer Feedback
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Customer Feedback Management</h2>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">New Feedback</h5>
                        <p class="card-text">Manage customer feedback about delivery services</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Feedback List</h5>
                            <div class="btn-group">
                                <a href="?status=all" class="btn btn-outline-secondary <?php echo $status_filter === 'all' ? 'active' : ''; ?>">All</a>
                                <a href="?status=Pending" class="btn btn-outline-warning <?php echo $status_filter === 'Pending' ? 'active' : ''; ?>">Pending</a>
                                <a href="?status=Processed" class="btn btn-outline-success <?php echo $status_filter === 'Processed' ? 'active' : ''; ?>">Processed</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Order ID</th>
                                        <th>Rating</th>
                                        <th>Comment</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feedback_list as $feedback): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($feedback['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($feedback['order_id']); ?></td>
                                        <td>
                                            <div class="rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi bi-star<?php echo $i <= $feedback['rating'] ? '-fill' : ''; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($feedback['comment']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($feedback['created_at'])); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($feedback['status']); ?>">
                                                <?php echo $feedback['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewDetails(<?php echo $feedback['id']; ?>)">
                                                <i class="bi bi-eye"></i> Details
                                            </button>
                                            <?php if ($feedback['status'] === 'Pending'): ?>
                                                <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $feedback['id']; ?>, 'Processed')">
                                                    <i class="bi bi-check"></i> Mark as Processed
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Details Modal -->
    <div class="modal fade" id="feedbackDetailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Feedback Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="feedbackDetails">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewDetails(feedbackId) {
            // In a real application, you would fetch the details from the server
            const modal = new bootstrap.Modal(document.getElementById('feedbackDetailsModal'));
            modal.show();
        }

        function updateStatus(feedbackId, status) {
            if (confirm('Are you sure you want to update the status?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" value="${feedbackId}">
                    <input type="hidden" name="status" value="${status}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> 