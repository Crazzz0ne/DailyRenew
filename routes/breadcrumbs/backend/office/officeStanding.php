<?php

Breadcrumbs::for('dashboard.officestanding.index', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push('Office Standing', route('dashboard.officestanding.index'));
});

Breadcrumbs::for('dashboard.officestanding.create', function ($trail) {
    $trail->parent('dashboard.officestanding.index');
    $trail->push('Create', route('dashboard.officestanding.create'));
});

Breadcrumbs::for('dashboard.officestanding.edit', function ($trail) {
    $trail->parent('dashboard.officestanding.index');
    $trail->push('Edit Office Standing');
});

Breadcrumbs::for('dashboard.officestanding.show', function ($trail) {
    $trail->parent('dashboard.officestanding.index');
    $trail->push('Office Standing');
});

Breadcrumbs::for('dashboard.office.officestandings.review', function ($trail) {
    $trail->parent('dashboard.officestanding.index');
    $trail->push('Review');
});

Breadcrumbs::for('dashboard.officestanding.update', function ($trail) {
    $trail->parent('dashboard.officestanding.index');
    $trail->push('Update Office Standing');
});
