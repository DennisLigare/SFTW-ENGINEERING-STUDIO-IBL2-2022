<?php

session_start();

if (!$_SESSION || $_SESSION['user_type'] !== 'admin') {
  header("Location: ../index.php");
}

if (!isset($_GET['user'])) {
  header("Location: admin.php");
}
$user_type = $_GET['user'];

require "../components/database.php";

$success_message = "";
if ($_POST) {
  if ($user_type == 'admin') {
    $statement = $pdo->prepare(
      "DELETE FROM admin 
      WHERE admin_id=:user_id"
    );
  } elseif ($user_type == 'specialist') {
    $statement = $pdo->prepare(
      "DELETE FROM specialist
       WHERE specialist_id=:user_id"
    );
  } elseif ($user_type == 'patient') {
    $statement = $pdo->prepare(
      "DELETE FROM patient 
      WHERE patient_id=:user_id"
    );
  }
  $statement->bindValue(":user_id", $_POST['object_id']);
  $statement->execute();

  $success_message = "User deleted successfully!";
}

if ($user_type == 'admin') {
  $statement = $pdo->query("SELECT * FROM admin");
} elseif ($user_type == 'specialist') {
  $statement = $pdo->query("SELECT * FROM specialist");
} elseif ($user_type == 'patient') {
  $statement = $pdo->query("SELECT * FROM patient");
}
$users = $statement->fetchAll(PDO::FETCH_ASSOC);

include "../components/header.php";
include "../components/navigation.php";

?>

<main class="my-3 flex-grow-1">
  <div class="container-sm">
    <div>
      <a href="admin.php" class="btn btn-secondary">Back</a>
    </div>
    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-3 mb-2">
      <?php if ($user_type == 'admin') : ?>
        <h1>Manage Admins</h1>
        <a href="add_user.php?user=admin" class="btn btn-success">
          Create New Admin
        </a>
      <?php elseif ($user_type == 'specialist') : ?>
        <h1>Manage Specialists</h1>
        <a href="add_user.php?user=specialist" class="btn btn-success">
          Create New Specialist
        </a>
      <?php elseif ($user_type == 'patient') : ?>
        <h1>Manage Patients</h1>
        <a href="add_user.php?user=patient" class="btn btn-success">
          Create New Patient
        </a>
      <?php endif; ?>
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
          <th>Name</th>
          <th>Mobile</th>
          <th>Email</th>
          <th>Gender</th>
          <?php if ($user_type !== 'admin') : ?>
            <th>Location</th>
          <?php endif; ?>
          <?php if ($user_type == 'patient') : ?>
            <th>Date of Birth</th>
          <?php endif; ?>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $i => $user) : ?>
          <tr>
            <td><?php echo $i + 1 ?></td>
            <td class="d-none">
              <?php echo $user['admin_id'] ?? $user['specialist_id'] ?? $user['patient_id'] ?>
            </td>
            <td>
              <?php echo $user['admin_name'] ?? $user['specialist_name'] ?? $user['patient_name'] ?>
            </td>
            <td>
              <?php echo $user['admin_mobile'] ?? $user['specialist_mobile'] ?? $user['patient_mobile'] ?>
            </td>
            <td>
              <?php echo $user['admin_email'] ?? $user['specialist_email'] ?? $user['patient_email'] ?>
            </td>
            <td>
              <?php echo ucfirst($user['admin_gender'] ?? $user['specialist_gender'] ?? $user['patient_gender']) ?>
            </td>
            <?php if ($user_type !== 'admin') : ?>
              <td>
                <?php echo $user['specialist_location'] ?? $user['patient_location'] ?>
              </td>
            <?php endif; ?>
            <?php if ($user_type == 'patient') : ?>
              <td>
                <?php
                echo date_format(date_create($user['patient_dob']), 'd-m-Y')
                ?>
              </td>
            <?php endif; ?>
            <td>
              <a href="edit_user.php?user=<?php echo $user_type ?>&id=<?php echo $user['admin_id'] ?? $user['specialist_id'] ?? $user['patient_id'] ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-pen"></i> Edit
              </a>
              <?php if ($user_type == 'admin' && $user['admin_id'] == $_SESSION['user_id']) : ?>
                <button type="button" class="btn btn-outline-danger btn-sm delete-button disabled" data-bs-toggle="modal" data-bs-target="#delete-modal">
                  <i class="fas fa-trash-alt"></i> Delete
                </button>
              <?php else : ?>
                <button type="button" class="btn btn-outline-danger btn-sm delete-button" data-bs-toggle="modal" data-bs-target="#delete-modal">
                  <i class="fas fa-trash-alt"></i> Delete
                </button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="modal" id="delete-modal">
      <div class="modal-dialog">
        <div class="modal-content">

          <div class="modal-header">
            <h4 class="modal-title">Delete Confirmation</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal">
            </button>
          </div>

          <div class="modal-body">
            <p>
              Are you sure that you want to delete
              "<strong id="delete_object"></strong>"
            </p>
            <form method="POST" id="modal_form">
              <input type="hidden" name="object_id" id="object_id">
            </form>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="modal_button">
              Accept
            </button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          </div>

        </div>
      </div>
    </div>
  </div>
</main>

<script src="../js/alerts.js"></script>
<script src="../js/delete.js"></script>

<?php include "../components/footer.php" ?>