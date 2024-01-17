@extends('layouts.app')

@php
// GRADE PAGE
@endphp

<?php
$subject_name = $subject->subject_name;
$grade_id = $subject->grade_id;
$subject_id = $subject->subject_id;
$view_option = $subject->view_option;


$subject_extra_name = isset($tests[0]) ? $tests[0]->subject_extra_name : '';


$title = "";
$description = "";
$canonical = '';

if (!empty($subject_extra_name)) :
    $title =  $subject_extra_name . " Chapters - Flippy";
endif;

if (!empty($subject_id) && !empty($subject_extra_name)) :
    $canonical = 'https://' . $_SERVER['HTTP_HOST'] . '/grade/' . $subject_id . '/' . spacestoDashes($subject_extra_name);
endif;

$description_unit_titles = '';

foreach ($tests as $test) {
    $description_unit_titles .= ' ' . ucwords(testNameAfterColon($test->test_name)) . ',';
}

$description_unit_titles = rtrim($description_unit_titles, ',');
$description_unit_titles .= '.';

if (!empty($description_unit_titles) && !empty($subject_extra_name)) :
    $description = $description_unit_titles;
endif;
?>

@section('title', $title)
@section('description', $description)
@section('canonical', $canonical)

@push('head')
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
<style>
    .navbar.progress-nav {
        background-color: #fff;
        ;
    }  

    .v-autocomplete__content {
        top: 62px !important;
        left: 0px !important;
    }

    .v-menu {
        display: block !important;
    }

    .v-label--active {
        left: -15px !important;
    }

    .v-text-field__details {
        display: none !important;
    }

    .v-menu__content {
        top: 57px !important;
        left: 0px !important;
    }
</style>
@endpush

@push('foot')
<script src="{{ asset('assets/js/page/progress.js') }}"></script>
<script src="{{ asset('assets/js/libs/lazysizes.min.js') }}"></script>

{{-- Set vue.js into production mode --}}
<script>    
    Vue.config.devtools = true;
</script>

{{-- A production build of vue.js --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script> --}}

{{-- A development build of vue.js --}}
<script src="https://unpkg.com/vue@2.6.14/dist/vue.js"></script>

<script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
<script>
    new Vue({
        el: '#app',
        vuetify: new Vuetify(),
        data: () => ({
            selected: null,
            chapters: null,
        }),
        methods: {
            getChapters() {
                fetch('<?php echo route('ajax.get.chapters', $subject_extra_name); ?>')
                    .then(res => res.json())
                    .then(res => {
                        this.chapters = res
                    });
            },
            reloadPage() {
                location.assign('/grade/' + this.selected.id + '/' + this.selected.slug);
            }
        },
        mounted() {
            this.selected = { value: '<?php echo $subject_extra_name; ?>' };
            this.getChapters();
        },
    })
</script>
@endpush

@section('content')
<div class="container-fluid" style="background-color: #fff;" id="app">
    <div class="row">
        <div class="col-md-12 py-4">
            <?php if (!empty($subject_extra_name) && !empty($subject_name)) : ?>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 offset-sm-3 text-center py-4 mb-4">
                            <v-select v-model="selected" :items="chapters" return-object filled label="Select By Subject" @change="reloadPage" />
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($tests as $test)
                            @if ($test->public == 2)
                                @php
                                $png_350_file_path = asset('upload/subjects/' . strtolower($test->subject_name)  . '/chapter-names/' . $test->grade_id . '/' . removeColonSpacesToDashes($test->test_name) . '/' . removeColonSpacesToDashes($test->test_name) . '.png');
                                $webp_350_file_path = str_replace("png", "webp", $png_350_file_path);
                                $webp_350_file_path = str_replace("375", "375-webp", $webp_350_file_path);
                                @endphp
                                <div class="col-md-6 text-center pb-4">
                                    <a href="/chapter/{{ $test->test_id }}/{{ spacestoDashes(testNameAfterColon($test->test_name)) }}">
                                        <picture>
                                            <source srcset="{{ $webp_350_file_path }}" type="image/webp">
                                            <source srcset="{{ $png_350_file_path }}" type="image/png">
                                            <img class="lazyload rounded img-fluid" style="box-shadow: 0px 0px 0px 2px #9b9b9b;" width="300" height="225" alt="{{ testNameAfterColon($test->test_name) }}" title="{{ testNameAfterColon($test->test_name) }}" data-src="{{ $png_350_file_path }}" src="{{ asset('upload/subjects/science/default/gs-375-default.png') }}">
                                        </picture>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
@endsection