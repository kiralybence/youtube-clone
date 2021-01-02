@extends('layouts.app')

@section('content')
<div class="container">
    @auth
    <h2 class="h4 mb-4">Latest videos from your subscriptions</h2>
    {{-- TODO: add placeholder message if there are no subscriptions --}}
    <div class="row mb-5">
        {{-- TODO: add placeholder message if there are no videos --}}
        @foreach($latestSub as $video)
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
    @endauth

    <h2 class="h4 mb-4">Latest videos</h2>
    <div class="row">
        @foreach($latestGlobal as $video)
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
