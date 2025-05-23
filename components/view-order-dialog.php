<?php
require_once 'config/database.php';

// Helper function to format status badge
function getStatusBadgeView($status) {
    switch ($status) {
        case 'new':
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800">Mới</span>';
        case 'shipping':
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800">Đang giao</span>';
        case 'received':
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">Đã nhận</span>';
        default:
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800">Chưa giao</span>';
    }
}

// Helper function to format money
function formatMoneyView($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}
?>

<!-- View Order Modal -->
<div id="viewOrderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg max-w-[600px] w-full mx-4">
        <div class="flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <div class="flex flex-col space-y-1.5">
                    <h2 class="text-lg font-semibold">Chi tiết đơn hàng</h2>
                    <p class="text-sm text-gray-500">Thông tin chi tiết và lịch sử đơn hàng</p>
                </div>
                <button onclick="closeModal('viewOrderModal')" class="text-gray-400 hover:text-gray-500">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="space-y-4 mt-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Mã đơn hàng</label>
                        <p id="view_code" class="text-sm text-gray-900"></p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Trạng thái</label>
                        <div id="view_status"></div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Tên đơn hàng</label>
                    <p id="view_name" class="text-sm text-gray-900"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Tên người nhận</label>
                        <p id="view_receive_name" class="text-sm text-gray-900"></p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">SĐT người nhận</label>
                        <p id="view_receive_phone" class="text-sm text-gray-900"></p>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Chi tiết đơn hàng</label>
                    <p id="view_detail" class="text-sm text-gray-900"></p>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Địa chỉ nhận hàng</label>
                    <p id="view_receive_address" class="text-sm text-gray-900"></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Shipper</label>
                        <p id="view_shipper" class="text-sm text-gray-900"></p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Tiền thu hộ</label>
                        <p id="view_collection_money" class="text-sm text-gray-900"></p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-700">Lịch sử giao hàng</label>
                    <div id="view_history" class="mt-2 space-y-2 max-h-40 overflow-y-auto">
                        <!-- History items will be inserted here -->
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="button" onclick="closeModal('viewOrderModal')"
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showViewOrderModal(order) {
    console.log('Opening view modal with order:', order);
    
    // Debug: Check if order object is valid
    if (!order || typeof order !== 'object') {
        console.error('Invalid order object:', order);
        alert('Lỗi: Không thể hiển thị thông tin đơn hàng');
        return;
    }

    // Fill in order details with error checking
    try {
        document.getElementById('view_code').textContent = order.code || 'N/A';
        document.getElementById('view_status').innerHTML = getStatusBadgeView(order.current_status);
        document.getElementById('view_name').textContent = order.name || 'N/A';
        document.getElementById('view_receive_name').textContent = order.receiver_name || 'N/A';
        document.getElementById('view_receive_phone').textContent = order.phone || 'N/A';
        document.getElementById('view_detail').textContent = order.detail || 'N/A';
        document.getElementById('view_receive_address').textContent = order.receive_address || 'N/A';
        document.getElementById('view_shipper').textContent = order.shipper_name || 'Chưa phân công';
        document.getElementById('view_collection_money').textContent = formatMoneyView(order.collection_money || 0);
    } catch (error) {
        console.error('Error setting order details:', error);
        alert('Lỗi: Không thể hiển thị thông tin đơn hàng');
        return;
    }

    // Get order history
    if (order.id) {
        fetch(`get-order-history.php?id=${order.id}`)
            .then(response => response.json())
            .then(data => {
                console.log('History data:', data);
                if (data.success) {
                    const historyHtml = data.history.map(item => `
                        <div class="flex items-center gap-2 text-sm border-l-2 border-gray-200 pl-3 py-2">
                            <span>${getStatusBadgeView(item.status)}</span>
                            <span class="text-gray-500">${item.assigned_at}</span>
                            <span class="text-gray-700">${item.shipper_name || 'Chưa phân công'}</span>
                        </div>
                    `).join('');
                    document.getElementById('view_history').innerHTML = historyHtml || '<p class="text-sm text-gray-500">Chưa có lịch sử giao hàng</p>';
                } else {
                    document.getElementById('view_history').innerHTML = '<p class="text-sm text-red-500">Không thể tải lịch sử đơn hàng</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching history:', error);
                document.getElementById('view_history').innerHTML = '<p class="text-sm text-red-500">Lỗi khi tải lịch sử</p>';
            });
    } else {
        console.error('Order ID is missing');
        document.getElementById('view_history').innerHTML = '<p class="text-sm text-red-500">Không thể tải lịch sử: Thiếu ID đơn hàng</p>';
    }

    // Show modal
    const modal = document.getElementById('viewOrderModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function getStatusBadgeView(status) {
    switch (status) {
        case 'new':
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800">Mới</span>';
        case 'shipping':
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800">Đang giao</span>';
        case 'received':
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">Đã nhận</span>';
        default:
            return '<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800">Chưa giao</span>';
    }
}

function formatMoneyView(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}
</script>