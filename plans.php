<?php
// Connexion à la base de données
$host = 'localhost';
$user = 'root'; // adapte si besoin
$password = '';
$dbname = 'architecture';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_errno) {
    http_response_code(500);
    exit('Erreur connexion MySQL : ' . $conn->connect_error);
}

// Endpoint API pour plans.html
if (isset($_GET['api'])) {
    header('Content-Type: application/json');
    $res = $conn->query('SELECT * FROM plans ORDER BY date DESC');
    $plans = [];
    while ($row = $res->fetch_assoc()) {
        // Corrige le chemin du fichier si besoin
        if (!preg_match('~^uploads/~', $row['file'])) {
            $row['file'] = 'uploads/' . $row['file'];
        }
        $plans[] = $row;
    }
    echo json_encode($plans);
    $conn->close();
    exit;
}

// Ajout de plan via fetch/ajax (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description']) && isset($_FILES['plan'])) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir);
    $fileName = uniqid('plan_') . '.' . pathinfo($_FILES['plan']['name'], PATHINFO_EXTENSION);
    $filePath = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['plan']['tmp_name'], $filePath)) {
        $desc = $_POST['description'];
        $file = $filePath;
        $sql = "INSERT INTO plans (description, file) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ss', $desc, $file);
            if ($stmt->execute()) {
                echo '<div style="color:green;">Plan ajouté avec succès !</div>';
            } else {
                echo '<div style="color:#d32f2f;">Erreur SQL (execute) : ' . $stmt->error . '</div>';
            }
            $stmt->close();
        } else {
            echo '<div style="color:#d32f2f;">Erreur SQL (prepare) : ' . $conn->error . '</div>';
        }
    } else {
        echo "<div style='color:#d32f2f;'>Erreur lors de l'upload du fichier.</div>";
    }
    $conn->close();
    exit;
}

$result = $conn->query("SELECT * FROM plans ORDER BY date DESC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Plans architecturaux</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f7f9fa; margin: 0; padding: 0; }
    .container { max-width: 700px; margin: 2rem auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 16px #0001; padding: 2rem; }
    h1, h2 { color: #1a73e8; text-align: center; }
    form { background: #f1f5fb; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 1px 4px #0001; }
    label { display: block; margin: 1rem 0 0.3rem 0; font-weight: 500; }
    input[type="file"], textarea, input[type="text"] { width: 100%; padding: 0.5rem; border-radius: 5px; border: 1px solid #b0bec5; margin-bottom: 1rem; font-size: 1rem; }
    button { background: #1a73e8; color: #fff; border: none; border-radius: 5px; padding: 0.7rem 1.5rem; font-size: 1.1rem; cursor: pointer; transition: background 0.2s; }
    button:hover { background: #155ab6; }
    .plans-list { margin-top: 2rem; }
    .plan-card { background: #f9fbfd; border: 1px solid #e3e8ee; border-radius: 10px; box-shadow: 0 1px 6px #0001; padding: 1.2rem; margin-bottom: 1.5rem; }
    .plan-card h3 { margin: 0 0 0.5rem 0; color: #1976d2; }
    .plan-card img, .plan-card embed { max-width: 100%; border-radius: 6px; margin-top: 0.7rem; box-shadow: 0 1px 4px #0001; }
    .plan-card p { margin: 0.5rem 0; }
    @media (max-width: 600px) {
      .container { padding: 0.7rem; }
      form { padding: 1rem; }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>📁 Plans architecturaux</h1>
    <h2>Ajouter un plan</h2>
    <form id="planForm" enctype="multipart/form-data">
      <label for="planDesc">Description :</label>
      <textarea id="planDesc" name="description" rows="2" required placeholder="Décrivez le plan..."></textarea>
      <label for="planFile">Fichier du plan (PDF, JPG, PNG) :</label>
      <input type="file" id="planFile" name="plan" accept=".pdf,.jpg,.jpeg,.png" required>
      <button type="submit">Enregistrer</button>
    </form>
    <div id="planMessage"></div>
    <h2 style="margin-top:2.5rem;">Liste des plans</h2>
    <div class="plans-list">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="plan-card">
            <h3>📝 <?= htmlspecialchars($row['description']) ?></h3>
            <p><strong>Date :</strong> <?= $row['date'] ?></p>
            <?php if (str_ends_with(strtolower($row['file']), '.pdf')): ?>
              <embed src="<?= $row['file'] ?>" type="application/pdf" width="100%" height="300px">
            <?php else: ?>
              <img src="<?= $row['file'] ?>" alt="plan">
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>Aucun plan enregistré.</p>
      <?php endif; ?>
    </div>
  </div>
  <script>
    document.getElementById('planForm').onsubmit = async function(e) {
      e.preventDefault();
      const form = e.target;
      const data = new FormData(form);
      const res = await fetch('plans.php', {
        method: 'POST',
        body: data
      });
      const text = await res.text();
      document.getElementById('planMessage').innerHTML = text;
      if(text.includes('succès')) form.reset();
    };
  </script>
</body>
</html>
