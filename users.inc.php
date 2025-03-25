<?php


function ag_micro_pola_content($uid) {
    $transient_name = 'ag_micro_pola_' . $uid;
    $content = get_transient($transient_name);
    if (false === $content) {
        $url = 'https://photos.coworking-metz.fr/polaroid/size/micro/'.$uid.'.jpg';
        $content = file_get_contents($url);
        $base64_content = base64_encode($content);
        set_transient($transient_name, $base64_content, DAY_IN_SECONDS);
        $content = $base64_content;
    }
    
    return 'url(data:image/jpeg;base64,' . $content . ')';
}


/**
 * FONCTION RELATIVES AUX UTILISATEURS ET CANDIDATS
 */

 /**
  * Calcule et retourne le quorum de l'ag (le tiers des membres électeurs)
  */
 function ag_quorum() {
    $nb_electeurs = count(get_users_electeurs());
    $quorum = ceil($nb_electeurs / 3);
    return $quorum;
 }
/**
 * Récupère tous les utilisateurs dont le meta 'candidat_au_ca' n'est pas false.
 *
 * @return WP_User[] Liste des utilisateurs.
 */
function get_users_candidat_au_ca($depouillement = false)
{
    $users = get_users([
        'meta_key' => 'candidat_au_ca',
        'meta_value' => '',
        'meta_compare' => '!=',
        'fields' => 'all',
    ]);

    $users = array_filter($users, function ($user) {
        return $user->candidat_au_ca;
    });
    shuffle($users);

    return $users;
}

/**
 * Retourne tous les comptes utilisateurs identifiés comme électeurs par l'API Tickets
 * La données est mise en cache pendant 12h pour éviter une surcharge d'appels à tickets
 * @return array
 */
function get_users_electeurs()
{
    // Check if the transient already exists
    $votants = get_transient('users_electeurs');
    if (false === $votants) {
        // Transient does not exist, so we fetch the data
        $json = file_get_contents(TICKET_BASE_URL.'/voting-members?key=bupNanriCit1');
        $votants = json_decode($json, true);
        // Store the result in a transient that expires after 12 hours (43200 seconds)
        set_transient('users_electeurs', $votants, 12 * HOUR_IN_SECONDS);
    }
    return $votants;
}
/**
 * Indique si un utilisateurs est considéré comme électeur
 * Un électeur est un membre ayant un age minimal (par exemple: 16 ans) et un nombre 
 * minimal de journées coworkées dans les X derniers mois (exemple : 20 journées sur 6 mois)
 * La règle de gestion précise est dans l'API Tcikets https://github.com/coworking-metz/tickets-backend
 */
function is_user_electeur($uid = false)
{

    if (!$uid)
        $uid = get_current_user_id();
    if (!$uid)
        return;
    $user = get_userdata($uid);
    if (!$user)
        return;

    if (isset($GLOBALS['is_user_electeur-' . $uid])) {
        return $GLOBALS['is_user_electeur-' . $uid];
    }
    $GLOBALS['is_user_electeur-' . $uid] = false;
    $votants = get_users_electeurs();
    foreach ($votants as $votant) {
        if ($votant['email'] == $user->user_email) {
            $GLOBALS['is_user_electeur-' . $uid] = true;
            return true;
        }
    }
}
