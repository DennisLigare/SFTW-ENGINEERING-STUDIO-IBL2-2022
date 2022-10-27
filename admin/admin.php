<?php

session_start();

if (!$_SESSION || $_SESSION['user_type'] !== 'admin') {
  header("Location: ../index.php");
}

require "../components/database.php";

$statement = $pdo->query("SELECT * FROM admin");
$admins = $statement->rowCount();

$statement = $pdo->query("SELECT * FROM patient");
$patients = $statement->rowCount();

$statement = $pdo->query("SELECT * FROM specialist");
$specialists = $statement->rowCount();

$statement = $pdo->query("SELECT * FROM specialization");
$specializations = $statement->rowCount();

include "../components/header.php";
include "../components/navigation.php";

?>

<main class="my-3 flex-grow-1">
  <div class="container-sm">
    <div class="mb-3 p-2 shadow-sm bg-success text-light">
      Welcome <strong><?php echo $_SESSION['username'] ?></strong>
    </div>
    <div class="pt-3 border-top border-3 border-primary">
      <div class="row mb-3">
        <div class="col-4">
          <div class="d-grid">
            <a href="users.php?user=admin" class="btn btn-info btn-block p-3 fw-bold text-uppercase d-flex justify-content-between">
              Manage Admins
              <span class="badge bg-danger"><?php echo $admins ?></span>
            </a>
          </div>
        </div>
        <div class="col-4">
          <div class="d-grid">
            <a href="users.php?user=specialist" class="btn btn-primary btn-block p-3 fw-bold text-uppercase d-flex justify-content-between">
              Manage Specialists
              <span class="badge bg-danger"><?php echo $specialists ?></span>
            </a>
          </div>
        </div>
        <div class="col-4">
          <div class="d-grid">
            <a href="users.php?user=patient" class="btn btn-warning btn-block p-3 fw-bold text-uppercase d-flex justify-content-between">
              Manage Patients
              <span class="badge bg-danger"><?php echo $patients ?></span>
            </a>
          </div>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col">
          <div class="d-grid">
            <a href="specializations.php" class="btn btn-secondary btn-block p-3 fw-bold text-uppercase d-flex justify-content-between">
              Manage Specializations
              <span class="badge bg-danger"><?php echo $specializations ?></span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include "../components/footer.php" ?>