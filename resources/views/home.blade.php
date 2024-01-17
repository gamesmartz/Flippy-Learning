@extends('layouts.app')

@php
// HOME PAGE
@endphp

@php
$grade_info = array();
@endphp

@section('title', 'Flippy - Play Hard, Study Smart')
@section('description', 'Master flashcards while playing your favorite video games.')
@section('canonical', config('app.url', 'https://flippy-ai.com'))

@section('header')
@endsection

@section('footer')
@endsection

@push('foot')
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
@endpush

@section('content')
<div class="container-fluid background-game first-section" style="background-position-x: 0; background-size: cover;">
    <div class="row">
        <div class="col-md-12">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 py-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-3 mb-2" style="display: flex; justify-content: center;">
                                <!-- <a href=""><img src="/assets/images/logo-beta-transparent.png" alt="Flippy Logo" title="Flippy Logo"></a> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container banner-vcenter" style="display: flex; justify-content: center; align-items: center; flex-direction: column;">
                    <div style="width: 90%; display: flex; justify-content: center;">
                        <div style="color: #fff; font-family: 'Raleway', sans-serif; text-align: center; background-color: rgba(35, 83, 144, 0.9); border-radius: 10px; padding: 15px 15px; font-weight: bold; font-size: 1.1em;">
                            <div style="font-size: 1.6em; line-height: 1.4; padding-top: 5px;"><span>Flippy</span></div>

                        </div>
                    </div>
                    <div style="width: 300px; margin-top: 120px; display: flex; flex-direction: column; justify-content: center;">
              
                            <a href="/progress?chapter=1st-4th%20Grade%20Science"><div class="btn" role="button" style="width: 100%; padding: 15px; background-color: rgba(35, 83, 144, 0.9); border-radius: 20px; text-transform: uppercase; color: #fff; margin-top: 10px;">Use Your Account</div></a>

                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="row" style="display: flex; justify-content: center;">

        <h1 style="text-align: center; font-family: 'Raleway', sans-serif; font-size: 1.6rem !important; color: #424242; padding: 15px 20px; padding-top: 35px; font-weight: initial; line-height: initial; line-height: 1.3em; max-width: 80%;">
            Play Hard, Study Smart.
        </h1>
        <div style="text-align: center; font-family: 'Raleway', sans-serif; font-size: 1.6rem !important; color: #424242; padding: 15px 20px; padding-top: 35px; font-weight: initial; line-height: initial; line-height: 1.3em; max-width: 80%;">
            Master flashcards while playing your favorite video games. For teachers: Get more free time. For students: Play the best games. Experience effective learning.
        </div>               

        <div class="col-md-12">
            <div style="padding-bottom: 30px; padding-top: 10px;" class="container">
                <div class="row" style="display: flex; justify-content: center; align-items: center;">    

                    <div style="text-align: center; margin-top: 0px; margin-bottom: 5px; font-family: 'Raleway', sans-serif; font-size: 1.6rem !important; color: #424242; padding: 10px 20px;">
                        How it works:
                    </div>
                    <div class="col-md-6" style="padding: 5px; display: flex; justify-content: center; flex-direction: column; padding: 5px; width: 100%;">
                        <a href="https://vimeo.com/794629378/e6a6956c6c" onclick="track_video_click('How Game Smartz Works')">
                            <picture style="display: flex; justify-content: center;">
                                <source data-srcset="/assets/images/index-images-small/video-how-game-smartz-works.webp" type="image/png">
                                <img class="lazyload" style="width: initial; max-width: 100%;  border-radius: 3px;" data-src="/assets/images/index-images-small/video-how-game-smartz-works.png" src="/assets/images/index-images-small/index-lazy-default.png" alt="How Flippy Works">
                            </picture>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div style="padding: 30px 0; padding-top: 40px;" class="container">
                <div class="row" style="display: flex; justify-content: center; align-items: center;">                        
                    <picture style="display: flex; justify-content: center;">
                        <source data-srcset="/assets/images/index-images-small/minecraft-and-gamesmartz.webp" type="image/webp">
                        <img class="lazyload" style="width: initial; max-width: 100%;" data-src="/assets/images/index-images-small/minecraft-and-gamesmartz.jpg" src="/assets/images/index-images-small/index-lazy-default.jpg" alt="Minecraft">
                    </picture>
                    <div style="text-align: center; margin-top: 0px; margin-bottom: 5px; font-family: 'Raleway', sans-serif; font-size: 1.2rem !important; color: #424242; padding: 5px 10px;">
                        An upward force on an object due to differences in pressure.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div style="padding: 30px 0;" class="container border-bottom">
                <div class="row" style="display: flex; justify-content: center; align-items: center;">                        
                    <picture style="display: flex; justify-content: center;">
                        <source data-srcset="/assets/images/index-images-small/dota2-and-gamesmartz.webp" type="image/webp">
                        <img class="lazyload" style="width: initial; max-width: 100%;" data-src="/assets/images/index-images-small/dota2-and-gamesmartz.jpg" src="/assets/images/index-images-small/index-lazy-default.jpg" alt="Dota 2">
                    </picture>
                    <div style="text-align: center; margin-top: 0px; margin-bottom: 5px; font-family: 'Raleway', sans-serif; font-size: 1.2rem !important; color: #424242; padding: 5px 10px;">
                        The force that causes an object to move in a circle.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div style="padding: 30px 0;" class="container border-bottom">
                <div class="row" style="display: flex; justify-content: center; align-items: center;">                        
                    <picture style="display: flex; justify-content: center;">
                        <source data-srcset="/assets/images/index-images-small/raft-and-gamesmartz.webp" type="image/webp">
                        <img class="lazyload" style="width: initial; max-width: 100%;" data-src="/assets/images/index-images-small/raft-and-gamesmartz.jpg" src="/assets/images/index-images-small/index-lazy-default.jpg" alt="Raft">
                    </picture>
                    <div style="text-align: center; margin-top: 0px; margin-bottom: 5px; font-family: 'Raleway', sans-serif; font-size: 1.2rem !important; color: #424242; padding: 5px 10px;">
                        The atomic orbital number of protons in an atom's nucleus.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div style="padding: 30px 0;" class="container border-bottom">
                <div class="row" style="display: flex; justify-content: center; align-items: center;">                        
                    <picture style="display: flex; justify-content: center;">
                        <source data-srcset="/assets/images/index-images-small/rocket-league-and-gamesmartz.webp" type="image/webp">
                        <img class="lazyload" style="width: initial; max-width: 100%;" data-src="/assets/images/index-images-small/rocket-league-and-gamesmartz.jpg" src="/assets/images/index-images-small/index-lazy-default.jpg" alt="Rocket League">
                    </picture>
                    <div style="text-align: center; margin-top: 0px; margin-bottom: 5px; font-family: 'Raleway', sans-serif; font-size: 1.2rem !important; color: #424242; padding: 5px 10px;">
                       The amount of matter in an object.
                    </div>
                </div>
            </div>
        </div>


        <div id="flashcards" class="col-md-12" style="padding-top: 20px;">
            <div class="container border-bottom" style=" padding-bottom: 50px;">  
                
                <div style="text-align: center; font-family: 'Raleway', sans-serif; font-size: 1.5rem !important; color: #424242; padding: 10px 20px ; padding-top: 40px;">
                    Flashcards used by students and teachers over a 100 million times on <a style="color: #598cb9; text-decoration: underline;" href="https://www.google.com/search?q=GameSmartz%20Definition%20Science&tbm=isch">Google images</a>.
                </div>   

                <div style="text-align: center; font-family: 'Raleway', sans-serif; font-size: 1.5rem !important; color: #424242; padding: 10px 20px 0 20px; ">
                    Supported Chapters:                     
                </div>

                <div style="font-size: 21px; padding-top: 25px; font-family: 'Raleway', sans-serif;" class="row">
                    <div class="col-md-6">
                        <div class="container">
                            <div  class="row">
                                <div style="display: flex; align-items: flex-start; flex-direction: column;"  class="col-12 p-0">

                                    {{-- Overall loop info: Loop through the supplied $gradeIds, calling get_test_data() every time. --}}
                                    @php
                                        // Used for looping through the get_test_data function.
                                        $gradeIds = [78, 79, 80, 81, 82, 83, 84, 85];
                                        // Used for manually setting the names per the corresponding subject->subject_id in the DB.
                                        $gradeNames = [
                                            78 => '1st-4th Grade Science',
                                            79 => '4th-6th Grade Science',
                                            80 => 'Earth Science',
                                            81 => 'Life Science',
                                            82 => 'Physical Science',
                                            83 => 'Biology',
                                            84 => 'Chemistry',
                                            85 => 'Marine Science'
                                            // Add more grades here as needed
                                        ];
                                    @endphp

                                 {{-- Loop through the $gradeIds array and putting the Grade details int the $gradeInfo array. --}}
                                @foreach ($gradeIds as $gradeId)
                                    @php
                                        $gradeInfo = get_test_data($gradeId);
                                    @endphp   

                                    <div class="nav-item">
                                        <a class="nav-link ps-0" style="color:#424242;" href="{{ route('grade', ['subject' => $gradeId]) }}/{{ spacestoDashes($gradeNames[$gradeId]) ?? 'Unknown Grade' }}">
                                            {{ $gradeNames[$gradeId] ?? 'Unknown Grade' }}
                                        </a>
                                    </div>                                    

                                    {{-- Loop through all the information in the $gradeInfo array    --}}
                                    @foreach ($gradeInfo as $gradeInfoItem)
                                        @if ($gradeInfoItem['public'] == 2)
                                            <div class="nav-item">
                                                <a style="font-size: 16px; margin-left: 15px; color:#598cb9;" class="nav-link ps-0" 
                                                    href="{{ route('chapter', [
                                                        'test' => $gradeInfoItem['test_id'],
                                                        'v' => spacestoDashes(testNameAfterColon($gradeInfoItem['test_name']))
                                                    ]) }}">
                                                        {{ testNameAfterColon($gradeInfoItem['test_name']) }}
                                                    </a>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="container">
                            <div  class="row">
                                <div style="display: flex; align-items: flex-start; flex-direction: column;"  class="col-12 p-0">
                                   
                                    {{-- Overall loop info: Loop through the supplied $gradeIds, calling get_test_data() every time. --}}
                                    @php
                                        // Used for looping through the get_test_data function.
                                        $gradeIds = [34, 35, 36, 37, 38, 39, 96];
                                        // Used for manually setting the names per the corresponding subject->subject_id in the DB.
                                        $gradeNames = [
                                            34 => '3rd-6th Grade US History',
                                            35 => '6th-8th Grade World History',
                                            36 => '6th-8th Grade Ancient Civilizations',
                                            37 => '6th-8th Grade Early US History',
                                            38 => '9th-12th Grade World History',
                                            39 => '9th-12th Grade US History',
                                            96 => 'Spanish 1',                                          
                                            // Add more grades here as needed
                                        ];
                                    @endphp

                                 {{-- Loop through the $gradeIds array and putting the Grade details int the $gradeInfo array. --}}
                                @foreach ($gradeIds as $gradeId)
                                    @php
                                        $gradeInfo = get_test_data($gradeId);
                                    @endphp
                                
                                    <div class="nav-item">
                                        <a class="nav-link ps-0" style="color:#424242;" href="{{ route('grade', ['subject' => $gradeId]) }}/{{ spacestoDashes($gradeNames[$gradeId]) ?? 'Unknown Grade' }}">
                                            {{ $gradeNames[$gradeId] ?? 'Unknown Grade' }}
                                        </a>
                                    </div> 

                                    {{-- Loop through all the information in the $gradeInfo array    --}}
                                    @foreach ($gradeInfo as $gradeInfoItem)
                                        @if ($gradeInfoItem['public'] == 2)
                                            <div class="nav-item">
                                                <a style="font-size: 16px; margin-left: 15px; color:#598cb9;" class="nav-link ps-0" 
                                                    href="{{ route('chapter', [
                                                        'test' => $gradeInfoItem['test_id'],
                                                        'v' => spacestoDashes(testNameAfterColon($gradeInfoItem['test_name']))
                                                    ]) }}">
                                                        {{ testNameAfterColon($gradeInfoItem['test_name']) }}
                                                    </a>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach                   

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid" style="background-repeat: repeat-y; background-color: #fff;">
            <div class="container pb-3">
                <div class="row col-md-12 justify-content-center">

                    <div class="row col-md-12 justify-content-center" style="padding: 10px 0 20px; margin-top: 20px;">
                        <div style="text-align: center; font-family: 'Raleway', sans-serif; font-size: 1.5rem !important; color: #424242; padding: 12px 2px;">                                
                            Play the best games. How to use with:
                        </div>                
                    </div>

                    {{-- Importing an array with all the games and links data. --}}
                    @php
                        include(base_path('app/Data/homeView.php'));
                    @endphp

                    {{-- Loop through the games array --}}
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

    <div class="col-md-12">
        <div style="padding: 30px 0;" class="container border-bottom">
            <div class="row" style="display: flex; justify-content: center; align-items: center;">

                <div style="text-align: center; margin-top: 0px; font-family: 'Raleway', sans-serif; font-size: 1.5rem !important; color: #424242; padding: 10px 20px;">
                    Incredible Results: Students were given a test in Science or History (shown below). They scored an average of 12%.
                </div>

                <picture style="display: flex; justify-content: center;">
                    <source data-srcset="/assets/images/index-images-small/test-before-game-smartz.webp" type="image/webp">
                    <img class="lazyload" style="width: initial; max-width: 100%;  border-radius: 5px;" data-src="/assets/images/index-images-small/test-before-game-smartz.png" src="/assets/images/index-images-small/index-lazy-default.png" alt="Test before using Flippy">
                </picture>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div style="padding: 30px 0;" class="container border-bottom">
            <div class="row" style="display: flex; justify-content: center; align-items: center;">
                <div style="text-align: center; font-family: 'Raleway', sans-serif; font-size: 1.5rem !important; color: #424242; padding: 10px 20px;">
                    After using GameTutor for an hour, they retook the same test and achieved an impressive average score of 83%!
                </div>

                <picture style="display: flex; justify-content: center;">
                    <source data-srcset="/assets/images/index-images-small/test-after-game-smartz.webp" type="image/webp">
                    <img class="lazyload" style="width: initial; max-width: 100%;  border-radius: 5px;" data-src="/assets/images/index-images-small/test-after-game-smartz.png" src="/assets/images/index-images-small/index-lazy-default.png" alt="Test after using Flippy">
                </picture>
            </div>
        </div>
    </div>


    </div>
</div>
<div class="col-md-12" id="index-reviews">
    <div style="padding: 30px 0;" class="container">
        <div class="row" style="display: flex; justify-content: center; align-items: center;">

            <div style="text-align: center; margin-top: 0px; margin-bottom: 15px; font-family: 'Raleway', sans-serif; font-size: 1.4rem !important; color: #424242; padding: 10px 20px;">
                A fun and simple way to learn about Science or History! -Jhonatan
            </div>
            <div style="text-align: center; margin-top: 0px; margin-bottom: 15px; font-family: 'Raleway', sans-serif; font-size: 1.4rem !important; color: #424242; padding: 10px 20px;">
                I answered only 4 science questions correct but after I answered them all! -Mia
            </div>
            <div style="text-align: center; margin-top: 0px; margin-bottom: 15px; font-family: 'Raleway', sans-serif; font-size: 1.4rem !important; color: #424242; padding: 10px 20px;">
                Before studying I was able to give maybe two correct answers.
                But after I was able to answer almost 90% correctly! -Ryan
            </div>
            <div style="text-align: center; margin-top: 0px; margin-bottom: 15px; font-family: 'Raleway', sans-serif; font-size: 1.4rem !important; color: #424242; padding: 10px 20px;">
                After playing games I re-took the same test and the result was impressive.
                I only had 5 incorrect answers compared to 0 correct before. -Tim
            </div>
            <div style="text-align: center; margin-top: 0px; margin-bottom: 15px; font-family: 'Raleway', sans-serif; font-size: 1.4rem !important; color: #424242; padding: 10px 20px;">
                Schools for pricing and custom solutions contact us below:
            </div>

        </div>
    </div>
</div>

</div>
</div>

<div style="margin-top: 40px;" class="container-fluid footer-section">
<div class="row">
    <div class="col-md-12">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-12 text-center">

                <div style="padding-top: 30px;" class="col-md-12 text-center py-2">
                    <a href="#" class="btn btn-green py-2 py-sm-3 get-started-btn-footer" role="button">Get Started</a>
                </div>

            </div>
        </div>

        <div class="col-md-12" style="padding-top: 40px;">
            <div class="container" style=" padding-bottom: 50px;">
                <div style="text-align: center; font-family: 'Raleway', sans-serif; font-size: 1.5rem !important; color: #b7b7b7; padding: 10px 20px;">
                    Subjects
                </div>
                <div style="font-size: 21px; padding-top: 25px; font-family: 'Raleway', sans-serif;" class="row">
                    <div class="col-md-6">
                        <div class="container">
                            <div  class="row">
                                <div style="display: flex; align-items: flex-start; flex-direction: column;"  class="col-12 p-0">
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=78&v=Science-4th-Grade">3rd-4th Grade Science</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=79&v=Science-5th-Grade">5th-6th Grade Science</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=80&v=Earth-Science">Earth Science</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=81&v=Life-Science">Life Science</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=82&v=Physical-Science">Physical Science</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=83&v=Biology">Biology</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=84&v=Chemistry">Chemistry</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=85&v=Marine-Science">Marine Science</a></div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="container">
                            <div  class="row">
                                <div style="display: flex; align-items: flex-start; flex-direction: column;"  class="col-12 p-0">
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=34&v=US-History-4th-6th">4th-6th Grade US History</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=35&v=World-History-6th-8th">6th-8th Grade World History</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=36&v=Ancient-Civilizations-6th-8th">6th-8th Grade Ancient Civilizations</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=37&v=Early-US-History-6th-8th">6th-8th Grade Early US History</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=38&v=World-History-9th-12th">9th-12th Grade World History</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=39&v=US-History-9th-12th">9th-12th Grade US History</a></div>
                                    <div class="nav-item"><a class="nav-link ps-0 grey-with-orange-hover" href="/grade?g=96&v=Spanish-1">3rd-9th Grade Spanish 1</a></div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

</div>

    <div style="background-color: #293036; display: flex; flex-direction: column; align-items: center;">


    <div style="margin-bottom: 10px; margin-top: 30px;"><a style=" padding: 10px 15px; font-size: 14px; font-family: 'Raleway', sans-serif; color: #d5d5d5;">contact: matt@gamesmartz.com</a></div>


        <div style="width: 100%; max-width: 935px; margin-top: 10px; margin-bottom: 35px; display: flex; justify-content: space-around; flex-wrap: wrap;">
            <div style="padding: 5px; display: flex; justify-content: center; align-items: center;">
                <div>
                    <a href="https://www.pinterest.com/gamesmartz/">
                        <img src="/assets/images/pinterest-stem.png" alt="Pinterest">
                    </a>
                </div>
            </div>
            <div style="padding: 5px; display: flex; justify-content: center; align-items: center;">
                <div>
                    <a href="https://www.google.com/search?q=GameSmartz Definition&tbm=isch">
                        <img src="/assets/images/google-images-stem.png" alt="Goolge Images">
                    </a>
                </div>
            </div>
            <div style="padding: 5px; display: flex; justify-content: center; align-items: center; margin-bottom: 20px;">
                <div>
                    <a href="https://seedfund.nsf.gov/topics/learning-cognition-technologies/">
                        <img src="/assets/images/nsf-consideration.png" alt="National Science Foundation Consideration">
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>



@endsection