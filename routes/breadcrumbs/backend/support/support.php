<?php

Breadcrumbs::for('dashboard.auth.support.index', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push('Support', route('dashboard.auth.support.index'));
});
