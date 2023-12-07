<?php

Breadcrumbs::for('dashboard.instruction', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push(__('labels.backend.access.instruction.index'), route('dashboard.instruction'));
});
