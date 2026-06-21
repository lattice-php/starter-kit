<?php
declare(strict_types=1);

return [
    'title' => 'Dashboard',
    'heading' => 'Dashboard',
    'welcome' => 'Welcome back, :name. You are viewing :team.',
    'cards' => [
        'server-driven' => [
            'title' => 'Server-driven pages',
            'body' => 'This dashboard is rendered from a Lattice page definition.',
        ],
        'team-context' => [
            'title' => 'Team context',
            'body' => 'Routes, breadcrumbs, and layout are resolved on the server.',
        ],
        'composable' => [
            'title' => 'Composable UI',
            'body' => 'Pages can compose cards, grids, forms, tables, and actions.',
        ],
        'next-steps' => [
            'title' => 'Next steps',
            'body' => 'Replace these starter metrics with real team activity as the kit grows.',
        ],
    ],
];
