<?php


namespace App\Repositories\Backend\Auth;


use App\Exceptions\GeneralException;
use App\Models\Office\OfficeStanding;
use App\Repositories\BaseRepository;
use Exception;
use Throwable;

class OfficeRepository extends BaseRepository
{
	public function model()
	{
		return OfficeStanding::class;

	}

	/**
	 * @param array $data
	 *
	 * @return Office
	 * @throws Throwable
	 * @throws Exception
	 */

	public function create(array $data): Office
	{
		return DB::transaction(function () use ($data) {
			$office = parent::create([
				'name' => $data['name'],
				'phone_number' => $data['phone_number'],
				'address' => $data['address'],
				'zip_code' => $data['zip_code'],
				'city' => $data['city'],
				'state' => $data['state'],
				'email' => $data['active'],

			]);
		});
	}

	public function update(Office $office, array $data): Office
	{
//        $this->checkUserByEmail($office, $data['email']);

		// See if adding any additional permissions
//        if (!isset($data['permissions']) || !count($data['permissions'])) {
//            $data['permissions'] = [];
//        }

		return DB::transaction(function () use ($office, $data) {
			if ($office->update([
				'name' => $data['name'],
				'address' => $data['address'],
				'city' => $data['zip_code'],
				'phone_number' => $data['phone_number'],
				'email' => $data['email'],
			])) {
//                // Add selected roles/permissions
//                $user->syncRoles($data['roles']);
//                $user->syncPermissions($data['permissions']);
//
//                event(new UserUpdated($user));

				return $office;
			}

			throw new GeneralException(__('exceptions.backend.access.users.update_error'));
		});
	}


}
