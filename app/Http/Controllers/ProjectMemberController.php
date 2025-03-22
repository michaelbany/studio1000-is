<?php

namespace App\Http\Controllers;

use App\Enums\ProjectRolesEnum;
use App\Enums\RolesEnum;
use App\Enums\MembersStatusEnum;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProjectMemberController extends Controller
{
    public function index(Project $project)
    {
        if (Gate::denies('view', $project)) {
            abort(404);
        }

        return Inertia::render('Projects/Members', [
            'project' => $project,
            'members' => $project->members
                ->filter(fn($project) => Gate::allows('view', $project->membership))
                ->values(),
            // 'members' => $project->members
            //     ->filter(fn($project) => Gate::allows('view', $project->membership))
            //     ->sortBy(fn($project) => $project->membership->role)
            //     ->values(),
            'slots' => $project->slots,
            'enum' => [
                'member_role' => ProjectRolesEnum::cases(),
                'member_status' => MembersStatusEnum::array(),
            ],
        ]);
    }

    /**
     * Creating slot
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'role' => ['required', Rule::enum(ProjectRolesEnum::class)],
            'label' => ['nullable', 'string', 'max:50'],
            'count' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        for ($i = 0; $i < $request->input('count'); $i++) {
            $slot = ProjectMember::makeEmpty($project);
            $slot->role = $request->input('role');
            $slot->label = $request->input('label');
            $slot->save();
        }

        return redirect()->back();
    }


    public function update(Request $request, Project $project, ProjectMember $member)
    {
        Gate::authorize('update', $project);

        $request->validate([
            'label' => ['nullable', 'string', 'max:50'],
        ]);

        $member->update($request->only('label'));

        return redirect()->back();
    }

    public function destroy(Project $project, ProjectMember $member)
    {
        Gate::authorize('member_delete', $project);
        
        if ($member->status === MembersStatusEnum::APPROVED) {
            $member->empty();
        } else {
            $member->delete();
        }

        return redirect()->back();
    }

    public function apply(Project $project, ProjectMember $member)
    {
        Gate::authorize('join', $project);

        $user = request()->user();

        $roleExists = $project->members()->where('user_id', $user->id)->wherePivot('role', $member->role)->exists();

        if ($roleExists) {
            return redirect()->back()->withErrors([
                'apply' => 'You have already applied for this role.',
            ]);
        }

        $pendingRequests = $project->members()->where('user_id', $user->id)->wherePivot('status', MembersStatusEnum::PENDING)->count();

        if ($pendingRequests >= 3) {
            return redirect()->back()->withErrors([
                'apply' => 'You already have 3 or more pending requests.',
            ]);
        }

        // $status = $user->role === RolesEnum::ADMIN || $project->owners()->contains($user) ? MembersStatusEnum::APPROVED : MembersStatusEnum::PENDING;

        $project->members()->attach($user->id, [
            'role' => $member->role,
            'label' => $member->label,
            'status' => MembersStatusEnum::PENDING,
        ]);

        return redirect()->back();
    }

    public function checkout(Request $request, Project $project, ProjectMember $member)
    {
        Gate::authorize('member_checkout', $project);

        $request->validate([
            'status' => ['required', Rule::enum(MembersStatusEnum::class)],
        ]);

        if ($request->input('status') === MembersStatusEnum::APPROVED->value) {
            $member->approve();
        } elseif ($request->input('status') === MembersStatusEnum::REJECTED->value) {
            $member->reject();
        } else {
            $member->pending();
        }

        return redirect()->back();
    }
}
