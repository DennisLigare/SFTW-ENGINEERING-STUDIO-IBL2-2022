<?php

session_start();

if (!$_SESSION || $_SESSION['user_type'] !== 'specialist') {
  header("Location: ../index.php");
}

require "../components/database.php";

$success_message = "";

if ($_POST) {

  $statement = $pdo->prepare(
    "UPDATE specialist 
    SET specialist_specialization_id=:specialization_id 
    WHERE specialist_id=:specialist_id"
  );
  $statement->bindValue(":specialization_id", $_POST['specialization']);
  $statement->bindValue(":specialist_id", $_SESSION['user_id']);
  $statement->execute();

  $success_message = "Specialization updated successfully!";
}

$statement = $pdo->prepare(
  "SELECT * FROM specialist 
  JOIN specialization 
  ON specialist_specialization_id=specialization_id 
  WHERE specialist_id=:id"
);
$statement->bindValue(":id", $_SESSION['user_id']);
$statement->execute();
$specialist_specialization = $statement->fetch(PDO::FETCH_ASSOC);

$statement = $pdo->query(
  "SELECT * FROM specialization"
);
$specializations = $statement->fetchAll(PDO::FETCH_ASSOC);

include "../components/header.php";
include "../components/navigation.php";

?>

<main class="my-3 flex-grow-1">
  <div class="container-sm">
    <div>
      <a href="specialist.php" class="btn btn-secondary">Back</a>
    </div>
    <form method="POST" class="border w-50 mx-auto p-3 shadow-sm">
      <h1 class="h2 text-center text-primary border-3 border-bottom border-primary pb-3">
        Manage Specialization
      </h1>
      <?php if ($success_message) : ?>
        <div class="alert alert-success">
          <?php echo $success_message ?>
        </div>
      <?php endif; ?>
      <div class="mb-3">
        <label class="form-label fw-bold">
          Current Specialization:
        </label>
        <input type="text" class="form-control" value="<?php echo $specialist_specialization['specialization_name'] ?? "" ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">Description:</label>
        <textarea rows="6" class="form-control" disabled><?php echo $specialist_specialization['specialization_desc'] ?? "" ?></textarea>
      </div>
      <div class="mb-3">
        <label for="specialization" class="form-label fw-bold">
          Update to:
        </label>
        <select name="specialization" id="specialization" class="form-control" required>
          <option>Select specialization...</option>
          <?php foreach ($specializations as $specialization) : ?>
            <option value="<?php echo $specialization['specialization_id'] ?>">
              <?php echo $specialization['specialization_name'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-success btn-block btn-ld fw-bold text-uppercase">Update</button>
      </div>
    </form>
  </div>
</main>

<script src="../js/alerts.js"></script>

<?php include "../components/footer.php" ?>