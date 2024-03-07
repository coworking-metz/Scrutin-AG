<?php


/**
 * Afficher le récapitulatif de dépouillement : canddiats élus, votes exprimés, votants
 */
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
/**
 * Indique si la page actuelle est en mode 'affichage du dépouillement'
 */
function ag_depouillement()
{
    if (!isset($_GET['depouillement']))
        return;
    if (!current_user_can('administrator'))
        return;
    return true;
}

/**
 * Effectue le dépouillement des votes pour les candidats.
 *
 * Cette fonction filtre d'abord les candidats n'ayant reçu aucun vote. Ensuite, elle trie les candidats
 * par nombre de votes décroissants. Si le nombre de candidats dépasse le maximum autorisé, elle limite
 * la liste aux candidats ayant un nombre de votes supérieur ou égal au dernier candidat accepté.
 *
 * @param array $candidats Tableau d'objets candidats à dépouiller.
 * @return array Tableau des candidats filtrés et triés selon les critères de votes.
 */
function ag_faire_depouillement($candidats)
{
    $candidats = array_filter($candidats, function ($candidat) {
        return ag_candidat_votes($candidat->ID) > 0;
    });
    usort($candidats, function ($a, $b) {
        $a = ag_candidat_votes($a->ID);
        $b = ag_candidat_votes($b->ID);
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? 1 : -1;
    });
    if (count($candidats) > ag_max()) {
        $dernier_vote_accepte = ag_candidat_votes($candidats[ag_max() - 1]->ID);
        $candidats = array_filter($candidats, function ($candidat) use ($dernier_vote_accepte) {
            return ag_candidat_votes($candidat->ID) >= $dernier_vote_accepte;
        });
    }

    return $candidats;
}