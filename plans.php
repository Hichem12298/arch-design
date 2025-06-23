<?php
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

// Authentification simple
if (isset($_POST['login'])) {
    if ($_POST['email'] === 'admin.aziz@gmail.com' && $_POST['password'] === '123') {
        $_SESSION['admin'] = true;
        header('Location: plans.php');
        exit;
    } else {
        $error = "Identifiants incorrects";
    }
}
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: plans.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Plans enregistrés</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>📁 Plans enregistrés <?php if($isAdmin) echo '<span style="color:#fff;background:#1a73e8;border-radius:4px;padding:2px 8px;font-size:0.9em;">admin</span>'; ?></h1>
  <?php if(!$isAdmin): ?>
    <form method="post" style="margin-bottom:2rem;">
      <label>Email admin : <input type="email" name="email" required></label>
      <label>Mot de passe : <input type="password" name="password" required></label>
      <button type="submit" name="login">Connexion admin</button>
      <?php if(isset($error)) echo '<div style="color:#d32f2f;">'.$error.'</div>'; ?>
    </form>
  <?php else: ?>
    <a href="plans.php?logout=1" style="float:right;text-decoration:underline;color:#d32f2f;">Déconnexion</a>
  <?php endif; ?>

  <div style="clear:both"></div>
  <div>
    <?php
    $plansFile = __DIR__ . '/plans.json';
    $plans = file_exists($plansFile) ? json_decode(file_get_contents($plansFile), true) : [];
    if (empty($plans)) {
      echo "<p>Aucun plan enregistré.</p>";
    } else {
      foreach (array_reverse($plans) as $i => $plan) {
        echo '<div class="plan-card" style="border:1px solid #ccc;border-radius:8px;padding:1rem;margin-bottom:1rem;box-shadow:0 0 5px rgba(0,0,0,0.1);">';
        echo '<h3>📝 Plan '.($i+1).'</h3>';
        echo '<p><strong>Description :</strong> '.htmlspecialchars($plan['desc'] ?? $plan['description']).'</p>';
        // Affichage image si image, sinon message pour PDF
        if (!empty($plan['file'])) {
          $ext = strtolower(pathinfo($plan['file'], PATHINFO_EXTENSION));
          if (in_array($ext, ['jpg','jpeg','png','gif'])) {
            echo '<img src="'.htmlspecialchars($plan['file']).'" alt="Plan '.($i+1).'" style="max-width:100%;margin-top:10px;"/>';
          } elseif ($isAdmin && $ext === 'pdf') {
            echo '<embed src="'.htmlspecialchars($plan['file']).'" type="application/pdf" width="100%" height="400px"/>';
          } elseif (!$isAdmin && $ext === 'pdf') {
            echo '<p style="color:#888;font-size:0.95em;">Aperçu PDF non disponible en mode public.</p>';
          }
        }
        if ($isAdmin) {
          echo '<p><strong>Ajouté le :</strong> '.($plan['date'] ?? '').'</p>';
          echo '<form method="post" action="delete_plan.php" style="margin-top:8px;"><input type="hidden" name="index" value="'.$i.'"><button type="submit" style="background:#d32f2f;color:#fff;border:none;padding:5px 12px;border-radius:4px;cursor:pointer;">Supprimer</button></form>';
        } else {
          echo '<p style="color:#d32f2f;"><em>Pour obtenir ce plan, contactez : <a href="mailto:azizhicham136@gmail.com">azizhicham136@gmail.com</a></em></p>';
        }
        echo '</div>';
      }
    }
    ?>
  </div>
  <a href="index.html" style="text-decoration:underline;color:#1a73e8;">← Retour</a>
</body>
</html>
