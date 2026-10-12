<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ __('jejak.candidates.profile.title', ['code' => $candidate->code]) }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 16px; color: #0b1f4b; margin: 0 0 4px; }
        h2 { font-size: 12px; color: #0b1f4b; border-bottom: 2px solid #f5b52e; padding-bottom: 2px; margin-top: 16px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #cbd5e1; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background: #eef2f7; }
        .muted { color: #64748b; }
        .footer { margin-top: 24px; font-size: 9px; color: #64748b; border-top: 1px solid #cbd5e1; padding-top: 6px; }
    </style>
</head>
<body>
    <h1>{{ __('jejak.candidates.profile.heading') }}</h1>
    <div class="muted">{{ __('jejak.ministry') }} · {{ __('jejak.candidates.profile.directorate') }}</div>

    <h2>{{ __('jejak.candidates.profile.sections.identity') }}</h2>
    <table>
        <tr>
            <th>{{ __('jejak.candidates.profile.fields.code') }}</th><td>{{ $candidate->code }}</td>
            <th>{{ __('jejak.candidates.profile.fields.label') }}</th><td>{{ $candidate->label }}</td>
        </tr>
        <tr>
            <th>{{ __('jejak.candidates.profile.fields.upt') }}</th><td>{{ $candidate->upt->name }}</td>
            <th>{{ __('jejak.candidates.profile.fields.competency_field') }}</th><td>{{ $candidate->competency_field?->getLabel() }}</td>
        </tr>
        <tr>
            <th>{{ __('jejak.candidates.profile.fields.designated_at') }}</th><td>{{ $candidate->designated_at?->translatedFormat('d F Y') }}</td>
            <th>{{ __('jejak.candidates.profile.fields.approved_by_position') }}</th><td>{{ $candidate->approved_by_position }}</td>
        </tr>
    </table>

    @if ($latest)
        <h2>{{ __('jejak.candidates.profile.sections.latest_assessment', ['date' => $latest->assessed_at->translatedFormat('d F Y')]) }}</h2>
        <table>
            <tr>
                <th>{{ __('jejak.candidates.profile.fields.component') }}</th>
                <th>{{ __('jejak.candidates.profile.fields.result') }}</th>
                <th>{{ __('jejak.candidates.profile.fields.conclusion') }}</th>
            </tr>
            <tr><td>{{ __('jejak.candidates.profile.rows.competency') }}</td><td>{{ $latest->competency_total }} / 60</td><td>{{ $latest->competency_level->getLabel() }}</td></tr>
            <tr><td>{{ __('jejak.candidates.profile.rows.confidence') }}</td><td>{{ $latest->confidence_total }} / 100</td><td>{{ $latest->confidence_level->getLabel() }}</td></tr>
            <tr>
                <td>{{ __('jejak.candidates.profile.rows.history') }}</td>
                <td>
                    @if (filled($latest->trainings_attended))
                        <div>{{ __('jejak.assessments.trainings_attended') }}: {!! nl2br(e($latest->trainings_attended)) !!}</div>
                    @endif
                    @if (filled($latest->certifications))
                        <div>{{ __('jejak.assessments.certifications') }}: {!! nl2br(e($latest->certifications)) !!}</div>
                    @endif
                </td>
                <td>{!! nl2br(e($latest->history_conclusion)) !!}</td>
            </tr>
            <tr><td>{{ __('jejak.candidates.profile.rows.observation') }}</td><td>{!! nl2br(e($latest->officer_observation)) !!}</td><td>{!! nl2br(e($latest->observation_conclusion)) !!}</td></tr>
            <tr><th>{{ __('jejak.candidates.profile.rows.final_recommendation') }}</th><td colspan="2">{{ $latest->final_recommendation->getLabel() }}</td></tr>
        </table>

        <h2>{{ __('jejak.candidates.profile.sections.competency_breakdown') }}</h2>
        <table>
            <tr>
                <th>{{ __('jejak.candidates.profile.fields.dimension') }}</th>
                <th>{{ __('jejak.candidates.profile.fields.subtotal') }}</th>
                <th>{{ __('jejak.candidates.profile.fields.average') }}</th>
            </tr>
            @foreach ($latest->competencyDimensionRows() as $row)
                <tr><td>{{ $row['label'] }}</td><td>{{ $row['subtotal'] }}/{{ $row['max'] }}</td><td>{{ number_format($row['average'], 2, ',', '.') }}</td></tr>
            @endforeach
        </table>

        <h2>{{ __('jejak.candidates.profile.sections.confidence_breakdown') }}</h2>
        <table>
            <tr>
                <th>{{ __('jejak.candidates.profile.fields.section') }}</th>
                <th>{{ __('jejak.candidates.profile.fields.subtotal') }}</th>
                <th>{{ __('jejak.candidates.profile.fields.average') }}</th>
            </tr>
            @foreach ($latest->confidenceSectionRows() as $row)
                <tr><td>{{ $row['label'] }}</td><td>{{ $row['subtotal'] }}/{{ $row['max'] }}</td><td>{{ number_format($row['average'], 2, ',', '.') }}</td></tr>
            @endforeach
        </table>
    @endif

    <h2>{{ __('jejak.candidates.profile.sections.history') }}</h2>
    <table>
        <tr>
            <th>{{ __('jejak.candidates.profile.fields.date') }}</th>
            <th>{{ __('jejak.candidates.profile.fields.competency_short') }}</th>
            <th>{{ __('jejak.candidates.profile.fields.confidence_short') }}</th>
            <th>{{ __('jejak.candidates.profile.fields.recommendation') }}</th>
        </tr>
        @foreach ($candidate->assessments as $assessment)
            <tr>
                <td>{{ $assessment->assessed_at->translatedFormat('d M Y') }}</td>
                <td>{{ $assessment->competency_total }}/60</td>
                <td>{{ $assessment->confidence_total }}/100</td>
                <td>{{ $assessment->final_recommendation?->getLabel() ?? __('jejak.candidates.assessments.columns.final_recommendation_placeholder') }}</td>
            </tr>
        @endforeach
    </table>

    @if (filled($candidate->notes))
        <h2>{{ __('jejak.candidates.profile.sections.notes') }}</h2>
        <p>{!! nl2br(e($candidate->notes)) !!}</p>
    @endif

    <div class="footer">
        {{ __('jejak.candidates.profile.footer') }}
        {{ __('jejak.candidates.profile.printed_at', ['datetime' => now()->translatedFormat('d F Y H:i')]) }}
    </div>
</body>
</html>
