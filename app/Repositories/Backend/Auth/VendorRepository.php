<?php


namespace App\Repositories\Backend\Auth;

use App\Models\Auth\Vendor;
use App\Repositories\BaseRepository;

class VendorRepository extends BaseRepository
{
	public function model()
	{
		return Vendor::class;
	}

	public function create(array $data): Vendor
	{
		return DB::transaction(function () use ($data) {
			$vendor = parent::create([
				'is_active' => $data['is_active'],
				'company_name' => $data['company_name'],
				'picture' => $data['picture'],
			]);
		});
	}
}
