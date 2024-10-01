<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;

use App\Models\Attendee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AttendeesStatsWidget extends BaseWidget
{
    public function getColumns(): int
    {
        return 2;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Attendee Counts', Attendee::count())
                ->color('success')
                ->chart(Attendee::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->pluck('total')
                    ->toArray()),
            Stat::make('Total Revenue', Attendee::sum('ticket_cost') / 100),
        ];
    }
}
