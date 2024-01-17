@extends('layouts.app')

@php
// CHAPTER PAGE
@endphp

@php
    $test_type = $test->test_type;
    $test_name = $test->test_name;
    $subject_extra_name = $test->subject_extra_name;
    $test_id = $test->test_id;

    if (!empty($test->test_id)) {
        $canonical = 'https://' . $_SERVER['HTTP_HOST'] . '/chapter/' . $test_id . '/' . spacestoDashes(testNameAfterColon($test_name));
        //$canonical = 'https://' . $_SERVER['HTTP_HOST'] . '/chapter?c=' . $test_id . '&v=' . spacestoDashes(testNameAfterColon($test_name));

    }

    $title = 'Chapter on ' . testNameAfterColon($test_name) . " - Flippy";

    $description = '';
    foreach ($question_results as $question_results_secondary_increment => $question_results_secondary_array) {
        $description .= ucwords($question_results_secondary_array['question_answer']) . ' Definition, ';
    } 
    $description = rtrim($description, ',');
    $description .= '.';
@endphp

{{-- Blade template content --}}
{{-- You can now use the defined variables --}}

@section('title', $title)
@section('description', $description)
@section('canonical', $canonical)

@push('foot')
<script src="{{ asset('assets/js/page/chapter.js?v=0.1') }}"></script>


<?php if (!empty($canonical)): ?>
    <script type="text/javascript">
        let canonicalURL = <?php echo e($canonical); ?>;
    </script>
<?php endif; ?>

@endpush

@section('content')
<div class="container-fluid" style="">
    <div class="row">
        <div style="padding-bottom: 50px;" class="col-md-12">
            <div class="container" style="padding: 0;">
                <div class="row">
                    <div class="col-sm-12 text-center py-1">

                        <div style="display: flex; justify-content: center; font-size: 1.2em; font-family: 'Raleway', sans-serif; padding-top: 15px; padding-bottom: 15px;">
                            <div style="color: #000;">

                                <span style="text-decoration: underline; "><a style="color: #000;" href="/grade/<?php echo $subject_id; ?>/<?php echo spacestoDashes($subject_extra_name); ?>"><?php echo $subject_extra_name; ?></a></span> /

                                <span style="color: #000;""><?php echo spacestoDashes(testNameAfterColon($test_name)); ?></span>

                            </div>
                        </div>

                            <div id="show-hide-all" style="display: flex; justify-content: center; cursor: pointer; font-weight: 500; font-size: 1.1rem; font-family: 'Raleway', sans-serif; color: #fff;">
                                <div>
                                    <div class="show-all-answers hide orange-box-chapter-pages" style="display: flex; justify-content: center; align-items: center; background-color: #00c164; width: 215px; height: 55px; padding: 10px 15px;"><div>Show All Answers</div><div style="width: 25px; height: 25px; margin-left: 6px"><img src="{{ asset('assets/images/eye-open.png') }}"></div></div>
                                    <div class="hide-all-answers show orange-box-chapter-pages" style="display: flex; justify-content: center; align-items: center; background-color: #00c164; width: 215px; height: 55px; padding: 10px 15px;"><div>Hide All Answers</div><div style="width: 25px; height: 25px; margin-left: 6px"><img src="{{ asset('assets/images/eye-close.png') }}"></div></div>
                                </div>
                            </div>

                    </div>
                </div>
                <div class="row" style="margin-top: 40px;">


                    @foreach ($question_results as $question_results_secondary_array)
                    @php
                        $image350Exists = !empty($question_results_secondary_array['image_350']) && file_exists(public_path($question_results_secondary_array['image_350']));
                        $id = spacestoDashes(strtolower($question_results_secondary_array['question_answer']));
                        $href = "definitions/" . $question_results_secondary_array['question_id'] . "/" . spacestoDashes($question_results_secondary_array['question_answer']);
                        $altTitle = ucwords($question_results_secondary_array['question_answer']) . ' Flashcard';
                        $png350FilePath = $image350Exists ? $question_results_secondary_array['image_350'] : $gs_350_default;
                        $webp350FilePath = str_replace(['png', '375'], ['webp', '375-webp'], $png350FilePath);
                        $png350NoTextPath = str_replace("375", "375-no-text", $png350FilePath);
                        $webp350NoTextPath = str_replace("375-webp", "375-no-text-webp", $webp350FilePath);
                    @endphp
                
                    @if ($image350Exists)
                        <div class="col-md-6 text-center pb-3 chapter-flip-card">
                            <a id="{{ $id }}" href="{{ url($href) }}">
                                <div style="min-height: 240px; display: flex; justify-content: center;" class="chapter-flip-card-inner chapter-transform-answer-shown">
                                    <div class="answer-card chapter-flip-card-front">
                                        <picture>
                                            <source data-srcset="{{ asset($webp350FilePath) }}" type="image/webp">
                                            <source data-srcset="{{ asset($png350FilePath) }}" type="image/png">
                                        </picture>
                                        <img class="lazyload rounded img-fluid" style="box-shadow: 0px 0px 0px 2px #9b9b9b;" width="300" height="225" alt="{{ $altTitle }}" title="{{ $altTitle }}" data-src="{{ asset($png350FilePath) }}" src="{{ asset('upload/subjects/science/default/gs-375-default.png') }}">
                                    </div>
                                    <div class="no-answer-card chapter-flip-card-back">
                                        <picture>
                                            <source data-srcset="{{ asset($webp350NoTextPath) }}" type="image/webp">
                                            <source data-srcset="{{ asset($png350NoTextPath) }}" type="image/png">
                                            <img class="lazyload rounded img-fluid" style="box-shadow: 0px 0px 0px 2px #2d9cfd;" width="300" height="225" alt="Flashcard without answer" data-src="{{ asset($png350NoTextPath) }}" src="{{ asset('upload/subjects/science/default/gs-375-default.png') }}">
                                        </picture>
                                    </div>
                                </div>
                            </a>
                            <div style="min-height: 55px; display: flex;  align-items: center; justify-content: center;">
                                <div class="show-answer show" style="display: flex; justify-content: center;">
                                    <span style="padding: 10px 20px;" class="show-answer-txt show orange-box-chapter-pages orange-box-chapter-pages-margin">Flip Flashcard</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-md-6 text-center pb-4">
                            <img class="lazyload rounded img-fluid" style="box-shadow: 0px 0px 0px 2px #2d9cfd;" width="375" height="281" src="{{ asset($gs_350_default) }}" title="{{ $question_results_secondary_array['image_350'] . ' Not Found' }}">
                        </div>
                    @endif
                @endforeach           
                </div>
            </div>
        </div>

        <div style="background-color: #293036; font-family: 'Raleway', sans-serif; display: flex; flex-direction: column; align-items: center; width: 100%;">

       
            <div style="display: flex; justify-content: center; font-size: 1.2em; font-family: 'Raleway', sans-serif; padding: 20px;">
                <div>
                    <div style="padding: 15px;">
                            <a style="font-size: 20px; color: #fff;" href="/" onclick="track_video_click('How to Study While Playing Video Games - Definition')">
                                <div style="background-color: #36a981; padding: 10px 15px; border-radius: 5px;;">
                                    What is Flippy?
                                </div>
                            </a>
                        </div>
                </div>
            </div>

            <div style="width: 100%; max-width: 935px; padding-top: 30px; padding-bottom: 20px; display: flex; justify-content: space-around; flex-wrap: wrap;">
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
                <div style="padding: 5px; display: flex; justify-content: center; align-items: center;">
                    <div>
                        <a href="https://seedfund.nsf.gov/topics/learning-cognition-technologies/">
                            <img src="/assets/images/nsf-consideration.png" alt="National Science Foundation Consideration">
                        </a>
                    </div>
                </div>
            </div>          

        </div>

    </div>

</div>

</div>
@endsection