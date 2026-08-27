@extends('layouts.admin')

@section('title', 'Cấu hình gửi mã nâng cao - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bộ lọc thông minh & Sinh mã tự động nâng cao</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 text-dark" role="alert">
        <strong><i class="fas fa-check-circle mr-1"></i> Thành công!</strong> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 text-dark" role="alert">
        <strong><i class="fas fa-exclamation-triangle mr-1"></i> Lỗi!</strong> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('details'))
    <div class="card border-left-info shadow mb-4">
        <div class="card-header py-3 bg-light text-dark font-weight-bold"><i class="fas fa-paper-plane mr-2"></i>Nhật ký chi tiết các mã đã gửi giả lập:</div>
        <div class="card-body text-dark" style="max-height: 250px; overflow-y: auto;">
            <table class="table table-sm table-striped text-dark">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Email</th>
                        <th>Mã Coupon</th>
                        <th>Nội dung thông điệp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('details') as $detail)
                    <tr>
                        <td class="font-weight-bold">{{ $detail['fullname'] }}</td>
                        <td>{{ $detail['email'] }}</td>
                        <td><span class="badge badge-success font-weight-bold">{{ $detail['code'] }}</span></td>
                        <td class="small">{{ $detail['body'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <form action="{{ route('quanly_guima.post') }}" method="POST">
        @csrf

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary"><i
                        class="fas fa-filter mr-2"></i>Bước 1:
                    Thiết lập điều kiện lọc khách hàng chi tiết</h6>
            </div>
            <div class="card-body text-dark">
                <div class="row">

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-top-danger h-100 py-3 bg-white shadow-sm">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-danger mb-3"><i
                                        class="fas fa-crown mr-1"></i> 1. Theo hạng thành viên
                                </h6>
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted">Chọn các
                                        hạng áp
                                        dụng:</label>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input"
                                            id="rank_diamond" name="ranks[]" value="diamond">
                                        <label class="custom-control-label font-weight-bold"
                                            for="rank_diamond">Hạng Kim Cương (VIP 2)</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input"
                                            id="rank_gold" name="ranks[]" value="gold">
                                        <label class="custom-control-label font-weight-bold"
                                            for="rank_gold">Hạng Vàng (VIP 1)</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input"
                                            id="rank_silver" name="ranks[]" value="silver">
                                        <label class="custom-control-label"
                                            for="rank_silver">Hạng
                                            Bạc</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                            id="rank_standard" name="ranks[]" value="standard">
                                        <label class="custom-control-label"
                                            for="rank_standard">Hạng
                                            Đồng (Thành viên mới)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-top-warning h-100 py-3 bg-white shadow-sm">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-warning mb-3"><i
                                        class="fas fa-history mr-1"></i> 2. Thời gian chưa mua
                                    hàng</h6>
                                <div class="form-group">
                                    <label for="inactive_period"
                                        class="small font-weight-bold text-muted">Kể từ đơn hàng
                                        cuối
                                        cách đây:</label>
                                    <select class="form-control font-weight-bold"
                                        id="inactive_period" name="inactive_period">
                                        <option value="all">-- Không lọc theo tiêu chí này --
                                        </option>
                                        <option value="1_month">Từ 1 tháng trở lên (&gt; 30
                                            ngày)
                                        </option>
                                        <option value="6_months">Từ 6 tháng trở lên (&gt; 180
                                            ngày)
                                        </option>
                                        <option value="1_year">Từ 1 năm trở lên (&gt; 365 ngày)
                                        </option>
                                    </select>
                                </div>
                                <small class="text-muted d-block mt-2">Hệ thống dựa vào lịch sử
                                    đơn hàng
                                    để quét tự động.</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-top-success h-100 py-3 bg-white shadow-sm">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-success mb-3"><i
                                        class="fas fa-user-plus mr-1"></i> 3. Định nghĩa Khách
                                    hàng mới
                                </h6>
                                <div class="form-group">
                                    <label for="new_customer_range"
                                        class="small font-weight-bold text-muted">Thời gian đăng
                                        ký tài
                                        khoản:</label>
                                    <select class="form-control font-weight-bold text-success"
                                        id="new_customer_range" name="new_customer_range">
                                        <option value="all">-- Không lọc theo tiêu chí này --
                                        </option>
                                        <option value="7_days" selected>Mới đăng ký trong vòng 7
                                            ngày
                                            qua</option>
                                        <option value="15_days">Mới đăng ký trong vòng 15 ngày
                                            qua
                                        </option>
                                        <option value="30_days">Mới đăng ký trong vòng 30 ngày
                                            qua
                                        </option>
                                    </select>
                                </div>
                                <small class="text-muted d-block mt-2">Phù hợp kích thích khách
                                    vừa tạo
                                    tài khoản thực hiện đơn đầu tiên.</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-top-primary h-100 py-3 bg-white shadow-sm">
                            <div class="card-body">
                                <h6 class="font-weight-bold text-primary mb-3"><i
                                        class="fas fa-paper-plane mr-1"></i> 4. Gửi đích danh Email</h6>
                                <div class="form-group">
                                    <label for="target_email"
                                        class="small font-weight-bold text-muted">Địa chỉ Gmail khách hàng:</label>
                                    <input type="email" class="form-control font-weight-bold text-primary"
                                        id="target_email" name="target_email"
                                        placeholder="customer@gmail.com">
                                </div>
                                <small class="text-muted d-block mt-2">Điền ô này để gửi cho duy nhất khách hàng này (bỏ qua bộ lọc hạng/thời gian).</small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary"><i
                        class="fas fa-cog mr-2"></i>Bước 2: Cấu
                    hình quy tắc sinh mã tự động (Generator Settings)</h6>
            </div>
            <div class="card-body text-dark">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="code_prefix" class="font-weight-bold">Tiền tố mã
                            (Prefix)</label>
                        <input type="text" class="form-control font-weight-bold text-uppercase"
                            id="code_prefix" name="code_prefix"
                            placeholder="Ví dụ: VIP7D, BACK6M">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="code_length" class="font-weight-bold">Độ dài chuỗi ngẫu
                            nhiên</label>
                        <select class="form-control" id="code_length" name="code_length">
                            <option value="6" selected>6 ký tự (Ví dụ: AX89E1)</option>
                            <option value="8">8 ký tự (Ví dụ: K4M2P7Q9)</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_type" class="font-weight-bold">Loại giảm
                            giá</label>
                        <select class="form-control" id="discount_type" name="discount_type">
                            <option value="percentage">Giảm theo phần trăm (%)</option>
                            <option value="fixed" selected>Giảm tiền cố định (đ)</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_value" class="font-weight-bold">Mức giảm giá cụ thể
                            <span class="text-danger">*</span></label>
                        <input type="number" class="form-control font-weight-bold text-danger"
                            id="discount_value" name="discount_value" value="20000" min="1"
                            required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="expiry_days" class="font-weight-bold">Số ngày hết
                            hạn</label>
                        <input type="number" class="form-control" id="expiry_days"
                            name="expiry_days" value="5" min="1">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="min_order" class="font-weight-bold">Đơn hàng tối thiểu áp
                            dụng</label>
                        <input type="number" class="form-control" id="min_order"
                            name="min_order" value="0">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="send_channel" class="font-weight-bold">Kênh thông
                            báo</label>
                        <select class="form-control" id="send_channel" name="send_channel">
                            <option value="email">Hệ thống gửi Email</option>
                            <option value="sms">Gửi SMS Brandname</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary"><i
                        class="fas fa-envelope mr-2"></i>Bước
                    3: Nội dung tin nhắn gửi đi</h6>
            </div>
            <div class="card-body text-dark">
                <div class="form-group">
                    <label for="email_subject" class="font-weight-bold">Tiêu đề tin nhắn /
                        Email</label>
                    <input type="text" class="form-control" id="email_subject"
                        name="email_subject"
                        value="Món quà bất ngờ dành tặng riêng bạn từ FOODDAILY!" required>
                </div>
                <div class="form-group">
                    <label for="message_body" class="font-weight-bold">Nội dung chi tiết</label>
                    <textarea class="form-control" id="message_body" name="message_body"
                        rows="4"
                        required>Chào bạn thân mến, FOODDAILY gửi tặng riêng bạn mã ưu đãi giảm giá độc quyền tự sinh: [MÃ_TỰ_SINH]. Mã áp dụng cho mọi đơn hàng trong vòng [SỐ_NGÀY] ngày tới. Đừng bỏ lỡ nhé!</textarea>
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small"><i class="fas fa-database mr-1"></i>Hệ thống
                        tự động
                        loại trùng nếu khách hàng thuộc nhiều nhóm lọc chéo cùng lúc.</span>
                    <div>
                        <button type="button" class="btn btn-secondary shadow-sm mr-1">Quét số
                            lượng
                            thực tế</button>
                        <button type="submit" class="btn btn-primary shadow-sm px-4">Bắt đầu tạo
                            mã &
                            Gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
