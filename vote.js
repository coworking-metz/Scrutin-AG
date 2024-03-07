document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form.candidats');
    form.addEventListener('submit', e => {
        if (!confirm(`Vous avez choisi ${selected()} candidat(e)s. Voulez-vous valider votre choix ?\nVotre votre sera définitif et ne pourra être modifié. `)) {
            e.preventDefault();
            return false;
        }
    })
    form.querySelectorAll('input').forEach(input => input.addEventListener('input', (e) => {
        if (selected() - 1 == ag_max) {
            alert(`Vous avez atteinds la limite de ${ag_max} candidats.\nVous devez déselectionner une autre personne pour choisir celle-ci`)
            e.target.checked = false;
            return;
        }
        displayStatus();
    }));
    const divStatus = document.querySelector('.vote-status .contenu');
    const bouton = document.querySelector('.vote-status button');
    function displayStatus() {
        const total = form.querySelectorAll('li').length;
        const html = [];
        const voteOk = !a_deja_vote && selected() >= ag_min && selected() <= ag_max;
        document.body.dataset.voteOk = voteOk;

        if (voteOk) {
            bouton.removeAttribute('disabled');
        } else {
            bouton.setAttribute('disabled', !voteOk);
        }
        if (a_deja_vote) return;
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
        divStatus.innerHTML = html.join('<br>');
    }

    function selected() {
        return form.querySelectorAll('li:has(input:checked)').length;

    }
    displayStatus();
})