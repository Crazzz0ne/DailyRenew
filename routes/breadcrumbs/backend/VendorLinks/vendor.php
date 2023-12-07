<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 7/28/2019
 * Time: 1:31 AM
 */

// Vendor Backend
Breadcrumbs::for('dashboard.vendorlinks.index', function ($trail) {
    $trail->parent('dashboard.dashboard');
    $trail->push(('Partner Links'), route('dashboard.vendorlinks.index'));
});

Breadcrumbs::for('dashboard.vendorlinks.vendor.index', function ($trail) {
    $trail->parent('dashboard.vendorlinks.index');
    $trail->push(('Partner'), route('dashboard.vendorlinks.vendor.index'));
});

Breadcrumbs::for('dashboard.vendorlinks.vendor.store', function ($trail) {
    $trail->parent('dashboard.vendorlinks.vendor.index');
    $trail->push(__('labels.backend.access.vendor.index'), route('dashboard.vendorlinks.vendor.index'));
});

Breadcrumbs::for('dashboard.vendorlinks.vendor.create', function ($trail) {
    $trail->parent('dashboard.vendorlinks.vendor.index');
    $trail->push(__('labels.backend.access.vendor.create'), route('dashboard.vendorlinks.vendor.create'));
});

Breadcrumbs::for('dashboard.vendorlinks.vendor.edit', function ($trail) {
    $trail->parent(('dashboard.vendorlinks.vendor.index'), route('dashboard.vendorlinks.index'));
    $trail->push(('Edit'));
});

Breadcrumbs::for('dashboard.vendorlinks.vendor.show', function ($trail) {
    $trail->parent(('dashboard.vendorlinks.vendor.index'), route('dashboard.vendorlinks.index'));
    $trail->push(('Partner'));
});
