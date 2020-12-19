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
            </div>
        </div>
    </div>
@endsection
