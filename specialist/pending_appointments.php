<?php

session_start();

if (!$_SESSION || $_SESSION['user_type'] !== 'specialist') {
  header("Location: ../index.php");
}

require "../components/database.php";

$success_message = "";
if ($_POST) {
  $statement = $pdo->prepare(
    "UPDATE appointment 
    SET appointment_status=:status 
    WHERE appointment_id=:appointment_id"
  );
  $statement->bindValue(":status", "seen");
  $statement->bindValue(":appointment_id", $_POST['appointment_id']);
  $statement->execute();

  $success_message = "Appointment updated successfully!";
}

$statement = $pdo->prepare(
  "SELECT * FROM appointment 
  JOIN ailment 
  ON appointment_ailment_id=ailment_id 
  JOIN patient 
  ON ailment_patient_id=patient_id 
  WHERE ailment_specialist_id=:specialist_id 
  AND appointment_status IS NULL"
);
$statement->bindValue(":specialist_id", $_SESSION['user_id']);
$statement->execute();
$pending_appointments = $statement->fetchAll(PDO::FETCH_ASSOC);

include "../components/header.php";
include "../components/navigation.php";

?>

<main class="my-3 flex-grow-1">
  <div class="container-sm">
    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-3 mb-2">
      <h1>Pending Appointments</h1>
      <a href="specialist.php" class="btn btn-secondary">Back</a>
    </div>
    <?php if ($success_message) : ?>
      <div class="alert alert-success">
        <?php echo $success_message ?>
      </div>
    <?php endif; ?>
    <table class="table table-sm table-striped table-bordered text-center">
      <thead class="bg-primary">
        <tr class="text-light">
          <th>#</th>
          <th>Patient Name</th>
          <th>Patient Mobile</th>
          <th>Patient Email</th>
          <th>Appointment Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pending_appointments as $i => $pending_appointment) : ?>
          <tr>
            <td><?php echo $i + 1 ?></td>
            <td>
              <?php echo $pending_appointment['patient_name'] ?>
            </td>
            <td>
              <?php echo ucfirst($pending_appointment['patient_mobile']) ?>
            </td>
            <td>
              <?php echo ucfirst($pending_appointment['patient_email']) ?>
            </td>
            <td>
              <?php echo date_format(date_create($pending_appointment['appointment_date']), 'd-m-Y') ?>
            </td>
            <td>
              <a href="patient_details.php?id=<?php echo $pending_appointment['ailment_id'] ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-eye"></i> View Details
              </a>
              <form method="POST" class="d-inline">
                <input type="hidden" name="appointment_id" value="<?php echo $pending_appointment['appointment_id'] ?>">
                <button type="submit" class="btn btn-outline-success btn-sm"><i class="fas fa-check"></i> Mark Seen</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<script src="../js/alerts.js"></script>

<?php include "../components/footer.php" ?>