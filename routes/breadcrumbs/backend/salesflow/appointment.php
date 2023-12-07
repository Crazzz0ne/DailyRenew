<?php

Breadcrumbs::for('dashboard.calender', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push(__('labels.backend.access.office.index'), route('dashboard.salesFlow.appointment.index'));
});

Breadcrumbs::for('dashboard.salesFlow.appointment.create', function ($trail) {
    $trail->parent('dashboard.salesFlow.appointment.index');
    $trail->push(__('labels.backend.access.office.create'), route('dashboard.salesFlow.appointment.create'));
});

Breadcrumbs::for('dashboard.salesFlow.appointment.edit', function ($trail) {
    $trail->parent('dashboard.salesFlow.appointment.index');
    $trail->push(__('labels.backend.access.office.edit'));
});

Breadcrumbs::for('dashboard.salesFlow.appointment.show', function ($trail) {
    $trail->parent('dashboard.salesFlow.appointment.index');
    $trail->push(__('labels.backend.access.office.show'));
});

Breadcrumbs::for('dashboard.salesFlow.appointment.update', function ($trail) {
    $trail->parent('dashboard.salesFlow.appointment.index');
    $trail->push(__('labels.backend.access.office.update'));
});

Breadcrumbs::for('dashboard.unsignedRep', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push('Set Appointment');
});
//Breadcrumbs::for('dashboard.announcement.index', function($trail) {
//    $trail->parent('dashboard.dashboard');
//    $trail->push(__('labels.backend.access.announcement.index'), route('dashboard.announcement.index'));
//});
