{{-- Vue partielle : contenu d'une leçon (utilisée en AJAX) --}}

<div class="lp-lesson-header">
    <span class="lp-order">Leçon {{ $lesson->order }}</span>
    <h2 class="lp-title">{{ $lesson->title }}</h2>
</div>

{{-- Vidéo --}}
@if($lesson->video_url)
    @php
        $videoUrl = $lesson->video_url;
        $embedUrl = $videoUrl;
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $videoUrl, $m)) {
            $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
        } elseif (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $videoUrl, $m)) {
            $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
        }
        $isExternal = str_contains($embedUrl, 'youtube.com/embed') || str_contains($embedUrl, 'vimeo');
    @endphp
    <div class="lp-video">
        @if($isExternal)
            <iframe src="{{ $embedUrl }}"
                    class="lp-video-frame"
                    allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture"
                    allowfullscreen></iframe>
        @else
            <video controls class="lp-video-frame" preload="metadata">
                <source src="{{ $embedUrl }}" type="video/mp4">
            </video>
        @endif
    </div>
@endif

{{-- Contenu textuel --}}
<div class="lp-content">
    {!! $lesson->content !!}
</div>

{{-- Ressources PDF --}}
@if($lesson->resources->count() > 0)
    <div class="lp-resources">
        <div class="lp-resources-title">📎 Ressources</div>
        @foreach($lesson->resources as $r)
            {{--  Cloudinary = URL complète (https://...) | ancien = chemin local --}}
            <a href="{{ Str::startsWith($r->file_path, 'http') ? $r->file_path : asset('storage/'.$r->file_path) }}"
               target="_blank" class="lp-resource-link">
                📄 {{ $r->title }}
            </a>
        @endforeach
    </div>
@endif

{{-- Actions : Terminer / Quiz --}}
@php $quiz = $lesson->quizzes->first(); @endphp
<div class="lp-actions">
    @if($progress?->status !== 'completed')
        <form id="completeForm" action="{{ route('courses.lesson.complete', [$module, $lesson]) }}" method="POST">
            @csrf
            <button type="submit" class="btn-cyber btn-sm">
                 Terminer
            </button>
        </form>
    @else
        <span class="tag tag-g"> Terminée</span>
    @endif

    @if($quiz)
        <a href="{{ route('quiz.show', $quiz) }}" class="btn-cyber-outline btn-sm">
             Quiz
        </a>
    @endif
</div>

{{-- Navigation précédente / suivante --}}
<div class="lp-nav">
    @if($prevLesson)
        <button class="lp-nav-btn"
                onclick="loadLesson('{{ route('courses.lesson.ajax', [$module, $prevLesson]) }}', {{ $prevLesson->id }})">
            ← {{ Str::limit($prevLesson->title, 30) }}
        </button>
    @else
        <div></div>
    @endif

    @if($nextLesson)
        <button class="lp-nav-btn lp-nav-next"
                onclick="loadLesson('{{ route('courses.lesson.ajax', [$module, $nextLesson]) }}', {{ $nextLesson->id }})">
            {{ Str::limit($nextLesson->title, 30) }} →
        </button>
    @endif
</div>
