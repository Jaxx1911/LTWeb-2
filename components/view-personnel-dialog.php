<?php
// Get the status from URL parameter or set default
?>
<!-- View Personnel Modal -->
<div id="viewPersonnelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto py-8">
    <div class="bg-white p-6 rounded-lg max-w-[800px] w-full mx-4 my-auto">
        <div class="flex flex-col max-h-[80vh]">
            <div class="flex flex-col space-y-1.5">
                <h2 class="text-lg font-semibold">Thông tin chi tiết nhân viên</h2>
                <p class="text-sm text-gray-500">Xem thông tin chi tiết của nhân viên</p>
            </div>
            
            <div class="mt-4 overflow-y-auto pr-2" style="scrollbar-width: thin;">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none text-gray-500">Họ và tên</label>
                            <div id="view_name" class="text-sm font-medium"></div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none text-gray-500">Mã nhân viên</label>
                            <div id="view_id" class="text-sm font-medium"></div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none text-gray-500">Số điện thoại</label>
                            <div id="view_phone" class="text-sm font-medium"></div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none text-gray-500">Email</label>
                            <div id="view_email" class="text-sm font-medium"></div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none text-gray-500">Khu vực phụ trách</label>
                            <div id="view_area" class="text-sm font-medium"></div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none text-gray-500">Trạng thái</label>
                            <div id="view_status" class="text-sm font-medium"></div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-gray-500">Địa chỉ</label>
                        <div id="view_address" class="text-sm"></div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-gray-500">Ghi chú</label>
                        <div id="view_notes" class="text-sm"></div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-gray-500">Tổng số đơn hàng đã giao</label>
                        <div id="view_deliveries" class="text-sm font-medium"></div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-lg font-medium">Đơn hàng gần đây</label>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2">Mã đơn</th>
                                        <th class="text-left py-2">Khách hàng</th>
                                        <th class="text-left py-2">Địa chỉ</th>
                                        <th class="text-center py-2">Trạng thái</th>
                                        <th class="text-center py-2">Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody id="view_orders">
                                    <!-- Orders will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4 sticky bottom-0 bg-white py-4 border-t">
                    <button onclick="closeViewPersonnelModal()"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                        Đóng
                    </button>
                    <button onclick="editPersonnelFromView()"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#e30613] text-white hover:bg-[#c00510] h-9 px-4 py-2">
                        <i data-lucide="edit" class="mr-2 h-4 w-4"></i> Chỉnh sửa
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom scrollbar styles */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
let currentViewPersonnel = null;

function getStatusBadgeHTML(status) {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'delivering': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return `<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold ${classes[status] || 'bg-gray-100 text-gray-800'}">${status}</span>`;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN', { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

async function viewPersonnel(personnelData) {
    try {
        const response = await fetch(`get-personnel-details.php?code=${personnelData.code}`);
        const result = await response.json();
        console.log(result);
        if (result.success) {
            currentViewPersonnel = result.data.personnel;
            const { personnel, orders } = result.data;
            
            // Fill personnel details
            document.getElementById('view_id').textContent = personnel.code;
            document.getElementById('view_name').textContent = personnel.name;
            document.getElementById('view_phone').textContent = personnel.phone;
            document.getElementById('view_email').textContent = personnel.email || '(Chưa cập nhật)';
            document.getElementById('view_area').textContent = personnel.area;
            document.getElementById('view_status').textContent = personnel.status === 'active' ? 'Đang hoạt động' : 'Không hoạt động';
            document.getElementById('view_address').textContent = personnel.address || '(Chưa cập nhật)';
            document.getElementById('view_notes').textContent = personnel.note || '(Chưa cập nhật)';
            document.getElementById('view_deliveries').textContent = personnel.total_deliveries || '0';

            // Fill orders table
            const ordersHTML = orders.map(order => `
                <tr class="border-b">
                    <td class="py-2">${order.code}</td>
                    <td class="py-2">
                        <div>
                            <div class="font-medium">${order.sender_name}</div>
                            <div class="text-sm text-gray-500">${order.sender_phone}</div>
                        </div>
                    </td>
                    <td class="py-2">${order.receive_address}</td>
                    <td class="py-2 text-center">${getStatusBadgeHTML(order.status)}</td>
                    <td class="py-2 text-center">${formatDate(order.created_at)}</td>
                </tr>
            `).join('');
            
            document.getElementById('view_orders').innerHTML = ordersHTML || '<tr><td colspan="5" class="text-center py-4">Chưa có đơn hàng nào</td></tr>';

            // Show the modal
            const modal = document.getElementById('viewPersonnelModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            alert('Không thể lấy thông tin nhân viên' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi lấy thông tin nhân viên');
    }
}

function closeViewPersonnelModal() {
    const modal = document.getElementById('viewPersonnelModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentViewPersonnel = null;
}

function editPersonnelFromView() {
    if (currentViewPersonnel) {
        closeViewPersonnelModal();
        editPersonnel(currentViewPersonnel);
    }
}

// Close modal when clicking outside
document.getElementById('viewPersonnelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeViewPersonnelModal();
    }
});
</script> 