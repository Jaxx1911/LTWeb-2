<?php
// Get the status from URL parameter or set default
$status = isset($_GET['status']) ? $_GET['status'] : 'active';

// Database connection
require_once __DIR__ . '/../config/database.php';

// Prepare the SQL query based on status filter
$sql = "SELECT id, code, name, phone, email, area, status, address, note, 
               (SELECT COUNT(*) FROM orders WHERE shipper_id = users.id) as deliveries
        FROM users 
        WHERE role = 'shipper'";

if ($status !== 'all') {
    $sql .= " AND status = :status";
}

$sql .= " ORDER BY code";

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
if ($status !== 'all') {
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
}
$stmt->execute();
$personnelData = $stmt->fetchAll();

// Filter data based on status
$filteredData = $status === 'all' ? $personnelData : array_filter($personnelData, function($person) use ($status) {
    return $person['status'] === $status;
});

// Helper function to get status badge HTML
function getStatusBadge($status) {
    if ($status === 'active') {
        return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800 hover:bg-green-200">Đang hoạt động</span>';
    }
    return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800 hover:bg-gray-200">Không hoạt động</span>';
}

// Helper function to get initials from name
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        $initials .= strtoupper($word[0]);
    }
    return $initials;
}
?>

<div class="rounded-md border">
    <div class="w-full overflow-auto">
        <table class="w-full caption-bottom text-sm">
            <thead class="[&_tr]:border-b">
                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                    <th class="h-12 px-4 text-left align-middle font-medium">Mã NV</th>
                    <th class="h-12 px-4 text-left align-middle font-medium">Nhân viên</th>
                    <th class="h-12 px-4 text-left align-middle font-medium hidden md:table-cell">Số điện thoại</th>
                    <th class="h-12 px-4 text-left align-middle font-medium hidden md:table-cell">Khu vực</th>
                    <th class="h-12 px-4 text-center align-middle font-medium">Trạng thái</th>
                    <th class="h-12 px-4 text-center align-middle font-medium hidden md:table-cell">Đơn hàng</th>
                    <th class="h-12 px-4 text-center align-middle font-medium">Thao tác</th>
                </tr>
            </thead>
            <tbody class="[&_tr:last-child]:border-0">
                <?php foreach ($filteredData as $person): ?>
                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                    <td class="p-4 align-middle font-medium"><?php echo htmlspecialchars($person['code']); ?></td>
                    <td class="p-4 align-middle">
                        <div class="flex items-center gap-2">
                            <span><?php echo htmlspecialchars($person['name']); ?></span>
                        </div>
                    </td>
                    <td class="p-4 align-middle hidden md:table-cell"><?php echo htmlspecialchars($person['phone']); ?></td>
                    <td class="p-4 align-middle hidden md:table-cell"><?php echo htmlspecialchars($person['area']); ?></td>
                    <td class="p-4 align-middle text-center"><?php echo getStatusBadge($person['status']); ?></td>
                    <td class="p-4 align-middle hidden md:table-cell text-center"><?php echo $person['deliveries']; ?></td>
                    <td class="p-4 align-middle text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick="viewPersonnel(<?php echo htmlspecialchars(json_encode($person)); ?>)" 
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                            </button>
                            <button onclick="editPersonnel(<?php echo htmlspecialchars(json_encode($person)); ?>)"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                <i data-lucide="edit" class="h-4 w-4"></i>
                            </button>
                            <button onclick="confirmDelete('<?php echo $person['id']; ?>', '<?php echo $person['name']; ?>')"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8 text-red-500 hover:text-red-600">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Delete Confirmation -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold">Xác nhận xóa</h3>
        <p class="text-gray-500 mt-2" id="deleteMessage"></p>
        <div class="flex justify-end gap-2 mt-4">
            <button onclick="closeDeleteModal()" 
                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                Hủy
            </button>
            <button onclick="deletePersonnel()" 
                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-red-500 text-white hover:bg-red-600 h-9 px-4 py-2">
                Xóa
            </button>
        </div>
    </div>
</div>

<script>
let personnelToDelete = null;

function viewPersonnel(personnelData) {
    // Fill the view form with personnel data
    document.getElementById('view_id').textContent = personnelData.code;
    document.getElementById('view_name').textContent = personnelData.name;
    document.getElementById('view_phone').textContent = personnelData.phone;
    document.getElementById('view_email').textContent = personnelData.email || '(Chưa cập nhật)';
    document.getElementById('view_area').textContent = personnelData.area;
    document.getElementById('view_status').textContent = personnelData.status === 'active' ? 'Đang hoạt động' : 'Không hoạt động';
    document.getElementById('view_address').textContent = personnelData.address || '(Chưa cập nhật)';
    document.getElementById('view_notes').textContent = personnelData.note || '(Chưa cập nhật)';
    document.getElementById('view_deliveries').textContent = personnelData.deliveries || '0';

    // Show the modal
    const modal = document.getElementById('viewPersonnelModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function editPersonnel(personnelData) {
    // Fill the edit form with personnel data
    document.getElementById('edit_id').value = personnelData.code;
    document.getElementById('edit_name').value = personnelData.name;
    document.getElementById('edit_phone').value = personnelData.phone;
    document.getElementById('edit_email').value = personnelData.email || '';
    document.getElementById('edit_area').value = personnelData.area;
    document.getElementById('edit_status').value = personnelData.status;
    document.getElementById('edit_address').value = personnelData.address || '';
    document.getElementById('edit_notes').value = personnelData.note || '';

    // Show the modal
    const modal = document.getElementById('editPersonnelModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEditPersonnelModal() {
    const modal = document.getElementById('editPersonnelModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('editPersonnelForm').reset();
}

function confirmDelete(id, name) {
    personnelToDelete = id;
    const modal = document.getElementById('deleteModal');
    const message = document.getElementById('deleteMessage');
    message.textContent = `Bạn có chắc chắn muốn xóa nhân viên ${name}? Hành động này không thể hoàn tác.`;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    personnelToDelete = null;
}

function deletePersonnel() {
    console.log('Delete personnel:', personnelToDelete);
    if (personnelToDelete) {
        const response = fetch(`delete-shipper.php?id=${personnelToDelete}`);
        const result = response.json();
        if (result.success) {
            alert(result.message);
            closeDeleteModal();
            location.reload();
        } else {
            alert(result.message);
        }
    }
    closeDeleteModal();
    location.reload();
}

// Close modals when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Add this at the end of your script
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all Lucide icons
    lucide.createIcons();
});
</script> 