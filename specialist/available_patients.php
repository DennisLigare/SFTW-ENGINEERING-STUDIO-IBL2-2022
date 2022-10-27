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
  JOIN patient 
  ON ailment_patient_id=patient_id 
  WHERE ailment_specialization_id=:specialization_id 
  AND ailment_status IS NULL"
);
$statement->bindValue(":specialization_id", $specialization_id);
$statement->execute();
$patients = $statement->fetchAll(PDO::FETCH_ASSOC);

include "../components/header.php";
include "../components/navigation.php";

?>

<main class="my-3 flex-grow-1">
  <div class="container-sm">
    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-3 mb-2">
      <h1>Available Patients</h1>
      <a href="specialist.php" class="btn btn-secondary">Back</a>
    </div>
    <table class="table table-sm table-striped table-bordered text-center">
      <thead class="bg-primary">
        <tr class="text-light">
          <th>#</th>
          <th>Patient Name</th>
          <th>Patient Gender</th>
          <th>Patient Age</th>
          <th>Patient Location</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($patients as $i => $patient) : ?>
          <tr>
            <td><?php echo $i + 1 ?></td>
            <td>
              <?php echo $patient['patient_name'] ?>
            </td>
            <td>
              <?php echo ucfirst($patient['patient_gender']) ?>
            </td>
            <td>
              <?php
              $age = date_diff(date_create(date('Y-m-d')), date_create($patient['patient_dob']));
              echo $age->format('%y') . ' Years';
              ?>
            </td>
            <td>
              <?php echo $patient['patient_location'] ?>
            </td>
            <td>
              <a href="patient_details.php?id=<?php echo $patient['ailment_id'] ?>" class="btn btn-outline-primary btn-sm">
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