@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Search</div>

                <div class="card-body">
                    <h2 class="mb-4">Videos</h2>
                    @forelse($videos as $video)
                        <div class="row mb-3">
                            <div class="col-6 video-thumbnail-container">
                                <a href="{{ route('video.show', ['video' => $video->unique_key]) }}">
                                    <img
                                        src="{{ $video->thumbnailUrl }}"
                                        class="video-thumbnail-img video-thumbnail-img-lg"
                                        alt="{{ $video->title }}"
                                        data-thumbnail-url="{{ $video->thumbnailUrl }}"
                                        data-preview-url="{{ $video->previewUrl }}"
                                    >
                                </a>
                            </div>
                            <div class="col-6 p-0 video-thumbnail-info">
                                <a href="{{ route('video.show', ['video' => $video->unique_key]) }}">
                                    <b>{{ $video->title }}</b><br>
                                </a>
                                {{ $video->viewCount }} views <b>&middot;</b> {{ $video->created_at->format('F d, Y') }}
                            </div>
                        </div>
                    @empty
                        No videos found.
                    @endforelse

                    <hr class="my-5">

                    <h2 class="mb-4">Channels</h2>
                    @forelse($users as $user)
                        {{-- TODO: proper user display once they'll have a public profile available --}}
                        {{ $user->name }}<br>
                    @empty
                        No channels found.
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
