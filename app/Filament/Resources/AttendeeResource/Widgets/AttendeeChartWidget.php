<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;

use App\Models\Attendee;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AttendeeChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Attendee Signups';

    public function getColumns(): int
    {
        return 1;
    }

    protected function getData(): array
    {
        $attendees = Attendee::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Attendees created',
                    'data' => array_values($attendees),
                ],
            ],
            'labels' => array_keys($attendees),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
