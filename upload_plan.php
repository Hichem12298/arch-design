<?php
// upload_plan.php : reçoit le fichier et la description, enregistre le fichier et ajoute l'entrée dans plans.json
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc = trim($_POST['planDesc'] ?? '');
    $file = $_FILES['planFile'] ?? null;

    if (!$desc || !$file || $file['error'] !== UPLOAD_ERR_OK) {
        header('Location: index.html?error=1');
        exit;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($ext, $allowed)) {
        header('Location: index.html?error=2');
        exit;
    }

    $filename = uniqid('plan_', true) . '.' . $ext;
    $dest = $uploadDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        header('Location: index.html?error=3');
        exit;
    }

    $plansFile = __DIR__ . '/plans.json';
    $plans = file_exists($plansFile) ? json_decode(file_get_contents($plansFile), true) : [];
    $plans[] = [
        'file' => 'uploads/' . $filename,
        'desc' => htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'),
        'original' => htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'),
        'date' => date('Y-m-d H:i')
    ];
    file_put_contents($plansFile, json_encode($plans, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    header('Location: plans.html?success=1');
    exit;
}
header('Location: index.html');
exit;
