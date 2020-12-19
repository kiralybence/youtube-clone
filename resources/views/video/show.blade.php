@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div id="video-player-container" class="mb-3">
                    <video controls autoplay>
                        <source src="{{ asset('/storage/videos/'.$video->filename) }}">
                    </video>
                </div>

                <h1 id="video-title">{{ $video->title }}</h1>
                <div id="video-info">{{ $video->viewCount }} views <b>&middot;</b> {{ $video->created_at->format('F d, Y') }}</div>
                <hr>
                <b>{{ $video->user->name }}</b>
                <p>{{ $video->description }}</p>
            </div>
            <div class="col-md-4">
                <h3>Other videos</h3>
                @foreach($recommendedVideos as $recommendedVideo)
                    <a href="{{ route('video.show', ['video' => $recommendedVideo->unique_key]) }}">
                        {{ $recommendedVideo->title }}
                    </a><br>
                @endforeach
            </div>
        </div>
    </div>
@endsection
