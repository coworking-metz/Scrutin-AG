<?php


/**
 * Formualaire de vote
 */

if (!get_current_user_id()) {
    // page réservée aux membres connectés: Si personne n'est connecté, on redirige vers le formulaire de connexion
    wp_redirect('/mon-compte/?redirect=/candidats-au-conseil-d-administration/');
    exit;
}
$users = get_users_candidat_au_ca();
if (ag_depouillement()) {
    // Si la page est celle du dépouillement, la liste des utilisateurs est modifiée pour ne conserver que les élus 
    $users = ag_faire_depouillement($users);
}
?>
<center>
    <h2>Association Coworking Metz</h2>
    <h1>Élection du conseil d'administration</h1>
    <h2>Assemblée générale du
        <?= date('d/m/Y', strtotime(ag_date())); ?>
    </h2>
</center>

<form class="candidats" method="post" action="/election-ca">
    <input type="hidden" name="action" value="election-ca">
    <?php if (ag_depouillement()) { ?>
        <?php ag_recap_depouillement($users); ?>
    <?php } else { ?>
        <center>
            <?= count($users); ?> candidats au total
        </center>

        <?php if (ag_voter()) { ?>
            <div class="vote-status">
                <div class="contenu">
                    <?php if (!is_user_electeur()) { ?>
                        <strong>Vous ne pouvez pas participer à ce vote</strong>
                    <?php } ?>
                    <?php if (a_deja_vote()) { ?>
                        <strong>Votre vote pour cette élection a été pris en compte.</strong>
                    <?php } ?>
                </div>
                <div>
                    <?php if (is_user_electeur() && !a_deja_vote()) { ?>
                        <button class="btn" type="submit">Valider votre choix</button>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
    <ul>
        <?php
        foreach ($users as $user) { ?>
            <li>
                <?php if (ag_voter() && !a_deja_vote()) { ?>
                    <input type="checkbox" name="candidats[]" value="<?= $user->ID; ?>"
                        title="Sélectionner <?= $user->display_name; ?>">
                <?php } ?>

                <span>
                    <strong>
                        <?= $user->display_name; ?>
                    </strong>
                    <?php if (ag_depouillement()) { ?>
                        <p>
                            <?= ag_candidat_votes($user) ?> votes
                        </p>
                    <?php } ?>
                </span>
                <figure>
                    <img src="/polaroid/<?= $user->ID; ?>.jpg">
                </figure>
            </li>
        <?php } ?>
    </ul>
</form>
<br><br>
<br><br>
<br><br>
<script>
    // Variables mises à disposition de vote.js
    const a_deja_vote = <?=json_encode(a_deja_vote());?>;
    const ag_voter = <?= json_encode(ag_voter()); ?>;
    const ag_max = <?= ag_max(); ?>;
    const ag_min = <?= ag_min(); ?>;

</script>

<style>

</style>