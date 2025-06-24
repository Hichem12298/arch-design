<?php
$host = "localhost";
$user = "root";
$pass = ""; // Mets ton mot de passe MySQL ici si nécessaire
$dbname = "architecture";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("❌ Connexion échouée : " . $conn->connect_error);
}
?>
