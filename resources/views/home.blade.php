@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @foreach($videos as $video)
        <div class="col-md-3 mb-5">
            <a href="{{ route('video.show', ['video' => $video->unique_key]) }}">
                <img src="{{ $video->thumbnailUrl }}" class="w-100 mb-1">
            </a>
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
