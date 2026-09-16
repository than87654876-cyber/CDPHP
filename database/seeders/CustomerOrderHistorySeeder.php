<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerOrderHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Đảm bảo dữ liệu danh mục và món ăn đã tồn tại
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }
        if (Dish::count() === 0) {
            $this->call(DishSeeder::class);
        }

        $allDishes = Dish::with('category')->where('is_available', true)->get();
        if ($allDishes->isEmpty()) {
            $allDishes = Dish::all();
        }

        if ($allDishes->isEmpty()) {
            $this->command->error('Không tìm thấy món ăn nào để tạo đơn hàng!');
            return;
        }

        // 2. Phân cụm món ăn theo 6 nhóm sở thích ẩm thực (Persona Clusters)
        // Group 0: Trà sữa & Tráng miệng & Bánh ngọt (Category 2: Đồ uống, 4: Bánh kem, 5: Tráng miệng)
        $clusterMilkTea = $allDishes->filter(fn($d) => in_array($d->category_id, [2, 4, 5]))->values();
        // Group 1: Gà rán & Đồ ăn nhanh & Nước ngọt (Category 1: Đồ ăn, 6: Pizza/Burger, 2: Đồ uống)
        $clusterFastFood = $allDishes->filter(fn($d) => in_array($d->category_id, [1, 6, 2]))->values();
        // Group 2: Pizza & Burger & Đồ ăn vặt (Category 6: Pizza/Burger, 1: Đồ ăn, 2: Đồ uống)
        $clusterPizza = $allDishes->filter(fn($d) => in_array($d->category_id, [6, 1, 2]))->values();
        // Group 3: Cơm tấm, Cơm hộp & Mì phở (Category 10: Cơm hộp, 9: Mì phở, 2: Đồ uống)
        $clusterRiceNoodles = $allDishes->filter(fn($d) => in_array($d->category_id, [10, 9, 2]))->values();
        // Group 4: Món chay thanh đạm & Sinh tố (Category 3: Đồ chay, 2: Đồ uống, 5: Tráng miệng)
        $clusterVegetarian = $allDishes->filter(fn($d) => in_array($d->category_id, [3, 2, 5]))->values();
        // Group 5: Lẩu & Sushi tụ họp gia đình/bạn bè (Category 7: Món lẩu, 8: Sushi, 2: Đồ uống, 5: Tráng miệng)
        $clusterHotpotSushi = $allDishes->filter(fn($d) => in_array($d->category_id, [7, 8, 2, 5]))->values();

        $clusters = [
            $clusterMilkTea->isNotEmpty() ? $clusterMilkTea : $allDishes,
            $clusterFastFood->isNotEmpty() ? $clusterFastFood : $allDishes,
            $clusterPizza->isNotEmpty() ? $clusterPizza : $allDishes,
            $clusterRiceNoodles->isNotEmpty() ? $clusterRiceNoodles : $allDishes,
            $clusterVegetarian->isNotEmpty() ? $clusterVegetarian : $allDishes,
            $clusterHotpotSushi->isNotEmpty() ? $clusterHotpotSushi : $allDishes,
        ];

        // 3. Danh sách 100 Họ tên Việt Nam & Địa chỉ TP.HCM phong phú
        $lastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý'];
        $middleNames = ['Văn', 'Thị', 'Hữu', 'Đức', 'Thanh', 'Minh', 'Thành', 'Quốc', 'Ngọc', 'Gia', 'Hoàng', 'Khánh', 'Xuân', 'Kim', 'Bảo', 'Tấn'];
        $firstNames = ['An', 'Bình', 'Cường', 'Dũng', 'Duy', 'Đạt', 'Hải', 'Hậu', 'Hiếu', 'Hoà', 'Huy', 'Hùng', 'Khoa', 'Kiệt', 'Lâm', 'Long', 'Minh', 'Nam', 'Nghĩa', 'Nhân', 'Phúc', 'Quân', 'Quang', 'Sang', 'Sơn', 'Tài', 'Tâm', 'Tân', 'Thái', 'Thắng', 'Thịnh', 'Thuận', 'Toàn', 'Trí', 'Trọng', 'Trung', 'Tú', 'Tuấn', 'Tùng', 'Vinh', 'Vũ', 'Ánh', 'Bích', 'Châu', 'Chi', 'Cúc', 'Diệp', 'Dung', 'Duyên', 'Giang', 'Hà', 'Hạnh', 'Hoa', 'Hương', 'Huyền', 'Khánh', 'Lan', 'Linh', 'Loan', 'Ly', 'Mai', 'Mi', 'My', 'Nga', 'Ngân', 'Ngọc', 'Nhung', 'Như', 'Oanh', 'Phương', 'Phượng', 'Quyên', 'Quỳnh', 'Tâm', 'Thảo', 'Thu', 'Thủy', 'Thư', 'Thương', 'Trang', 'Trâm', 'Trinh', 'Trúc', 'Tú', 'Tuyết', 'Uyên', 'Vân', 'Vi', 'Vy', 'Yến'];

        $streets = [
            '123 Nguyễn Huệ, Phường Bến Nghé, Quận 1',
            '45 Lê Lợi, Phường Bến Thành, Quận 1',
            '88 Hai Bà Trưng, Phường Đa Kao, Quận 1',
            '12 Pasteur, Phường Võ Thị Sáu, Quận 3',
            '234 Nam Kỳ Khởi Nghĩa, Phường 7, Quận 3',
            '67 Cách Mạng Tháng 8, Phường 5, Quận 3',
            '302 Trần Hưng Đạo, Phường 11, Quận 5',
            '15 An Dương Vương, Phường 8, Quận 5',
            '56 Nguyễn Trãi, Phường 3, Quận 5',
            '789 Nguyễn Thị Thập, Phường Tân Phú, Quận 7',
            '102 Huỳnh Tấn Phát, Phường Tân Thuận Đông, Quận 7',
            '22 Đường số 10, KĐT Him Lam, Quận 7',
            '415 Sư Vạn Hạnh, Phường 12, Quận 10',
            '88 Ba Tháng Hai, Phường 14, Quận 10',
            '19 Tô Hiến Thành, Phường 15, Quận 10',
            '210 Phan Xích Long, Phường 2, Quận Phú Nhuận',
            '55 Hoàng Văn Thụ, Phường 8, Quận Phú Nhuận',
            '143 Lê Văn Sỹ, Phường 14, Quận Phú Nhuận',
            '320 Điện Biên Phủ, Phường 25, Quận Bình Thạnh',
            '95 Xô Viết Nghệ Tĩnh, Phường 17, Quận Bình Thạnh',
            '180 D2 (Nguyễn Gia Trí), Phường 25, Quận Bình Thạnh',
            '72 Cộng Hòa, Phường 4, Quận Tân Bình',
            '19 Hoàng Hoa Thám, Phường 13, Quận Tân Bình',
            '305 Trường Chinh, Phường 14, Quận Tân Bình',
            '450 Quang Trung, Phường 10, Quận Gò Vấp',
            '88 Phan Văn Trị, Phường 7, Quận Gò Vấp',
            '12 Lê Đức Thọ, Phường 16, Quận Gò Vấp',
            '215 Võ Văn Ngân, Phường Linh Chiểu, TP. Thủ Đức',
            '89 Đặng Văn Bi, Phường Bình Thọ, TP. Thủ Đức',
            '150 Xa Lộ Hà Nội, Phường Thảo Điền, TP. Thủ Đức',
        ];

        $orderNotesList = [
            'Giao trước cửa nhà giúp mình, cảm ơn shop!',
            'Gọi điện thoại trước khi giao 5 phút nhé.',
            'Ít cay, lấy thêm muỗng nĩa dùng 1 lần.',
            'Trà sữa 50% đường 70% đá giúp mình.',
            'Giao trong giờ hành chính tại quầy lễ tân.',
            'Để ở bảo vệ tòa nhà giúp mình, mình đã thanh toán.',
            'Món ăn nóng giòn, đóng gói cẩn thận giúp shop nhé.',
            'Giao hỏa tốc giúp em đang bận họp.',
            'Cho nhiều nước sốt và tương ớt chấm kèm.',
            'Đơn hàng ăn trưa công ty, xuất hóa đơn nếu có thể.',
        ];

        $paymentMethods = ['bank_transfer', 'momo', 'cash', 'vnpay'];
        $hashedPassword = Hash::make('123456');

        $totalOrdersCount = 0;
        $totalOrderItemsCount = 0;

        DB::beginTransaction();
        try {
            for ($i = 1; $i <= 100; $i++) {
                $email = sprintf('customer%03d@test.com', $i);
                $lName = $lastNames[array_rand($lastNames)];
                $mName = $middleNames[array_rand($middleNames)];
                $fName = $firstNames[array_rand($firstNames)];
                $fullname = "{$lName} {$mName} {$fName}";
                $phone = '09' . str_pad((string)mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                $address = $streets[($i - 1) % count($streets)];

                // Tìm hoặc tạo tài khoản khách hàng
                $customer = User::where('email', $email)->first();
                if (!$customer) {
                    $customer = new User();
                    $customer->email = $email;
                }

                $customer->fullname = $fullname;
                $customer->phone = $phone;
                $customer->password = $hashedPassword;
                $customer->role = 'customer';
                $customer->status = true;
                $customer->notes = $address;
                $customer->points = 0;
                $customer->membership = 'bronze';
                $customer->save();

                // Gán cụm sở thích chính cho khách hàng
                $primaryClusterIndex = ($i - 1) % count($clusters);
                $secondaryClusterIndex = ($i) % count($clusters);
                $primaryDishes = $clusters[$primaryClusterIndex];
                $secondaryDishes = $clusters[$secondaryClusterIndex];

                // Xác định số đơn hàng cho khách hàng này (từ 3 đến 10 đơn hàng)
                $numOrders = mt_rand(3, 10);

                // Tạo danh sách thời gian phân bố tăng dần trong 365 ngày gần đây
                $orderTimestamps = [];
                for ($o = 0; $o < $numOrders; $o++) {
                    $daysAgo = mt_rand(5, 360);
                    $hoursAgo = mt_rand(8, 21); // Giờ đặt hàng từ 8h sáng đến 21h tối
                    $minutesAgo = mt_rand(0, 59);
                    $orderTimestamps[] = Carbon::now()->subDays($daysAgo)->setHour($hoursAgo)->setMinute($minutesAgo)->setSecond(mt_rand(0, 59));
                }
                // Sắp xếp thời gian đơn hàng từ cũ đến mới
                usort($orderTimestamps, fn($a, $b) => $a->timestamp <=> $b->timestamp);

                foreach ($orderTimestamps as $orderIndex => $orderTime) {
                    // Xác định trạng thái đơn hàng
                    // 80% Completed, 6% Delivering, 5% Preparing, 5% Confirmed, 4% Cancelled
                    $randStatus = mt_rand(1, 100);
                    if ($randStatus <= 80) {
                        $orderStatus = 'completed';
                        $paymentStatus = 'paid';
                    } elseif ($randStatus <= 86) {
                        $orderStatus = 'delivering';
                        $paymentStatus = 'paid';
                    } elseif ($randStatus <= 91) {
                        $orderStatus = 'preparing';
                        $paymentStatus = 'paid';
                    } elseif ($randStatus <= 96) {
                        $orderStatus = 'confirmed';
                        $paymentStatus = 'paid';
                    } else {
                        $orderStatus = 'cancelled';
                        $paymentStatus = mt_rand(0, 1) ? 'refunded' : 'pending';
                    }

                    $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                    $note = $orderNotesList[array_rand($orderNotesList)];

                    // Tạo mẫu một số đơn có khiếu nại hoàn tiền
                    $isRefundSample = ($orderStatus === 'cancelled' || ($orderIndex === 0 && $i % 10 === 0));
                    if ($isRefundSample) {
                        $refundReasons = [
                            'Giao hàng trễ hơn 45 phút so với dự kiến do mưa lớn',
                            'Đặt nhầm số lượng món ăn và cần hoàn tiền',
                            'Món ăn bị tràn trong quá trình tài xế vận chuyển',
                            'Không liên hệ được với khách do sự cố mạng viễn thông',
                            'Khách bận họp đột xuất nên yêu cầu hủy và hoàn trả tiền',
                        ];
                        $refundReason = $refundReasons[array_rand($refundReasons)];
                        $note .= "\n[Yêu cầu hoàn tiền: Lý do: {$refundReason}, Số tiền yêu cầu: ".number_format($finalAmount, 0, ',', '.')." đ]";
                        if ($paymentStatus === 'refunded') {
                            $note .= "\n[Admin Phản hồi: Đã đối soát và duyệt hoàn trả dòng tiền cho khách. (Đã duyệt hoàn tiền)]";
                        }
                    }

                    // Chọn từ 2 đến 6 món khác nhau cho đơn hàng này
                    $numItems = mt_rand(2, 6);
                    $selectedDishes = collect();

                    // Lấy 70% món từ cụm sở thích chính, 30% từ cụm phụ hoặc toàn bộ thực đơn
                    while ($selectedDishes->count() < $numItems) {
                        $pool = (mt_rand(1, 100) <= 70) ? $primaryDishes : $secondaryDishes;
                        $randomDish = $pool->random();
                        if (!$selectedDishes->contains('id', $randomDish->id)) {
                            $selectedDishes->push($randomDish);
                        }
                    }

                    // Tính tổng tiền đơn hàng
                    $totalAmount = 0;
                    $itemsToInsert = [];

                    foreach ($selectedDishes as $dish) {
                        $qty = mt_rand(1, 3);
                        $price = (float)$dish->price;
                        $totalAmount += $price * $qty;

                        $itemsToInsert[] = [
                            'dish_id' => $dish->id,
                            'quantity' => $qty,
                            'price' => $price,
                            'created_at' => $orderTime,
                            'updated_at' => $orderTime,
                        ];
                    }

                    $finalAmount = $totalAmount;

                    // Tạo đơn hàng mới
                    $order = new Order();
                    $order->user_id = $customer->id;
                    $order->order_type = 'single';
                    $order->total_amount = $totalAmount;
                    $order->final_amount = $finalAmount;
                    $order->payment_method = $paymentMethod;
                    $order->payment_status = $paymentStatus;
                    $order->order_status = $orderStatus;
                    $order->health_notes = "Giao hàng: {$address}. SĐT: {$phone}. Ghi chú: {$note}";
                    $order->points_accumulated = ($paymentStatus === 'paid');
                    $order->created_at = $orderTime;
                    $order->updated_at = $orderTime;
                    $order->save();

                    // Lưu các món ăn trong đơn
                    foreach ($itemsToInsert as $itemData) {
                        $orderItem = new OrderItem();
                        $orderItem->order_id = $order->id;
                        $orderItem->dish_id = $itemData['dish_id'];
                        $orderItem->quantity = $itemData['quantity'];
                        $orderItem->price = $itemData['price'];
                        $orderItem->created_at = $itemData['created_at'];
                        $orderItem->updated_at = $itemData['updated_at'];
                        $orderItem->save();

                        $totalOrderItemsCount++;
                    }

                    // Tích lũy điểm thưởng cho khách hàng nếu đơn đã thanh toán
                    if ($paymentStatus === 'paid') {
                        $customer->addPoints($finalAmount);
                    }

                    // Tạo đánh giá thực tế cho khoảng 25% các đơn hàng hoàn tất
                    if ($orderStatus === 'completed' && mt_rand(1, 100) <= 25) {
                        $reviewComments = [
                            'Đồ ăn giao nhanh, nóng hổi, gà rán giòn rụm rất ngon miệng!',
                            'Trà sữa chuẩn vị, ít ngọt đúng ý mình, trân châu mềm dẻo 10/10.',
                            'Cơm tấm sườn nướng thơm phức, nước mắm pha vừa miệng, sẽ ủng hộ dài dài.',
                            'Pizza phô mai kéo sợi béo ngậy, bánh giòn đế xốp, ship đúng giờ.',
                            'Mì xào giòn hải sản nhiều tôm mực tươi ngon, đóng gói hộp sạch sẽ.',
                            'Phở bò nước dùng trong ngọt thanh tự nhiên, thịt bò mềm tươi.',
                            'Dịch vụ rất chu đáo, nhân viên giao hàng lịch sự và nhiệt tình.',
                            'Đồ chay nấu rất vừa vị, thanh đạm tốt cho sức khỏe.',
                            'Sushi cá hồi tươi rói, sốt chấm wasabi cay nồng chuẩn vị Nhật.',
                            'Combo gà rán và khoai tây lắc phô mai ngon đỉnh chóp!',
                            'Giao hàng nhanh trong 20 phút, bao bì thân thiện môi trường.',
                            'Quán làm đồ ăn rất có tâm, đồ uống vừa miệng.',
                        ];

                        $rating = (mt_rand(1, 100) <= 80) ? 5 : ((mt_rand(1, 100) <= 85) ? 4 : 3);
                        \App\Models\Review::create([
                            'user_id' => $customer->id,
                            'order_id' => $order->id,
                            'rating' => $rating,
                            'comment' => $reviewComments[array_rand($reviewComments)],
                            'created_at' => $orderTime->copy()->addMinutes(mt_rand(40, 180)),
                            'updated_at' => $orderTime->copy()->addMinutes(mt_rand(40, 180)),
                        ]);
                    }

                    $totalOrdersCount++;
                }

                // Lưu lại trạng thái điểm và xếp hạng thành viên của khách
                $customer->save();
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $this->command->info("Đã tạo thành công 100 tài khoản khách hàng (customer001@test.com -> customer100@test.com)!");
        $this->command->info("Tổng số đơn hàng đã tạo: {$totalOrdersCount} đơn.");
        $this->command->info("Tổng số chi tiết món ăn (Order Items): {$totalOrderItemsCount} mục.");
    }
}
