<?php


function ag_settings()
{
    return get_field('assemblee_generale', 'option');
}

function ag_max()
{
    return ag_settings()['scrutin']['ca_max'] ?? 9;
}
function ag_min()
{
    return ag_settings()['scrutin']['ca_min'] ?? 3;
}
function ag_date()
{
    return ag_settings()['details']['ag_date'] ?? 'ag';
}

