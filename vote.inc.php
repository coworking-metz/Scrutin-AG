<?php

/**
 * FONCTIONS RELATIVES AUX VOTES
 */

/**
 * Gère le vote de l'utilisateur pour les candidats au conseil d'administration.
 * 
 * Ce code vérifie d'abord si le paramètre 'ag-vote' est présent dans l'URL. Si oui, il attache une fonction anonyme 
 * à l'action 'init' de WordPress. Cette fonction effectue les actions suivantes :
 * 1. Récupère l'ID de l'utilisateur actuel.
 * 2. Si aucun utilisateur n'est connecté, arrête l'exécution du script.
 * 3. Vérifie si l'utilisateur a déjà voté. Si oui, enregistre une erreur de tentative de vote multiple.
 * 4. Sauvegarde anonyme des votes de l'utilisateur pour les candidats sélectionnés.
 * 5. Redirige l'utilisateur vers la page des candidats au conseil d'administration.
 * 
 * @return void
 */

if (($_POST['action'] ?? false) === 'election-ca') {
    add_action('init', function () {
        $uid = get_current_user_id();

        if (!$uid)
            exit;

        if (a_deja_vote($uid))
            ag_log_erreur('Tentative de vote multiple',"error");

        ag_sauver_votes($uid, $_POST['candidats']);
        wp_redirect('/election-ca/');
        exit;
    });
}

/**
 * Vérifie si l'état du scrutin permet de voter
 * 
 * Cette fonction contrôle d'abord si le scrutin est ouvert en vérifiant l'état de 'etat_vote' dans la configuration du scrutin.
 * Elle vérifie ensuite si on est sur la page de dépouillement. Le vote n'est pas possible lors du dépouillement,
 * Elle verifie ensuite que l'utilisateur courant est autorisé à voter (membre électeur). 
 * Si l'une de ces conditions n'est pas remplie, la fonction termine son exécution.
 * Si toutes les vérifications sont passées, la fonction retourne true, indiquant que le vote est possible.
 * 
 * @return bool|null Retourne true si l'utilisateur peut voter, sinon null si une des conditions empêche le vote.
 */
function ag_voter()
{

    if (ag_settings()['scrutin']['etat_vote'] == 'closed')
        return;
    if (ag_depouillement())
        return;

    if (!is_user_electeur())
        return;

    return true;
}

/**
 * Compte le nombre d'électeurs ayant déjà voté pour une date donnée.
 * 
 * Cette fonction récupère le nombre d'utilisateurs ayant voté,
 * basé sur la date actuelle du scrutin. 
 * Le résultat est le nombre d'utilisateurs qui ont déjà voté.
 * 
 * @return int Le nombre d'utilisateurs ayant déjà voté.
 */
function ag_votants()
{
    $key = 'a-vote-' . ag_date();
    $args = [
        'meta_query' => [
            [
                'key' => $key,
                'compare' => 'EXISTS',
            ],
        ]
    ];

    $users = get_users($args);

    return count($users);
}

/**
 * Enregistre les votes d'un utilisateur pour les candidats sélectionnés.
 * 
 * Cette fonction génère un identifiant unique (UUID) pour le votant, récupère la date actuelle du scrutin. 
 * La fonction enregistre le vote avec l'heure actuelle et l'UUID généré. Elle met également à jour l'utilisateur pour indiquer
 * qu'il a voté à cette date. Un message est loggé pour confirmer l'enregistrement du vote.
 * 
 * @param int $uid L'ID de l'utilisateur votant.
 * @param array $candidats Les IDs des candidats sélectionnés.
 * @return void
 */
function ag_sauver_votes($uid, $candidats)
{
    $uuid = generate_uuid();

    $date = ag_date();
    $key = 'votes-' . $date;

    $candidats = $_POST['candidats'] ?? false;

    foreach ($candidats as $candidat) {
        $votes = get_user_meta($candidat, $key, true);
        if (!$votes) {
            $votes = [];
        }
        $votes[$uuid] = ['time' => time(), 'uuid' => $uuid];
        update_user_meta($candidat, $key, $votes);
    }

    update_user_meta($uid, 'a-vote-' . $date, true);
    ag_log_message('Vote enregistré','vote');
}
/**
 * Vérifie si l'utilisateur a déjà voté pour la date courante.
 *
 * Cette fonction vérifie si l'utilisateur spécifié par son identifiant (ou l'utilisateur actuellement connecté
 * si aucun identifiant n'est fourni) a une métadonnée indiquant qu'il a voté pour la date courante.
 *
 * @param int|bool $uid L'identifiant de l'utilisateur. Si faux, utilise l'ID de l'utilisateur actuellement connecté.
 * @return mixed La valeur de la métadonnée 'a-vote-' suivi de la date actuelle si elle existe, faux sinon.
 */
function a_deja_vote($uid = false)
{
    if (!$uid)
        $uid = get_current_user_id();
    $date = ag_date();
    $key = 'a-vote-' . $date;
    return get_user_meta($uid, $key, true);
}
/**
 * Compte le nombre de votes pour un utilisateur donné pour la date d'ag courante.
 *
 * @param int $uid L'identifiant de l'utilisateur dont on veut compter les votes.
 * @return int Le nombre de votes pour l'utilisateur spécifié pour la date courante, ou zéro si aucune métadonnée n'existe.
 */
function ag_candidat_votes($user)
{

    if (is_numeric($user)) {
        $date = ag_date();
        $key = 'votes-' . $date;
        $votes = get_user_meta($user, $key, true);
        if(!$votes) return 0;
        return @count($votes);
    } else {
        if (empty($user->votes))
            return 0;

        return intval($user->votes);
    }
}


