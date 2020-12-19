@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="video-player-container mb-3">
                    <video controls autoplay>
                        <source src="{{ asset('/storage/videos/'.$video->filename) }}">
                    </video>
                </div>

                <h1 class="video-title">{{ $video->title }}</h1>
                <div class="video-info">{{ $video->viewCount }} views <b>&middot;</b> {{ $video->created_at->format('F d, Y') }}</div>
                <hr>
                <b>{{ $video->user->name }}</b>
                <p>{{ $video->description }}</p>
            </div>
            <div class="col-md-4">
                <h2 class="h4 mb-3">Other videos</h2>
                @foreach($recommendedVideos as $recommendedVideo)
                    <div class="row mb-3">
                        <div class="col-6">
                            <a href="{{ route('video.show', ['video' => $recommendedVideo->unique_key]) }}">
                                <img src="{{ $recommendedVideo->thumbnailUrl }}" class="w-100">
                            </a>
                        </div>
                        <div class="col-6 p-0 video-thumbnail-info">
                            <a href="{{ route('video.show', ['video' => $recommendedVideo->unique_key]) }}">
                                <b>{{ $recommendedVideo->title }}</b><br>
                            </a>
                            {{ $recommendedVideo->viewCount }} views
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
