<div id="assignOrderModal" class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="modal-content relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white h-[300px]">
                <div class="mt-3">
                    <h2 class="text-lg font-semibold mb-4">Giao đơn hàng</h2>
                    <form id="assignOrderForm" onsubmit="handleAssignOrder(event)">
                        <input type="hidden" name="order_id" id="assign_order_id">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Chọn shipper</label>
                            <select name="user_id" required class="w-full px-3 py-2 border rounded-md">
                                <option value="">Chọn shipper</option>
                                <?php foreach ($shippers as $shipper): ?>
                                    <option value="<?php echo $shipper['id']; ?>"><?php echo htmlspecialchars($shipper['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex justify-end gap-4">
                            <button type="button" onclick="closeModal('assignOrderModal')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Hủy</button>
                            <button type="submit" class="px-4 py-2 bg-[#e30613] text-white rounded-md hover:bg-[#c30613]">Giao đơn</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</div>
