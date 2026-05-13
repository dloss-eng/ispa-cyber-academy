<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    {{-- 📄 CSS externe --}}
    <link rel="stylesheet" href="{{ public_path('css/report.css') }}">

</head>

<body>

    {{-- 🛡️ Header --}}
    <div class="report-title">
        🛡️ ISPA Cyber Academy
    </div>

    <div class="report-subtitle">
        {{ $etab->name }} · {{ date('d/m/Y') }}
    </div>

    {{-- 📊 Titre --}}
    <h1>
        Rapport : {{ $classe->name }}
    </h1>

    {{-- 📈 Stats --}}
    <p>
        Élèves : <strong>{{ $stats->count() }}</strong>
    </p>

    {{-- 📋 Tableau --}}
    <table>

        <thead>
            <tr>
                <th>Élève</th>
                <th>Points</th>
                <th>Leçons</th>
                <th>Quiz</th>
                <th>Score moy</th>
                <th>Badges</th>
            </tr>
        </thead>

        <tbody>

            @foreach($stats as $s)

                <tr>
                    <td>{{ $s['user']->name }}</td>
                    <td>{{ $s['points'] }}</td>
                    <td>{{ $s['progress'] }}</td>
                    <td>{{ $s['quizzes'] }}</td>
                    <td>{{ $s['avg_score'] }}%</td>
                    <td>{{ $s['badges'] }}</td>
                </tr>

            @endforeach

        </tbody>

    </table>

    {{-- 📎 Footer --}}
    <div class="report-footer">
        Généré par ISPA Cyber Academy · {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>
