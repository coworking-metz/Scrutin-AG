<?php


/**
 * Formualaire de vote
 */

$users = get_users_candidat_au_ca();
if (ag_depouillement()) {
    // Si la page est celle du dépouillement, la liste des utilisateurs est modifiée pour ne conserver que les élus 
    $users = ag_faire_depouillement($users);
}
?>
<center>
    <strong>Association Coworking Metz</strong>
    <h2>Élection du conseil d'administration</h2>
    <p>Assemblée générale du
        <?= date('d/m/Y', strtotime(ag_date())); ?>
</p>
</center>

<?php if (!get_current_user_id()) {?>
    <center>
        <p>Vous devez être connecté(e) avec votre compte coworker pour pouvoir voter.</p>
        <a class="btn btn-solid btn-xlg semi-round btn-bordered border-thin ld_button_653a54d4ec23e lqd-unit-animation-done" href="/mon-compte/?redirect=/election-ca/"><span class="btn-txt">Connexion</span></a>
    </center>
    <?php } else { ?>

<form class="candidats" method="post" action="/election-ca" data-depouillement="<?=ag_depouillement()?'true':'false';?>">
    <input type="hidden" name="action" value="election-ca">
        <?php if (ag_depouillement()) { ?>
            <?php ag_recap_depouillement($users); ?>
        <?php } else { ?>
            <center>
                <?= pluriel(count($users),'candidat(e)'); ?> au total
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
                                <?= pluriel(ag_candidat_votes($user),'vote') ?>
                            </p>
                        <?php } ?>
                    </span>
                    <figure>
                        <img src="/polaroid/<?= $user->ID; ?>-small.jpg" loading="lazy">
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
<?php }?>
<style>

</style>