<?php

use App\Models\Auth\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MoreRolesAndPermissions  extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Create Roles

        $regionalManager = Role::create(['name' => 'regional manager']);
        $d2dsp1 = Role::create(['name' => 'door 2 door sp1']);
        $roofAss = Role::create(['name' => 'roof assessor']);
        // Create Permissions
        $permissionGroup = [];


        $permissionGroup['regionalManager'] = [
            'create close date',
            'manage region',
            'view offices',
            'view contract amount',
            'view reporting'
        ];
        $permissionGroup['d2dsp1']  = [
            'accept d2d call center'
        ];

        $permissionGroup['roof assessor']  = [
            'view company reporting',
            'accept roof assessor',
            'view reporting',
            'administrate company',
            'accept change order',

        ];



        $permissionGroup['backend'] = ['view backend'];

        foreach ($permissionGroup as $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }
        }

        // ALWAYS GIVE ADMIN ROLE ALL PERMISSIONS




        $d2dsp1->givePermissionTo(
            $permissionGroup['d2dsp1']
        );

        $regionalManager->givePermissionTo(
            $permissionGroup['regionalManager']
        );

        $roofAss->givePermissionTo(
            $permissionGroup['roof assessor']
        );

        $this->enableForeignKeys();
    }
}
