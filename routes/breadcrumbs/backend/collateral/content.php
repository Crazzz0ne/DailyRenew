<?php

Breadcrumbs::for('dashboard.printable.content.show', function ($trail) {
    $trail->parent('dashboard.printable.index');
    $trail->push('Download');
});
Breadcrumbs::for('dashboard.printable.content.create', function ($trail) {
    $trail->parent('dashboard.printable.show', redirect()->getUrlGenerator()->previous());
    $trail->push('Create');
});
Breadcrumbs::for('dashboard.printable.content.edit', function ($trail) {
    $trail->parent('dashboard.printable.show', redirect()->getUrlGenerator()->previous());
    $trail->push('Printable');
});
