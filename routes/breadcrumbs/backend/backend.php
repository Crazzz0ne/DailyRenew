<?php

Breadcrumbs::for('dashboard.dashboard', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('dashboard.dashboard'));
});

Breadcrumbs::for('dashboard.lead', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('dashboard.lead'));
});

Breadcrumbs::for('dashboard.user', function ($trail) {
    $trail->push(__('strings.backend.dashboard.title'), route('dashboard.user'));
});


require __DIR__ . '/auth.php';
require __DIR__ . '/announcement/announcement.php';
require __DIR__ . '/collateral/category.php';
require __DIR__ . '/collateral/content.php';
require __DIR__ . '/instruction/instruction.php';
require __DIR__ . '/log-viewer.php';
require __DIR__ . '/mastermind/category.php';
require __DIR__ . '/mastermind/content.php';
require __DIR__ . '/office/office.php';
require __DIR__ . '/office/officeStanding.php';
require __DIR__ . '/office/managerEfficiency.php';
require __DIR__ . '/training/category.php';
require __DIR__ . '/training/content.php';
require __DIR__ . '/salesflow/appointment.php';
require __DIR__ . '/salesflow/masstext.php';
require __DIR__ . '/VendorLinks/vendor.php';
require __DIR__ . '/VendorLinks/link.php';
require __DIR__ . '/VendorLinks/login.php';
require __DIR__ . '/VendorLinks/category.php';
require __DIR__ . '/support/support.php';
require __DIR__ . '/user/user.php';
