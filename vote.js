/**
 * Gestion de l'interface de vote
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form.candidats');
    // effectuer une action lorsque le formulaire est soumis
    form.addEventListener('submit', e => {
        // Affiche une boîte de confirmation avant de soumettre le formulaire, demandant une validation de l'utilisateur
        // Si l'utilisateur ne confirme pas, empêche la soumission du formulaire
        if (!confirm(`Vous avez choisi ${selected()} candidat(e)s. Voulez-vous valider votre choix ?\nVotre votre sera définitif et ne pourra être modifié. `)) {
            e.preventDefault();
            return false;
        }
    })

    // Sélectionne tous les champs de saisie du formulaire et ajoute un écouteur d'événements 'input' à chacun
    form.querySelectorAll('input').forEach(input => input.addEventListener('input', (e) => {
        // Vérifie si le nombre de candidats sélectionnés atteint la limite maximum et affiche une alerte si c'est le cas
        if (selected() - 1 == ag_max) {
            alert(`Vous avez atteinds la limite de ${ag_max} candidats.\nVous devez déselectionner une autre personne pour choisir celle-ci`)
            e.target.checked = false;
            return;
        }
        // Met à jour l'affichage du statut après chaque modification de sélection
        displayStatus();
    }));

    const divStatus = document.querySelector('.vote-status .contenu');

    const bouton = document.querySelector('.vote-status button');

    /**
     * Mise à jour l'affichage du statut en fonction du nombre de candidats sélectionnés
     */
    function displayStatus() {
        updateVoteVisual();
        // Obtient le nombre total de candidats et génère le contenu HTML en fonction du statut de la sélection
        const total = form.querySelectorAll('li').length;
        const html = [];
        // Détermine si le vote est autorisé en fonction de si l'utilisateur a déjà voté et si le nombre de sélectionnés est dans les limites permises
        const voteOk = !a_deja_vote && selected() >= ag_min && selected() <= ag_max;
        // Met à jour un attribut 'data-' sur le corps du document en fonction de si le vote est autorisé ou non
        document.body.dataset.voteOk = voteOk;

        // Active ou désactive le bouton de vote en fonction de si le vote est autorisé
        if (voteOk) {
            bouton.removeAttribute('disabled');
        } else {
            bouton.setAttribute('disabled', !voteOk);
        }
        // Si l'utilisateur a déjà voté, ne procède pas plus loin
        if (a_deja_vote) return;
        // Construit le contenu HTML à afficher en fonction du nombre de candidats sélectionnés et des règles du vote
        if (selected()) {
            if (selected() == ag_max) {
                html.push(`Vous avez sélectionné ${ag_max} candidats.`);

            } else if (selected() < ag_min) {
                html.push(`Vous devez encore sélectionner au moins <strong>${ag_min - selected()} candidat(s)</strong>.`);
            } else {
                html.push(`Vous avez sélectionné <strong>${selected()} candidat(e)${selected() > 1 ? 's' : ''}</strong>.`);
                html.push(`<small>Vous pouvez encore en choisir ${ag_max - selected()}</small>`);
            }
        } else if (ag_min) {
            html.push(`Vous devez sélectionner  ${ag_min} candidat(e)s, et ${ag_max} au maximum.`);
        } else {
            html.push(`Vous pouvez sélectionner jusqu'à ${ag_max} candidat(e)s.`);

        }
        // Met à jour le contenu de la div de statut avec le HTML généré
        divStatus.innerHTML = html.join('<br>');
    }

    function updateVoteVisual() {
        document.querySelectorAll('form.candidats li').forEach(li => {
            if(li.querySelector('input:checked')){
                li.dataset.checked=true;
        } else {
            delete li.dataset.checked;
        }
        })
    }
    /**
     * retourne le nombre de candidats actuellement sélectionnés
     * @returns int
     */
    function selected() {
        return form.querySelectorAll('li[data-checked="true"]').length;

    }
    // Appelle la fonction displayStatus une première fois pour initialiser l'affichage 
    displayStatus();
})