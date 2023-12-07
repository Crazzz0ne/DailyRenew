<?php

Breadcrumbs::for('dashboard.masstext.index', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push('Mass text' , route('dashboard.masstext.index'));
});

Breadcrumbs::for('dashboard.masstext.create', function ($trail) {
    $trail->parent('dashboard.masstext.index');
    $trail->push(__('labels.backend.access.office.create'), route('dashboard.masstext.create'));
});
