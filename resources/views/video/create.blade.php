@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Upload</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('video.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="title-input">Title</label>

                                <input
                                    type="text"
                                    class="form-control @error('title') is-invalid @enderror"
                                    id="title-input"
                                    name="title"
                                    value="{{ old('title') }}"
                                >

                                @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="video-description-input">Description</label>

                                <textarea
                                    class="form-control @error('description') is-invalid @enderror"
                                    id="description-input"
                                    name="description"
                                >{{ old('description') }}</textarea>

                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="video-input">Video</label>

                                <input
                                    type="file"
                                    class="form-control @error('video') is-invalid @enderror"
                                    id="video-input"
                                    name="video"
                                    aria-describedby="video-help"
                                >

                                @error('video')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror

                                <small
                                    id="video-help"
                                    class="form-text text-muted"
                                >Supported file formats: mp4, webm</small>
                            </div>

                            {{-- TODO: custom thumbnail --}}
                            {{-- TODO: visibility (public/unlisted/private) --}}

                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
