<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 + Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <style>
    body{min-height:100vh;background: radial-gradient(1200px 600px at 10% 0%, #f8fafc 40%, #eef2f7 100%);}
    .auth-card{max-width: 420px;border: 0;border-radius: 1rem;box-shadow: 0 20px 60px rgba(0,0,0,.08);}
    .brand-badge{display:inline-flex;align-items:center;gap:.5rem;font-weight:700;font-size:1.1rem;color:#111;}
    .form-control{border-radius:.75rem;}
    .btn-auth{border-radius:.75rem;font-weight:600;}
  </style>
</head>
<body>
  <div class="container d-flex align-items-center justify-content-center py-5">
    <div class="card auth-card w-100">
      <div class="card-body p-4 p-md-5">

        <div class="mb-4 text-center">
          <span class="brand-badge"><i class="fa-solid fa-store"></i> Eddie</span>
          <h1 class="h4 mt-3 mb-1">Chào mừng trở lại</h1>
          <p class="text-muted mb-0">Đăng nhập để tiếp tục mua sắm</p>
        </div>

        @if (session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
          <div class="alert alert-danger d-none d-md-block">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ url('login') }}" method="POST" novalidate>
          @csrf

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
              <input type="email" name="email" id="email"
                     class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
              <input type="password" name="password" id="password"
                     class="form-control @error('password') is-invalid @enderror"
                     placeholder="••••••••" required>
              <button class="btn btn-outline-secondary" type="button" id="togglePwd" tabindex="-1" aria-label="Hiện/ẩn mật khẩu">
                <i class="fa-regular fa-eye"></i>
              </button>
              @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
              <label class="form-check-label" for="remember">Ghi nhớ tôi</label>
            </div>

            @if (app('router')->has('password.request'))
              <a class="small text-decoration-none" href="{{ route('password.request') }}">Quên mật khẩu?</a>
            @endif
          </div>

          <button type="submit" class="btn btn-dark w-100 btn-auth">Đăng nhập</button>
        </form>

        <p class="text-center mt-4 mb-0">
          Chưa có tài khoản? <a href="{{ url('register') }}" class="text-decoration-none">Đăng ký ngay</a>
        </p>
      </div>
    </div>
  </div>

  <script>
    const btn = document.getElementById('togglePwd');
    const pwd = document.getElementById('password');
    btn?.addEventListener('click', () => {
      const isText = pwd.type === 'text';
      pwd.type = isText ? 'password' : 'text';
      btn.querySelector('i')?.classList.toggle('fa-eye');
      btn.querySelector('i')?.classList.toggle('fa-eye-slash');
    });
  </script>
</body>
</html>
