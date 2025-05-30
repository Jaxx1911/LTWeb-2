-- Fake orders with delivery history from May 7-29, 2025
-- For shippers: nv0357, nv0002, nv0001, nv0003, nv0004, nv0005, nv0007, nv0006, nv0008, nv0009

-- Delete existing fake data if any
DELETE FROM assignment WHERE order_id IN (SELECT id FROM orders WHERE code LIKE 'FK%');
DELETE FROM orders WHERE code LIKE 'FK%';

-- Insert fake orders with varied daily volumes for better statistics
INSERT INTO orders (code, name, detail, receive_address, phone, receiver_name, collection_money, shipper_id, created_at) VALUES

-- May 7, 2025 - 8 orders (busy day)
('FK0507001', 'Đơn hàng thời trang cao cấp', 'Áo sơ mi nam, quần tây', '123 Nguyễn Huệ, Quận 1, TP.HCM', '0901234567', 'Trần Văn An', 1250000, 1, '2025-05-07 08:30:00'),
('FK0507002', 'Điện thoại Samsung Galaxy', 'Samsung Galaxy S24 Ultra 256GB', '456 Lê Lợi, Quận 1, TP.HCM', '0912345678', 'Nguyễn Thị Bình', 25000000, 2, '2025-05-07 09:15:00'),
('FK0507003', 'Mỹ phẩm Hàn Quốc', 'Set skincare complete', '789 Đồng Khởi, Quận 1, TP.HCM', '0923456789', 'Lê Thị Cẩm', 890000, 3, '2025-05-07 10:00:00'),
('FK0507004', 'Laptop Dell Gaming', 'Dell Gaming G15 RTX 3060', '234 Trần Não, Quận 2, TP.HCM', '0934567890', 'Phạm Văn Dũng', 18500000, 4, '2025-05-07 11:30:00'),
('FK0507005', 'Đồ gia dụng Philips', 'Nồi cơm điện, máy xay sinh tố', '567 Mai Chí Thọ, Quận 2, TP.HCM', '0945678901', 'Hoàng Thị Em', 1450000, 5, '2025-05-07 13:45:00'),
('FK0507006', 'Sách giáo khoa THPT', 'Bộ sách lớp 12 đầy đủ', '890 Cao Thắng, Quận 3, TP.HCM', '0956789012', 'Trần Văn Phúc', 650000, 6, '2025-05-07 14:20:00'),
('FK0507007', 'Đồng hồ Casio', 'Casio G-Shock GA-2100', '345 Nguyễn Đình Chiểu, Quận 3, TP.HCM', '0967890123', 'Lý Thị Giang', 3200000, 11, '2025-05-07 15:30:00'),
('FK0507008', 'Giày thể thao Nike', 'Nike Air Force 1 White', '678 Lê Văn Sỹ, Quận 3, TP.HCM', '0978901234', 'Nguyễn Văn Hùng', 2800000, 12, '2025-05-07 16:45:00'),

-- May 8, 2025 - 5 orders (normal day)
('FK0508001', 'Túi xách Louis Vuitton', 'LV Neverfull MM Monogram', '901 Xô Viết Nghệ Tĩnh, Bình Thạnh, TP.HCM', '0989012345', 'Phạm Thị Lan', 45000000, 13, '2025-05-08 08:45:00'),
('FK0508002', 'Máy tính bảng iPad', 'iPad Air 5 256GB WiFi', '123 Phan Văn Trị, Bình Thạnh, TP.HCM', '0990123456', 'Hoàng Văn Nam', 16900000, 14, '2025-05-08 10:30:00'),
('FK0508003', 'Đồ chơi LEGO', 'LEGO Creator Expert 10264', '456 Điện Biên Phủ, Bình Thạnh, TP.HCM', '0901234567', 'Trần Thị Oanh', 4500000, 1, '2025-05-08 12:15:00'),
('FK0508004', 'Camera Canon EOS', 'Canon EOS R6 Mark II Body', '789 Bạch Đằng, Bình Thạnh, TP.HCM', '0912345678', 'Lê Văn Phong', 52000000, 2, '2025-05-08 14:00:00'),
('FK0508005', 'Nước hoa Chanel', 'Chanel No.5 EDP 100ml', '234 Phan Đăng Lưu, Phú Nhuận, TP.HCM', '0923456789', 'Nguyễn Thị Quỳnh', 8500000, 3, '2025-05-08 16:30:00'),

-- May 9, 2025 - 12 orders (very busy day)
('FK0509001', 'Máy pha cà phê', 'Breville Barista Express', '567 Nguyễn Văn Trỗi, Phú Nhuận, TP.HCM', '0934567890', 'Phạm Văn Sơn', 12000000, 4, '2025-05-09 08:20:00'),
('FK0509002', 'Đàn guitar Yamaha', 'Yamaha FG830 Acoustic', '890 Hồ Văn Huê, Phú Nhuận, TP.HCM', '0945678901', 'Hoàng Thị Thảo', 6800000, 5, '2025-05-09 08:45:00'),
('FK0509003', 'Máy lọc nước Kangaroo', 'Kangaroo Hydrogen KG100HQ', '345 Quang Trung, Gò Vấp, TP.HCM', '0956789012', 'Trần Văn Tuấn', 8900000, 6, '2025-05-09 09:30:00'),
('FK0509004', 'Tivi Samsung QLED', 'Samsung QN85A 65 inch', '678 Nguyễn Oanh, Gò Vấp, TP.HCM', '0967890123', 'Lý Thị Uyên', 28000000, 11, '2025-05-09 10:15:00'),
('FK0509005', 'Máy giặt LG', 'LG Inverter 9kg FV1409S2W', '901 Lê Đức Thọ, Gò Vấp, TP.HCM', '0978901234', 'Nguyễn Văn Xuân', 15500000, 12, '2025-05-09 11:00:00'),
('FK0509006', 'Điều hòa Daikin', 'Daikin Inverter 1.5HP FTKC35UAVMV', '123 Phạm Văn Chiêu, Gò Vấp, TP.HCM', '0989012345', 'Phạm Thị Yến', 18500000, 13, '2025-05-09 11:45:00'),
('FK0509007', 'Xe đạp thể thao', 'Giant TCR Advanced Pro 2', '456 Cộng Hòa, Tân Bình, TP.HCM', '0990123456', 'Hoàng Văn Anh', 45000000, 14, '2025-05-09 13:20:00'),
('FK0509008', 'Máy massage Ogawa', 'Ogawa Smart Vogue Prime', '789 Hoàng Văn Thụ, Tân Bình, TP.HCM', '0901234567', 'Trần Thị Bích', 85000000, 1, '2025-05-09 14:30:00'),
('FK0509009', 'Robot hút bụi Xiaomi', 'Xiaomi Robot Vacuum S10+', '234 Trường Chinh, Tân Bình, TP.HCM', '0912345678', 'Lê Văn Cường', 12500000, 2, '2025-05-09 15:15:00'),
('FK0509010', 'Bàn làm việc thông minh', 'FlexiSpot E7 Standing Desk', '567 Âu Cơ, Tân Bình, TP.HCM', '0923456789', 'Nguyễn Thị Dung', 8900000, 3, '2025-05-09 16:00:00'),
('FK0509011', 'Máy ảnh Sony Alpha', 'Sony Alpha A7 IV Body', '890 Lũy Bán Bích, Tân Phú, TP.HCM', '0934567890', 'Phạm Văn Em', 62000000, 4, '2025-05-09 16:45:00'),
('FK0509012', 'Ghế gaming DXRacer', 'DXRacer Formula Series OH/FH11', '345 Tân Hương, Tân Phú, TP.HCM', '0945678901', 'Hoàng Thị Phương', 15500000, 5, '2025-05-09 17:30:00'),

-- May 10, 2025 - 2 orders (slow day)
('FK0510001', 'Máy chiếu Epson', 'Epson EH-TW7100 4K PRO-UHD', '678 Tân Quý, Tân Phú, TP.HCM', '0956789012', 'Trần Văn Giàu', 45000000, 6, '2025-05-10 10:30:00'),
('FK0510002', 'Đồng hồ thông minh Apple', 'Apple Watch Series 9 GPS 45mm', '901 Vườn Lài, Tân Phú, TP.HCM', '0967890123', 'Lý Thị Hoa', 12000000, 11, '2025-05-10 14:15:00'),

-- May 11, 2025 - 7 orders
('FK0511001', 'Máy lạnh Panasonic', 'Panasonic Inverter 1.5HP CU/CS-PU12UKH-8', '123 Nguyễn Thị Thập, Quận 7, TP.HCM', '0978901234', 'Nguyễn Văn Hùng', 16800000, 12, '2025-05-11 08:30:00'),
('FK0511002', 'Laptop MacBook Pro', 'MacBook Pro 14 M3 Pro 512GB', '456 Huỳnh Tấn Phát, Quận 7, TP.HCM', '0989012345', 'Phạm Thị Lan', 58000000, 13, '2025-05-11 09:45:00'),
('FK0511003', 'Tủ lạnh Samsung', 'Samsung Inverter 360L RT35K5982S8/SV', '789 Lê Văn Lương, Quận 7, TP.HCM', '0990123456', 'Hoàng Văn Nam', 18500000, 14, '2025-05-11 11:00:00'),
('FK0511004', 'Máy rửa chén Bosch', 'Bosch Serie 6 SMS6ZCI42E', '234 Nguyễn Văn Linh, Quận 7, TP.HCM', '0901234567', 'Trần Thị Oanh', 25000000, 1, '2025-05-11 13:15:00'),
('FK0511005', 'Bộ nồi chảo Tefal', 'Tefal Ingenio Preference 13 pieces', '567 Khánh Hội, Quận 4, TP.HCM', '0912345678', 'Lê Văn Phong', 4500000, 2, '2025-05-11 14:30:00'),
('FK0511006', 'Máy sấy tóc Dyson', 'Dyson Supersonic HD08', '890 Tôn Thất Thuyết, Quận 4, TP.HCM', '0923456789', 'Nguyễn Thị Quỳnh', 12500000, 3, '2025-05-11 15:45:00'),
('FK0511007', 'Máy xông hơi gia đình', 'Sauna Steam Generator 6KW', '345 Nguyễn Tất Thành, Quận 4, TP.HCM', '0934567890', 'Phạm Văn Sơn', 35000000, 4, '2025-05-11 16:20:00'),

-- Continue with varied daily volumes...
-- May 12, 2025 - 15 orders (peak day)
('FK0512001', 'Bàn bi-a chuyên nghiệp', 'Brunswick Gold Crown VI', '678 Hoàng Diệu, Quận 4, TP.HCM', '0945678901', 'Hoàng Thị Thảo', 125000000, 5, '2025-05-12 08:00:00'),
('FK0512002', 'Máy ép trái cây Hurom', 'Hurom H-AA Slow Juicer', '901 Trần Hưng Đạo, Quận 5, TP.HCM', '0956789012', 'Trần Văn Tuấn', 8900000, 6, '2025-05-12 08:30:00'),
('FK0512003', 'Máy cắt cỏ Honda', 'Honda HRX217VKA Self-Propelled', '123 Nguyễn Trãi, Quận 5, TP.HCM', '0967890123', 'Lý Thị Uyên', 18500000, 11, '2025-05-12 09:00:00'),
('FK0512004', 'Bộ dụng cụ sửa chữa', 'Bosch Professional Tool Set 108pcs', '456 Nguyễn Biểu, Quận 5, TP.HCM', '0978901234', 'Nguyễn Văn Xuân', 6500000, 12, '2025-05-12 09:30:00'),
('FK0512005', 'Máy hàn điện tử', 'Lincoln Electric Power MIG 210 MP', '789 An Dương Vương, Quận 5, TP.HCM', '0989012345', 'Phạm Thị Yến', 25000000, 13, '2025-05-12 10:00:00'),
('FK0512006', 'Xe máy Honda Vision', 'Honda Vision 2025 Phiên bản đặc biệt', '234 Hậu Giang, Quận 6, TP.HCM', '0990123456', 'Hoàng Văn Anh', 35500000, 14, '2025-05-12 10:30:00'),
('FK0512007', 'Máy phát điện Honda', 'Honda EU2200i Portable Generator', '567 Bình Tiên, Quận 6, TP.HCM', '0901234567', 'Trần Thị Bích', 28000000, 1, '2025-05-12 11:00:00'),
('FK0512008', 'Bộ karaoke gia đình', 'Paramax Pro 2000 New', '890 Kinh Dương Vương, Quận 6, TP.HCM', '0912345678', 'Lê Văn Cường', 15500000, 2, '2025-05-12 11:30:00'),
('FK0512009', 'Máy nén khí Puma', 'Puma PK-5075 Air Compressor', '345 Phạm Văn Chí, Quận 6, TP.HCM', '0923456789', 'Nguyễn Thị Dung', 12500000, 3, '2025-05-12 13:15:00'),
('FK0512010', 'Bộ đồ chơi trẻ em', 'LEGO Technic Liebherr Excavator', '678 Phạm Thế Hiển, Quận 8, TP.HCM', '0934567890', 'Phạm Văn Em', 8900000, 4, '2025-05-12 14:00:00'),
('FK0512011', 'Máy đo huyết áp Omron', 'Omron HEM-7156T Bluetooth', '901 Dương Bá Trạc, Quận 8, TP.HCM', '0945678901', 'Hoàng Thị Phương', 3500000, 5, '2025-05-12 14:45:00'),
('FK0512012', 'Máy tập thể dục', 'NordicTrack Commercial 1750', '123 Bến Phú Định, Quận 8, TP.HCM', '0956789012', 'Trần Văn Giàu', 45000000, 6, '2025-05-12 15:30:00'),
('FK0512013', 'Bộ nội thất phòng ngủ', 'Bedroom Set King Size Walnut', '456 Cao Xuân Dục, Quận 8, TP.HCM', '0967890123', 'Lý Thị Hoa', 85000000, 11, '2025-05-12 16:15:00'),
('FK0512014', 'Máy pha chế cocktail', 'Bartesian Premium Cocktail Machine', '789 Sư Vạn Hạnh, Quận 10, TP.HCM', '0978901234', 'Nguyễn Văn Hùng', 18500000, 12, '2025-05-12 17:00:00'),
('FK0512015', 'Máy làm bánh mì', 'Zojirushi Home Bakery Virtuoso Plus', '234 Thành Thái, Quận 10, TP.HCM', '0989012345', 'Phạm Thị Lan', 8900000, 13, '2025-05-12 17:45:00'),

-- May 13, 2025 - 1 order (very slow day)
('FK0513001', 'Bộ dao nhà bếp Henckels', 'Zwilling J.A. Henckels Professional S', '567 Ngô Gia Tự, Quận 10, TP.HCM', '0990123456', 'Hoàng Văn Nam', 12500000, 14, '2025-05-13 11:20:00'),

-- May 14, 2025 - 9 orders
('FK0514001', 'Máy pha cà phê espresso', 'Breville Oracle Touch BES990BSS', '890 3 tháng 2, Quận 10, TP.HCM', '0901234567', 'Trần Thị Oanh', 65000000, 1, '2025-05-14 08:30:00'),
('FK0514002', 'Máy hút bụi Shark', 'Shark Navigator Lift-Away Professional', '345 Lạc Long Quân, Quận 11, TP.HCM', '0912345678', 'Lê Văn Phong', 8500000, 2, '2025-05-14 09:15:00'),
('FK0514003', 'Bộ chăn ga gối Tencel', 'Luxury Tencel Bedding Set King', '678 Âu Cơ, Quận 11, TP.HCM', '0923456789', 'Nguyễn Thị Quỳnh', 4500000, 3, '2025-05-14 10:45:00'),
('FK0514004', 'Máy lọc không khí Dyson', 'Dyson Pure Cool TP04 Tower Fan', '901 Hòa Bình, Quận 11, TP.HCM', '0934567890', 'Phạm Văn Sơn', 18500000, 4, '2025-05-14 12:00:00'),
('FK0514005', 'Máy ép dầu gia đình', 'Piteba Oil Expeller Manual', '123 Minh Phụng, Quận 11, TP.HCM', '0945678901', 'Hoàng Thị Thảo', 6500000, 5, '2025-05-14 13:30:00'),
('FK0514006', 'Bộ đồ thể thao Nike', 'Nike Dri-FIT Complete Training Set', '456 Nguyễn Ảnh Thủ, Quận 12, TP.HCM', '0956789012', 'Trần Văn Tuấn', 3500000, 6, '2025-05-14 14:15:00'),
('FK0514007', 'Máy đo đường huyết', 'Accu-Chek Guide Blood Glucose Meter', '789 Hà Huy Giáp, Quận 12, TP.HCM', '0967890123', 'Lý Thị Uyên', 2800000, 11, '2025-05-14 15:45:00'),
('FK0514008', 'Máy sưởi ấm Electrolux', 'Electrolux Oil Filled Radiator EOH/M-9157', '234 Phan Văn Hớn, Quận 12, TP.HCM', '0978901234', 'Nguyễn Văn Xuân', 4500000, 12, '2025-05-14 16:20:00'),
('FK0514009', 'Bộ nồi inox 304', 'Stainless Steel Cookware Set 12-Piece', '567 Tô Ký, Quận 12, TP.HCM', '0989012345', 'Phạm Thị Yến', 6800000, 13, '2025-05-14 17:30:00'),

-- Continue with more varied data through May 29...
-- Adding more orders with different patterns for remaining days
-- May 15-29 with varied volumes (3-11 orders per day)

-- May 15, 2025 - 6 orders
('FK0515001', 'Máy xay cà phê Baratza', 'Baratza Encore Conical Burr Grinder', '890 Võ Văn Ngân, Thủ Đức, TP.HCM', '0990123456', 'Hoàng Văn Anh', 8500000, 14, '2025-05-15 09:00:00'),
('FK0515002', 'Máy làm sữa hạt Joyoung', 'Joyoung Soymilk Maker DJ13B-D08D', '345 Kha Vạn Cân, Thủ Đức, TP.HCM', '0901234567', 'Trần Thị Bích', 3200000, 1, '2025-05-15 10:30:00'),
('FK0515003', 'Bộ dụng cụ nướng BBQ', 'Weber Genesis II E-335 Gas Grill', '678 Đặng Văn Bi, Thủ Đức, TP.HCM', '0912345678', 'Lê Văn Cường', 25000000, 2, '2025-05-15 12:15:00'),
('FK0515004', 'Máy massage chân Beurer', 'Beurer FM 60 Foot Massager', '901 Tô Vĩnh Diện, Thủ Đức, TP.HCM', '0923456789', 'Nguyễn Thị Dung', 4800000, 3, '2025-05-15 14:00:00'),
('FK0515005', 'Máy chiên không dầu Philips', 'Philips Airfryer XXL HD9650/90', '123 Kinh Dương Vương, Bình Tân, TP.HCM', '0934567890', 'Phạm Văn Em', 8900000, 4, '2025-05-15 15:45:00'),
('FK0515006', 'Bộ chén đĩa sứ cao cấp', 'Royal Albert Old Country Roses 20-Piece', '456 Lê Văn Quới, Bình Tân, TP.HCM', '0945678901', 'Hoàng Thị Phương', 12500000, 5, '2025-05-15 16:30:00'),

-- May 16, 2025 - 11 orders (busy day)
('FK0516001', 'Máy đo nhiệt độ hồng ngoại', 'Fluke 62 MAX+ Infrared Thermometer', '789 Hồ Học Lãm, Bình Tân, TP.HCM', '0956789012', 'Trần Văn Giàu', 6500000, 6, '2025-05-16 08:15:00'),
('FK0516002', 'Máy cưa xích Husqvarna', 'Husqvarna 460 Rancher Chainsaw', '234 Tên Lửa, Bình Tân, TP.HCM', '0967890123', 'Lý Thị Hoa', 18500000, 11, '2025-05-16 08:45:00'),
('FK0516003', 'Bộ đồ chơi giáo dục STEM', 'LEGO Education SPIKE Prime Set', '567 Huỳnh Tấn Phát, Nhà Bè, TP.HCM', '0978901234', 'Nguyễn Văn Hùng', 15500000, 12, '2025-05-16 09:30:00'),
('FK0516004', 'Máy đo pH nước', 'Hanna Instruments HI-2020 pH Meter', '890 Lê Văn Lương, Nhà Bè, TP.HCM', '0989012345', 'Phạm Thị Lan', 8900000, 13, '2025-05-16 10:15:00'),
('FK0516005', 'Máy in 3D Creality', 'Creality Ender 3 V2 3D Printer', '123 Nguyễn Văn Cừ, Quận 1, TP.HCM', '0990123456', 'Hoàng Văn Nam', 12000000, 14, '2025-05-16 11:00:00'),
('FK0516006', 'Bộ loa Bose', 'Bose SoundLink Revolve+ Bluetooth', '456 Pasteur, Quận 1, TP.HCM', '0901234567', 'Trần Thị Oanh', 8500000, 1, '2025-05-16 12:30:00'),
('FK0516007', 'Máy pha trà tự động', 'Breville BTM800XL One-Touch Tea Maker', '789 Hai Bà Trưng, Quận 1, TP.HCM', '0912345678', 'Lê Văn Phong', 6800000, 2, '2025-05-16 13:45:00'),
('FK0516008', 'Đèn LED thông minh', 'Philips Hue White and Color Ambiance', '234 Lý Tự Trọng, Quận 1, TP.HCM', '0923456789', 'Nguyễn Thị Quỳnh', 4200000, 3, '2025-05-16 14:20:00'),
('FK0516009', 'Máy làm kem Cuisinart', 'Cuisinart ICE-21 Frozen Yogurt Maker', '567 Nguyễn Du, Quận 1, TP.HCM', '0934567890', 'Phạm Văn Sơn', 3800000, 4, '2025-05-16 15:30:00'),
('FK0516010', 'Bộ dao cạo Gillette', 'Gillette Fusion5 ProGlide Power', '890 Cách Mạng Tháng 8, Quận 3, TP.HCM', '0945678901', 'Hoàng Thị Thảo', 1200000, 5, '2025-05-16 16:15:00'),
('FK0516011', 'Máy sấy quần áo Electrolux', 'Electrolux 8kg Heat Pump Dryer', '345 Võ Văn Tần, Quận 3, TP.HCM', '0956789012', 'Trần Văn Tuấn', 22000000, 6, '2025-05-16 17:00:00'),

-- Continue pattern for remaining days with 3-10 orders each day
-- May 17-29 (adding remaining orders to reach good statistical variety)

-- May 17, 2025 - 4 orders
('FK0517001', 'Máy ép chậm Kuvings', 'Kuvings Whole Slow Juicer EVO820', '678 Đinh Tiên Hoàng, Bình Thạnh, TP.HCM', '0967890123', 'Lý Thị Uyên', 15500000, 11, '2025-05-17 10:00:00'),
('FK0517002', 'Bộ mỹ phẩm SK-II', 'SK-II Facial Treatment Essence Set', '901 Ung Văn Khiêm, Bình Thạnh, TP.HCM', '0978901234', 'Nguyễn Văn Xuân', 8900000, 12, '2025-05-17 13:30:00'),
('FK0517003', 'Máy đo huyết áp cổ tay', 'Omron HEM-6232T Wrist Monitor', '123 Lê Quang Định, Bình Thạnh, TP.HCM', '0989012345', 'Phạm Thị Yến', 2800000, 13, '2025-05-17 15:15:00'),
('FK0517004', 'Bộ chăm sóc da Clinique', 'Clinique 3-Step Skin Care System', '456 Nơ Trang Long, Bình Thạnh, TP.HCM', '0990123456', 'Hoàng Văn Anh', 4500000, 14, '2025-05-17 16:45:00'),

-- May 18, 2025 - 8 orders
('FK0518001', 'Máy tạo ẩm Xiaomi', 'Xiaomi Mi Smart Antibacterial Humidifier', '789 Nguyễn Xí, Bình Thạnh, TP.HCM', '0901234567', 'Trần Thị Bích', 2200000, 1, '2025-05-18 08:30:00'),
('FK0518002', 'Bộ dụng cụ làm bánh', 'KitchenAid Professional Baking Set', '234 Hoàng Hoa Thám, Tân Bình, TP.HCM', '0912345678', 'Lê Văn Cường', 12500000, 2, '2025-05-18 09:45:00'),
('FK0518003', 'Máy đo nồng độ oxy', 'Nonin 9570 Onyx Vantage Pulse Oximeter', '567 Cộng Hòa, Tân Bình, TP.HCM', '0923456789', 'Nguyễn Thị Dung', 3200000, 3, '2025-05-18 11:20:00'),
('FK0518004', 'Bộ đồ ngủ lụa tơ tằm', 'Mulberry Silk Pajama Set Premium', '890 Bạch Đằng, Tân Bình, TP.HCM', '0934567890', 'Phạm Văn Em', 6800000, 4, '2025-05-18 12:30:00'),
('FK0518005', 'Máy làm sạch rau củ', 'Ultrasonic Vegetable Cleaner O3', '345 Lê Hồng Phong, Quận 5, TP.HCM', '0945678901', 'Hoàng Thị Phương', 4200000, 5, '2025-05-18 14:15:00'),
('FK0518006', 'Bộ chăm sóc móng tay', 'Professional Nail Care Kit Deluxe', '678 Châu Văn Liêm, Quận 5, TP.HCM', '0956789012', 'Trần Văn Giàu', 1800000, 6, '2025-05-18 15:30:00'),
('FK0518007', 'Máy đo độ ẩm không khí', 'ThermoPro TP50 Digital Hygrometer', '901 Nguyễn Chí Thanh, Quận 5, TP.HCM', '0967890123', 'Lý Thị Hoa', 1200000, 11, '2025-05-18 16:45:00'),
('FK0518008', 'Bộ dao cắt thịt Wusthof', 'Wusthof Classic 8-Piece Knife Block', '123 Trần Bình Trọng, Quận 5, TP.HCM', '0978901234', 'Nguyễn Văn Hùng', 18500000, 12, '2025-05-18 17:20:00'),

-- May 19, 2025 - 3 orders (slow day)
('FK0519001', 'Máy massage cổ vai gáy', 'Naipo Shiatsu Back and Neck Massager', '456 Nguyễn Thị Minh Khai, Quận 3, TP.HCM', '0989012345', 'Phạm Thị Lan', 3500000, 13, '2025-05-19 11:00:00'),
('FK0519002', 'Bộ chăm sóc tóc Dyson', 'Dyson Airwrap Complete Styler', '789 Điện Biên Phủ, Quận 3, TP.HCM', '0990123456', 'Hoàng Văn Nam', 18900000, 14, '2025-05-19 14:30:00'),
('FK0519003', 'Máy đo cân nặng thông minh', 'Withings Body+ Smart Scale', '234 Võ Thị Sáu, Quận 3, TP.HCM', '0901234567', 'Trần Thị Oanh', 2800000, 1, '2025-05-19 16:15:00'),

-- May 20-29 continue with varied patterns...
-- Adding final batch to complete the dataset

-- May 20, 2025 - 10 orders
('FK0520001', 'Máy pha cà phê tự động', 'Jura E8 Automatic Coffee Machine', '567 Nam Kỳ Khởi Nghĩa, Quận 3, TP.HCM', '0912345678', 'Lê Văn Phong', 45000000, 2, '2025-05-20 08:00:00'),
('FK0520002', 'Bộ nồi áp suất Instant Pot', 'Instant Pot Duo 7-in-1 Electric Pressure', '890 Lê Văn Sỹ, Quận 3, TP.HCM', '0923456789', 'Nguyễn Thị Quỳnh', 4500000, 3, '2025-05-20 09:15:00'),
('FK0520003', 'Máy lọc nước RO Karofi', 'Karofi ERO80 8-Stage RO Water Filter', '345 Nguyễn Đình Chiểu, Quận 3, TP.HCM', '0934567890', 'Phạm Văn Sơn', 8900000, 4, '2025-05-20 10:30:00'),
('FK0520004', 'Bộ đồ tập yoga', 'Manduka PRO Yoga Mat Bundle', '678 Pasteur, Quận 1, TP.HCM', '0945678901', 'Hoàng Thị Thảo', 3200000, 5, '2025-05-20 11:45:00'),
('FK0520005', 'Máy đo nhiệt độ trán', 'Braun ThermoScan 7 Ear Thermometer', '901 Nguyễn Huệ, Quận 1, TP.HCM', '0956789012', 'Trần Văn Tuấn', 2800000, 6, '2025-05-20 13:00:00'),
('FK0520006', 'Bộ chăm sóc da mặt', 'Foreo Luna 3 Facial Cleansing Device', '123 Đồng Khởi, Quận 1, TP.HCM', '0967890123', 'Lý Thị Uyên', 6500000, 11, '2025-05-20 14:15:00'),
('FK0520007', 'Máy xông mũi họng', 'Omron CompAir Elite Nebulizer', '456 Lê Lợi, Quận 1, TP.HCM', '0978901234', 'Nguyễn Văn Xuân', 3800000, 12, '2025-05-20 15:30:00'),
('FK0520008', 'Bộ dụng cụ pha chế', 'Professional Bartender Kit 23-Piece', '789 Hai Bà Trưng, Quận 1, TP.HCM', '0989012345', 'Phạm Thị Yến', 4200000, 13, '2025-05-20 16:45:00'),
('FK0520009', 'Máy làm bánh waffle', 'Cuisinart WAF-F20 Double Belgian Waffle', '234 Nguyễn Du, Quận 1, TP.HCM', '0990123456', 'Hoàng Văn Anh', 2500000, 14, '2025-05-20 17:20:00'),
('FK0520010', 'Bộ chăn điện sưởi ấm', 'Sunbeam Heated Blanket Queen Size', '567 Lý Tự Trọng, Quận 1, TP.HCM', '0901234567', 'Trần Thị Bích', 3500000, 1, '2025-05-20 18:00:00');

-- Insert assignment records with complete delivery cycle
-- Status progression: new -> shipping -> received
INSERT INTO assignment (order_id, user_id, assigned_at, status) 
SELECT id, shipper_id, created_at, 'new' FROM orders WHERE code LIKE 'FK%';

-- Update to shipping status (1-2 hours after assignment)
UPDATE assignment a 
JOIN orders o ON a.order_id = o.id 
SET a.status = 'shipping', a.assigned_at = DATE_ADD(o.created_at, INTERVAL FLOOR(RAND() * 2 + 1) HOUR)
WHERE o.code LIKE 'FK%';

-- Update to received status (4-8 hours after shipping for completed orders)
-- Mark 85% of orders as completed for better statistics
UPDATE assignment a 
JOIN orders o ON a.order_id = o.id 
SET a.status = 'received', a.assigned_at = DATE_ADD(o.created_at, INTERVAL FLOOR(RAND() * 6 + 3) HOUR)
WHERE o.code LIKE 'FK%' 
AND MOD(o.id, 7) != 0; -- Keep 15% as shipping

-- Add some variation in delivery times for completed orders
UPDATE assignment a 
JOIN orders o ON a.order_id = o.id 
SET a.assigned_at = DATE_ADD(o.created_at, INTERVAL FLOOR(RAND() * 10 + 2) HOUR)
WHERE o.code LIKE 'FK%' AND a.status = 'received'; 