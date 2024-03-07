<?php

/**
 * Récupère les paramètres de l'assemblée générale depuis la page d'options ACF.
 *
 * Cette fonction utilise ACF pour récupérer les paramètres de l'assemblée générale
 * stockés dans les options du thème. Elle retourne un tableau avec tous les paramètres
 * liés à l'assemblée générale.
 *
 * @return array Les paramètres de l'assemblée générale.
 */
function ag_settings()
{
    return get_field('assemblee_generale', 'option');
}

/**
 * Récupère le nombre maximum de candidats pour le conseil d'administration.
 *
 * Cette fonction retourne le nombre maximum de candidats pour le conseil d'administration
 * défini dans les paramètres de l'assemblée générale. Si la valeur n'est pas définie,
 * elle retourne une valeur par défaut de 9.
 *
 * @return int Le nombre maximum de candidats pour le CA.
 */
function ag_max()
{
    return ag_settings()['scrutin']['ca_max'] ?? 9;
}

/**
 * Récupère le nombre minimum de candidats pour le conseil d'administration.
 *
 * Cette fonction retourne le nombre minimum de candidats pour le conseil d'administration
 * défini dans les paramètres de l'assemblée générale. Si la valeur n'est pas définie,
 * elle retourne une valeur par défaut de 0.
 *
 * @return int Le nombre minimum de candidats pour le CA.
 */
function ag_min()
{
    return ag_settings()['scrutin']['ca_min'] ?? 0;
}

/**
 * Récupère la date de l'assemblée générale.
 *
 * Cette fonction retourne la date de l'assemblée générale telle que définie dans les
 * paramètres. Si la date n'est pas définie, elle retourne false par défaut.
 *
 * @return string La date de l'assemblée générale.
 */
function ag_date()
{
    return ag_settings()['details']['ag_date'] ?? false;
}
