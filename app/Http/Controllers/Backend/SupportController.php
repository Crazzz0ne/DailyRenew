<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\Support\SupportMailable;

use App\Models\Support\Support;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mail;
use function url;

class SupportController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$supports = Support::where('id', '>', 0)->orderBy('id', 'desc')->get();

		return view('backend.support.index', compact('supports'));

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{

		// TODO: why did this not work
		$validator = Validator::make($request->all(), [
			'subject' => 'required',
			'body' => 'required',
		]);
		///
		///
		if (!$request->subject) {
			$subject = ' no subject';
		} else {
			$subject = $request->subject;
		}
		$support          = new Support();
		$support->subject = $subject;
		$support->body    = $request->body;
		$support->user_id = Auth::user()->id;
		$support->url     = url()->previous();
		$support->save();

		Mail::to('chrisfurman86@gmail.com')->queue(new SupportMailable($support));

		return redirect()->back()->with('success', 'Your message has been sent');

	}

	/**
	 * Display the specified resource.
	 *
	 * @param \App\Models\Support $support
	 * @return Response
	 */
	public function show(Support $support)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\Support $support
	 * @return Response
	 */
	public function edit(Support $support)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param \App\Models\Support $support
	 * @return Response
	 */
	public function update(Request $request, Support $support)
	{
		$update         = Support::where('id', '=', $request->id)->get()->first();
		$update->status = $request->status;
		$update->update();

		return redirect()->back()->with('success', 'Updated');

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\Support $support
	 * @return Response
	 */
	public function destroy(Support $support)
	{
		//
	}


	public function uploadCSV(Request $request)
    {
        request()->validate([
            'csv' => 'required|mimes:csv,txt'
        ]);

        $path = request()->file('csv')->getRealPath();
        return $file = file($path);;


    }
}
