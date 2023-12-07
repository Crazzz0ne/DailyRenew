<?php

//Breadcrumbs::for('dashboard.vendorlinks.link.index', function ($trail) {
//    $trail->parent('dashboard.dashboard');
//    $trail->push(__('labels.backend.access.link.show'));
//});

//Breadcrumbs::for('dashboard.vendorlinks.link.show', function ($trail) {
//    $trail->parent('dashboard.vendorlinks.vendor.index');
//    $trail->push(('Link'), route('dashboard.vendorlinks.vendor.index'));
//});

Breadcrumbs::for('dashboard.vendorlinks.linklogin.create', function ($trail) {
    $trail->parent('dashboard.vendorlinks.index');
    $trail->push(__('labels.backend.access.link.create'));
});

Breadcrumbs::for('dashboard.vendorlinks.linklogin.edit', function ($trail) {
    $trail->parent('dashboard.vendorlinks.link.index');
    $trail->push(__('labels.backend.access.link.edit'));
});
//
//Breadcrumbs::for('dashboard.vendorlinks.link.update', function ($trail) {
//    $trail->parent('dashboard.vendorlinks.link.index');
//    $trail->push(__('labels.backend.access.link.edit'));
//});
//
//Breadcrumbs::for('dashboard.vendorlinks.link.store', function ($trail) {
//    $trail->parent('dashboard.vendorlinks.link.index');
//    $trail->push('Store Link');
//});
