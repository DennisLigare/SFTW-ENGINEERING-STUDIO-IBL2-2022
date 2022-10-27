<?php

session_start();

if (!$_SESSION || $_SESSION['user_type'] !== 'specialist') {
  header("Location: ../index.php");
}

require "../components/database.php";

$statement = $pdo->prepare(
  "SELECT specialist_specialization_id FROM specialist 
  WHERE specialist_id=:specialist_id"
);
$statement->bindValue(":specialist_id", $_SESSION['user_id']);
$statement->execute();
$specialization_id = $statement->fetch(PDO::FETCH_COLUMN);

$statement = $pdo->prepare(
  "SELECT * FROM ailment 
  WHERE ailment_specialization_id=:specialization_id 
  AND ailment_status IS NULL"
);
$statement->bindValue(":specialization_id", $specialization_id);
$statement->execute();
$available_patients = $statement->rowCount();

$statement = $pdo->prepare(
  "SELECT * FROM appointment 
  JOIN ailment 
  ON appointment_ailment_id=ailment_id 
  WHERE ailment_specialist_id=:specialist_id 
  AND appointment_status IS NULL"
);
$statement->bindValue(":specialist_id", $_SESSION['user_id']);
$statement->execute();
$pending_appointments = $statement->rowCount();

$statement = $pdo->prepare(
  "SELECT * FROM appointment 
  JOIN ailment 
  ON appointment_ailment_id=ailment_id 
  WHERE ailment_specialist_id=:specialist_id 
  AND NOT appointment_status IS NULL"
);
$statement->bindValue(":specialist_id", $_SESSION['user_id']);
$statement->execute();
$past_appointments = $statement->rowCount();

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
        <div class="col d-grid">
          <a href="edit_details.php" class="btn btn-info btn-lg btn-block p-3 fw-bold text-uppercase">
            Manage Details
          </a>
        </div>
        <div class="col d-grid">
          <a href="specialization.php" class="btn btn-warning btn-lg btn-block p-3 fw-bold text-uppercase">Manage Specialization
          </a>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col d-grid">
          <a href="available_patients.php" class="btn btn-primary btn-lg btn-block p-3 fw-bold text-uppercase d-flex justify-content-between">
            Available Patients
            <span class="badge bg-danger">
              <?php echo $available_patients ?>
            </span>
          </a>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col d-grid">
          <a href="pending_appointments.php" class="btn btn-warning btn-lg btn-block p-3 fw-bold text-uppercase d-flex justify-content-between">
            Pending Appointments
            <span class="badge bg-danger">
              <?php echo $pending_appointments ?>
            </span>
          </a>
        </div>
        <div class="col d-grid">
          <a href="appointment_history.php" class="btn btn-secondary btn-lg btn-block p-3 fw-bold text-uppercase d-flex justify-content-between">
            Appointment History
            <span class="badge bg-danger">
              <?php echo $past_appointments ?>
            </span>
          </a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include "../components/footer.php" ?>