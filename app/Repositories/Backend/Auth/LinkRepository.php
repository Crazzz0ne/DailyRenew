<?php

namespace App\Repositories\Backend\Auth;


use App\Models\Auth\Link;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class LinkRepository extends BaseRepository
{
	public function model()
	{
		return Link::class;
	}

	public function create(array $data): Link
	{
		return DB::transaction(function () use ($data) {
			$link = parent::create([
				'sort_id' => $data['sort_id'],
				'representative' => $data['representative'],
				'email' => $data['email'],
				'office_phone' => $data['office_phone'],
				'cell_phone' => $data['cell_phone'],
				'active' => $data['active'],
				'vendor_id' => $data['vendor_id'],
				'category_id' => $data['category_id'],
			]);
		});
	}
}
