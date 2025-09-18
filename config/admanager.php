<?php

return [
    'stockmanager' => ['base_url' => env('STOCKMANAGER_BASE_URL', 'https://stockmanager.in.ac-creteil.fr/api')],


    'entities' => [
        'DSDEN77' => [
            'label' => 'DSDEN 77',
            'ou_gl' => 'OU=DSDEN77,OU=Partages,DC=ad,DC=ac-creteil',
            'ou_gg' => 'OU=DSDEN77,OU=Services,DC=ad,DC=ac-creteil',
        ],
        'DSDEN93' => [
            'label' => 'DSDEN 93',
            'ou_gl' => 'OU=DSDEN93,OU=Partages,DC=ad,DC=ac-creteil',
            'ou_gg' => 'OU=DSDEN93,OU=Services,DC=ad,DC=ac-creteil',
        ],
        'DSDEN94' => [
            'label' => 'DSDEN 94',
            'ou_gl' => 'OU=DSDEN94,OU=Partages,DC=ad,DC=ac-creteil',
            'ou_gg' => 'OU=DSDEN94,OU=Services,DC=ad,DC=ac-creteil',
        ],
        'RECTORAT' => [
            'label' => 'Rectorat',
            'ou_gl' => 'OU=RECTORAT,OU=Partages,DC=ad,DC=ac-creteil',
            'ou_gg' => 'OU=RECTORAT,OU=Services,DC=ad,DC=ac-creteil',
        ],
    ],
    // Codes qui apparaissent après le préfixe GL
    'entity_codes' => [
        'DSDEN77' => '77',
        'DSDEN93' => '93',
        'DSDEN94' => '94',
        'RECTORAT' => 'REC',
    ],
    'prefix_gl' => 'GL',
    'prefix_gg' => 'GG',
];