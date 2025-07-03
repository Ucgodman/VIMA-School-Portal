<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
  <div class="container-fluid">
    <!-- Branding / Page Title -->
    <h3 class="fw-bold mb-0 d-lg-none"><?= $pageTitle; ?></h3>

    <!-- Toggle button for mobile view -->
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarContent"
      aria-controls="navbarContent"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar content -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <!-- Page Title (visible on larger screens) -->
      <div class="d-none d-lg-block me-auto">
        <h3 class="fw-bold mb-0"><?= $pageTitle; ?></h3>
      </div>

      <!-- User profile dropdown -->
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle d-flex align-items-center"
            href="#"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            <img
              src="../assets/images/profile.jpg"
              alt="Profile"
              class="rounded-circle me-2"
              style="width: 40px; height: 40px;"
            />
            <span>
              <span class="text-muted">Hi,</span>
              <strong><?= htmlspecialchars($_SESSION['user']['firstname'] ?? 'User'); ?></strong>
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li class="text-center p-3">
              <img
                src="../assets/images/profile.jpg"
                alt="Profile"
                class="rounded mb-2"
                style="width: 60px; height: 60px;"
              />
              <h5 class="mb-2"><?= htmlspecialchars($_SESSION['user']['firstname'] ?? 'User'); ?></h5>
              <a href="/vimaportal/logout.php" class="btn btn-sm btn-outline-secondary">Logout</a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
