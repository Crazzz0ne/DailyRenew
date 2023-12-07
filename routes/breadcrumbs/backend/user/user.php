<?php


Breadcrumbs::for('dashboard.user.account', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push('Account', route('dashboard.user.account'));
});


//Breadcrumbs::for('dashboard.training.show', function ($trail) {
//    $trail->parent('dashboard.training.all');
//    $trail->push('Trainings');
//});
//
//Breadcrumbs::for('dashboard.training.category.edit', function ($trial) {
//    $trial->parent('dashboard.training.all');
//    $trial->push('Edit');
//});
//
//Breadcrumbs::for('dashboard.training.category.create', function ($trial) {
//    $trial->parent('dashboard.training.all');
//    $trial->push('Create');
//});
