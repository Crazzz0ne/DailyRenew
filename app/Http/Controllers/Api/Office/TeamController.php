<?php

namespace App\Http\Controllers\Api\Office;

use App\Http\Controllers\Controller;
use App\Http\Resources\Office\TeamResource;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\Office\Team;
use Google\Service\Directory\Users;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Office $office, Request $request)
    {
        return TeamResource::collection(Team::where('office_id', $office->id)->with('users')->get());
    }

    public function store(Office $office, Request $request)
    {
        $team = new Team();
        $team->office_id = $office->id;
        $team->name = $request->teamName;
        $team->captain_id = $request->teamCaptain;
        $team->save();
        foreach ($request->teamMembers as $member) {
            User::where('id', $member)->update(['team_id' => $team->id]);
        }
        if ($request->teamCaptain) {
            User::where('id', $request->teamCaptain)->update(['team_id' => $team->id]);
        }
    }

    public function addTeamMember(Office $office, Team $team, User $user, Request $request)
    {
        foreach ($request->teamMembers as $member) {
            User::where('id', $member)->update(['team_id' => $team->id]);
        }
        $user->team_id = $team->id;
    }

    public function addTeamCaptain(Office $office, Team $team, User $user, Request $request)
    {
        $team->captain_id = $user->id;
        $team->save();
      $user->team_id = $team->id;
      return $user->save();

    }

    public function update(Office $office, Team $team, Request $request)
    {
        return $team->update(['name' => $request->name, 'captain_id' => $request->userId]);
    }

    public function show(Office $office, Team $team, Request $request)
    {
        return new TeamResource($team);
    }

    public function delete(Office $office, Team $team, Request $request)
    {
        User::where('team_id', $team->id)->update(['team_id' => 0]);
        return $team->delete();
    }

    public function removeTeamMember(Office $office, Team $team, User $user, Request $request)
    {
        $user->team_id = 0;
        return $user->save();

    }

    public function removeTeamCaptain(Office $office, Team $team, User $user, Request $request)
    {
        $user->team_id = 0;
        $user->save();
        $team->captain_id = null;
        return $team->save();

    }

}
