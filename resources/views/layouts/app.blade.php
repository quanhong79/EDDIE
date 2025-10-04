<!DOCTYPE html>
<html lang="{{ app()->getLocale() ?? 'vi' }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Shop')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Bootstrap & FontAwesome --}}
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    .navbar-custom{ background:#fff; border-bottom:1px solid #eee; box-shadow:0 2px 10px rgba(0,0,0,.04); }
    .navbar-custom .navbar-brand{ font-weight:700; font-size:1.25rem; color:#111 !important; letter-spacing:.2px; }
    .navbar-custom .nav-link{ color:#111 !important; font-weight:500; transition:all .15s ease; padding:.6rem .9rem; }
    .navbar-custom .nav-link:hover{ color:#000 !important; text-decoration:none; }
    .navbar-custom .nav-item.active > .nav-link,
    .navbar-custom .nav-link.active{ color:#000 !important; border-bottom:2px solid #111; }
    .navbar-custom .dropdown-menu{ border:1px solid #eee; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.08); overflow:hidden; }
    .dropdown-menu .dropdown-item.active,
    .dropdown-menu .dropdown-item:active{ background:#111; color:#fff; }
    .btn-auth{ border-radius:20px; padding:6px 16px; font-weight:600; border:1px solid #111; color:#111; background:#fff; }
    .btn-auth:hover{ background:#111; color:#fff; }
    .navbar-light .navbar-toggler{ border:0; }
    .navbar-light .navbar-toggler-icon{
      background-image:url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(17,17,17,1)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }
    .gap-2{ gap:.5rem; }

    /* Badge bump */
    #cart-count.bump { animation: cart-bump .35s ease-out; }
    @keyframes cart-bump {
      0% { transform: scale(1); }
      15% { transform: scale(1.25); }
      60% { transform: scale(0.95); }
      100% { transform: scale(1); }
    }
  </style>
  @stack('head')
</head>

<body>
@php
  $isLogged = auth()->check();
  $user     = $isLogged ? auth()->user() : null;
  $role     = $isLogged ? ($user->role ?? 'user') : null;
  $isAdmin  = $isLogged && (($role === 'admin') || (method_exists($user, 'isAdmin') && $user->isAdmin()));
  $cartCount = isset($cartCount) ? (int)$cartCount : 0; // lấy từ View Composer
@endphp

<nav class="navbar navbar-expand-lg navbar-light navbar-custom" role="navigation" aria-label="Main navigation">
  <div class="container">
    {{-- Logo --}}
    <a class="navbar-brand d-flex align-items-center" href="{{ route('welcome') }}">
      <i class="fas fa-store mr-2" aria-hidden="true"></i> <span>Eddie</span>
    </a>

    {{-- Toggler --}}
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      {{-- LEFT MENU --}}
      <ul class="navbar-nav mr-auto">
        <li class="nav-item {{ request()->routeIs('welcome') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('welcome') }}">Trang chủ</a>
        </li>

        {{-- QUẦN --}}
        <li class="nav-item dropdown {{ request()->is('c/quan*') ? 'active' : '' }}">
          <a class="nav-link dropdown-toggle" href="{{ route('category.show','quan') }}" id="navQuan" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Quần</a>
          <div class="dropdown-menu" aria-labelledby="navQuan">
            <a class="dropdown-item {{ request()->is('c/quan') ? 'active' : '' }}" href="{{ route('category.show','quan') }}">Tất cả</a>
            <a class="dropdown-item {{ request()->is('c/short*') ? 'active' : '' }}" href="{{ route('category.show','short') }}">Short</a>
            <a class="dropdown-item {{ request()->is('c/quan-the-thao*') ? 'active' : '' }}" href="{{ route('category.show','quan-the-thao') }}">Quần thể thao</a>
            <a class="dropdown-item {{ request()->is('c/quan-jean*') ? 'active' : '' }}" href="{{ route('category.show','quan-jean') }}">Quần jean</a>
            <a class="dropdown-item {{ request()->is('c/quan-kaki*') ? 'active' : '' }}" href="{{ route('category.show','quan-kaki') }}">Quần kaki</a>
            <a class="dropdown-item {{ request()->is('c/quan-au*') ? 'active' : '' }}" href="{{ route('category.show','quan-au') }}">Quần âu</a>
          </div>
        </li>

        {{-- ÁO --}}
        <li class="nav-item dropdown {{ request()->is('c/ao*') ? 'active' : '' }}">
          <a class="nav-link dropdown-toggle" href="{{ route('category.show','ao') }}" id="navAo" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Áo</a>
          <div class="dropdown-menu" aria-labelledby="navAo">
            <a class="dropdown-item {{ request()->is('c/ao') ? 'active' : '' }}" href="{{ route('category.show','ao') }}">Tất cả</a>
            <a class="dropdown-item {{ request()->is('c/ao-tshirt*') ? 'active' : '' }}" href="{{ route('category.show','ao-tshirt') }}">Áo T-shirt</a>
            <a class="dropdown-item {{ request()->is('c/ao-hoodies-sweater*') ? 'active' : '' }}" href="{{ route('category.show','ao-hoodies-sweater') }}">Áo Hoodies-Sweater</a>
            <a class="dropdown-item {{ request()->is('c/ao-jacket-gilets*') ? 'active' : '' }}" href="{{ route('category.show','ao-jacket-gilets') }}">Áo Jacket-Gilets</a>
            <a class="dropdown-item {{ request()->is('c/ao-so-mi*') ? 'active' : '' }}" href="{{ route('category.show','ao-so-mi') }}">Áo sơ mi</a>
          </div>
        </li>

        {{-- KHÁC --}}
        <li class="nav-item dropdown {{ request()->is('c/khac*') ? 'active' : '' }}">
          <a class="nav-link dropdown-toggle" href="{{ route('category.show','khac') }}" id="navKhac" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Khác</a>
          <div class="dropdown-menu" aria-labelledby="navKhac">
            <a class="dropdown-item {{ request()->is('c/khac') ? 'active' : '' }}" href="{{ route('category.show','khac') }}">Tất cả</a>
            <a class="dropdown-item {{ request()->is('c/giay*') ? 'active' : '' }}" href="{{ route('category.show','giay') }}">Giày</a>
            <a class="dropdown-item {{ request()->is('c/dep*') ? 'active' : '' }}" href="{{ route('category.show','dep') }}">Dép</a>
            <a class="dropdown-item {{ request()->is('c/tat*') ? 'active' : '' }}" href="{{ route('category.show','tat') }}">Tất</a>
          </div>
        </li>

        @if(app('router')->has('about'))
          <li class="nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('about') }}">Về chúng tôi</a>
          </li>
        @endif

        {{-- ADMIN EXTRA --}}
        @if($isAdmin)
          <li class="nav-item {{ request()->routeIs('admin.thongke.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.thongke.index') }}">Thống kê</a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.products.index') }}">Sản phẩm</a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.categories.index') }}">Danh mục</a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.reviews.index') }}">Review</a>
          </li>
        @endif
      </ul>

      {{-- RIGHT MENU --}}
      <ul class="navbar-nav ml-auto align-items-lg-center">
        {{-- Search (desktop) --}}
        <li class="nav-item d-none d-lg-block">
          <form action="{{ route('product.index') }}" method="GET" class="form-inline">
            <div class="input-group input-group-sm mr-2" style="min-width:240px;">
              <input type="text" name="q" class="form-control" placeholder="Tìm sản phẩm..." value="{{ request('q') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-dark" type="submit">
                  <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><span class="sr-only">Tìm</span>
                </button>
              </div>
            </div>
          </form>
        </li>

        {{-- Search (mobile) --}}
        <li class="nav-item d-lg-none w-100 px-2 mb-2">
          <form action="{{ route('product.index') }}" method="GET">
            <div class="input-group input-group-sm">
              <input type="text" name="q" class="form-control" placeholder="Tìm sản phẩm..." value="{{ request('q') }}">
              <div class="input-group-append">
                <button class="btn btn-outline-dark" type="submit">
                  <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><span class="sr-only">Tìm</span>
                </button>
              </div>
            </div>
          </form>
        </li>

        {{-- Cart --}}
        <li class="nav-item">
          <a class="nav-link position-relative" href="{{ route('cart.index') }}">
            <i class="fas fa-shopping-cart" aria-hidden="true"></i> <span>Giỏ hàng</span>
            <span id="cart-count"
                  class="badge badge-danger ml-1"
                  aria-live="polite"
                  @if(!$cartCount) style="display:none" @endif>
              {{ $cartCount }}
            </span>
          </a>
        </li>

        {{-- User --}}
        @auth
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-user-circle" aria-hidden="true"></i> {{ $user->name }}
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
              @if(app('router')->has('settings.index'))
                <a class="dropdown-item" href="{{ route('settings.index') }}">
                  <i class="fa-solid fa-gear mr-1" aria-hidden="true"></i> Cài đặt
                </a>
              @endif
              @if(app('router')->has('orders.index'))
                <a class="dropdown-item" href="{{ route('orders.index') }}">
                  <i class="fas fa-box mr-1" aria-hidden="true"></i> Đơn hàng của tôi
                </a>
              @endif
              @if(auth()->check() && ((auth()->user()->role ?? null) === 'admin' || (auth()->user()->is_admin ?? false)))
                <a class="dropdown-item" href="{{ route('orders.index') }}">
                  <i class="fas fa-clipboard-list mr-1" aria-hidden="true"></i> Quản lý đơn hàng (Admin)
                </a>
              @endif
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{ route('logout') }}"
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt mr-1" aria-hidden="true"></i> Đăng xuất
              </a>
            </div>
          </li>
        @else
          <li class="nav-item mr-2">
            <a class="btn btn-auth" href="{{ route('login') }}"><i class="fas fa-sign-in-alt mr-1" aria-hidden="true"></i> Đăng nhập</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-auth" href="{{ route('register') }}"><i class="fas fa-user-plus mr-1" aria-hidden="true"></i> Đăng ký</a>
          </li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

{{-- Hidden logout form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
  @csrf
</form>

{{-- CONTENT --}}
<div class="container mt-4">
  @yield('toolbar')
  @yield('content')

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
      {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
      {{ session('error') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  {{-- Benefits --}}
  <section class="footer-benefits py-4 border-top">
    <div class="container">
      <div class="row text-center g-3">
        <div class="col-6 col-md-3">
          <div class="fb-item">
            <i class="fa-solid fa-box-open fa-2x mb-2" aria-hidden="true"></i>
            <div class="fw-semibold">Miễn phí vận chuyển</div>
            <div class="text-muted small">Áp dụng cho mọi đơn từ 500k</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="fb-item">
            <i class="fa-solid fa-rotate-left fa-2x mb-2" aria-hidden="true"></i>
            <div class="fw-semibold">Đổi hàng dễ dàng</div>
            <div class="text-muted small">7 ngày đổi hàng</div>
          </div>
        </div>
        <div class="col-6 col-md-3 mt-3 mt-md-0">
          <div class="fb-item">
            <i class="fa-solid fa-headset fa-2x mb-2" aria-hidden="true"></i>
            <div class="fw-semibold">Hỗ trợ nhanh</div>
            <div class="text-muted small">HOTLINE 24/7: 0964942121</div>
          </div>
        </div>
        <div class="col-6 col-md-3 mt-3 mt-md-0">
          <div class="fb-item">
            <i class="fa-regular fa-credit-card fa-2x mb-2" aria-hidden="true"></i>
            <div class="fw-semibold">Thanh toán đa dạng</div>
            <div class="text-muted small">COD / Napas / Visa</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- FOOTER --}}
  <footer class="site-footer pt-4 pb-5" style="background:#fafafa;border-top:1px solid #eee;">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-3">
          <h6 class="text-danger fw-bold mb-3">Thời trang nam Eddie</h6>
          <p class="text-muted small mb-3">Hệ thống thời trang nam, hướng tới phong cách lịch lãm, trẻ trung.</p>
          <div class="d-flex gap-2 mb-3" aria-label="Social links">
            <a class="btn btn-light btn-sm border" href="#" aria-label="Facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
            <a class="btn btn-light btn-sm border" href="#" aria-label="TikTok"><i class="fab fa-tiktok" aria-hidden="true"></i></a>
            <a class="btn btn-light btn-sm border" href="#" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
            <a class="btn btn-light btn-sm border" href="#" aria-label="YouTube"><i class="fab fa-youtube" aria-hidden="true"></i></a>
          </div>
          <div class="small text-muted mb-2">Phương thức thanh toán</div>
          <div class="d-flex align-items-center flex-wrap gap-2">
            <i class="fa-brands fa-cc-visa fa-2x" aria-hidden="true"></i>
            <i class="fa-brands fa-cc-mastercard fa-2x" aria-hidden="true"></i>
            <i class="fa-brands fa-cc-apple-pay fa-2x" aria-hidden="true"></i>
            <i class="fa-brands fa-cc-paypal fa-2x" aria-hidden="true"></i>
          </div>
        </div>

        <div class="col-md-3">
          <h6 class="fw-bold mb-3">Thông tin liên hệ</h6>
          <ul class="list-unstyled small text-muted mb-3">
            <li><strong>Địa chỉ:</strong> Tầng 8, tòa nhà Ford, 313 Trường Chinh, Thanh Xuân, Hà Nội</li>
            <li><strong>Điện thoại:</strong> 0964942121</li>
            <li><strong>Email:</strong> cskh@Eddie.vn</li>
          </ul>
          <div class="small text-muted mb-2">Phương thức vận chuyển</div>
          <div class="d-flex align-items-center flex-wrap gap-2">
            <span class="badge bg-light text-dark border">Giao Hàng Nhanh</span>
            <span class="badge bg-light text-dark border">Ninja</span>
            <span class="badge bg-light text-dark border">J&amp;T</span>
          </div>
        </div>

        <div class="col-md-3">
          <h6 class="fw-bold mb-3">Nhóm liên kết</h6>
          <ul class="list-unstyled small mb-0">
            <li><a class="text-decoration-none text-muted" href="{{ url('/search') }}">Tìm kiếm</a></li>
            @if(app('router')->has('about'))
              <li><a class="text-decoration-none text-muted" href="{{ route('about') }}">Giới thiệu</a></li>
            @endif
            <li><a class="text-decoration-none text-muted" href="#">Chính sách đổi trả</a></li>
            <li><a class="text-decoration-none text-muted" href="#">Chính sách bảo mật</a></li>
            <li><a class="text-decoration-none text-muted" href="#">Tuyển dụng</a></li>
            <li><a class="text-decoration-none text-muted" href="#">Liên hệ</a></li>
          </ul>
        </div>

        <div class="col-md-3">
          <h6 class="text-danger fw-bold mb-3">Đăng ký nhận tin</h6>
          <p class="small text-muted">Nhận thông báo sản phẩm mới &amp; ưu đãi đặc biệt.</p>
          @if(app('router')->has('newsletter.subscribe'))
            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex mb-3">
              @csrf
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="fa-regular fa-envelope" aria-hidden="true"></i></span>
                <input type="email" name="email" class="form-control" placeholder="Nhập email của bạn" required>
                <button class="btn btn-danger">Đăng ký</button>
              </div>
            </form>
          @else
            <div class="input-group mb-3">
              <span class="input-group-text bg-white"><i class="fa-regular fa-envelope" aria-hidden="true"></i></span>
              <input type="email" class="form-control" placeholder="Nhập email của bạn">
              <button class="btn btn-danger" disabled>Đăng ký</button>
            </div>
          @endif
        </div>
      </div>

      <hr class="my-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted">
        <div>© {{ date('Y') }} Eddie. All rights reserved.</div>
        <div class="mt-2 mt-md-0">Hotline: 0964 942 121 · Email: cskh@Eddie.vn</div>
      </div>
    </div>
  </footer>
</div>

{{-- Modal chọn phương thức thanh toán --}}
<div class="modal fade" id="choosePayModal" tabindex="-1" role="dialog" aria-labelledby="choosePayModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="choosePayModalLabel">Chọn phương thức thanh toán</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Bạn muốn thanh toán như thế nào?</p>

        <div class="d-flex flex-column">
          {{-- COD --}}
          <form id="codForm" action="{{ route('checkout.cod') }}" method="POST" class="mb-2">
            @csrf
            <button class="btn btn-outline-dark btn-block" type="submit">
              Thanh toán khi nhận hàng (COD)
            </button>
          </form>

          {{-- VNPay (POST) --}}
          <form method="POST" action="{{ route('checkout.vnpay') }}" class="mb-2">
            @csrf
            <button type="submit" class="btn btn-primary btn-block">Thanh toán VNPay</button>
          </form>

          {{-- Online nội bộ (Bank Manual) --}}
          @if(app('router')->has('checkout.bank'))
            <form method="GET" action="{{ route('checkout.bank') }}">
              <button type="submit" class="btn btn-info btn-block">Thanh toán Online (Nội bộ)</button>
            </form>
          @endif
        </div>

        <small class="text-muted d-block mt-2">
          * COD: đơn sẽ tạo và <strong>chờ duyệt</strong>.<br>
          * VNPay: thanh toán xong, đơn vẫn <strong>chờ admin duyệt</strong>.<br>
          * Online nội bộ: nhập thông tin chuyển khoản, hệ thống ghi nhận <strong>đã thanh toán</strong> và <strong>chờ duyệt</strong>.
        </small>
      </div>
    </div>
  </div>
</div>

{{-- JS --}}
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" defer></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Badge helper
  window.setCartCount = function (n) {
    var badge = document.getElementById('cart-count');
    if (!badge) return;
    n = parseInt(n || 0, 10);
    if (n > 0) {
      badge.textContent = n;
      badge.style.display = '';
      badge.classList.remove('bump');
      void badge.offsetWidth;
      badge.classList.add('bump');
      setTimeout(function () { badge.classList.remove('bump'); }, 400);
    } else {
      badge.textContent = '0';
      badge.style.display = 'none';
    }
  };

  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

  // Delegated intercept cho mọi form /cart/add/{id}
  document.body.addEventListener('submit', function (e) {
    var form = e.target;
    if (!(form instanceof HTMLFormElement)) return;

    var action = (form.getAttribute('action') || '').replace(window.location.origin, '');
    if (!action.includes('/cart/add/')) return;       // chỉ bắt form thêm giỏ
    if (form.dataset.noajax === '1') return;          // cho phép bỏ qua AJAX

    e.preventDefault();

    var fd = new FormData(form);
    var buyNow = (fd.get('buy_now') === '1') || (form.dataset.redirect === 'cart') || (form.dataset.buyNow === '1');

    fetch(form.action, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      },
      body: fd
    })
    .then(async function (r) {
      var ct = r.headers.get('content-type') || '';
      var isJSON = ct.includes('application/json');
      var body = null;
      try { body = isJSON ? await r.json() : null; } catch (_) {}

      if (!r.ok) {
        if (r.status === 419) {
          alert('Phiên làm việc đã hết hạn (419). Vui lòng tải lại trang rồi thử lại.');
        } else if (body && body.message) {
          alert(body.message);
        } else {
          alert('Không thể thêm vào giỏ (HTTP ' + r.status + ').');
        }
        if (buyNow) window.location.href = "{{ route('cart.index') }}";
        return { ok: false };
      }

      return body || { ok: true, cart_count: undefined };
    })
    .then(function (data) {
      if (!data || data.ok !== true) return;

      if (typeof data.cart_count !== 'undefined') {
        window.setCartCount(data.cart_count);
      } else {
        // Fallback: nếu không trả count, tăng tạm +1 để thấy nhảy
        var badge = document.getElementById('cart-count');
        var cur = parseInt((badge && badge.textContent) || '0', 10);
        if (!isNaN(cur)) window.setCartCount(cur + 1);
      }

      if (buyNow) {
        window.location.href = "{{ route('cart.index') }}";
      }
    })
    .catch(function (err) {
      console.debug('add-to-cart failed:', err && err.message ? err.message : err);
      if (buyNow) window.location.href = "{{ route('cart.index') }}";
      else alert('Có lỗi mạng khi thêm vào giỏ.');
    });
  });
});
</script>

@stack('scripts')

{{-- Chat widget (tuỳ chọn). Dùng includeIf để tránh lỗi khi thiếu file --}}
@auth
  @include('partials.ai_chat')
@endauth
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>

</body>
</html>
