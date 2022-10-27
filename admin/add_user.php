<?php

session_start();

if (!$_SESSION || $_SESSION['user_type'] !== 'admin') {
  header("Location: ../index.php");
}

if (!isset($_GET['user'])) {
  header("Location: admin.php");
}
$user_type = $_GET['user'];

$error_message = "";
$success_message = "";

require "../components/database.php";
if ($_POST) {
  $statement = $pdo->prepare(
    "SELECT * FROM login WHERE login_username=:username"
  );
  $statement->bindValue(":username", $_POST['username']);
  $statement->execute();
  $login = $statement->fetch(PDO::FETCH_ASSOC);

  if (!$login) {

    if ($user_type == 'admin') {
      $statement = $pdo->prepare(
        "INSERT INTO admin 
        (admin_name, admin_mobile, admin_email, admin_gender) 
        VALUES (:full_name, :mobile, :email, :gender)"
      );
    } elseif ($user_type == 'patient') {
      $statement = $pdo->prepare(
        "INSERT INTO patient 
        (patient_name, patient_mobile, patient_email, patient_gender, patient_location, patient_dob) 
        VALUES (:full_name, :mobile, :email, :gender, :location, :dob)"
      );
      $statement->bindValue(":location", $_POST['location']);
      $statement->bindValue(":dob", $_POST['dob']);
    } elseif ($user_type == 'specialist') {
      $statement = $pdo->prepare(
        "INSERT INTO specialist 
        (specialist_name, specialist_mobile, specialist_email, specialist_gender, specialist_location) 
        VALUES (:full_name, :mobile, :email, :gender, :location)"
      );
      $statement->bindValue(":location", $_POST['location']);
    }
    $statement->bindValue(":full_name", $_POST['full_name']);
    $statement->bindValue(":mobile", $_POST['mobile']);
    $statement->bindValue(":email", $_POST['email']);
    $statement->bindValue(":gender", $_POST['gender']);
    $statement->execute();

    $user_id = $pdo->lastInsertId();

    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    if ($user_type == 'admin') {
      $statement = $pdo->prepare(
        "INSERT INTO login 
        (login_username, login_password, login_rank, login_admin_id) 
        VALUES (:username, :password, :rank, :user_id)"
      );
    } elseif ($user_type == 'patient') {
      $statement = $pdo->prepare(
        "INSERT INTO login 
        (login_username, login_password, login_rank, login_patient_id) 
        VALUES (:username, :password, :rank, :user_id)"
      );
    } elseif ($user_type == 'specialist') {
      $statement = $pdo->prepare(
        "INSERT INTO login 
        (login_username, login_password, login_rank, login_specialist_id) 
        VALUES (:username, :password, :rank, :user_id)"
      );
    }
    $statement->bindValue(":username", $_POST['username']);
    $statement->bindValue(":password", $password);
    $statement->bindValue(":rank", $user_type);
    $statement->bindValue(":user_id", $user_id);
    $statement->execute();

    $success_message = "User created successfully!";
  } else {
    $error_message = "A user with the same username exists.";
  }
}

if ($_POST && $error_message) {
  $full_name = $_POST['full_name'];
  $username = $_POST['username'];
  $mobile = $_POST['mobile'];
  $email = $_POST['email'];
  $gender = $_POST['gender'];
  $location = $_POST['location'] ?? '';
  $dob = $_POST['dob'] ?? '';
} else {
  $full_name = '';
  $username = '';
  $mobile = '';
  $email = '';
  $gender = '';
  $location = '';
  $dob = '';
}


include "../components/header.php";
include "../components/navigation.php";

?>

<main class="my-3 flex-grow-1">
  <div class="container-sm">
    <div>
      <a href="users.php?user=<?php echo $user_type ?>" class="btn btn-secondary">Back</a>
    </div>

    <form method="POST" class="border w-75 mx-auto p-3 shadow-sm">
      <h1 class="text-center text-primary border-3 border-bottom border-primary pb-3">
        <?php if ($user_type == 'admin') : ?>
          Add Admin
        <?php elseif ($user_type == 'specialist') : ?>
          Add Specialist
        <?php elseif ($user_type == 'patient') : ?>
          Add Patient
        <?php endif; ?>
      </h1>
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
      <div class="row mb-3">
        <div class="col">
          <label for="full_name" class="form-label fw-bold">Full Name:</label>
          <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo $full_name ?>" placeholder="Enter full name" required>
        </div>
        <div class="col">
          <label for="username" class="form-label fw-bold">Username:</label>
          <input type="text" name="username" id="username" class="form-control" value="<?php echo $username ?>" placeholder="Enter username" required>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col">
          <label for="mobile" class="form-label fw-bold">Phone Number:</label>
          <input type="text" name="mobile" id="mobile" class="form-control" value="<?php echo $mobile ?>" maxlength="10" placeholder="Enter phone number" required>
        </div>
        <div class="col">
          <label for="email" class="form-label fw-bold">Email:</label>
          <input type="email" name="email" id="email" class="form-control" value="<?php echo $email ?>" placeholder="Enter email" required>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-6">
          <label for="gender" class="form-label fw-bold">Gender:</label>
          <select name="gender" id="gender" class="form-select" required>
            <option value="">Select gender...</option>
            <option value="male" <?php echo $gender == 'male' ? 'selected' : '' ?>>Male</option>
            <option value="female" <?php echo $gender == 'female' ? 'selected' : '' ?>>Female</option>
          </select>
        </div>
        <?php if ($user_type !== 'admin') : ?>
          <div class="col-6">
            <label for="location" class="form-label fw-bold">Location:</label>
            <input type="text" name="location" id="location" class="form-control" value="<?php echo $location ?>" placeholder="Enter location">
          </div>
        <?php endif; ?>
      </div>
      <?php if ($user_type == 'patient') : ?>
        <div class="row mb-3">
          <div class="col-6">
            <label for="dob" class="form-label fw-bold">Date of Birth:</label>
            <input type="date" name="dob" id="dob" class="form-control" value="<?php echo $dob ?>" max="<?php echo date('Y-m-d') ?>">
          </div>
        </div>
      <?php endif; ?>
      <div class="row mb-3">
        <div class="col">
          <label for="password" class="form-label fw-bold">Password:</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
        </div>
        <div class="col">
          <label for="confirm_password" class="form-label fw-bold">Confirm Password:</label>
          <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm password" required>
        </div>
      </div>
      <div class="mb-3">
        <button type="reset" class="btn btn-secondary">Reset</button>
        <button type="submit" class="btn btn-primary">Add</button>
      </div>
    </form>

  </div>
</main>

<script src="../js/script.js"></script>
<script src="../js/alerts.js"></script>

<?php include "../components/footer.php" ?>