<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Dữ Liệu Fake</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🚀 Import Dữ Liệu Fake Cho Biểu Đồ Thống Kê</h1>
    
    <?php
    if (isset($_POST['import'])) {
        try {
            require_once 'config/database.php';
            
            // Delete existing fake data
            echo "<h3>🗑️ Xóa dữ liệu fake cũ...</h3>";
            $pdo->exec("DELETE FROM assignment WHERE order_id IN (SELECT id FROM orders WHERE code LIKE 'FK%')");
            $pdo->exec("DELETE FROM orders WHERE code LIKE 'FK%'");
            echo "<p class='success'>✅ Đã xóa dữ liệu fake cũ</p>";
            
            // Insert new varied data
            echo "<h3>📊 Thêm dữ liệu mới với số lượng đa dạng...</h3>";
            
            // Array of daily order counts for varied statistics
            $daily_data = [
                '2025-05-07' => 8,   // busy day
                '2025-05-08' => 5,   // normal day  
                '2025-05-09' => 12,  // very busy day
                '2025-05-10' => 2,   // slow day
                '2025-05-11' => 7,   // normal day
                '2025-05-12' => 15,  // peak day
                '2025-05-13' => 1,   // very slow day
                '2025-05-14' => 9,   // busy day
                '2025-05-15' => 6,   // normal day
                '2025-05-16' => 11,  // busy day
                '2025-05-17' => 4,   // slow day
                '2025-05-18' => 8,   // normal day
                '2025-05-19' => 3,   // slow day
                '2025-05-20' => 10,  // busy day
                '2025-05-21' => 6,   // normal day
                '2025-05-22' => 13,  // very busy day
                '2025-05-23' => 5,   // normal day
                '2025-05-24' => 7,   // normal day
                '2025-05-25' => 4,   // slow day
                '2025-05-26' => 9,   // busy day
                '2025-05-27' => 8,   // normal day
                '2025-05-28' => 6,   // normal day
                '2025-05-29' => 11   // busy day
            ];
            
            $order_counter = 1;
            $shipper_ids = [1, 2, 3, 4, 5, 6, 11, 12, 13, 14]; // Available shipper IDs
            
            foreach ($daily_data as $date => $count) {
                for ($i = 0; $i < $count; $i++) {
                    $order_code = 'FK' . str_replace('-', '', $date) . sprintf('%03d', $i + 1);
                    $shipper_id = $shipper_ids[array_rand($shipper_ids)];
                    $hour = rand(8, 17);
                    $minute = rand(0, 59);
                    $created_at = $date . ' ' . sprintf('%02d:%02d:00', $hour, $minute);
                    $collection_money = rand(500000, 50000000);
                    
                    $products = [
                        'Điện thoại Samsung Galaxy S24',
                        'Laptop MacBook Pro M3',
                        'Máy lọc nước Kangaroo',
                        'Tivi Samsung QLED 65 inch',
                        'Máy giặt LG Inverter',
                        'Tủ lạnh Panasonic',
                        'Điều hòa Daikin 1.5HP',
                        'Máy pha cà phê Breville',
                        'Robot hút bụi Xiaomi',
                        'Đồng hồ Apple Watch',
                        'Máy massage Ogawa',
                        'Bàn làm việc thông minh',
                        'Ghế gaming DXRacer',
                        'Máy chiếu Epson 4K',
                        'Camera Canon EOS R6'
                    ];
                    
                    $addresses = [
                        'Nguyễn Huệ, Quận 1, TP.HCM',
                        'Lê Lợi, Quận 1, TP.HCM', 
                        'Đồng Khởi, Quận 1, TP.HCM',
                        'Trần Não, Quận 2, TP.HCM',
                        'Mai Chí Thọ, Quận 2, TP.HCM',
                        'Cao Thắng, Quận 3, TP.HCM',
                        'Nguyễn Đình Chiểu, Quận 3, TP.HCM',
                        'Lê Văn Sỹ, Quận 3, TP.HCM'
                    ];
                    
                    $names = [
                        'Trần Văn An', 'Nguyễn Thị Bình', 'Lê Thị Cẩm', 'Phạm Văn Dũng',
                        'Hoàng Thị Em', 'Trần Văn Phúc', 'Lý Thị Giang', 'Nguyễn Văn Hùng',
                        'Phạm Thị Lan', 'Hoàng Văn Nam', 'Trần Thị Oanh', 'Lê Văn Phong'
                    ];
                    
                    $product = $products[array_rand($products)];
                    $address = $addresses[array_rand($addresses)];
                    $name = $names[array_rand($names)];
                    $phone = '09' . rand(10000000, 99999999);
                    
                    // Insert order
                    $stmt = $pdo->prepare("
                        INSERT INTO orders (code, name, detail, receive_address, phone, receiver_name, collection_money, shipper_id, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$order_code, $product, $product . ' chính hãng', $address, $phone, $name, $collection_money, $shipper_id, $created_at]);
                    
                    $order_id = $pdo->lastInsertId();
                    
                    // Insert assignment
                    $status = (rand(1, 100) <= 85) ? 'received' : 'shipping'; // 85% completed
                    $assigned_time = date('Y-m-d H:i:s', strtotime($created_at) + rand(3600, 28800)); // 1-8 hours later
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO assignment (order_id, user_id, assigned_at, status) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([$order_id, $shipper_id, $assigned_time, $status]);
                    
                    $order_counter++;
                }
                echo "<p class='info'>📅 $date: Đã thêm $count đơn hàng</p>";
            }
            
            // Get final statistics
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE code LIKE 'FK%'");
            $total_orders = $stmt->fetch()['total'];
            
            $stmt = $pdo->query("SELECT COUNT(*) as completed FROM assignment a JOIN orders o ON a.order_id = o.id WHERE o.code LIKE 'FK%' AND a.status = 'received'");
            $completed_orders = $stmt->fetch()['completed'];
            
            echo "<h3 class='success'>✅ Import hoàn tất!</h3>";
            echo "<p><strong>Tổng số đơn hàng fake:</strong> $total_orders</p>";
            echo "<p><strong>Đơn hàng đã hoàn thành:</strong> $completed_orders</p>";
            echo "<p><strong>Tỷ lệ hoàn thành:</strong> " . round(($completed_orders/$total_orders)*100, 1) . "%</p>";
            
            // Show daily statistics
            $stmt = $pdo->query("
                SELECT DATE(created_at) as order_date, COUNT(*) as daily_count 
                FROM orders 
                WHERE code LIKE 'FK%' 
                GROUP BY DATE(created_at) 
                ORDER BY order_date
            ");
            $daily_stats = $stmt->fetchAll();
            
            echo "<h3>📈 Thống kê theo ngày:</h3>";
            echo "<table>";
            echo "<tr><th>Ngày</th><th>Số đơn hàng</th><th>Biểu đồ</th></tr>";
            foreach ($daily_stats as $stat) {
                $bars = str_repeat('█', min($stat['daily_count'], 20));
                echo "<tr>";
                echo "<td>" . date('d/m/Y', strtotime($stat['order_date'])) . "</td>";
                echo "<td style='text-align: center;'><strong>" . $stat['daily_count'] . "</strong></td>";
                echo "<td style='color: #007bff;'>$bars</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<p class='success'>🎉 Biểu đồ thống kê giờ đây sẽ hiển thị dữ liệu đa dạng và sinh động!</p>";
            echo "<a href='statistics.php' class='btn'>📊 Xem Thống Kê</a>";
            echo "<a href='admin.php' class='btn'>🏠 Về Dashboard</a>";
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Lỗi: " . $e->getMessage() . "</p>";
        }
    } else {
        ?>
        <p>Dữ liệu hiện tại có vẻ đơn điệu (3 đơn mỗi ngày). File này sẽ tạo dữ liệu đa dạng hơn:</p>
        <ul>
            <li>📈 Số đơn hàng khác nhau mỗi ngày (1-15 đơn)</li>
            <li>🎯 Ngày cao điểm và ngày thấp điểm</li>
            <li>📊 Biểu đồ thống kê sinh động hơn</li>
            <li>🔄 Tỷ lệ hoàn thành thực tế (85%)</li>
        </ul>
        
        <form method="POST">
            <button type="submit" name="import" class="btn">🚀 Bắt Đầu Import Dữ Liệu</button>
        </form>
        
        <p><strong>Lưu ý:</strong> Quá trình này sẽ xóa tất cả dữ liệu fake cũ (mã đơn bắt đầu bằng FK) và tạo dữ liệu mới.</p>
        <?php
    }
    ?>
</body>
</html> 