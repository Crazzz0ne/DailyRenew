<?php

Breadcrumbs::for('dashboard.announcement.index', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push(__('labels.backend.access.announcement.index'), route('dashboard.announcement.index'));
});

Breadcrumbs::for('dashboard.announcement.show', function ($trail) {
    $trail->parent('dashboard.announcement.index');
    $trail->push(__('labels.backend.access.announcement.show'));
});

Breadcrumbs::for('dashboard.announcement.create', function ($trail) {
    $trail->parent('dashboard.announcement.index');
    $trail->push(__('labels.backend.access.announcement.create'), route('dashboard.announcement.create'));
});

Breadcrumbs::for('dashboard.announcement.destroy', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push(__('labels.backend.access.announcement.destroy'), route('dashboard.announcement.destroy'));
});

Breadcrumbs::for('dashboard.announcement.edit', function ($trail) {
    $trail->parent('dashboard.announcement.index');
    $trail->push(__('labels.backend.access.announcement.edit'));
});
