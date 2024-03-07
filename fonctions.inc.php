<?php

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
function ag_sauver_votes($uid, $candidats)
{
    $hash = generate_uuid();

    $date = ag_date();
    $key = 'votes-' . $date;

    $candidats = $_POST['candidats'] ?? false;
    if (!$candidats)
        exit;

    foreach ($candidats as $candidat) {
        $votes = get_user_meta($candidat, $key, true);
        if (!$votes) {
            $votes = [];
        }
        $votes[$hash] = ['time' => time(), 'hash' => $hash];
        update_user_meta($candidat, $key, $votes);
    }

    update_user_meta($uid, 'a-vote-' . $date, true);
    ag_log_message('Vote enregistré');

}
function a_deja_vote($uid = false)
{
    if (!$uid)
        $uid = get_current_user_id();
    $date = ag_date();
    $key = 'a-vote-' . $date;
    return get_user_meta($uid, $key, true);
}
function ag_votes($uid)
{
    $date = ag_date();
    $key = 'votes-' . $date;

    $votes = get_user_meta($uid, $key, true);
    if (!$votes)
        return 0;

    return count($votes);
}

function ag_faire_depouillement($candidats)
{
    $candidats = array_filter($candidats, function ($candidat) {
        return ag_votes($candidat->ID) > 0;
    });
    usort($candidats, function ($a, $b) {
        $a = ag_votes($a->ID);
        $b = ag_votes($b->ID);
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? 1 : -1;
    });
    if (count($candidats) > ag_max()) {
        $dernier_vote_accepte = ag_votes($candidats[ag_max() - 1]->ID);
        $candidats = array_filter($candidats, function ($candidat) use ($dernier_vote_accepte) {
            return ag_votes($candidat->ID) >= $dernier_vote_accepte;
        });
    }

    return $candidats;
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

function get_users_electeurs()
{
    // Check if the transient already exists
    $votants = get_transient('users_electeurs');
    if (false === $votants) {
        // Transient does not exist, so we fetch the data
        $json = file_get_contents('https://tickets.coworking-metz.fr/api/voting-members?key=bupNanriCit1');
        $votants = json_decode($json, true);
        // Store the result in a transient that expires after 12 hours (43200 seconds)
        set_transient('users_electeurs', $votants, 12 * HOUR_IN_SECONDS);
    }
    return $votants;
}
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
