<?php

session_start();

if (!$_SESSION || $_SESSION['user_type'] !== 'admin') {
  header("Location: ../index.php");
}

require "../components/database.php";

$error_message = "";
$success_message = "";

if ($_POST) {
  $statement = $pdo->prepare(
    "SELECT * FROM specialization WHERE specialization_name=:name"
  );
  $statement->bindValue(":name", $_POST['name']);
  $statement->execute();
  $specialization = $statement->fetch(PDO::FETCH_ASSOC);

  if (!$specialization) {
    $statement = $pdo->prepare(
      "INSERT INTO specialization 
      (specialization_name, specialization_desc) 
      VALUES (:name, :desc)"
    );
    $statement->bindValue(":name", $_POST['name']);
    $statement->bindValue(":desc", $_POST['description']);
    $statement->execute();

    $success_message = "Specialization added successfully!";
  } else {
    $error_message = "A specialization with the same name already exists!";
  }
}

if ($_POST && $error_message) {
  $name = $_POST['name'] ?? "";
  $description = $_POST['description'] ?? "";
} else {
  $name = "";
  $description = "";
}

include "../components/header.php";
include "../components/navigation.php";

?>

<main class="my-3 flex-grow-1">
  <div class="container-sm">
    <div>
      <a href="specializations.php" class="btn btn-secondary">Back</a>
    </div>
    <form method="POST" class="border w-50 mx-auto p-3 shadow-sm">
      <h1 class="text-center text-primary border-3 border-bottom border-primary pb-3">Add Specialization</h1>
      <?php if ($error_message) : ?>
        <div class="alert alert-danger">
          <?php echo $error_message ?>
        </div>
      <?php endif; ?>
      <?php if ($success_message) : ?>
        <div class="alert alert-success">
          <?php echo $success_message ?>
        </div>
      <?php endif; ?>
      <div class="mb-3">
        <label for="name" class="form-label fw-bold">Name:</label>
        <input type="text" name="name" id="name" class="form-control" value="<?php echo $name ?>" placeholder="Enter specialization name" required>
      </div>
      <div class="mb-3">
        <label for="description" class="form-label fw-bold">Description:</label>
        <textarea name="description" id="description" rows="5" class="form-control" placeholder="Enter specialization description"><?php echo $description ?></textarea>
      </div>
      <div class="mb-3">
        <button type="reset" class="btn btn-secondary">Reset</button>
        <button type="submit" class="btn btn-primary">Add</button>
      </div>
    </form>
  </div>
</main>

<script src="../js/alerts.js"></script>

<?php include "../components/footer.php" ?>