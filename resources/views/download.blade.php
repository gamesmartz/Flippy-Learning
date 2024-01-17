@extends('layouts.app')

@section('title', 'Use GameSmartz with your Favorite Games')
@section('description', 'Use GameSmartz with your Favorite Games')

@push('foot')
<script src="{{ asset('assets/js/page/download.js') }}"></script>
@endpush

@section('content')
<div class="container-fluid" style="background-color: #fff;">
    <div class="container" style="padding-bottom: 120px;">
        <div class="row col-md-12 justify-content-center" style="">

            <div class="row col-md-12 justify-content-center" style="padding-top: 40px;">
                <div class="col-md-6 text-center">
                    <div style="margin-bottom: 20px; padding: 10px;" class="btn btn-primary btn-app-secondary"><a class="text-white" href="upload/desktop/GameSmartz.application" onclick="track_video_click('Download Game Smartz Beta')">Download Beta for Windows</a></div>
                </div>
            </div>

            <div class="row col-md-12 justify-content-center" style="padding: 10px 0 20px; margin-top: 20px;">
                <div style="font-size: 16px; color: #fff; display: flex; justify-content: center;" >
                    <div style="background-color: #5b5b5b; padding: 10px; border-radius: 5px;">Game Tutor is compatible with the following games: Check back for more games weekly.</div>
                </div>
            </div>

            @php 
            // Importing an array with all the games and links data.
            include(base_path('app/Data/homeView.php'));
            @endphp
           
            @foreach ($games as $game)
                <div class="col-md-5" style="max-width: 330px;">
                    <div class="card app-card mb-4">
                        <div class="card-body" style="padding-top: 5px;">
                            <div class="row align-items-center">
                                <div class="col-4 text-center" style="padding-right: 5px;">
                                    <a href="{{ $game['videoUrl'] }}" onclick="{{ $game['trackVideoClick'] }}"><img src="upload/subjects/science/chapter-icons/general/video/badge-video-yellow.png" class="img-thumbnail border-0" style="max-width: 90px;" alt="Video Badge" title="Video Badge"></a>
                                </div>
                                <div class="col-8 text-center" style="padding: 0 5px;">
                                    <a style="color: #598cb9;" href="{{ $game['linkUrl'] }}">
                                        <picture style="display: flex; justify-content: center;">
                                            <img class="lazyload" style="width: initial; max-width: 100%; max-height: 78px;" data-src="{{ $game['imageUrl'] }}" src="/assets/images/index-images-small/index-lazy-default.png" alt="{{ $game['imageAlt'] }}" title="{{ $game['title'] }}">
                                        </picture>
                                    </a>
                                </div>
                            </div>
                            <div style="text-align: center;"><a style="color: #598cb9;" href="{{ $game['linkUrl'] }}">{{ $game['title'] }}</a></div>
                        </div>
                    </div>
                </div>
            @endforeach

            </div>
        </div>
    </div> 
    
</div>
</div>
@endsection