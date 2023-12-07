<?php

Breadcrumbs::for('dashboard.auth.user.index', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push(__('labels.backend.access.users.management'), route('dashboard.auth.user.index'));
});

Breadcrumbs::for('dashboard.auth.user.deactivated', function ($trail) {
    $trail->parent('dashboard.auth.user.index');
    $trail->push(__('menus.backend.access.users.deactivated'), route('dashboard.auth.user.deactivated'));
});

Breadcrumbs::for('dashboard.auth.user.deleted', function ($trail) {
    $trail->parent('dashboard.auth.user.index');
    $trail->push(__('menus.backend.access.users.deleted'), route('dashboard.auth.user.deleted'));
});

Breadcrumbs::for('dashboard.auth.user.create', function ($trail) {
    $trail->parent('dashboard.auth.user.index');
    $trail->push(__('labels.backend.access.users.create'), route('dashboard.auth.user.create'));
});

Breadcrumbs::for('dashboard.auth.user.show', function ($trail, $id) {
    $trail->parent('dashboard.auth.user.index');
    $trail->push(__('menus.backend.access.users.view'), route('dashboard.auth.user.show', $id));
});

Breadcrumbs::for('dashboard.auth.user.edit', function ($trail, $id) {
    $trail->parent('dashboard.auth.user.index');
    $trail->push(__('menus.backend.access.users.edit'), route('dashboard.auth.user.edit', $id));
});

Breadcrumbs::for('dashboard.auth.user.change-password', function ($trail, $id) {
    $trail->parent('dashboard.auth.user.index');
    $trail->push(__('menus.backend.access.users.change-password'), route('dashboard.auth.user.change-password', $id));
});
