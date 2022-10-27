<?php

session_start();

require "components/database.php";

$username = "";
$message = "";
if ($_POST) {
  $statement = $pdo->prepare(
    "SELECT * FROM login WHERE login_username=:username"
  );
  $statement->bindValue(":username", $_POST['username']);
  $statement->execute();
  $login = $statement->fetch(PDO::FETCH_ASSOC);

  if ($login) {
    if (password_verify($_POST['password'], $login['login_password'])) {

      $id = $login['login_admin_id'] ?? $login['login_patient_id'] ?? $login['login_specialist_id'];
      if ($login['login_rank'] == 'admin') {
        $statement = $pdo->prepare(
          "SELECT * FROM admin WHERE admin_id=:id"
        );
      } elseif ($login['login_rank'] == 'patient') {
        $statement = $pdo->prepare(
          "SELECT * FROM patient WHERE patient_id=:id"
        );
      } elseif ($login['login_rank'] == 'specialist') {
        $statement = $pdo->prepare(
          "SELECT * FROM specialist WHERE specialist_id=:id"
        );
      }
      $statement->bindValue(":id", $id);
      $statement->execute();
      $user = $statement->fetch(PDO::FETCH_ASSOC);

      $_SESSION['user_id'] = $user['admin_id'] ?? $user['patient_id'] ?? $user['specialist_id'];
      $_SESSION['username'] = $user['admin_name'] ?? $user['patient_name'] ?? $user['specialist_name'];
      $_SESSION['user_type'] = $login['login_rank'];

      header("Location: index.php");
    } else {
      $message = "You have entered an incorrect password.";
      $username = $_POST['username'];
    }
  } else {
    $message = "User does not exist.";
    $username = $_POST['username'];
  }
}

include "components/header.php";
include "components/navigation.php";

?>

<main class="my-3 flex-grow-1">

  <div class="container">

    <form method="POST" class="border w-50 mx-auto p-3 shadow-sm">
      <h1 class="text-center text-primary border-3 border-bottom border-primary pb-3">Login</h1>
      <?php if ($message) : ?>
        <div class="alert alert-danger">
          <?php echo $message ?>
        </div>
      <?php endif; ?>
      <div class="mb-3">
        <label for="username" class="form-label fw-bold">Username:</label>
        <div class="input-group">
          <span class="input-group-text text-primary"><i class="fas fa-user"></i></span>
          <input type="text" name="username" id="username" class="form-control" value="<?php echo $username ?>" placeholder="Enter username" required>
        </div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label fw-bold">Password:</label>
        <div class="input-group">
          <span class="input-group-text text-primary"><i class="fas fa-key"></i></span>
          <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
        </div>
      </div>
      <div class="mb-3">
        <button type="reset" class="btn btn-secondary">Reset</button>
        <button type="submit" class="btn btn-primary">Login</button>
      </div>
      <p>Don't have an account? <a href="register.php" class="link-primary">Register Now</a></p>
    </form>

  </div>

</main>

<?php include "components/footer.php"; ?>