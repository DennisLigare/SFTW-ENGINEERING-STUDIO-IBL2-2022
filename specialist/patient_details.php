<?php

session_start();

if (!$_SESSION || $_SESSION['user_type'] !== 'specialist') {
  header("Location: ../index.php");
}

if (!isset($_GET['id'])) {
  header("Location: ../index.php");
} else {
  $ailment_id = $_GET['id'];
}

require "../components/database.php";

if ($_POST) {

  $statement = $pdo->prepare(
    "UPDATE ailment 
    SET ailment_specialist_id=:specialist_id, ailment_status=:status 
    WHERE ailment_id=:ailment_id"
  );
  $statement->bindValue(":specialist_id", $_SESSION['user_id']);
  $statement->bindValue(":status", 'accepted');
  $statement->bindValue(":ailment_id", $_POST['ailment_id']);
  $statement->execute();

  $statement = $pdo->prepare(
    "SELECT * FROM ailment 
    JOIN appointment 
    ON ailment_id=appointment_ailment_id 
    WHERE ailment_id=:ailment_id"
  );
  $statement->bindValue(":ailment_id", $_POST['ailment_id']);
  $statement->execute();
  $appointment = $statement->fetch(PDO::FETCH_ASSOC);

  if (!$appointment) {
    $statement = $pdo->prepare(
      "INSERT INTO appointment 
      (appointment_ailment_id, appointment_date) 
      VALUES (:ailment_id, :appointment_date)"
    );
    $statement->bindValue(":ailment_id", $_POST['ailment_id']);
    $statement->bindValue(":appointment_date", $_POST['appointment_date']);
  } else {
    $statement = $pdo->prepare(
      "UPDATE appointment 
      SET appointment_date=:appointment_date 
      WHERE appointment_id=:appointment_id"
    );
    $statement->bindValue(":appointment_date", $_POST['appointment_date']);
    $statement->bindValue(":appointment_id", $appointment['appointment_id']);
  }
  $statement->execute();

  header("Location: pending_appointments.php");
}

$statement = $pdo->prepare(
  "SELECT * FROM ailment 
  JOIN patient 
  ON ailment_patient_id=patient_id 
  WHERE ailment_id=:id"
);
$statement->bindValue(":id", $ailment_id);
$statement->execute();
$patient = $statement->fetch(PDO::FETCH_ASSOC);

$status = $patient['ailment_status'] ? 'accepted' : "";

if ($status) {
  $statement = $pdo->prepare(
    "SELECT appointment_date, appointment_status FROM appointment 
    WHERE appointment_ailment_id=:ailment_id"
  );
  $statement->bindValue(":ailment_id", $patient['ailment_id']);
  $statement->execute();
  $appointment = $statement->fetch(PDO::FETCH_ASSOC);
}
$appointment_date = $appointment['appointment_date'] ?? "";
$appointment_status = $appointment['appointment_status'] ?? "";

$patient_age = date_diff(date_create(date('Y-m-d')), date_create($patient['patient_dob']))->format('%y');

include "../components/header.php";
include "../components/navigation.php";

?>

<main class="my-3 flex-grow-1">
  <div class="container-sm">
    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-3 mb-2">
      <h1>Patient Details</h1>
      <?php if ($status) : ?>
        <?php if ($appointment_status) : ?>
          <a href="appointment_history.php" class="btn btn-secondary">Back</a>
        <?php else : ?>
          <a href="pending_appointments.php" class="btn btn-secondary">Back</a>
        <?php endif; ?>
      <?php else : ?>
        <a href="available_patients.php" class="btn btn-secondary">Back</a>
      <?php endif; ?>
    </div>
    <div class="p-2 border rounded">
      <div class="row">
        <div class="col">
          <p class="fs-4">
            Name:
            <span class="fw-bold fs-5">
              <?php echo $patient['patient_name'] ?>
            </span>
          </p>
        </div>
        <div class="col">
          <p class="fs-4">
            Gender:
            <span class="fw-bold fs-5">
              <?php echo ucfirst($patient['patient_gender']) ?>
            </span>
          </p>
        </div>
        <div class="col">
          <p class="fs-4">
            Age:
            <span class="fw-bold fs-5">
              <?php echo $patient_age ?> Years
            </span>
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <p class="fs-4">
            Mobile No:
            <span class="fw-bold fs-5">
              <?php echo $patient['patient_mobile'] ?>
            </span>
          </p>
        </div>
        <div class="col">
          <p class="fs-4">
            Email:
            <span class="fw-bold fs-5">
              <?php echo $patient['patient_email'] ?>
            </span>
          </p>
        </div>
        <div class="col">
          <p class="fs-4">
            Location:
            <span class="fw-bold fs-5">
              <?php echo $patient['patient_location'] ?>
            </span>
          </p>
        </div>
      </div>
      <div class="mb-3 border-bottom">
        <p class="pb-2 fw-bold fs-5 border-bottom">Ailment Description</p>
        <p class="lead"><?php echo $patient['ailment_desc'] ?></p>
      </div>
      <?php if (!$appointment_status) : ?>
        <div>
          <form method="POST" class="w-25">
            <input type="hidden" name="ailment_id" value="<?php echo $ailment_id ?>">
            <div class="mb-3">
              <label for="appointment_date" class="form-label fw-bold fs-5">
                <?php if ($status) : ?>
                  Reschedule Appointment Date:
                <?php else : ?>
                  Appointment Date:
                <?php endif; ?>
              </label>
              <input type="date" name="appointment_date" id="appointment_date" min="<?php echo date('Y-m-d') ?>" value="<?php echo $status ? $appointment_date : '' ?>" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">
              <?php if ($status) : ?>
                Reschedule Appointment
              <?php else : ?>
                Accept Patient
              <?php endif; ?>
            </button>
          </form>
        </div>
      <?php else : ?>
        <div>
          <p class="fw-bold fs-5 border-bottom">Patient Seen On</p>
          <p class="lead"><?php echo date_format(date_create($appointment_date), 'D, d M Y') ?></p>
        </div>
      <?php endif; ?>
    </div>
    <?php if ($appointment_status) : ?>
      <div class="mt-3">
        <button type="button" class="btn btn-success" onclick="window.print()">
          <i class="fas fa-print"></i>
          Print
        </button>
      </div>
    <?php endif; ?>
  </div>
</main>


<?php include "../components/footer.php" ?>