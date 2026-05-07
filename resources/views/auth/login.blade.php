<div class="text-center mb-3">
    <h5 class="modal-title">Đăng nhập</h5>
</div>

<form method="POST" action="{{ route('login.post') }}">
    @csrf

    <!-- Email input -->
    <div class="form-floating mb-4">
        <input type="email" name="email" id="form2Example1" class="form-control" placeholder="Email address" required>
        <label for="form2Example1">Email address</label>

        @error('email')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <!-- Password input -->
    <div class="form-floating mb-4">
        <input type="password" name="password" id="form2Example2" class="form-control" placeholder="Password" required>
        <label for="form2Example2">Password</label>

        @error('password')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="row mb-4">
        <div class="col d-flex justify-content-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="form2Example31">
                <label class="form-check-label" for="form2Example31">Remember me</label>
            </div>
        </div>

        <div class="col text-end">
            <a href="#">Forgot password?</a>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-4">
        Sign in
    </button>

    <div class="text-center">
        <p>
            Not a member?
            <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#registerModal">
                Register
            </a>
        </p>

        <p>or sign in with:</p>

        <button type="button" class="btn btn-link btn-floating mx-1">
            <i class="fa-brands fa-facebook-f"></i>
        </button>

        <button type="button" class="btn btn-link btn-floating mx-1">
            <i class="fa-brands fa-google"></i>
        </button>

        <button type="button" class="btn btn-link btn-floating mx-1">
            <i class="fa-brands fa-twitter"></i>
        </button>

        <button type="button" class="btn btn-link btn-floating mx-1">
            <i class="fa-brands fa-github"></i>
        </button>
    </div>
</form>
