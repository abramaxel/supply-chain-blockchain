<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Blockchain App')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


  @stack('styles')
  <style>
    body { min-height: 100vh; overflow-x: hidden; background-color: #f4f6f9; }
    .sidebar-left { position: fixed; top: 20px; left: 20px; height: calc(100vh - 40px); width: 70px;
      background-color: #0b132b; color: white; transition: width 0.3s ease; z-index: 1030;
      border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.2); padding-top: 1rem;
      overflow: hidden; display: flex; flex-direction: column;
    }
    .sidebar-left:hover { width: 270px; }
    .sidebar-left .nav-link, .sidebar-left .logout { display: flex; align-items: center; justify-content: center;
      padding: 0.5rem; white-space: nowrap; transition: justify-content 0.3s ease, padding-left 0.3s ease;
    }
    .sidebar-left:hover .nav-link, .sidebar-left:hover .logout { justify-content: flex-start; padding-left: 1rem; }
    .sidebar-left .nav-link span, .sidebar-left .logout span { display: none; }
    .sidebar-left:hover .nav-link span, .sidebar-left:hover .logout span { display: inline; margin-left: 0.5rem; }
    .sidebar-left .divider { border-top: 1px solid rgba(255,255,255,0.2); margin: 1rem 0 0.5rem; }

    .sidebar-right { position: fixed; top: calc(50% - 150px); right: 20px; height: 200px; width: 70px;
      background-color: #fff; border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.05);
      transition: width 0.3s ease; z-index: 1030; display: flex; flex-direction: column;
      justify-content: center; overflow: hidden; padding-top: 1rem;
    }
    .sidebar-right .nav-label { display: flex; align-items: center; justify-content: center; padding: 10px 0;
      cursor: pointer; transition: all 0.3s ease; }
    .sidebar-right:hover { width: 200px; }
    .sidebar-right:hover .nav-label { justify-content: flex-start; padding-left: 12px; }
    .sidebar-right .nav-label span { display: none; }
    .sidebar-right:hover .nav-label span { display: inline; margin-left: 8px; }

    .sidebar-popup { position: fixed; right: 100px; width: 480px; background-color: #fff;
      border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.05); animation: popupFadeIn 0.3s ease-out;
      z-index: 1029; transition: right 0.3s ease, height 0.3s ease; display: none; flex-direction: column; }
    .sidebar-right:hover ~ .sidebar-popup { right: 230px; }
    #popup-total, #popup-timeline { top: 80px; height: calc(100vh - 100px); }
    .sidebar-popup.show { display: flex; }
    @keyframes popupFadeIn { from { opacity: 0; transform: translateX(50px); } to   { opacity: 1; transform: translateX(0); } }
    .sidebar-popup .card { border: 0; height: 100%; display: flex; flex-direction: column; }
    .sidebar-popup .card-body { display: flex; flex-direction: column; justify-content: flex-start; padding: 1rem;
      overflow-y: auto; flex: 1; }
    .topbar { position: fixed; top: 20px; left: 100px; right: 20px; height: 50px; background-color: #f4f6f9;
      border-radius: 12px; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; z-index: 1025;
      transition: left 0.3s ease; }
    .sidebar-left:hover ~ .topbar { left: 300px; }
    .topbar .profile-icon { font-size: 2rem; color: #333; cursor: pointer; }
    .main-content { margin-left: 110px; margin-right: 270px; padding-top: 90px; padding-right: 2rem; transition: margin-left 0.3s ease; }
    .sidebar-left:hover ~ .main-content { margin-left: 300px; }
    @media (max-width: 768px) {
      .sidebar-left, .sidebar-right, .topbar, .sidebar-popup { display: none; }
      .main-content { margin: 0; padding: 1rem; }
    }
  </style>
</head>
<body>
  <!-- Left Sidebar -->
  <div class="sidebar-left d-none d-md-block">
    @include('layouts.sidebar')
  </div>
  <!-- Right Sidebar -->
  @include('layouts.sidebar-right')
  <!-- Popups (Total, Timeline, dsb) -->
  @yield('popups')
  <!-- Topbar -->
  <div class="topbar d-none d-md-flex">
    <h5 class="mb-0">@yield('module-title', 'Supply Chain')</h5>
    <div class="d-flex align-items-center">
      <span class="me-3">Hi, Admin</span>
      <i class="bi bi-person-circle profile-icon"></i>
    </div>
  </div>
  <!-- Main Content -->
  <div class="main-content pe-0">
    <div class="container-fluid ps-0">
      <div class="form-wrapper">
        @yield('content')
      </div>
    </div>
  </div>
  @stack('scripts')
</body>
</html>
