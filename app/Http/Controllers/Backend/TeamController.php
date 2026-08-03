<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreTeamMemberRequest;
use App\Http\Requests\Backend\UpdateTeamMemberRequest;
use App\Models\ActivityLog;
use App\Models\TeamMember;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public $user;

    public function __construct(private ImageUploadService $imageUploadService)
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('team.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any team member !');
        }

        $query = TeamMember::ordered();

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }

        $members = $query->paginate(15)->withQueryString();

        return view('backend.team.index', compact('members'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('team.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any team member !');
        }

        return view('backend.team.create');
    }

    public function store(StoreTeamMemberRequest $request)
    {
        if (is_null($this->user) || ! $this->user->can('team.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any team member !');
        }

        $data = $request->validated();
        $member = new TeamMember($data);

        if ($request->hasFile('profile_image')) {
            $member->profile_image = $this->imageUploadService->upload($request->file('profile_image'), 'team');
        }

        $member->save();

        ActivityLog::record('created', 'Team', $member->id, "Added team member \"{$member->name}\".");

        session()->flash('success', $member->name.' has been added to the team !!');
        return redirect()->route('admin.team.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('team.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any team member !');
        }

        $member = TeamMember::findOrFail($id);
        return view('backend.team.edit', compact('member'));
    }

    public function update(UpdateTeamMemberRequest $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('team.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any team member !');
        }

        $member = TeamMember::findOrFail($id);
        $member->fill($request->validated());

        if ($request->hasFile('profile_image')) {
            $member->profile_image = $this->imageUploadService->replace($request->file('profile_image'), 'team', $member->profile_image);
        }

        $member->save();

        ActivityLog::record('updated', 'Team', $member->id, "Updated team member \"{$member->name}\".");

        session()->flash('success', $member->name.' has been updated !!');
        return redirect()->route('admin.team.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('team.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete any team member !');
        }

        $member = TeamMember::find($id);

        if (! is_null($member)) {
            $this->imageUploadService->delete($member->profile_image);
            $member->delete();

            ActivityLog::record('deleted', 'Team', $id, "Removed team member \"{$member->name}\".");
        }

        session()->flash('success', 'Team member has been removed !!');
        return back();
    }
}
