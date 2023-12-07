<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Class PermissionRoleTableSeeder.
 */
class PermissionRoleTableSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Create Roles
        $super = Role::create(['name' => config('access.users.super_role')]);
        $admin = Role::create(['name' => config('access.users.admin_role')]);
        $executive = Role::create(['name' => config('access.users.executive_role')]);
        $manager = Role::create(['name' => config('access.users.manager_role')]);
        $user = Role::create(['name' => config('access.users.default_role')]);
        $canvasser = Role::create(['name' => 'canvasser']);
        $sp1 = Role::create(['name' => 'sp1']);
        $sp2 = Role::create(['name' => 'sp2']);
        $integrations = Role::create(['name' => 'integrations']);
        $proposalBuilder = Role::create(['name' => 'proposal builder']);
        $salesRep = Role::create(['name' => 'sales rep']);
        $teamCaptain = Role::create(['name' => 'team captain']);

        // Create Permissions
        $permissionGroup = [];
        $permissionGroup['announcement'] = ['view announcement',
//            'administrate office announcement',
            'administrate all announcements'];

        $permissionGroup['user'] = ['view user',
//            'administrate office user',
            'administrate all users'];

        $permissionGroup['office'] = ['view office',
//            'administrate their office',
            'administrate all offices'];
        $permissionGroup['officeStanding'] = ['view office standings',
//            'approve mastermind',
            'administrate all office standings'];

        $permissionGroup['training'] = ['view training',
//            'administrate office training',
            'administrate all trainings'];

        $permissionGroup['printable'] = ['view printable',
//            'administrate office printable',
            'administrate all printables'];

        $permissionGroup['partnerlinks'] = ['view partnerlinks',
//            'administrate office vendorlink',
            'administrate all partnerlinks'];

        $permissionGroup['mastermind'] = ['view mastermind',
//            'approve mastermind',
            'administrate all masterminds'];

        $permissionGroup['logins'] = ['view logins',
//            'approve mastermind',
            'administrate all logins'];


        $permissionGroup['lead edit actions'] = [
            'edit credit',
            'edit requested system',
            'edit proposal',
            'edit system',
            'edit utility login',
            'edit ach',
            'edit utility',
            'edit NTS',
            'edit integrations status',
            'edit user on lead',
            'edit JIJ',

        ];

        $permissionGroup['lead view'] = [
            'view requested system',
            'view proposed system',
            'view system',
            'view ppw',
            'view utility logins',
            'view request-proposed',
            'view team'
        ];

        $permissionGroup['lead create actions'] = [
            'create sp1 request',
            'create integrations request',
            'create requested system',
            'create proposal builder request',
            'create close date',
            'create follow up',
            'create go back',
            'create site survey date',
            'create lead',
            'self gen',
            'create credit runner',
            'create sales force runner',
            'create change order'
        ];

        $permissionGroup['lead'] = [
            'administrate office',
            'administrate company',
            'team work',
            'see all roles',
            'closer'

        ];

        $permissionGroup['queue'] = [
            'accept sp1',
            'accept sp2',
            'accept proposal builder',
            'accept integrations',
            'accept credit app',
            'accept NTS',
            'accept credit runner',
            'accept sales force runner'
        ];

        $permissionGroup['commission'] = [
            'view commission',
            'view office commission',
            'view company commission',
            'edit commission',
        ];
        $permissionGroup['reporting']  = [
            'view reporting',
            'view office reporting',
            'view company reporting'
        ];



        $permissionGroup['backend'] = ['view backend'];

        foreach ($permissionGroup as $permissions) {
            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission]);
            }
        }

        // ALWAYS GIVE ADMIN ROLE ALL PERMISSIONS

        $admin->givePermissionTo(Permission::all());

        // Assign Permissions to other Roles
        $executive->givePermissionTo('view backend');
        $executive->givePermissionTo($permissionGroup['announcement']);
        $executive->givePermissionTo($permissionGroup['office']);
        $executive->givePermissionTo($permissionGroup['training']);
        $executive->givePermissionTo($permissionGroup['partnerlinks']);
        $executive->givePermissionTo($permissionGroup['mastermind']);
        $executive->givePermissionTo($permissionGroup['logins']);
        $executive->givePermissionTo($permissionGroup['printable']);
        $executive->givePermissionTo($permissionGroup['officeStanding']);
        $executive->givePermissionTo($permissionGroup['lead']);
        $executive->givePermissionTo($permissionGroup['lead create actions']);
        $executive->givePermissionTo($permissionGroup['lead view']);
        $executive->givePermissionTo($permissionGroup['lead edit actions']);
        $executive->givePermissionTo($permissionGroup['reporting']);

        $manager->givePermissionTo($permissionGroup['user'][0]);

        $manager->givePermissionTo(
            $permissionGroup['lead edit actions'][9],
            $permissionGroup['office'][0],
            $permissionGroup['lead'][0],

            'view backend'
        );

        $user->givePermissionTo(
            $permissionGroup['office'][0],
        );

        $canvasser->givePermissionTo(
            $permissionGroup['lead create actions'][0],
            $permissionGroup['lead create actions'][1],
            $permissionGroup['lead create actions'][6],
            $permissionGroup['lead create actions'][8],
            $permissionGroup['lead'][2],
            $permissionGroup['backend'],
        );

        $sp1->givePermissionTo(
            $permissionGroup['lead create actions'][8],
            $permissionGroup['queue'][0],
            $permissionGroup['lead edit actions'][0],
            $permissionGroup['lead create actions'][1],
            $permissionGroup['lead create actions'][4],
            $permissionGroup['lead create actions'][5],
            $permissionGroup['lead create actions'][8],
            $permissionGroup['lead view'][2],
            $permissionGroup['lead'][2],
            $permissionGroup['backend']
        );

        $sp2->givePermissionTo(
            $permissionGroup['lead create actions'][8],
            $permissionGroup['lead view'][0],
            $permissionGroup['lead view'][1],
            $permissionGroup['lead view'][2],
            $permissionGroup['lead view'][5],
            $permissionGroup['queue'][1],
            $permissionGroup['lead edit actions'][1],
            $permissionGroup['lead edit actions'][5],
            $permissionGroup['lead edit actions'][6],
            $permissionGroup['lead create actions'][1],
            $permissionGroup['lead create actions'][2],
            $permissionGroup['lead create actions'][3],
            $permissionGroup['lead create actions'][4],
            $permissionGroup['lead create actions'][5],
            $permissionGroup['lead create actions'][8],
            $permissionGroup['lead create actions'][12],
            $permissionGroup['lead'][2],
            $permissionGroup['lead'][4],
            $permissionGroup['backend']
        );
        $salesRep->givePermissionTo(
            $permissionGroup['lead create actions'][8],
            $permissionGroup['lead view'][0],
            $permissionGroup['lead view'][1],
            $permissionGroup['lead view'][2],
            $permissionGroup['lead view'][5],
            $permissionGroup['lead edit actions'][0],
            $permissionGroup['lead edit actions'][1],
            $permissionGroup['lead edit actions'][2],
            $permissionGroup['lead edit actions'][3],
            $permissionGroup['lead edit actions'][5],
            $permissionGroup['lead edit actions'][6],
            $permissionGroup['lead create actions'][1],
            $permissionGroup['lead create actions'][2],
            $permissionGroup['lead create actions'][3],
            $permissionGroup['lead create actions'][4],
            $permissionGroup['lead create actions'][5],
            $permissionGroup['lead create actions'][8],
            $permissionGroup['lead'][4],
            $permissionGroup['backend']
        );



        $integrations->givePermissionTo(
            $permissionGroup['lead view'][0],
            $permissionGroup['lead view'][1],
            $permissionGroup['lead view'][2],
            $permissionGroup['lead view'][4],
            $permissionGroup['lead edit actions'][6],
            $permissionGroup['lead edit actions'][8],
            $permissionGroup['queue'][3],
            $permissionGroup['queue'][6],
            $permissionGroup['queue'][7],
            $permissionGroup['lead'][3],
            $permissionGroup['backend']
        );

        $proposalBuilder->givePermissionTo(
            $permissionGroup['lead create actions'][8],
            $permissionGroup['lead create actions'][12],
            $permissionGroup['lead view'][0],
            $permissionGroup['lead view'][1],
            $permissionGroup['lead view'][2],
            $permissionGroup['lead view'][3],
            $permissionGroup['lead view'][4],
            $permissionGroup['lead view'][5],
            $permissionGroup['lead edit actions'][3],
            $permissionGroup['lead edit actions'][4],
            $permissionGroup['lead edit actions'][5],
            $permissionGroup['lead edit actions'][6],
            $permissionGroup['lead edit actions'][7],
            $permissionGroup['lead'][3],
            $permissionGroup['queue'][2],
            $permissionGroup['queue'][7],
            $permissionGroup['backend']
        );

        $manager->givePermissionTo('view backend');
        $teamCaptain->givePermissionTo('view team');

        $this->enableForeignKeys();
    }
}
