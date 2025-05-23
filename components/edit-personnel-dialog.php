<div id="editPersonnelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg max-w-[600px] w-full mx-4">
        <div class="flex flex-col">
            <div class="flex flex-col space-y-1.5">
                <h2 class="text-lg font-semibold">Chỉnh sửa thông tin nhân viên</h2>
                <p class="text-sm text-gray-500">Cập nhật thông tin chi tiết của nhân viên</p>
            </div>
            
            <form id="editPersonnelForm" class="mt-4">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="edit_name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Họ và tên</label>
                            <input type="text" id="edit_name" name="name" placeholder="Nguyễn Văn A" required
                                   class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        </div>
                        
                        <div class="space-y-2">
                            <label for="edit_id" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Mã nhân viên</label>
                            <input type="text" id="edit_id" name="id" placeholder="NV1001" required readonly
                                   class="flex h-10 w-full rounded-md border border-input bg-gray-100 px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 cursor-not-allowed">
                        </div>

                        <div class="space-y-2">
                            <label for="edit_phone" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Số điện thoại</label>
                            <input type="tel" id="edit_phone" name="phone" placeholder="0901234567" required
                                   class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        </div>

                        <div class="space-y-2">
                            <label for="edit_email" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Email</label>
                            <input type="email" id="edit_email" name="email" placeholder="example@mail.com"
                                   class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        </div>

                        <div class="space-y-2">
                            <label for="edit_area" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Khu vực phụ trách</label>
                            <select id="edit_area" name="area" required
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="Thanh Trì">Thanh Trì</option>
                                <option value="Hà Đông">Hà Đông</option>
                                <option value="Thanh Xuân">Thanh Xuân</option>
                                <option value="Cầu Giấy">Cầu Giấy</option>
                                <option value="Đống Đa">Đống Đa</option>
                                <option value="Hai Bà Trưng">Hai Bà Trưng</option>
                                <option value="Hoàn Kiếm">Hoàn Kiếm</option>
                                <option value="Tây Hồ">Tây Hồ</option>
                                <option value="Long Biên">Long Biên</option>
                                <option value="Bắc Từ Liêm">Bắc Từ Liêm</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="edit_status" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Trạng thái</label>
                            <select id="edit_status" name="status" required
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="active">Đang hoạt động</option>
                                <option value="inactive">Không hoạt động</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="edit_address" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Địa chỉ</label>
                        <input type="text" id="edit_address" name="address" placeholder="123 Đường ABC, Quận XYZ, TP.HCM"
                               class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    </div>

                    <div class="space-y-2">
                        <label for="edit_notes" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Ghi chú</label>
                        <textarea id="edit_notes" name="notes" placeholder="Thông tin thêm về nhân viên..." rows="3"
                                  class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="closeEditPersonnelModal()"
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
// Remove any existing event listeners
const editForm = document.getElementById('editPersonnelForm');
editForm.removeEventListener('submit', handleEditSubmit);

// Define the submit handler function
function handleEditSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('handle-edit-personnel.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            closeEditPersonnelModal();
            // Reload the personnel list
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật thông tin nhân viên');
    });
}

// Add the event listener
editForm.addEventListener('submit', handleEditSubmit);
</script> 