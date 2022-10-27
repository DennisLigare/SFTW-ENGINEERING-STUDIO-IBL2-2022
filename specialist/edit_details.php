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
    SET specialist_mobile=:mobile, specialist_email=:email, specialist_location=:location 
    WHERE specialist_id=:id"
  );
  $statement->bindValue(":mobile", $_POST['mobile']);
  $statement->bindValue(":email", $_POST['email']);
  $statement->bindValue(":location", $_POST['location']);
  $statement->bindValue(":id", $_SESSION['user_id']);
  $statement->execute();

  $success_message = "Detail's updated successfully!";
}

$statement = $pdo->prepare(
  "SELECT * FROM specialist 
  JOIN login 
  ON specialist_id=login_specialist_id 
  WHERE specialist_id=:id"
);
$statement->bindValue(":id", $_SESSION['user_id']);
$statement->execute();
$details = $statement->fetch(PDO::FETCH_ASSOC);

$mobile = $details['specialist_mobile'];
$email = $details['specialist_email'];
$location = $details['specialist_location'];

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
        User Details
      </h1>
      <?php if ($success_message) : ?>
        <div class="alert alert-success">
          <?php echo $success_message ?>
        </div>
      <?php endif; ?>
      <div class="mb-3">
        <label class="form-label fw-bold">Username:</label>
        <input type="text" value="<?php echo $details['login_username'] ?>" class="form-control" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">Full Name:</label>
        <input type="text" class="form-control" value="<?php echo $details['specialist_name'] ?>" disabled>
      </div>
      <div class="row mb-3">
        <div class="col">
          <label for="mobile" class="form-label fw-bold">Phone Number:</label>
          <input type="text" name="mobile" id="mobile" class="form-control" value="<?php echo $mobile ?>" maxlength="10" required>
        </div>
        <div class="col">
          <label for="email" class="form-label fw-bold">Email:</label>
          <input type="email" name="email" id="email" class="form-control" value="<?php echo $email ?>" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">Gender:</label>
        <input type="text" class="form-control" value="<?php echo ucfirst($details['specialist_gender']) ?>" disabled>
      </div>
      <div class="mb-3">
        <label for="location" class="form-label fw-bold">Location</label>
        <input type="text" name="location" id="location" class="form-control" value="<?php echo $location ?>" required>
      </div>
      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-success btn-block btn-ld fw-bold text-uppercase">Update</button>
      </div>
    </form>
  </div>
</main>

<script src="../js/alerts.js"></script>

<?php include "../components/footer.php" ?>