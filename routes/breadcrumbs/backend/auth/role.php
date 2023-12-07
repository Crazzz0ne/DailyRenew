<?php

Breadcrumbs::for('dashboard.auth.role.index', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push(__('menus.backend.access.roles.management'), route('dashboard.auth.role.index'));
});

Breadcrumbs::for('dashboard.auth.role.create', function ($trail) {
    $trail->parent('dashboard.auth.role.index');
    $trail->push(__('menus.backend.access.roles.create'), route('dashboard.auth.role.create'));
});

Breadcrumbs::for('dashboard.auth.role.edit', function ($trail, $id) {
    $trail->parent('dashboard.auth.role.index');
    $trail->push(__('menus.backend.access.roles.edit'), route('dashboard.auth.role.edit', $id));
});
