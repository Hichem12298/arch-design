<?php
include 'db.php'; // Connexion à la base

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $description = trim($_POST['description']);
  $file = $_FILES['plan_file'];

  $uploadDir = "uploads/";
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  $fileName = basename($file["name"]);
  $targetPath = $uploadDir . time() . "_" . $fileName;

  if (move_uploaded_file($file["tmp_name"], $targetPath)) {
    $stmt = $conn->prepare("INSERT INTO plans (description, file) VALUES (?, ?)");
    $stmt->bind_param("ss", $description, $targetPath);

    if ($stmt->execute()) {
      echo "✅ Plan enregistré avec succès. <a href='plans.php'>Voir les plans</a>";
    } else {
      echo "❌ Erreur lors de l'insertion en base.";
    }

    $stmt->close();
  } else {
    echo "❌ Échec du téléchargement du fichier.";
  }
}

$conn->close();
?>
