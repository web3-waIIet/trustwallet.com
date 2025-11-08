<?php
// Autoriser toutes les origines (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Répondre immédiatement aux requêtes OPTIONS (préflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Lire les données JSON envoyées
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Vérifier si on a reçu l'image
if (!isset($data['imageBase64'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "No image received"]);
    exit();
}

// Nettoyer et décoder l'image
$imgData = str_replace('data:image/png;base64,', '', $data['imageBase64']);
$imgData = str_replace(' ', '+', $imgData);
$decoded = base64_decode($imgData);

if ($decoded === false) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid base64 data"]);
    exit();
}

// Sauvegarder l'image dans le dossier images/
if (!is_dir('images')) {
    mkdir('images', 0777, true);
}
$filename = 'images/' . uniqid('img_') . '.png';
if (!file_put_contents($filename, $decoded)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to save image"]);
    exit();
}

// Réponse JSON
echo json_encode([
    "status" => "success",
    "message" => "Image saved",
    "file" => $filename
]);
?>
