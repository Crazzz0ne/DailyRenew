<?php

Breadcrumbs::for('dashboard.managerefficiency.index', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push('Manager Efficiency', route('dashboard.managerefficiency.index'));
});

Breadcrumbs::for('dashboard.managerefficiency.create', function ($trail) {
    $trail->parent('dashboard.managerefficiency.index');
    $trail->push('How Efficient Were You', route('dashboard.managerefficiency.create'));
});

Breadcrumbs::for('dashboard.office.managerefficiency.review', function ($trail) {
    $trail->parent('dashboard.managerefficiency.index');
    $trail->push('Edit');
});

Breadcrumbs::for('dashboard.managerefficiency.show', function ($trail) {
    $trail->parent('dashboard.managerefficiency.index');
    $trail->push('Show');
});


