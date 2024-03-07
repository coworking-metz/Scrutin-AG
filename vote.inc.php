<?php


if (isset($_GET['ag-vote'])) {
    add_action('init', function () {
        $uid = get_current_user_id();

        if (!$uid)
            exit;

        if (a_deja_vote($uid))
            ag_log_erreur('Tentative de vote multiple');

        ag_sauver_votes($uid, $_POST['candidats']);
        wp_redirect('/candidats-au-conseil-d-administration/');
        exit;
    });
}
