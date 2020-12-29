@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @foreach($videos as $video)
        <div class="col-md-3 mb-5">
            <div class="video-thumbnail-container">
                <a href="{{ route('video.show', ['video' => $video->unique_key]) }}">
                    <img
                        src="{{ $video->thumbnailUrl }}"
                        class="video-thumbnail-img video-thumbnail-img-md mb-1"
                        alt="{{ $video->title }}"
                        data-thumbnail-url="{{ $video->thumbnailUrl }}"
                        data-preview-url="{{ $video->previewUrl }}"
                    >
                </a>
            </div>
            <div class="video-thumbnail-info">
                <a href="{{ route('video.show', ['video' => $video->unique_key]) }}">
                    <b>{{ $video->title }}</b><br>
                </a>
                {{ $video->user->name }}<br>
                {{ $video->viewCount }} views <b>&middot;</b> {{ $video->created_at->format('F d, Y') }}
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
