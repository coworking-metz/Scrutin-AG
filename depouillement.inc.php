<?php



function ag_recap_depouillement($users)
{
    if (!count($users)) {
        ?>
        <center><b>Les votes récoltés sont pour l'instant insufisants pour déterminer les élus</b></center>
        <?php
        return;
    }
    ?>
    <center>
        <b>
            <?= count($users); ?> candidats élus -
            <?= ag_votants(); ?> électeur/trices
        </b>
        <?php $candidats_en_rab = count($users) - ag_max();
        if ($candidats_en_rab>0) { ?>
            <br><small>
                <?= $candidats_en_rab; ?> candidat(e)
                <?= $candidats_en_rab > 1 ? 's' : ''; ?> en plus ont été sélectionné(e)s car ils avaient le même nombre de votes
                que
                la
                <?= ag_max(); ?>e personne dans la liste
            </small>
        <?php } ?>
    </center>
    <?php
}
function ag_depouillement()
{
    if (!isset($_GET['depouillement']))
        return;
    if (!current_user_can('administrator'))
        return;
    return true;
}