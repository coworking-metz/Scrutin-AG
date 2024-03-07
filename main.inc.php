<?php

include __DIR__ . '/ag/vote.inc.php';
include __DIR__ . '/ag/fonctions.inc.php';
include __DIR__ . '/ag/log.inc.php';
include __DIR__ . '/ag/reglages.inc.php';
include __DIR__ . '/ag/depouillement.inc.php';

add_action('wp_enqueue_scripts', function () {
    if (is_page('election-ca')) {
        $base = '/wp-content/mu-plugins/includes/ag';
        $css = $base . '/vote.css';
        wp_enqueue_style('vote-style', $css, [], filemtime(ABSPATH.$css));
        $js = $base . '/vote.js';
        wp_enqueue_script('vote-script', $js, array (), filemtime(ABSPATH.$js), true);
    }
});

function ag_candidats()
{
    include __DIR__ . '/ag/choix.inc.php';
}
