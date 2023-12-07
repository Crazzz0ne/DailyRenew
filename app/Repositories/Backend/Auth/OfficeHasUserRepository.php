<?php

namespace App\Repositories\Backend\Auth;

use app\Models\Auth\OfficeHasUser;
use App\Repositories\BaseRepository;

class OfficeHasUserRepository extends BaseRepository
{
	public function model()
	{
		return OfficeHasUserRepository::class;
	}

	public function create(array $data): OfficeHasUser
	{
		return DB::transaction(function () use ($data) {
			$officeHasUser = parent::create([
				'office_id' => $data['office_id'],
				'user_id' => $data['user_id'],
			]);
		});
	}

}
