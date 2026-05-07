<h4 class="text-center mb-3">Đăng ký</h4>

<form method="POST" action="{{ route('register.post') }}">
    @csrf

    <input type="text" name="name" class="form-control mb-3" placeholder="Họ tên">

    <input type="email" name="email" class="form-control mb-3" placeholder="Email">

    <input type="password" name="password" class="form-control mb-3" placeholder="Mật khẩu">

    <input type="password" name="password_confirmation" class="form-control mb-3" placeholder="Nhập lại mật khẩu">

    <button type="submit" class="btn btn-success w-100 mb-3">
        Đăng ký
    </button>

    <div class="text-center">
        <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">
            Đã có tài khoản? Đăng nhập
        </a>
    </div>
</form>
