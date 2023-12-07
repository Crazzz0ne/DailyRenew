<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 10/17/2019
 * Time: 1:31 AM
 */

Breadcrumbs::for('dashboard.mastermind.all', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push('Mastermind Category', route('dashboard.mastermind.all'));
});


Breadcrumbs::for('dashboard.mastermind.show', function ($trail) {
    $trail->parent('dashboard.mastermind.all');
    $trail->push('Mastermind');
});

Breadcrumbs::for('dashboard.mastermind.category.edit', function ($trial) {
    $trial->parent('dashboard.mastermind.all');
    $trial->push('Edit');
});

Breadcrumbs::for('dashboard.mastermind.category.create', function ($trial) {
    $trial->parent('dashboard.mastermind.all');
    $trial->push('Create');
});

