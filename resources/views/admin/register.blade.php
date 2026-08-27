<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Đăng ký</title>

    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{ asset('logo.jpg') }}" rel="icon">
    <!-- Custom styles for this template-->
    <link href="{{ asset('admin/css/sb-admin-2.min.css') }}" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-flex align-items-center justify-content-center p-4">
                        <img src="{{ asset('logo.jpg') }}" alt="" class="img-fluid">
                    </div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">ĐĂNG KÝ TÀI KHOẢN</h1>
                            </div>
                             @if ($errors->any())
                                 <div class="alert alert-danger py-2 small">
                                     <ul class="mb-0">
                                         @foreach ($errors->all() as $error)
                                             <li>{{ $error }}</li>
                                         @endforeach
                                     </ul>
                                 </div>
                             @endif

                             <form class="user" action="{{ route('admin.dangky.post') }}" method="POST">
                                 @csrf
                                 <div class="form-group">
                                     <input type="text" class="form-control form-control-user" id="exampleFullName"
                                         name="fullname" value="{{ old('fullname') }}" placeholder="Họ và Tên" required>
                                 </div>
                                 <div class="form-group">
                                     <input type="email" class="form-control form-control-user" id="exampleInputEmail"
                                         name="email" value="{{ old('email') }}" placeholder="Địa chỉ Email" required>
                                 </div>
                                 <div class="form-group row">
                                     <div class="col-sm-6 mb-3 mb-sm-0">
                                         <input type="password" class="form-control form-control-user"
                                             id="exampleInputPassword" name="password" placeholder="Mật khẩu" required>
                                     </div>
                                     <div class="col-sm-6">
                                         <input type="password" class="form-control form-control-user"
                                             id="exampleRepeatPassword" name="password_confirmation" placeholder="Xác nhận mật khẩu" required>
                                     </div>
                                 </div>
                                 <button type="submit" class="btn btn-primary btn-user btn-block">
                                     Đăng ký
                                 </button>
                             </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="{{ route('quenmatkhau') }}">Quên mật khẩu?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="{{ route('dangnhap') }}">Đã có tài khoản? Đăng nhập!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('admin/js/sb-admin-2.min.js') }}"></script>

</body>

</html>
