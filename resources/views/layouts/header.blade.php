<div class="topbar d-none d-md-flex">
    <h5 class="mb-0">@yield('module-title', 'Supply Chain')</h5>
    <div class="d-flex align-items-center">
        <span class="me-3">
            Hi, {{ Auth::user()?->name ?? 'Admin' }}
        </span>
        <i class="bi bi-person-circle profile-icon"></i>
    </div>
</div>
