<?php

use Illuminate\Support\Facades\DB;


// replace spaces with dashes
function spacestoDashes ($stringWithSpaces) {
    $stringWithSpacesFixed = preg_replace('/\s+/', '-', $stringWithSpaces);
    return $stringWithSpacesFixed;
}

// take test name and remove everything after colon, also remove non alpha-numeric
function testNameAfterColon ($testName) {
    $testNameOnly = explode(':', $testName);
    $testNameOnly =  trim($testNameOnly[1]) ;
    $testNameOnly = preg_replace("/[^a-z0-9 &-]+/i", '', $testNameOnly);
    return $testNameOnly;
}

function testNameBeforeColon ($testName) {
    $testNameOnlyArray = explode(':', $testName);
    $testNameOnly =  rtrim($testNameOnlyArray[0]) ;
    $testNameOnly = preg_replace("/[^a-z0-9 &-]+/i", '', $testNameOnly);
    return $testNameOnly;
}

// remove colon, replace spaces with dashes
function removeColonSpacesToDashes($string)
{
    $stringNoColon = str_replace(':', '', $string);
    $stringFinal = preg_replace('/\s+/', '-', $stringNoColon);
    $stringFinal = preg_replace("/[^a-z0-9 &-]+/i", '', $stringFinal);
    $stringFinal = strtolower($stringFinal);
    return $stringFinal;
}

// get seconds from  hours minutes
function get_seconds($value) {
    $minutes = 0;
    $seconds = 0;
    $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $value);

    sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

    return $hours * 3600 + $minutes * 60 + $seconds;
}

function rbGenerateRandom($len)
{
    $strpattern = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    $result = "";
    for ($i = 0; $i < $len; $i++) {
        $rand = rand(0, strlen($strpattern) - 1);
        $result = $result . $strpattern[$rand];
    }
    return $result;
}

// Delete the folder and files
function deleteDirectory($dirname) {
    if (is_dir($dirname)) {
        $dir_handle = opendir($dirname);
    }
    if (!$dir_handle) {
        return false;
    }

    while ($file = readdir($dir_handle)) {
        if ($file != "." && $file != "..") {
            if (!is_dir($dirname . "/" . $file))
                unlink($dirname . "/" . $file);
            else
            deleteDirectory($dirname . '/' . $file);
        }
    }

    closedir($dir_handle);
    rmdir($dirname);
    return true;
}

function deleteFiles($arr_files)
{
    foreach ($arr_files as $file_name) {
        if (is_dir($file_name)) {
            deleteDirectory($file_name);
        } elseif (is_file($file_name)) {
            unlink($file_name);
        }
    }
}

function trimAndClean ($data) {
    return trim(htmlspecialchars($data));
}


function getMasterNumberArray()
{
    return [
            '' => 'Use Global',
            '1' => '1',
            '2' => '2',
            '3' => '3'
    ];
}

function timeAwardArray()
{
    return [
        '1:30' => '1:30',
        '1:40' => '1:40',
        '1:50' => '1:50',
        '2:00' => '2:00',
        '2:10' => '2:10',
        '2:20' => '2:20',
        '2:30' => '2:30',
        '2:40' => '2:40',
        '2:50' => '2:50',
        '3:00' => '3:00',
    ];
}

function maxTimeArray()
{
    return [
        "10:00" => "10:00",
        "20:00" => "20:00",
        "30:00" => "30:00",
    ];
}

function getConfigValue($db, $option_name) {
    $sql = "SELECT option_value FROM config WHERE option_name = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $option_name);

    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($option_value);
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();
    return $option_value;
}

function conversionTempsEnHms($tempsEnSecondes) {
    $t = round($tempsEnSecondes);
    if ($t < 0) {
        $t = abs($t);
        return '-'. sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);
    } else return sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);
}