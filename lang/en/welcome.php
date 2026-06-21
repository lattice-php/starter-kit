<?php
declare(strict_types=1);

return [
    'title' => 'Lattice',
    'badge' => 'Lattice Starter Kit',
    'heading' => 'Server-driven React for Laravel',
    'subtitle' => 'Compose pages, forms, tables, and actions in PHP while React renders a typed, registry-driven interface.',
    'actions' => [
        'dashboard' => 'Open dashboard',
        'register' => 'Register',
    ],
    'capabilities' => [
        'forms' => [
            'title' => 'Forms first',
            'body' => 'Define form schemas and handlers as independent server-side objects, then mount several of them on the same page.',
        ],
        'tables' => [
            'title' => 'Tables as primitives',
            'body' => 'Render query-backed lists with sorting, filtering, pagination, and actions without requiring a resource class.',
        ],
        'pages' => [
            'title' => 'Pages compose everything',
            'body' => 'Use pages as the serialization boundary for layouts, interactive fragments, forms, and tables.',
        ],
    ],
];
