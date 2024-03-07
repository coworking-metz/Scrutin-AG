<?php

/**
 * Inclusions des différents fichiers nécéssaires au fonctionnement
 */
include __DIR__ . '/vote.inc.php'; // fonctions relatives aux votes
include __DIR__ . '/users.inc.php'; // fonctions relatives aux utilisateurs et candidats
include __DIR__ . '/log.inc.php'; // fonctions de log
include __DIR__ . '/reglages.inc.php'; // fonctions pour lire les réglages de l'ag
include __DIR__ . '/depouillement.inc.php'; // fonctions relatives au dépouillement des votes

/**
 * Ajout des CSS et JS dédiées au formulaire de vote
 */
add_action('wp_enqueue_scripts', function () {
    if(is_admin()) return;
    if (is_page('election-ca')) {
        $base = '/wp-content/mu-plugins/includes/ag';
        $css = $base . '/vote.css';
        wp_enqueue_style('vote-style', $css, [], filemtime(ABSPATH.$css));
        $js = $base . '/vote.js';
        wp_enqueue_script('vote-script', $js, array (), filemtime(ABSPATH.$js), true);
    }
});

/**
 * Fonction utilisée par la page wordpress election-ca pour afficher le formulaire de vote
 */
function ag_candidats()
{
    include __DIR__ . '/formulaire.inc.php';
}


/**
 * Génère un UUID en utilisant l'API https://www.uuidtools.com/api/generate/v1
 *
 * Cette fonction fait une requête à l'API uuidtools pour générer un UUID de version 1.
 * Elle utilise cURL pour faire la requête et retourne l'UUID sous forme de chaîne de caractères.
 * En cas d'échec de la requête, elle retournera null.
 *
 * @return string|null L'UUID généré ou null en cas d'échec.
 */
function generate_uuid() {
    $curl = curl_init("https://www.uuidtools.com/api/generate/v1");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    
    if ($response) {
        $uuids = json_decode($response);
        return $uuids[0] ?? null;
    }
    
    return null;
}