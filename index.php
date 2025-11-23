<?php
// -------------------------
// DEBUG PHP — AFFICHE TOUT
// -------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Debug du modèle c4ai-aya-expanse-32b</h2>";

// -------------------------
// CONTENU À ENVOYER AU MODÈLE
// -------------------------
$userMessage = "Bonjour, test du modèle aya expanse.";

// -------------------------
// CONFIG API COHERE
// -------------------------
$api_url = "https://api.cohere.ai/v2/chat";
$api_key = "Uw540GN865rNyiOs3VMnWhRaYQ97KAfudAHAnXzJ";

// -------------------------
// PAYLOAD POUR COHERE
// -------------------------
$payload = [
    "model" => "c4ai-aya-expanse-32b",
    "messages" => [
        [
            "role" => "user",
            "content" => $userMessage
        ]
    ],
    "temperature" => 0.3
];

// AFFICHAGE DU JSON ENVOYÉ
echo "<h3>Payload envoyé :</h3>";
echo "<pre>" . json_encode($payload, JSON_PRETTY_PRINT) . "</pre>";

// -------------------------
// REQUÊTE CURL
// -------------------------
$ch = curl_init($api_url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . $api_key
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Evite certains bugs SSL

$response = curl_exec($ch);
$curl_error = curl_error($ch);
$curl_info = curl_getinfo($ch);

curl_close($ch);

// -------------------------
// DEBUG CURL
// -------------------------
echo "<h3>Infos CURL :</h3>";
echo "<pre>" . print_r($curl_info, true) . "</pre>";

if ($curl_error) {
    echo "<h3>Erreur CURL :</h3>";
    echo "<pre>$curl_error</pre>";
}

// -------------------------
// AFFICHAGE DE LA RÉPONSE BRUTE
// -------------------------
echo "<h3>Réponse brute API :</h3>";
echo "<pre>$response</pre>";

// -------------------------
// DÉCODE LA RÉPONSE JSON
// -------------------------
$data = json_decode($response, true);

// -------------------------
// DEBUG DU JSON DÉCODÉ
// -------------------------
echo "<h3>Réponse JSON décodée :</h3>";
echo "<pre>" . print_r($data, true) . "</pre>";

// -------------------------
// EXTRACTION DU MESSAGE
// -------------------------
echo "<h3>Message du modèle :</h3>";

if (isset($data["message"]["content"])) {
    echo "<div style='padding:10px;border:1px solid #333;background:#f5f5f5;'>";
    echo $data["message"]["content"];
    echo "</div>";
} else {
    echo "<p>⚠️ Le modèle n'a pas renvoyé de contenu.</p>";
}
?>





