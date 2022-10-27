<header class="py-1 bg-primary">

  <div class="container-sm">

    <nav class="navbar navbar-expand-sm navbar-dark">
      <a href="../index.php" class="navbar-brand fs-3 fw-bold">
        Movisions Specialist System
      </a>
      <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav-collapsible">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="nav-collapsible">
        <div class="navbar-nav ms-auto">
          <?php if (!$_SESSION) : ?>
            <a href="./login.php" class="nav-link fs-5">Login</a>
            <a href="./register.php" class="nav-link fs-5">Register</a>
          <?php else : ?>
            <?php if ($_SESSION['user_type'] == 'admin') : ?>
              <a href="../admin/admin.php" class="nav-link fs-5">
                Admin Dashboard
              </a>
            <?php elseif ($_SESSION['user_type'] == 'specialist') : ?>
              <a href="../specialist/specialist.php" class="nav-link fs-5">
                Specialist Dashboard
              </a>
            <?php elseif ($_SESSION['user_type'] == 'patient') : ?>
              <a href="../patient/patient.php" class="nav-link fs-5">
                Patient Dashboard
              </a>
            <?php endif; ?>
            <a href="../logout.php" class="nav-link fs-5">Logout</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

  </div>

</header>