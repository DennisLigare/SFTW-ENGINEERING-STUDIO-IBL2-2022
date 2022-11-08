<?php

session_start();

include "components/header.php";
include "components/navigation.php";

?>

<main class="my-3 flex-grow-1">

  <div class="container">

    <div class="d-flex justify-content-center align-items-center">
      <div class="w-50">
        <h1>Get in touch with a specialist today!</h1>
        <p class="lead">
          With our system, you get linked to a specialist that specializes in the exact area of the ailment you are suffering from.
        </p>
        <?php if (!$_SESSION) : ?>
          <a href="register.php" class="btn btn-primary btn-lg">Get Started!</a>
        <?php elseif ($_SESSION['user_type'] == 'admin') : ?>
          <a href="admin/admin.php" class="btn btn-success btn-lg">Dashboard</a>
        <?php elseif ($_SESSION['user_type'] == 'specialist') : ?>
          <a href="specialist/specialist.php" class="btn btn-success btn-lg">Dashboard</a>
        <?php elseif ($_SESSION['user_type'] == 'patient') : ?>
          <a href="patient/patient.php" class="btn btn-success btn-lg">Dashboard</a>
        <?php endif; ?>
      </div>
      <div class="col w-50">
        <?php if (!$_SESSION) : ?>
          <img src="images/doctor.jpg" alt="Doctor Image" class="img-fluid">
        <?php else : ?>
          <img src="images/doctor-patient.jpg" alt="Doctor Patient Image" class="img-fluid">
        <?php endif; ?>
      </div>
    </div>

  </div>
</main>

<?php include "components/footer.php"; ?>