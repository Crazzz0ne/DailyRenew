<?php

Breadcrumbs::for('dashboard.training.content.show', function ($trail) {
    $trail->parent('dashboard.training.all');
    $trail->push('Trainings');
});
Breadcrumbs::for('dashboard.training.content.create', function ($trail) {
    $trail->parent('dashboard.training.show', redirect()->getUrlGenerator()->previous());
    $trail->push('Create');
});
Breadcrumbs::for('dashboard.training.content.edit', function ($trail) {
    $trail->parent('dashboard.training.show', redirect()->getUrlGenerator()->previous());
    $trail->push('Training');
});
Breadcrumbs::for('dashboard.training.content.tag', function ($trail) {
    $trail->parent('dashboard.training.all', redirect()->getUrlGenerator()->previous());
    $trail->push('Tags');
});
