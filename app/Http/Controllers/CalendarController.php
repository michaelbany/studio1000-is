<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

class CalendarController extends Controller
{
    public function index(Request $request, User $user, string $token)
    {
        abort_if($user->calendar_token !== $token, 403);

        $projectId = $request->query('project');

        $schedules = $user->schedules()
            ->when($projectId, fn($q) => $q->where('project_id', $projectId))
            ->get();

        $calendar = Calendar::create("1000S for {$user->name}" . ($projectId ? " – {$schedules->first()?->project?->name}" : ''));

        foreach ($schedules as $schedule) {
            $projectName = $schedule->project?->name ?? 'Unknown Project';

            $calendar->event(Event::create("[$projectName] {$schedule->title}")
                ->startsAt($schedule->start_date)
                ->endsAt($schedule->end_date)
                ->address($schedule->location?->address ?? 'Unknown')
                ->description($schedule->description ?? '')
            );
        }

        return response($calendar->get(), 200, [
            'Content-Type' => 'text/calendar',
            'Content-Disposition' => 'inline; filename="' . str_replace(' ', '_', strtolower($user->name)) . ($projectId ? '_' . strtolower($schedules->first()?->project?->name) : '') . '_calendar.ics"',
        ]);
    }
}
