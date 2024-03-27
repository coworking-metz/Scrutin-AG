<?php

/**
 * FONCTION DE LOGS
 * Pour consulter la log en temsp réeel, aller sur https://coworking-metz-ag.requestcatcher.com/
 */


/**
 * Enregistre un message d'erreur et termine le script.
 *
 * Cette fonction est utilisée pour enregistrer un message d'erreur dans un service externe
 * avant d'arrêter l'exécution du script PHP. Elle fait appel à `ag_log_message` pour l'enregistrement
 * du message et utilise `exit` pour arrêter l'exécution.
 *
 * @param string $message Le message d'erreur à enregistrer.
 */
function ag_log_erreur($message, $slug="")
{
    ag_log_message($message, $slug);
    exit;
}

/**
 * Enregistre un message dans un service externe.
 *
 * Cette fonction envoie un message, avec des informations supplémentaires comme la date et l'heure actuelles,
 * l'adresse IP du client, l'ID de l'utilisateur actuel et le nom d'affichage de l'utilisateur,
 * à un service externe via une requête POST. 
 *
 * @param string $message Le message à enregistrer.
 */
function ag_log_message($message, $slug='')
{
    // Préparation des données à envoyer
    $data = [
        'message' => $message,
        'datetime' => date('Y-m-d H:i:s'), // Date et heure actuelles
        'ip' => $_SERVER['REMOTE_ADDR'], // Adresse IP du client
        'uid'=> get_current_user_id(), // ID de l'utilisateur actuel
        'name'=> wp_get_current_user()->display_name??'', // Nom d'affichage de l'utilisateur actuel
    ];

    $slug = sanitize_title($data['name']).($slug ? '/'.$slug : '');
    // Encodage des données en JSON
    $jsonPayload = json_encode($data, JSON_PRETTY_PRINT);

    // Initialisation de cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://coworking-metz-ag.requestcatcher.com/$slug");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonPayload)
    ]);

    // Exécution de la requête POST
    $response = curl_exec($ch);
    // Vérification des erreurs
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    // Fermeture de la session cURL
    curl_close($ch);
}
