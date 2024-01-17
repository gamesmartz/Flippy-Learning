@extends('layouts.app')

<?php
if ($subject == 'science') {

    $sql = "
            SELECT
            `subject`.seo_subject_image,
            test.subject_extra_name,
            `subject`.subject_id,
            grade.grade_name
            FROM
            `subject`
            INNER JOIN test ON `subject`.subject_id = test.subject_id
            INNER JOIN grade ON `subject`.grade_id = grade.grade_id
            WHERE
            `subject`.view_option = '1' AND
            `subject`.subject_name = 'Science'
            GROUP BY
            `subject`.subject_id
        ";
} elseif ($subject == 'history') {
    $sql = "
            SELECT
            `subject`.seo_subject_image,
            test.subject_extra_name,
            `subject`.subject_id,
            grade.grade_name
            FROM
            `subject`
            INNER JOIN test ON `subject`.subject_id = test.subject_id
            INNER JOIN grade ON `subject`.grade_id = grade.grade_id
            WHERE
            `subject`.view_option = '1' AND
            `subject`.subject_name = 'History'
            GROUP BY
            `subject`.subject_id
        ";
}

$stmt = $db->prepare($sql);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($seo_subject_image, $subject_extra_name, $subject_id, $grade_name);
// dont fetch and close yet

$subjects_from_db = array();
$subjects_from_db_i = 0;

// for every item found in questions, loop through, generating an array of objects with all data per test id, found in answers,
// so for example an array with 20 items, so this is the raw data
while ($stmt->fetch()) {

    $subjects_from_db[$subjects_from_db_i]['seo_subject_image'] = $seo_subject_image;
    $subjects_from_db[$subjects_from_db_i]['subject_extra_name'] = $subject_extra_name;
    $subjects_from_db[$subjects_from_db_i]['subject_id'] = $subject_id;
    $subjects_from_db[$subjects_from_db_i]['grade_name'] = $grade_name;

    $subjects_from_db_i++;
}

// get info from config table
$sql = "SELECT option_value FROM config WHERE option_name = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $option_name);

$option_name = "gs_350_default";
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($gs_350_default);
$stmt->fetch();
$stmt->free_result();

$option_name = "gs_800_default";
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($gs_800_default);
$stmt->fetch();
$stmt->free_result();

$stmt->close();

$pageDescriptionList = '';
foreach ($subjects_from_db as $subjects_from_db_increment => $subjects_from_db_results) {
     $pageDescriptionList .= $subjects_from_db[$subjects_from_db_increment]['subject_extra_name'] . ', ';
}
$title = ucfirst($subject) . " Flashcards | GameSmartz";
$description = ucfirst($subject) . " Flashcards - " . $pageDescriptionList . "| GameSmartz.";
$canonical = 'https://gamesmartz.com/subject?subject=' . $subject;

?>

@section('title', $title)
@section('description', $description)
@section('canonical', $canonical)

@section('content')
<div class="container-fluid">
        <div class="row">
            <div class="col-md-12 py-4">
                <div style="display: flex; justify-content: center; align-items: center;">
                    <div style="color: #fff; font-size: 1.7em; margin-top: 3px; color: #383838;"><?php echo ucwords($subject); ?></div>
                </div>
                <div class="container pt-4">
                    <div class="row">
                        <?php if (!empty($subjects_from_db)) : ?>
                        <?php foreach ($subjects_from_db as $subjects_from_db_increment => $subjects_from_db_results) : ?>
                            <div class="col-md-6 text-center pb-4">
                                <a href="/grade/<?php echo $subjects_from_db[$subjects_from_db_increment]['subject_id'] . '/' . spacestoDashes($subjects_from_db[$subjects_from_db_increment]['subject_extra_name']); ?>">
                                    <?php
                                    $png_350_file_path = $subjects_from_db[$subjects_from_db_increment]['seo_subject_image'];
                                    $webp_350_file_path = str_replace("png", "webp", $subjects_from_db[$subjects_from_db_increment]['seo_subject_image']);
                                    ?>
                                    <?php if (!empty($png_350_file_path) && file_exists($png_350_file_path)) : ?>
                                        <picture>
                                            <source srcset="/<?php echo $webp_350_file_path ?>" type="image/webp">
                                            <source srcset="/<?php echo $png_350_file_path ?>" type="image/png">
                                            <img class="lazyload rounded img-fluid" style="box-shadow: 0px 0px 0px 2px #2d9cfd;" width="375" height="281" alt="<?php echo $subjects_from_db[$subjects_from_db_increment]['grade_name'] . ' ' . $subjects_from_db[$subjects_from_db_increment]['subject_extra_name']; ?>" title="<?php echo $subjects_from_db[$subjects_from_db_increment]['grade_name'] . ' ' . $subjects_from_db[$subjects_from_db_increment]['subject_extra_name']; ?>" src="/<?php echo $png_350_file_path; ?>">
                                        </picture>
                                    <?php else : ?>
                                        <img class="lazyload rounded img-fluid" style="box-shadow: 0px 0px 0px 2px #2d9cfd;" width="375" height="281" src="/<?php echo $gs_350_default; ?>" title="<?php echo $subjects_from_db[$subjects_from_db_increment]['subject_extra_name']; ?>">
                                    <?php endif; ?>
                                    <div class="mt-2" style="color: #fff; font-size: 1.2em; margin-top: 3px;"><?php echo $subjects_from_db[$subjects_from_db_increment]['subject_extra_name']; ?></div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection