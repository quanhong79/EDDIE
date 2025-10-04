<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng ký</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 + Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <style>
    body{
      min-height:100vh;
      background: radial-gradient(1200px 600px at 10% 0%, #f8fafc 40%, #eef2f7 100%);
    }
    .auth-card{
      max-width: 480px;
      border: 0;
      border-radius: 1rem;
      box-shadow: 0 20px 60px rgba(0,0,0,.08);
    }
    .brand-badge{
      display:inline-flex;align-items:center;gap:.5rem;
      font-weight:700;font-size:1.1rem;color:#111;
    }
    .form-control{ border-radius:.75rem; }
    .btn-auth{ border-radius:.75rem; font-weight:600; }
  </style>
</head>
<body>
  <div class="container d-flex align-items-center justify-content-center py-5">
    <div class="card auth-card w-100">
      <div class="card-body p-4 p-md-5">

        <div class="mb-4 text-center">
          <span class="brand-badge">
            <i class="fa-solid fa-store"></i> Eddie
          </span>
          <h1 class="h4 mt-3 mb-1">Tạo tài khoản</h1>
          <p class="text-muted mb-0">Đăng ký để bắt đầu mua sắm</p>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
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

        <form action="{{ url('register') }}" method="POST" novalidate>
          @csrf

          {{-- Name --}}
          <div class="mb-3">
            <label for="name" class="form-label">Họ & Tên</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-regular fa-user"></i></span>
              <input type="text"
                     name="name" id="name"
                     class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name') }}"
                     placeholder="Nguyễn Văn A"
                     required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- Email --}}
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
              <input type="email"
                     name="email" id="email"
                     class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email') }}"
                     placeholder="you@example.com"
                     required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          {{-- Password --}}
          <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
              <input type="password"
                     name="password" id="password"
                     class="form-control @error('password') is-invalid @enderror"
                     placeholder="••••••••"
                     required>
              <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                <i class="fa-regular fa-eye"></i>
              </button>
              @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
            <small class="text-muted">Tối thiểu 8 ký tự, nên có chữ hoa, số và ký tự đặc biệt.</small>
          </div>

          {{-- Confirm --}}
          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-shield-halved"></i></span>
              <input type="password"
                     name="password_confirmation" id="password_confirmation"
                     class="form-control"
                     placeholder="Nhập lại mật khẩu"
                     required>
              <button class="btn btn-outline-secondary" type="button" id="togglePwd2">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn btn-dark w-100 btn-auth">Đăng ký</button>
        </form>

        <p class="text-center mt-4 mb-0">
          Đã có tài khoản?
          <a href="{{ url('login') }}" class="text-decoration-none">Đăng nhập</a>
        </p>
      </div>
    </div>
  </div>

  <script>
    // toggle show/hide password for both fields
    function toggle(btnId, inputId){
      const btn = document.getElementById(btnId);
      const input = document.getElementById(inputId);
      btn?.addEventListener('click', () => {
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        const icon = btn.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
      });
    }
    toggle('togglePwd', 'password');
    toggle('togglePwd2', 'password_confirmation');
  </script>
</body>
</html>
