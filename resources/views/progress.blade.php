@extends('layouts.app')

@php
// PROGRESS PAGE
@endphp

@section('title', 'Progress Badges | GameSmartz')
@section('description', 'Progress badges, track your progress. | GameSmartz')

@push('head')
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
<style>
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
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
<script>
    new Vue({
        el: '#app',
        vuetify: new Vuetify(),
        data: () => ({
            descriptionLimit: 60,
            entries: [],
            isLoading: false,
            search: null,
            selected: null,
            chapters: null,
            selected_chapter: null,
            hideNoData: true
        }),
        methods: {
            getChapters() {
                fetch('<?php echo route('ajax.get.chapters', $chapter); ?>')
                    .then(res => res.json())
                    .then(res => {
                        this.chapters = res
                    });
            },
            reloadPage(type, text) {
                if (type == 'chapter') {
                    location.assign('/progress?chapter=' + text);
                }
                if (type == 'search') {
                    location.assign('/progress?search=' + text);
                }
            }
        },
        computed: {
            fields() {
                if (!this.search) return []

                return Object.keys(this.search).map(key => {
                    return {
                        key,
                        value: this.search[key] || 'n/a',
                    }
                })
            },
            items() {
                return this.entries.map(entry => {
                    const Description = entry.Description.length > this.descriptionLimit ?
                        entry.Description.slice(0, this.descriptionLimit) + '...' :
                        entry.Description
                    return Object.assign({}, entry, {
                        Description
                    })
                })
            },
        },
        watch: {
            search(val) {
                // Items have already been loaded
                if (this.items.length > 0) return

                // Items have already been requested
                if (this.isLoading) return

                this.isLoading = true

                // Lazily load input items
                fetch('<?php echo route('ajax.search.chapters'); ?>')
                    .then(res => res.json())
                    .then(res => {
                        const {
                            count,
                            entries
                        } = res
                        this.count = count
                        this.entries = entries
                    })
                    .catch(err => {
                        console.log(err)
                    })
                    .finally(() => {
                        this.isLoading = false;
                        this.hideNoData = false;
                    })
            },
        },
        mounted() {
            <?php if (!empty($search)) : ?>
                this.selected = {
                    API: '<?php echo $search ?>',
                    Description: '<?php echo $search ?>',
                };
                this.search = {
                    API: '<?php echo $search ?>',
                    Description: '<?php echo $search ?>',
                };
                this.hideNoData = false;
            <?php endif; ?>
            <?php if (!empty($chapter)) : ?>
                this.selected_chapter = '<?php echo $chapter ?>';
            <?php endif; ?>
            this.getChapters();
        },
    })
</script>
@endpush

@section('content')
<?php
$pageData = [];

$sql = "SELECT
    `subject`.seo_subject_image,
    test.subject_extra_name,
    `subject`.subject_id,
    grade.grade_name
    FROM
    `subject`
    INNER JOIN test ON `subject`.subject_id = test.subject_id
    INNER JOIN grade ON `subject`.grade_id = grade.grade_id ";
if (!empty($search)) :
    $sql .= "INNER JOIN question ON `test`.test_id = question.test_id";
endif;
$sql .= " WHERE `subject`.view_option = '1' AND ";
if (!empty($search)) :
    $sql .= "`question`.question_answer = '" . $search . "' AND ";
endif;
if (!empty($chapter)) :
    $sql .= "`test`.subject_extra_name = '" . $chapter . "' ";
else :
    $sql .= "`subject`.subject_name IN ('Science','History','Spanish')";
endif;

$sql .= "
    GROUP BY 
    `subject`.subject_id
    ORDER BY             
    FIELD(`subject`.subject_name,'Science','History','Spanish'),
    `subject`.grade_id ASC
";
$stmt = $db->prepare($sql);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($seo_subject_image, $subject_extra_name, $subject_id, $grade_name);

while ($stmt->fetch()) {

    /* for every item in the question array loop through the answer array */
    $sql = "
    SELECT 
        test.test_id, test.test_name, test.public, test.school_year,
        test.subject_extra_name, subject.subject_name, subject.grade_id                
        FROM subject                 
        INNER JOIN test ON test.subject_id=subject.subject_id";
    if (!empty($search)) :
        $sql .= " INNER JOIN question ON `test`.test_id = question.test_id";
    endif;
    $sql .= " WHERE subject.subject_id = ?";
    if (!empty($search)) :
        $sql .= " AND `question`.question_answer = '" . $search . "' ";
    endif;
    $sql .= " ORDER BY test.school_year ASC";
    $stmtSubject = $db->prepare($sql);
    $stmtSubject->bind_param("s", $subject_id);
    $stmtSubject->execute();
    $stmtSubject->store_result();
    $stmtSubject->bind_result($test_id, $test_name, $public, $school_year, $subject_extra_name, $subject_name, $grade_id);

    $test_results = array();
    /* loop through each item found in question */
    while ($stmtSubject->fetch()) {
        if ($public == 2) :
            array_push($test_results, [
                'test_id' => $test_id,
                'test_name' => $test_name,
                'public' => $public,
                'school_year' => $school_year,
                'subject_name' => $subject_name,
                'subject_extra_name' => $subject_extra_name,
                'grade_id' => $grade_id
            ]);
        endif;
    }

    array_push($pageData, [
        'subject_extra_name' => $subject_extra_name,
        'subject_id' => $subject_id,
        'totalActiveRecords' => count($test_results),
        'test_results' => $test_results,
    ]);
}


// see if the user has mastered the test yet. If mode_number is > 1, they have mastered it
$mastery_results = array();
if ($loggedUser) :
    $loggedUserId = $loggedUser->id;
    $sql = "
        SELECT 
        user_test.test_id, user_test.mode_number, user_test.question_history            
        FROM user_test
        WHERE user_test.id = ?
        ORDER BY user_test.test_id
        ";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $loggedUserId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($test_id, $mode_number, $question_history);
    
    /* loop through each item found in question */
    while ($stmt->fetch()) {
        array_push($mastery_results, [
            'mode_number' => $mode_number,
            'test_id' => $test_id,
            'question_history' => $question_history
        ]);
    }
    $stmt->free_result();
    $stmt->close();
endif;
?>
<div class="container-fluid" id="app">
    <a href="" class="return-to-top"><img style="width: 100%;" src="{{ asset('assets/images/icon-scroll-up.png') }}"></a>
    <div class="row">
        <div class="col-md-12 py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <v-autocomplete filled :items="items" :loading="isLoading" :search-input.sync="search" item-text="Description" item-value="API" label="Search by Vocab Word" placeholder="Start typing to search" return-object v-model="selected" @change="reloadPage('search', `${selected.API}`)" :hide-no-data="hideNoData" />
                        <v-expand-transition>
                            <v-list v-if="search" class="red lighten-3">
                                <v-list-item v-for="(field, i) in fields" :key="i">
                                    <v-list-item-content>
                                        <v-list-item-title v-text="field.value"></v-list-item-title>
                                        <v-list-item-subtitle v-text="field.key"></v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </v-expand-transition>
                    </div>
                    <div class="col-md-6">
                        <v-select v-model="selected_chapter" :items="chapters" filled label="Select By Subject" @change="reloadPage('chapter', `${selected_chapter}`)" />
                    </div>
                </div>
                <div class="row">
                    <div style="display: flex; justify-content: space-between;  flex-direction: column;" class="">
                        <?php
                        if (is_array($pageData) && count($pageData) > 0) :
                            foreach ($pageData as $data) :
                                if ($data['totalActiveRecords'] > 0) :
                        ?>
                                    <div class="container-fluid ps-0 pe-0 pb-5"> 

                                        <div class="row">                                      
                                          
                                                <div class="col-12 text-center" style="margin-top: 10px; padding-bottom:0;">
                                                    <a style="color:#000;" class="text-decoration-none" href="/grade/<?php echo $data['subject_id'] . '/' . spacestoDashes($data['subject_extra_name']); ?>">Subject - {{ $data['subject_extra_name']}}</a>
                                                </div>

                                            <?php
                                            
                                            if (is_array($data['test_results']) && count($data['test_results']) > 0) :
                                                foreach ($data['test_results'] as $test_results_second_array) :
                                                     
                                                    // original filter, not using                                                    
                                                    // $class = ($counter === 0) ? 'col-md-12' : 'col-md-6 col-sm-12';
                                                    //  $counter++;
                                                    //  if ($data['totalActiveRecords'] % 2 == 0 && $data['totalActiveRecords'] == $counter) :
                                                    //     $class = 'col-sm-12 ';
                                                    //  endif;

                                                     // if there is 1, col-md-12 else if there is more than 2, col-sm-6   
                                                     $class = 'col-md-12';                                                     
                                                     if ($data['totalActiveRecords'] % 2 == 0 ) :
                                                        $class = 'col-sm-6 ';
                                                     endif;

                                                    // $icon_image_badge_blue = ('upload/subjects/' . strtolower($test_results_second_array['subject_name'])  . '/chapter-icons/' . $test_results_second_array['grade_id'] . '/' . removeColonSpacesToDashes($test_results_second_array['test_name']) .  '/badge-blue/'  . removeColonSpacesToDashes($test_results_second_array['test_name']) . '.png');
                                                    // $icon_image_badge_grey = ('upload/subjects/' . strtolower($test_results_second_array['subject_name'])  . '/chapter-icons/' . $test_results_second_array['grade_id'] . '/' . removeColonSpacesToDashes($test_results_second_array['test_name']) .  '/badge-grey/'  . removeColonSpacesToDashes($test_results_second_array['test_name']) . '.png');
                                                    // $icon_image_badge_yellow = ('upload/subjects/' . strtolower($test_results_second_array['subject_name'])  . '/chapter-icons/' . $test_results_second_array['grade_id'] . '/' . removeColonSpacesToDashes($test_results_second_array['test_name']) .  '/badge-yellow/'  . removeColonSpacesToDashes($test_results_second_array['test_name']) . '.png');
                                                    // $icon_image_badge_default = '/assets/images/icon-progress-default.png';

                                            ?>
                                                    <div class="<?php echo $class; ?>">

                                                        <div class="f-size-13" style="margin-top: 3px;"><a style="color: #000;" href="/chapter/<?php echo $test_results_second_array['test_id']; ?>/<?php echo spacestoDashes(testNameAfterColon($test_results_second_array['test_name'])); ?>">Chapter - {{ testNameAfterColon($test_results_second_array['test_name']) }} </a></div>

                                                        @php
                                                            $key_of_user_test = array_search($test_results_second_array['test_id'], array_column($mastery_results, 'test_id'));
                                                        @endphp

                                                        <div class="f-size-13" style="margin-top: 2px;">
                                                            <span style="color: #000;" class="text-decoration-none">
                                                                Mastered 
                                                                    @if(isset($mastery_results[$key_of_user_test]['mode_number']))
                                                                    {{ $mastery_results[$key_of_user_test]['mode_number'] }}
                                                                    @else 0
                                                                    @endif 
                                                                Times
                                                                </span>
                                                        </div>                                                        

                                                        <a href="/chapter/<?php echo $test_results_second_array['test_id']; ?>/<?php echo spacestoDashes(testNameAfterColon($test_results_second_array['test_name'])); ?>">
                                                            <?php                                                            
                                                            // // following 1st checks to see if file paths exist, if they exist, it checks the database to see if the user has taken
                                                            // // the test in question. If they have taken the test, and if they have previously mastered it, show the correct badges
                                                            // // first check to see if file even exists
                                                            // if (file_exists($icon_image_badge_blue) && file_exists($icon_image_badge_grey) && file_exists($icon_image_badge_yellow)) {
                                                            //     // if the test we are looking at, has been previously used by the user. 1st we do a search for the test id, and search the array to see if this test has previoulsy been taken

                                                            //     $key_of_user_test = array_search($test_results_second_array['test_id'], array_column($mastery_results, 'test_id'));
                                                            //     // if there was a key found (this is the array key of the $mastery_results, where it was found)

                                                            //     if ($key_of_user_test !== false) {

                                                            //         // if question_history in the user_test table is not empty, then there is an array there, and a question has been answered, so show the blue badge
                                                            //         if (!empty($mastery_results[$key_of_user_test]['question_history'])) {
                                                            //             $icon_image_badge_used = $icon_image_badge_blue;
                                                            //         } // elseif mode_number greater than 0, show yellow badge, because the test has been mastered
                                                            //         elseif ($mastery_results[$key_of_user_test]['mode_number'] > 0) {
                                                            //             $icon_image_badge_used = $icon_image_badge_yellow;
                                                            //         } // else if no question has been answered and not mastered, show a grey icon
                                                            //         else {
                                                            //             $icon_image_badge_used = $icon_image_badge_grey;
                                                            //         }
                                                            //     }

                                                            //     // user has not taken the test, so show grey
                                                            //     else {
                                                            //         $icon_image_badge_used = $icon_image_badge_grey;
                                                            //     }
                                                            // }
                                                            // // one of the above paths does not exist, so show blank badge as a placeholder, and in order to fix
                                                            // else {
                                                            //     $icon_image_badge_used = $icon_image_badge_default;
                                                            // }
                                                            ?>
                                                            
                                                            @php
                                                                // changing how the chapter image works

                                                                $icon_image_badge_used = ('upload/subjects/' . strtolower($test_results_second_array['subject_name'])  . '/chapter-names/' . $test_results_second_array['grade_id'] . '/' . removeColonSpacesToDashes($test_results_second_array['test_name']) . '/' . removeColonSpacesToDashes($test_results_second_array['test_name']) . '.png');

                                                            @endphp

                                                            <img style="max-width: 300px; border-radius: 10px;" class="lazyload" alt="<?php echo testNameAfterColon($test_results_second_array['test_name']); ?>" title="<?php echo testNameAfterColon($test_results_second_array['test_name']); ?>" data-src="<?php echo $icon_image_badge_used; ?>" src="/assets/images/icon-progress-default.png">


                                                        </a>
                                                        <div class="f-size-13" style="margin-top: 7px;">
                                                            <a style="color: #000;" class="text-decoration-none" href="/test-question/<?php echo $test_results_second_array['test_id']; ?>/que">Quiz Yourself</a>
                                                        </div>                                                       

                                                        <div>
                                                            
                                                            @if (!empty($que) && $loggedUser)
                                                                @php
                                                                    $que_value_found = 0;
                                                                    foreach ($que as $que_value) {
                                                                        if ($que_value == $test_results_second_array['test_id']) {
                                                                            $que_value_found = 1;
                                                                            break;
                                                                        }
                                                                    }
                                                                @endphp

                                                                <div class="add-remove-queue-button">
                                                                    @if ($que_value_found == 1)
                                                                        <a style="color: #000;" role="button" class="f-size-13 text-decoration-none" onclick="remove_que(this, {{ $test_results_second_array['test_id'] }})">Remove from Game Queue</a>
                                                                    @else
                                                                        <a style="color: #000;" role="button" class="f-size-13 text-decoration-none" onclick="add_que(this, {{ $test_results_second_array['test_id'] }})">Add to Game Queue</a>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <div style="margin-top: 2px;">
                                                                    <a style="color: #000;" role="button" class="f-size-13 text-decoration-none" href="/login">Login to Add to Game Queue</a>
                                                                </div>
                                                            @endif                                                              
                                                            
                                                        </div>
                                                    </div>
                                            <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </div>
                                    </div>
                        <?php
                                endif;
                            endforeach;
                        endif;
                        ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div style="margin: 20px; min-height: 180px;" class="card app-card">
                            <div class="card-header border-0 bg-transparent">
                                <div class="mb-0 pt-3">
                                    How to Use Game Tutor
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4 col-sm-3 text-center">
                                        <a href="https://vimeo.com/697177854/c1bf442f8b"><img src="/upload/subjects/science/chapter-icons/general/video/badge-video-yellow.png" class="img-thumbnail border-0" alt="Video Badge" title="Video Badge"></a>
                                    </div>
                                    <div class="col-8 pt-sm-4 d-flex align-items-center">
                                        <div style="font-family: 'Raleway', sans-serif;" class="text-card">How to Use Game Tutor - 3:00</div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div style="margin: 20px; min-height: 180px;" class="card app-card">
                            <div class="card-header border-0 bg-transparent">
                                <div class="mb-0 pt-3">
                                    Next Achievement
                                    <a style="color:#000;" href="" class="f-size-13 float-end text-uppercase text-decoration-none app-link">View All</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3 text-center">
                                        <img src="/upload/subjects/science/chapter-icons/4/unit-1-classifying-living-things/badge-blue/unit-1-classifying-living-things.png" class="img-thumbnail border-0" alt="" title="">
                                    </div>
                                    <div class="col-9 pt-sm-3">
                                        <div class="container-fluid ps-0 pe-0">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p style="font-family: 'Raleway', sans-serif; text-align: left;" class="text-card">Master 10 new facts in 24 hours</p>
                                                </div>
                                                <div class="col-7 col-sm-9 pt-1">
                                                    <div class="progress rounded-pill">
                                                        <div class="progress-bar" role="progressbar" style="width: 1%;background: #ffc800;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <div class="col-5 col-sm-3">
                                                    1/50
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>                        
                    </div>  

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection