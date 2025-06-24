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
        // Ne pas afficher ni image ni PDF, juste la description
        echo '</div>';
      }
    }
    ?>
  </div>
  <a href="index.html" style="text-decoration:underline;color:#1a73e8;">← Retour</a>
</body>
</html>
