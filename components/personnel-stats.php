<?php
// Get the active status from URL parameter or set default
$active = isset($_GET['active']) ? $_GET['active'] : null;

// Adjust stats based on active filter
$totalPersonnel = $active === null ? 42 : ($active ? 36 : 6);
$totalDeliveries = $active === null ? 1248 : ($active ? 1225 : 23);
$avgRating = $active === null ? 4.6 : ($active ? 4.7 : 3.5);
$coverageAreas = $active === null ? 15 : ($active ? 15 : 4);

// Helper function to get status text
function getStatusText($active) {
    if ($active === true) return "Đang hoạt động";
    if ($active === false) return "Không hoạt động";
    return "Tất cả nhân viên";
}
?>

<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
    <!-- Total Personnel Card -->
    <div class="rounded-lg border bg-card text-card-foreground shadow transition-all duration-200 hover:shadow-md">
        <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
            <h3 class="text-sm font-medium">Tổng nhân viên</h3>
            <i class="lucide-users h-4 w-4 text-[#e30613]"></i>
        </div>
        <div class="p-6 pt-0">
            <div class="text-2xl font-bold"><?php echo $totalPersonnel; ?></div>
            <p class="text-xs text-gray-500">
                <?php echo getStatusText($active); ?>
            </p>
        </div>
    </div>

    <!-- Total Deliveries Card -->
    <div class="rounded-lg border bg-card text-card-foreground shadow transition-all duration-200 hover:shadow-md">
        <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
            <h3 class="text-sm font-medium">Tổng đơn hàng</h3>
            <i class="lucide-package h-4 w-4 text-[#e30613]"></i>
        </div>
        <div class="p-6 pt-0">
            <div class="text-2xl font-bold"><?php echo $totalDeliveries; ?></div>
            <p class="text-xs text-gray-500">
                Trung bình <?php echo round($totalDeliveries / $totalPersonnel); ?> đơn/người
            </p>
        </div>
    </div>

    <!-- Average Rating Card -->
    <div class="rounded-lg border bg-card text-card-foreground shadow transition-all duration-200 hover:shadow-md">
        <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
            <h3 class="text-sm font-medium">Đánh giá trung bình</h3>
            <i class="lucide-star h-4 w-4 text-[#e30613]"></i>
        </div>
        <div class="p-6 pt-0">
            <div class="text-2xl font-bold"><?php echo $avgRating; ?> ⭐</div>
            <p class="text-xs text-gray-500">Dựa trên đánh giá của khách hàng</p>
        </div>
    </div>

    <!-- Coverage Areas Card -->
    <div class="rounded-lg border bg-card text-card-foreground shadow transition-all duration-200 hover:shadow-md">
        <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
            <h3 class="text-sm font-medium">Khu vực phủ sóng</h3>
            <i class="lucide-map-pin h-4 w-4 text-[#e30613]"></i>
        </div>
        <div class="p-6 pt-0">
            <div class="text-2xl font-bold"><?php echo $coverageAreas; ?></div>
            <p class="text-xs text-gray-500">Quận/Huyện tại TP.HCM</p>
        </div>
    </div>
</div> 