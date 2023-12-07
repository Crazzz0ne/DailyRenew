<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 7/28/2019
 * Time: 1:31 AM
 */
Breadcrumbs::for('dashboard.office.index', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push(__('labels.backend.access.office.index'), route('dashboard.office.index'));
});

Breadcrumbs::for('dashboard.office.create', function ($trail) {
    $trail->parent('dashboard.office.index');
    $trail->push(__('labels.backend.access.office.create'), route('dashboard.office.create'));
});

Breadcrumbs::for('dashboard.office.edit', function ($trail) {
    $trail->parent('dashboard.office.index');
    $trail->push(__('labels.backend.access.office.edit'));
});

Breadcrumbs::for('dashboard.office.show', function ($trail) {
    $trail->parent('dashboard.office.index');
    $trail->push(__('labels.backend.access.office.show'));
});

Breadcrumbs::for('dashboard.office.update', function ($trail) {
    $trail->parent('dashboard.office.index');
    $trail->push(__('labels.backend.access.office.update'));
});
//Breadcrumbs::for('dashboard.announcement.index', function($trail) {
//    $trail->parent('dashboard.dashboard');
//    $trail->push(__('labels.backend.access.announcement.index'), route('dashboard.announcement.index'));
//});
