<?php
// --------------------------------------------------
//  AFFICHAGE DES ERREURS (évite page blanche !)
// --------------------------------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --------------------------------------------------
//  TRAITEMENT PHP DU CHAT
// --------------------------------------------------

$model = $_POST['model'] ?? "cosmosrp";
$userMessage = $_POST['message'] ?? "";
$jarvisResponse = "";

if (!empty($userMessage)) {

    if ($model === "cosmosrp") {

        // -------------------------------
        // API COSMOSRP
        // -------------------------------
        $api_url = "https://api.pawan.krd/cosmosrp/v1/chat/completions";

        $payload = [
            "model" => "cosmosrp",
            "messages" => [
                ["role" => "system", "content" => "Tu es JARVIS AI, assistant virtuel professionnel créé par Pepe Musafiri."],
                ["role" => "user", "content" => $userMessage]
            ]
        ];

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $res = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $jarvisResponse = "Erreur CURL : " . $curlError;
        } else {
            $data = json_decode($res, true);
            $jarvisResponse = $data["choices"][0]["message"]["content"] ?? "Erreur : pas de réponse de CosmosRP";
        }

    } else if ($model === "c4ai") {

        // -------------------------------
        // API COHERE c4ai-aya-expanse-32b (CORRIGÉE)
        // -------------------------------
        $api_url = "https://api.cohere.ai/v2/chat";

        // IMPORTANT : Cohere N'ACCEPTE PAS les messages assistant dans l’historique
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

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer Uw540GN865rNyiOs3VMnWhRaYQ97KAfudAHAnXzJ"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $res = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $jarvisResponse = "Erreur CURL : " . $curlError;
        } else {
            $data = json_decode($res, true);

            // Vérif Debug
            if (isset($data["message"]["content"][0]["text"])) {
                $jarvisResponse = $data["message"]["content"][0]["text"];
            } else {
                $jarvisResponse = "Réponse inattendue de Cohere :<br><pre>" . print_r($data, true) . "</pre>";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>JARVIS AI — Interface</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">

<style>
:root{
    --accent:#00eaff;
    --panel-bg:rgba(0,255,255,0.06);
}
body{
    background:#020610;
    font-family:"Orbitron";
    color:var(--accent);
    margin:0;
}
.app-grid{
    display:grid;
    grid-template-columns:320px 1fr 320px;
    gap:20px;
    padding:20px;
    min-height:100vh;
}
@media(max-width:992px){
    .app-grid{grid-template-columns:1fr;}
    .right-panel{display:none;}
}
.panel{
    background:var(--panel-bg);
    border:1px solid rgba(0,255,255,0.1);
    border-radius:14px;
    padding:14px;
    display:flex;
    flex-direction:column;
    gap:10px;
}
#response{
    height:60vh;
    overflow:auto;
    color:#dff9ff;
    padding:12px;
    background:rgba(0,10,16,0.25);
    border-radius:8px;
}
.msg-user{
    align-self:flex-end;
    background:rgba(0,255,255,0.05);
    padding:10px;
    border-radius:10px;
    margin:8px 0;
}
.msg-jarvis{
    align-self:flex-start;
    background:rgba(255,255,255,0.05);
    padding:10px;
    border-radius:10px;
    margin:8px 0;
}
.center-panel{
    display:flex;
    justify-content:center;
    align-items:center;
    border-radius:14px;
    overflow:hidden;
}
</style>
</head>

<body>

<div class="app-grid">

    <!-- LEFT PANEL -->
    <div class="panel left-panel">
        <h3 style="text-align:center;">JARVIS AI</h3>

        <div id="response">
            <?php if (!empty($userMessage)): ?>
                <div class="msg-user"><?= htmlspecialchars($userMessage) ?></div>
                <div class="msg-jarvis"><?= $jarvisResponse ?></div>
            <?php else: ?>
                <div class="msg-jarvis">Bonjour, je suis JARVIS. Comment puis-je vous aider ?</div>
            <?php endif; ?>
        </div>

        <form method="POST">
            <input type="text" name="message" placeholder="Parle à JARVIS..." class="form-control" required>

            <select name="model" class="form-control mt-2" style="background:#000;color:var(--accent);">
                <option value="cosmosrp">CosmosRP</option>
                <option value="c4ai">C4AI Aya Expanse 32B</option>
            </select>

            <button class="btn btn-info w-100 mt-3">Envoyer</button>
        </form>
    </div>

    <!-- CENTER -->
    <div class="panel center-panel">
        <img id="jarvis-gif" src="jarvis.gif" alt="JARVIS" style="width:100%;height:100%;object-fit:cover;">
    </div>

    <!-- RIGHT PANEL -->
    <div class="panel right-panel">
        <h4 style="text-align:center;">Système</h4>
        <p>Statut : <b style="color:#8bffcf">En ligne</b></p>
        <p>Modèle sélectionné : <b><?= htmlspecialchars($model) ?></b></p>
    </div>

</div>

</body>
</html>


