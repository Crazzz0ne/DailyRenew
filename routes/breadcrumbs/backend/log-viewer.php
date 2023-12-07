<?php

Breadcrumbs::for('log-viewer::dashboard', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push(__('menus.backend.log-viewer.main'), url('dashboard/log-viewer'));
});

Breadcrumbs::for('log-viewer::logs.list', function ($trail) {
    $trail->parent('log-viewer::dashboard');
    $trail->push(__('menus.backend.log-viewer.logs'), url('dashboard/log-viewer/logs'));
});

Breadcrumbs::for('log-viewer::logs.show', function ($trail, $date) {
    $trail->parent('log-viewer::logs.list');
    $trail->push($date, url('dashboard/log-viewer/logs/' . $date));
});

Breadcrumbs::for('log-viewer::logs.filter', function ($trail, $date, $filter) {
    $trail->parent('log-viewer::logs.show', $date);
    $trail->push(ucfirst($filter), url('dashboard/log-viewer/' . $date . '/' . $filter));
});
