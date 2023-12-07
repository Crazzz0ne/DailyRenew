<?php


Breadcrumbs::for('dashboard.printable.all', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push('Printable Category', route('dashboard.printable.all'));
});

Breadcrumbs::for('dashboard.printable.index', function ($trail) {
    $trail->parent('dashboard.printable.all');
    $trail->push('Printable Category');
});

Breadcrumbs::for('dashboard.printable.show', function ($trail) {
    $trail->parent('dashboard.printable.all');
    $trail->push('Printable Index');
});

Breadcrumbs::for('dashboard.printable.category.edit', function ($trial) {
    $trial->parent('dashboard.printable.all');
    $trial->push('Edit');
});

Breadcrumbs::for('dashboard.printable.category.create', function ($trial) {
    $trial->parent('dashboard.printable.all');
    $trial->push('Create');
});

