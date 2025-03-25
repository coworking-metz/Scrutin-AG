<?php

/**
 * Inclusions des différents fichiers nécéssaires au fonctionnement
 */
include __DIR__ . '/vote.inc.php'; // fonctions relatives aux votes
include __DIR__ . '/users.inc.php'; // fonctions relatives aux utilisateurs et candidats
include __DIR__ . '/log.inc.php'; // fonctions de log
include __DIR__ . '/reglages.inc.php'; // fonctions pour lire les réglages de l'ag
include __DIR__ . '/depouillement.inc.php'; // fonctions relatives au dépouillement des votes

/**
 * Ajout des CSS et JS dédiées au formulaire de vote
 */
add_action('wp_enqueue_scripts', function () {
    if(is_admin()) return;
    if (is_page('election-ca')) {
        $base = '/wp-content/mu-plugins/includes/ag';
        $css = $base . '/vote.css';
        wp_enqueue_style('vote-style', $css, [], filemtime(ABSPATH.$css));
        $js = $base . '/vote.js';
        wp_enqueue_script('vote-script', $js, array (), filemtime(ABSPATH.$js), true);
    }
});

/**
 * Fonction utilisée par la page wordpress election-ca pour afficher le formulaire de vote
 */
function ag_candidats()
{
    include __DIR__ . '/formulaire.inc.php';
}

/**
* Page de liste des candidats
*/
if(isset($_GET['candidat_au_ca'])) {


	add_action('mu_plugin_loaded',function() {
		$meta_query = [
			[
				'key'     => 'candidat_au_ca',
				'value'   => '0',
				'compare' => '!=',
			],
		];

		add_action('pre_get_users', function ($query) use($meta_query){
			if (is_admin()) {

				$query->set('meta_query', $meta_query);
			}
		});

		add_filter('views_users', function ($views) use ($meta_query) {
			$user_query = new WP_User_Query([
				'meta_query' => $meta_query
			]);

			$count = $user_query->get_total();

			$class = 'current';
			$views['all'] = preg_replace('/class="current"/', '', $views['all']);
			$item = sprintf(
				'<a href="%s" class="%s">Candidats <span class="count">(%d)</span></a>',
				admin_url('users.php?candidats=1'),
				esc_attr($class),
				$count
			);
			return array_merge(['candidats' => $item], $views);
		});
	});

}

if(isset($_GET['votants'])) {

	add_action('mu_plugin_loaded',function() {
		$votants = tickets('/voting-members?minActivity=20');
		add_action('pre_user_query', function($user_query) use ($votants) {
			if (is_admin()) {
				global $wpdb;
					$emails = array_column($votants,'email');

				$placeholders = implode(', ', array_fill(0, count($emails), '%s'));
				$user_query->query_where .= $wpdb->prepare(" AND user_email IN ($placeholders)", $emails);
			}
		});
		add_filter('users_per_page', function($users_per_page) use ($votants) {
			return count($votants);
		});

		add_filter('views_users', function ($views) use ($votants) {

			$votants = tickets('/voting-members?minActivity=20');
			$count = count($votants);

			$class = 'current';
	        $views['all'] = preg_replace('/class="current"/', '', $views['all']);
			$item = sprintf(
				'<a href="%s" class="%s">Votants <span class="count">(%d)</span></a>',
				admin_url('users.php?votants=1'),
				esc_attr($class),
				$count
			);

		    return array_merge(['votants' => $item], $views);
		});
	});

}
/**
 * Génère un UUID en utilisant l'API https://www.uuidtools.com/api/generate/v1
 *
 * Cette fonction fait une requête à l'API uuidtools pour générer un UUID de version 1.
 * Elle utilise cURL pour faire la requête et retourne l'UUID sous forme de chaîne de caractères.
 * En cas d'échec de la requête, elle retournera null.
 *
 * @return string|null L'UUID généré ou null en cas d'échec.
 */
function generate_uuid() {
    $curl = curl_init("https://www.uuidtools.com/api/generate/v1");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    
    if ($response) {
        $uuids = json_decode($response);
        return $uuids[0] ?? null;
    }
    
    return null;
}