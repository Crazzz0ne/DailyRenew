<?php

namespace App\Http;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\UserHasLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpseclib3\Crypt\Random;
use Spatie\Tags\Tag;

class OnboardUserTransfer extends Controller
{
    public function bringInUser(Request $request)
    {
        foreach ($request->payload as $payload) {
            $user = User::create($payload['user']);
            foreach ($payload['availability'] as $availability) {
                $user->availability()->create([
                    'start' => $availability['start'],
                    'end' => $availability['end'],
                    'user_id' => $user->id
                ]);
            }
            $tags = [];
            foreach ($payload['tags'] as $tag) {
                $tags[] = $tag['name']['en'];
                Tag::findOrCreate($tag['name']['en'], 'EligibleCity');
//          $user->attachTag($tag['name']['en']);
            }
            $user->attachTags($tags, 'EligibleCity');

            foreach ($payload['roles'] as $role) {
                $user->assignRole($role['name']);
            }
        }
    }


    public function bringInLead(Request $request)
    {

//        $url = "http://www.google.co.in/intl/en_com/images/srpr/logo1w.png";
//        $contents = file_get_contents($url);
//        $name = substr($url, strrpos($url, '/') + 1);
//        dd($name);
//        Storage::put($name, $contents);
////        dd('yeet');
        $leads = collect($request->payload);

        foreach ($leads as $totalLead) {

            $customer = Customer::create([
                'first_name' => $totalLead['customer']['first_name'], 'last_name' => $totalLead['customer']['last_name'],
                'email' => $totalLead['customer']['email'], 'cell_phone' => $totalLead['customer']['cell_phone'],
                'home_phone' => $totalLead['customer']['home_phone'], 'street_address' => $totalLead['customer']['street_address'],
                'city' => $totalLead['customer']['city'], 'state' => $totalLead['customer']['state'],
                'zip_code' => $totalLead['customer']['zip_code'], 'lat' => $totalLead['customer']['lat'], 'lng' => $totalLead['customer']['lng'],
                'language' => $totalLead['customer']['language'],

            ]);
            $utility = $totalLead['utility'];
            $leadUtility = LeadUtility::create([
                'kw_year_usage' => $utility['kw_year_usage'], 'power_company_id' => 5, 'average_bill' => $utility['average_bill'],
                'name_on_bill' => $utility['name_on_bill']
            ]);

            $salesPacket = $totalLead['salesPacket'];
            $newSalesPacket = SalesPacket::create([
                'ach_doc_signed' => $salesPacket['ach_doc_signed'], 'converted' => $salesPacket['converted'],
                'solar_agreement_signed' => $salesPacket['solar_agreement_signed'], 'proposal_doc_signed' => $salesPacket['proposal_doc_signed'],
                'cpuc_doc_signed' => $salesPacket['cpuc_doc_signed'], 'submitted_for_permitting_date' => $salesPacket['submitted_for_permitting_date'],
                'submitted' => $salesPacket['submitted'], 'sat' => $salesPacket['sat'], 'permitting_received_date' => $salesPacket['permitting_received_date'],
                'design_plan_sent_date' => $salesPacket['design_plan_sent_date']
            ]);


            $lead = $totalLead['lead'];

            if ($lead['office_id'] === 64) {
                $lead['office_id'] = 5;
            }
            if ($lead['origin_office_id'] === 64) {
                $lead['origin_office_id'] = 5;
            }
            if ($lead['office_id'] === 60) {
                $lead['office_id'] = 4;
            }
            if ($lead['origin_office_id'] === 60) {
                $lead['origin_office_id'] = 4;
            }

            $lead = Lead::create([
                'status_id' => $lead['status_id'], 'office_id' => $lead['office_id'],
                'origin_office_id' => $lead['origin_office_id'], 'customer_id' => $customer->id,
                'epc_id' => 1, 'epc_owner_id' => $lead['epc_owner_id'], 'stale' => $lead['stale'], 'source' =>
                    $lead['source'], 'sales_packet_id' => $newSalesPacket->id,
                'integrations_approved' => $lead['integrations_approved'], 'close_date' => $lead['close_date'],
                'jeopardy_id' => $lead['jeopardy_id'], 'credit_status_id' => $lead['credit_status_id'],
                'created_at' => $lead['created_at'], 'updated_at' => $lead['updated_at'], 'utility_id' => $leadUtility->id,
            ]);

            $reps = $totalLead['reps'];
            foreach ($reps as $rep) {
                $user = User::where('email', $rep['email'])->first();
                if ($user === null) {
                    continue;
                }
                UserHasLead::create([
                    'user_id' => $user->id, 'lead_id' => $lead->id, 'position_id' => $rep['pivot']['position_id'],
                    'created_at' => $rep['created_at'], 'updated_at' => $rep['updated_at']
                ]);
            }

//            create lead upload records
            $leadUploads = $totalLead['uploads'];
//            $this->processUploads($leadUploads, $lead);
            $this->processAppointments($totalLead['appointments']);

//


        }
    }
    function processUploads($leadUploads, $lead){
        foreach ($leadUploads as $leadUpload) {
//                save file to disk from link

            if (!$leadUpload['path']) {
                continue;
            }
            $response = Http::get($leadUpload['path']);
            $fileContents = $response->body();
//                \/([^\/\?]+)(?=\?X-Amz-Content-Sha256)
//                use the regex above on $leadUpload['path'] to get the file name
            $fileName = Str::random(5);

            preg_match('/\.([^\.]+)(?=\?)/', $leadUpload['path'], $matches);
            if (!isset($matches[1])) {
                continue;
            }
            $file_extension = $matches[1];

//             dd($response);
            $user = User::where('email', $leadUpload['user_email'])->first();
            if ($user === null) {
                $user = User::where('id', 1)->first();
            }



            $path = Storage::disk('s3')->put('lead/' . $lead->id . '/' . $leadUpload['type'] . '/' . $fileName . '.' . $file_extension,
                $fileContents, 'private');
            $lead->leadUploads()->create([
                'name' => $leadUpload['name'], 'path' => $path,
                'type' => $leadUpload['type'], 'size' => $leadUpload['size'],
                'user_id' => $user->id,
                'created_at' => $leadUpload['created_at'],
                'updated_at' => $leadUpload['updated_at']
            ]);
        }
    }

    function processAppointments($appointments)
    {
        foreach ($appointments as $appointment) {
            $user = User::where('email', $appointment['user_email'])->first();
            if ($user === null) {
                $user = User::where('id', 1)->first();
            }
            $createdBy = User::where('email', $appointment['created_by'])->first();
            if ($createdBy === null) {
                $createdBy = User::where('id', 1)->first();
            }
            $appointment = Appointment::create([
                'lead_id' => $lead->id, 'user_id' => $user->id,
                'start_time' => $appointment['start_time'], 'finish_time' => $appointment['finish_time'],
                'title' => $appointment['title'], 'comments' => $appointment['description'],
                'created_at' => $appointment['created_at'], 'updated_at' => $appointment['updated_at'], 'created_by' => $createdBy->id
            ]);
        }
    }

}

