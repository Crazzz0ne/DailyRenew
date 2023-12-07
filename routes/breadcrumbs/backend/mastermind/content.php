<?php

Breadcrumbs::for('dashboard.mastermind.content.show', function ($trail) {
    $trail->parent('dashboard.mastermind.all');
    $trail->push('Mastermind');
});
Breadcrumbs::for('dashboard.mastermind.content.create', function ($trail) {
    $trail->parent('dashboard.mastermind.show', redirect()->getUrlGenerator()->previous());
    $trail->push('Create');
});
Breadcrumbs::for('dashboard.mastermind.content.edit', function ($trail) {
    $trail->parent('dashboard.mastermind.show', redirect()->getUrlGenerator()->previous());
    $trail->push('Training');
});
Breadcrumbs::for('dashboard.mastermind.content.tag', function ($trail) {
    $trail->parent('dashboard.mastermind.all', redirect()->getUrlGenerator()->previous());
    $trail->push('Tags');
});
