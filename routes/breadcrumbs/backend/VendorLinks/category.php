<?php

// Category
Breadcrumbs::for('dashboard.vendorlinks.category.index', function ($trail) {
    $trail->parent('dashboard.vendorlinks.index');
    $trail->push(('Category'), route('dashboard.vendorlinks.category.index'));
});

Breadcrumbs::for('dashboard.vendorlinks.category.show', function ($trail) {
    $trail->parent('dashboard.vendorlinks.category.index');
    $trail->push('Show');
});

Breadcrumbs::for('dashboard.vendorlinks.category.create', function ($trail) {
    $trail->parent('dashboard.vendorlinks.category.index');
    $trail->push('Create', route('dashboard.vendorlinks.category.create'));
});

Breadcrumbs::for('dashboard.vendorlinks.category.edit', function ($trail) {
    $trail->parent('dashboard.vendorlinks.category.index', route('dashboard.vendorlinks.category.index'));
    $trail->push(__('labels.backend.access.category.edit'));
});

Breadcrumbs::for('dashboard.vendorlinks.category.update', function ($trail) {
    $trail->parent('dashboard.vendorlinks.category.index', route('dashboard.vendorlinks.category.index'));
    $trail->push(__('labels.backend.access.category.update'));
});

Breadcrumbs::for('dashboard.vendorlinks.category.store', function ($trail) {
    $trail->parent('dashboard.vendorlinks.category.index');
    $trail->push(__('labels.backend.access.vendor.index'), route('dashboard.vendorlinks.vendor.index'));
});

Breadcrumbs::for('dashboard.vendorlinks.category.destroy', function ($trail) {
    $trail->parent('dashboard.vendorlinks.category.index');
    $trail->push(__('labels.backend.access.vendor.index'), route('dashboard.vendorlinks.vendor.index'));
});
