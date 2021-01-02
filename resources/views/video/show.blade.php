@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="video-player-container mb-3">
                    <video controls autoplay id="video-main">
                        <source src="{{ $video->videoUrls['source'] }}">
                    </video>
                </div>

                <h1 class="video-title">{{ $video->title }}</h1>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="video-info">
                            {{ $video->viewCount }} views <b>&middot;</b> {{ $video->created_at->format('F d, Y') }}
                        </div>
                    </div>
                    <div class="col-sm-6 text-right">
                        <video-rating video-key="{{ $video->unique_key }}"></video-rating>
                    </div>
                </div>
                <div class="mt-2">
                    @foreach($video->videoUrls as $res => $url)
                    <button
                        class="btn @if($res === 'source') btn-dark font-weight-bold @else btn-secondary @endif btn-sm quality-setter"
                        data-url="{{ $url }}"
                    >
                        @if($res === 'source')
                            Source ({{ $video->source_res ?? 'unknown' }})
                        @else
                            {{ $res }}p
                        @endif
                    </button>
                    @endforeach
                </div>
                <hr>
                <b>{{ $video->user->name }}</b>
                <p>{{ $video->description }}</p>
                <hr>
                <comment-section video-key="{{ $video->unique_key }}"></comment-section>
            </div>
            <div class="col-md-4">
                @foreach($recommendedVideos as $recommendedVideo)
                    <div class="row mb-3">
                        <div class="col-6 video-thumbnail-container">
                            <a href="{{ route('video.show', ['video' => $recommendedVideo->unique_key]) }}">
                                <img
                                    src="{{ $recommendedVideo->thumbnailUrl }}"
                                    class="video-thumbnail-img video-thumbnail-img-sm"
                                    alt="{{ $recommendedVideo->title }}"
                                    data-thumbnail-url="{{ $recommendedVideo->thumbnailUrl }}"
                                    data-preview-url="{{ $recommendedVideo->previewUrl }}"
                                >
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
