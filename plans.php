<?php
include 'db.php';

// Ajout de plan via formulaire (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description']) && isset($_FILES['plan'])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir);
    $fileName = uniqid('plan_') . '.' . pathinfo($_FILES['plan']['name'], PATHINFO_EXTENSION);
    $filePath = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['plan']['tmp_name'], $filePath)) {
        $desc = $conn->real_escape_string($_POST['description']);
        $file = $conn->real_escape_string($filePath); // Stocke le chemin complet
        $sql = "INSERT INTO plans (description, file) VALUES ('$desc', '$file')";
        if ($conn->query($sql)) {
            $success = 'Plan ajouté avec succès !';
        } else {
            $error = 'Erreur SQL : ' . $conn->error;
        }
    } else {
        $error = "Erreur lors de l'upload du fichier.";
    }
}

$result = $conn->query("SELECT * FROM plans ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Plans enregistrés</title>
  <style>
    .plan {
      border: 1px solid #ccc;
      padding: 10px;
      margin: 10px 0;
    }
  </style>
</head>
<body>
  <h1>📁 Liste des plans</h1>
  <a href="ajout_plan.html">➕ Ajouter un nouveau plan</a>
  <hr>
  <form method="post" enctype="multipart/form-data" style="margin-bottom:2rem;">
    <label>Description : <input type="text" name="description" required></label>
    <label>Fichier : <input type="file" name="plan" accept=".jpg,.jpeg,.png,.gif,.pdf" required></label>
    <button type="submit">Ajouter un plan</button>
    <?php if(isset($success)) echo '<div style="color:green;">'.$success.'</div>'; ?>
    <?php if(isset($error)) echo '<div style="color:#d32f2f;">'.$error.'</div>'; ?>
  </form>

  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="plan">
        <p><strong>Description :</strong> <?= htmlspecialchars($row['description']) ?></p>
        <p><strong>Date :</strong> <?= $row['date'] ?></p>
        <?php if (str_ends_with(strtolower($row['file']), '.pdf')): ?>
          <embed src="<?= $row['file'] ?>" type="application/pdf" width="100%" height="300px">
        <?php else: ?>
          <img src="<?= $row['file'] ?>" alt="plan" width="300">
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>Aucun plan enregistré.</p>
  <?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>
