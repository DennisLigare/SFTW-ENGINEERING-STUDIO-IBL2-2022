<?php

session_start();

if (!$_SESSION || $_SESSION['user_type'] !== 'admin') {
  header("Location: ../index.php");
}

require "../components/database.php";

$success_message = "";
if ($_POST) {
  $statement = $pdo->prepare(
    "DELETE FROM specialization WHERE specialization_id=:id"
  );
  $statement->bindValue(":id", $_POST['object_id']);
  $statement->execute();

  $success_message = "Specialization deleted successfully!";
}

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
      <a href="admin.php" class="btn btn-secondary">Back</a>
    </div>
    <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-3 mb-2">
      <h1>Manage Specializations</h1>
      <a href="add_specialization.php" class="btn btn-success">
        Create Specialization
      </a>
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
          <th>Description</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($specializations as $i => $specialization) : ?>
          <tr>
            <td><?php echo $i + 1 ?></td>
            <td class="d-none"><?php echo $specialization['specialization_id'] ?></td>
            <td>
              <?php echo $specialization['specialization_name'] ?>
            </td>
            <td>
              <?php echo substr($specialization['specialization_desc'], 0, 60) . "..." ?>
            </td>
            <td>
              <a href="edit_specialization.php?id=<?php echo $specialization['specialization_id'] ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-pen"></i> Edit
              </a>
              <button type="button" class="btn btn-outline-danger btn-sm delete-button" data-bs-toggle="modal" data-bs-target="#delete-modal">
                <i class="fas fa-trash-alt"></i> Delete
              </button>
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