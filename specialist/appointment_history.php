<?php

session_start();

if (!$_SESSION || $_SESSION['user_type'] !== 'specialist') {
  header("Location: ../index.php");
}

require "../components/database.php";

$statement = $pdo->prepare(
  "SELECT * FROM appointment 
  JOIN ailment 
  ON appointment_ailment_id=ailment_id 
  JOIN patient 
  ON ailment_patient_id=patient_id 
  WHERE ailment_specialist_id=:specialist_id 
  AND NOT appointment_status IS NULL"
);
$statement->bindValue(":specialist_id", $_SESSION['user_id']);
$statement->execute();
$past_appointments = $statement->fetchAll(PDO::FETCH_ASSOC);

include "../components/header.php";
include "../components/navigation.php";

?>

<main class="my-3 flex-grow-1">
  <div class="container-sm">
    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-3 mb-2">
      <h1>Past Appointments</h1>
      <a href="specialist.php" class="btn btn-secondary">Back</a>
    </div>
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
        <?php foreach ($past_appointments as $i => $past_appointment) : ?>
          <tr>
            <td><?php echo $i + 1 ?></td>
            <td>
              <?php echo $past_appointment['patient_name'] ?>
            </td>
            <td>
              <?php echo ucfirst($past_appointment['patient_mobile']) ?>
            </td>
            <td>
              <?php echo ucfirst($past_appointment['patient_email']) ?>
            </td>
            <td>
              <?php echo date_format(date_create($past_appointment['appointment_date']), 'd-m-Y') ?>
            </td>
            <td>
              <a href="patient_details.php?id=<?php echo $past_appointment['ailment_id'] ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-eye"></i> View Details
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>


<?php include "../components/footer.php" ?>