<?php


/**
 * Afficher le récapitulatif de dépouillement : canddiats élus, votes exprimés, votants
 */
function ag_recap_depouillement($users)
{
    if (!count($users)) {
        ?>
        <center>Actuellement, il y a
            <?= ag_votants(); ?> vote(s) exprimé(s), le quorum est à
            <?= ag_quorum(); ?>.
        </center>
        <?php
        return;
    }
    ?>
    <center>
        <b>
            <?= pluriel(count($users), 'candidat(e) élu(e)'); ?>  -
            <?= pluriel(ag_votants(), 'électeur/trice', 's','/'); ?>
        </b>
        <?php $candidats_en_rab = count($users) - ag_max();
        if ($candidats_en_rab > 0) { ?>
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
 * par nombre de votes décroissants. 
 * 
 * On vérifie que le nombre de votes exprimés est supérieur ou égale au quorum
 * 
 * Si le nombre de candidats dépasse le maximum autorisé, elle limite
 * la liste aux candidats ayant un nombre de votes supérieur ou égal au dernier candidat accepté.
 *
 * @param array $candidats Tableau d'objets candidats à dépouiller.
 * @return array Tableau des candidats filtrés et triés selon les critères de votes.
 */
function ag_faire_depouillement($candidats)
{
    $candidats = array_map(function ($candidat) {
        $candidat->votes = ag_candidat_votes($candidat->ID);
        return $candidat;
    }, $candidats);

    $candidats = array_filter($candidats, function ($candidat) {
        return $candidat->votes > 0;
    });
    usort($candidats, function ($a, $b) {
        $a = $a->votes;
        $b = $b->votes;
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? 1 : -1;
    });

    // $max = max(array_column($candidats, 'votes'));

    // nombre de votes exprimés à date
    $nb_votes_exprimes = ag_votants();


    if (count($candidats) > ag_max()) {
        $dernier_vote_accepte = ag_candidat_votes($candidats[ag_max() - 1]->ID);
        $candidats = array_filter($candidats, function ($candidat) use ($dernier_vote_accepte) {
            return ag_candidat_votes($candidat->ID) >= $dernier_vote_accepte;
        });
    }


    return $candidats;
}