<?php
require_once 'config/database.php';

// Get all shippers for dropdown
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'shipper' AND status = 'active'");
$stmt->execute();
$shippers = $stmt->fetchAll();
?>

<!-- Edit Order Modal -->
<div id="editOrderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg max-w-[600px] w-full mx-4">
        <div class="flex flex-col">
            <div class="flex flex-col space-y-1.5">
                <h2 class="text-lg font-semibold">Chỉnh sửa đơn hàng</h2>
                <p class="text-sm text-gray-500">Cập nhật thông tin chi tiết của đơn hàng</p>
            </div>
            
            <form id="editOrderForm" class="mt-4">
                <input type="hidden" name="id" id="edit_id">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="edit_code" class="text-sm font-medium leading-none">Mã đơn hàng</label>
                            <input type="text" id="edit_code" name="code" placeholder="DH1001" required
                                   class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        </div>
                        
                        <div class="space-y-2">
                            <label for="edit_order_name" class="text-sm font-medium leading-none">Tên đơn hàng</label>
                            <input type="text" id="edit_order_name" name="order_name" placeholder="Đơn hàng thời trang" required
                                   class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        </div>

                        <div class="space-y-2">
                            <label for="edit_name" class="text-sm font-medium leading-none">Tên người nhận</label>
                            <input type="text" id="edit_name" name="name" placeholder="Nguyễn Văn A" required
                                   class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        </div>

                        <div class="space-y-2">
                            <label for="edit_phone" class="text-sm font-medium leading-none">Số điện thoại người nhận</label>
                            <input type="tel" id="edit_phone" name="phone" placeholder="0912345678" required pattern="[0-9]{10}"
                                   class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        </div>

                        <div class="space-y-2">
                            <label for="edit_collection_money" class="text-sm font-medium leading-none">Tiền thu hộ</label>
                            <input type="number" id="edit_collection_money" name="collection_money" placeholder="100000" required
                                   class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        </div>

                        <div class="space-y-2">
                            <label for="edit_shipper_id" class="text-sm font-medium leading-none">Shipper</label>
                            <select name="shipper_id" id="edit_shipper_id" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                                <option value="">Chọn shipper</option>
                                <?php foreach ($shippers as $shipper): ?>
                                    <option value="<?php echo $shipper['id']; ?>"><?php echo htmlspecialchars($shipper['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="edit_detail" class="text-sm font-medium leading-none">Chi tiết đơn hàng</label>
                        <textarea id="edit_detail" name="detail" placeholder="Mô tả chi tiết đơn hàng..." rows="3" required
                                  class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label for="edit_receive_address" class="text-sm font-medium leading-none">Địa chỉ nhận hàng</label>
                        <textarea id="edit_receive_address" name="receive_address" placeholder="123 Đường ABC, Quận XYZ..." rows="3" required
                                  class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeModal('editOrderModal')"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                        Hủy
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#e30613] text-white hover:bg-[#c00510] h-9 px-4 py-2">
                        <i data-lucide="save" class="mr-2 h-4 w-4"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('editOrderForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
        const formData = new FormData(this);
        const response = await fetch('handle-edit-order.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            closeModal('editOrderModal');
            location.reload();
        } else {
            alert(result.message || 'Có lỗi xảy ra khi cập nhật đơn hàng');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật đơn hàng');
    }
});
</script> 