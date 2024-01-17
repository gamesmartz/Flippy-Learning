@extends('layouts.admin')

@section('title', "Manage Options - GameSmartz")
@section('description', "Manage Options")

@push('head')
<script src="{{ asset('assets/js/page/admin-options.js') }}"></script>
@endpush

@section('content')
<!-- Modal Changes Saved - Start -->
<div class="modal-wrapper save-modal-wrapper">
      <div class="b-close"></div>
      <h4>Changes Saved</h4>
      <hr>
      <div class="mt15">
          <br>
          <button type="submit" class="btn btn-success" onclick="close_saveModal()">ok</button>
      </div>
  </div>
<!-- Modal Changes Saved - End -->

<!-- Modal Define Games - Start -->
  <div class="modal-wrapper game-modal-wrapper">
      <div class="b-close"></div>
      <h4>Game</h4>
      <div class="mt25">
          <input type="hidden" class="game-field" id="game_index">
          <div class="mb15">
              <label for="game_name" class="game-lead">Game Name: </label>
              <input type="text" class="game-field textbox" id="game_name">
          </div>
          <div class="mb15">
              <label for="game_exe" class="game-lead">Game EXE: </label>
              <input type="text" class="game-field textbox" id="game_exe">
          </div>
          <div class="mb15">
              <label for="game_link" class="game-lead">Game Link: </label>
              <input type="text" class="game-field textbox" id="game_link">
          </div>
          <div class="mb15">
              <label for="game_style" class="game-lead">Game Style: </label>
              <input type="text" class="game-field textbox" id="game_style">
          </div>
      </div>
      <div class="mt15">
          <br>
          <button type="submit" class="btn btn-success" onclick="save_game()">save</button>
      </div>
  </div>
<!-- Modal Define Games - End -->

<!-- Modal Delete Game - Start -->
  <div class="modal-wrapper delete-game-modal">
      <div class="b-close"></div>
      <h4>Are you sure you want to delete this game?</h4>
      <div class="mt15">
          <br>
          <input type="hidden" id="delete_index">
          <button type="submit" class="btn btn-success" onclick="confirm_delete()">ok</button>
      </div>
  </div>
<!-- Modal Delete Game - End -->

<!-- Modal Delete Compliment - Start -->
  <div class="modal-wrapper delete-compliment-modal">
      <div class="b-close"></div>
      <h4>Are you sure you want to delete this compliment?</h4>
      <div class="mt15">
          <br>
          <button type="submit" class="btn btn-success" onclick="confirm_compliment_delete()">ok</button>
      </div>
  </div>
<!-- Modal Delete Compliment - End -->

<!-- Modal Delete Object - Start -->
  <div class="modal-wrapper delete-object-modal">
      <div class="b-close"></div>
      <h4></h4>
      <div class="mt15">
          <br>
          <button type="submit" class="btn btn-success" onclick="delete_object(type)">ok</button>
      </div>
  </div>
<!-- Modal Delete Object - End -->

<!-- Modal Define 1st Person Game - Start -->
  <div class="modal-wrapper person-modal-wrapper">
      <div class="b-close"></div>
      <h4>Game</h4>
      <div class="mt25">
          <input type="hidden" class="game-field" id="person_index">
          <div class="mb15">
              <label for="game_name" class="game-lead">Game Name: </label>
              <input type="text" class="game-field textbox" id="person_name">
          </div>
          <div class="mb15">
              <label for="game_exe" class="game-lead">Game EXE: </label>
              <input type="text" class="game-field textbox" id="person_exe">
          </div>
      </div>
      <div class="mt15">
          <br>
          <button type="submit" class="btn btn-success" onclick="save_person_game()">save</button>
      </div>
  </div>
<!-- Modal Define 1st Person Game - End -->

<!-- Modal Delete 1st Person Game - Start -->
<div class="modal-wrapper delete-person-modal">
      <div class="b-close"></div>
      <h4>Are you sure you want to delete this 1st person game?</h4>
      <div class="mt15">
          <br>
          <input type="hidden" id="delete_person_index">
          <button type="submit" class="btn btn-success" onclick="confirm_person_delete()">ok</button>
      </div>
  </div>
<!-- Modal Delete 1st Person Game - End -->



<div class="container-fluid mb-3"><!-- Container 1 - Start -->


  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 1 - Start -->
    <div class="col-12"><!-- Col 1 - Start -->
      <div class="lead page-name">Admin Options</div>

      <div class="float-right" style="float: right;">
        <p>
          <select name="admin-nav" id="admin-nav" data-desc="admin nav" class="admin-nav action-dropdown">
            <option value="/admin/tests">admin-tests</option>
            <?php
            if ($loggedUser->user_role == 1) {
              ?>
              <option value="/admin/users">admin-users</option>
              <option value="/admin/videos">admin-videos</option>
              <option value="/admin/options">admin-options</option>
              <?php
            }
            ?>
          </select>
        </p>
      </div>

    </div><!-- Col 1 - End -->
  </div><!-- Row 1 - End -->




  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 2 - Start -->
    <div class="col-12"><!-- Col 2 - Start -->
      <div class="lead page-name mb-3">Wait Before Asking Again</div>

        <?php
        /* get miss wait time config value from config table */
        $sql = "SELECT option_value FROM config WHERE option_name = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $option_name);

        $option_name = 'miss_wait_time';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <p>
            <span class="fs1-2em mr5-imp">After a question has been missed, wait X questions before showing again:</span>
            <input type="text" id="miss_wait_time" value="<?php echo $option_value; ?>" class="textbox form-control gs-default-form-width">
        </p>
        <div class="sp25"></div>
        <?php
        /* get first wait time config value from config table */
        $option_name = 'first_wait_time';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <p>
            <span class="fs1-2em mr5-imp">After answered correct the 1st time,  wait X questions before showing again:</span>
            <input type="text" id="first_wait_time" value="<?php echo $option_value; ?>" class="textbox form-control gs-default-form-width">
        </p>
        <div class="sp25"></div>
        <?php
        /* get second wait time config value from config table */
        $option_name = 'second_wait_time';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <p>
            <span class="fs1-2em mr5-imp">After answered correct the 2nd time, in a row, wait X questions before showing again:</span>
            <input type="text" id="second_wait_time" value="<?php echo $option_value; ?>" class="textbox form-control gs-default-form-width">
        </p>
        <div class="sp25"></div>
        <?php
        /* get Third wait time config value from config table */
        $option_name = 'third_wait_time';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <p>
            <span class="fs1-2em mr5-imp">After answered correct the 3rd time, in a row, wait X questions before showing again:</span>
            <input type="text" id="third_wait_time" value="<?php echo $option_value; ?>" class="textbox form-control gs-default-form-width">
        </p>
        <div class="sp25"></div>
        <?php
        /* get forth wait time config value from config table */
        $option_name = 'fourth_wait_time';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <p>
            <span class="fs1-2em mr5-imp">After answered correct the 4th time, in a row, wait X questions before showing again:</span>
            <input type="text" id="fourth_wait_time" value="<?php echo $option_value; ?>" class="textbox form-control gs-default-form-width">
        </p>
        <div class="sp25"></div>
        <?php
        /* get fifth wait time config value from config table */
        $option_name = 'fifth_wait_time';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <p>
            <span class="fs1-2em mr5-imp">After answered correct the 5th time, in a row, wait X questions before showing again:</span>
            <input type="text" id="fifth_wait_time" value="<?php echo $option_value; ?>" class="textbox form-control gs-default-form-width">
        </p>
        <div class="sp25"></div>
        <?php
        /* get sixth wait time config value from config table */
        $option_name = 'sixth_wait_time';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <p>
            <span class="fs1-2em mr5-imp">After answered correct the 6th time, in a row, wait X questions before showing again:</span>
            <input type="text" id="sixth_wait_time" value="<?php echo $option_value; ?>" class="textbox form-control gs-default-form-width">
         </p>
      <div class="mb-4">
        <button class="btn btn-success" onclick="save_wait_time()">Save All</button>
      </div>


    </div><!-- Col 2 - End -->
  </div><!-- Row 2 - End -->


  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 3 - Start -->
    <div class="col-12"><!-- Col 3 - Start -->
      <div class="lead page-name mb-3">In a Row Correct for Mastery</div>
      <div class="mb-3">
        <span>In a row until mastery:</span>
        <?php
        /* get memorization mastery value from config table  */
        $option_name = 'memorization_mastery';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <input type="text" id="memorization-mastery-value" value="<?php echo $option_value; ?>" class="textbox form-control gs-default-form-width">
      </div>

      <div class="mb-4">
        <button class="btn btn-success" onclick="save_memorization_mastery()">Save</button>
      </div>

    </div><!-- Col 3 - End -->
  </div><!-- Row 3 - End -->


  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 4 - Start -->
    <div class="col-12"><!-- Col 4 - Start -->
      <div class="lead page-name mb-3">Time Award Per Question</div>

      <div class="mb-3">
        <?php
        /* get memorization mastery value from config table  */
        $option_name = 'time_award';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <select id="time_award_value"
                class="textbox form-control gs-default-drop-down-width">
          <?php foreach (timeAwardArray() as $time_award) :
            if ($option_value === $time_award)
              echo '<option value="' . $time_award . '" selected>' . $time_award . '</option>';
            else
              echo '<option value="' . $time_award . '">' . $time_award . '</option>';
          endforeach ?>
        </select>
      </div>


          <div class="mb-4">
            <button class="btn btn-success" onclick="save_time_award()">Save</button>
          </div>

    </div><!-- Col 4 - End -->
  </div><!-- Row 4 - End -->



  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 5 - Start -->
    <div class="col-12"><!-- Col 5 - Start -->
      <div class="lead page-name mb-3">Maximum Time Possible</div>

          <div>
            <div class="mb-3">
              <span>Maximum Time Possible:</span>
              <?php
              /* get memorization mastery value from config table  */
              $option_name = 'max_time';
              $stmt->execute();
              $stmt->store_result();
              $stmt->bind_result($option_value);
              $stmt->fetch();
              $stmt->free_result();
              ?>

              <select id="max_time_value"
                      class="textbox form-control gs-default-drop-down-width">
                <?php foreach (maxTimeArray() as $max_time) :
                  if ($option_value === $max_time)
                    echo '<option value="' . $max_time . '" selected>' . $max_time . '</option>';
                  else
                    echo '<option value="' . $max_time . '">' . $max_time . '</option>';
                endforeach ?>
              </select>
            </div>

            <div class="mb-4">
              <button class="btn btn-success" onclick="save_max_time()">Save</button>
            </div>
          </div>

    </div><!-- Col 5 - End -->
  </div><!-- Row 5 - End -->



  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5"><!-- Row 6 - Start -->
    <div class="col-12"><!-- Col 6 - Start -->
      <div class="lead page-name mb-3">Problem Solving</div>

      <div class="mb-3">
        <span>In a row until mastery:</span>
        <?php
        /* get problem master value from config table */
        $option_name = 'problem_mastery';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <input type="text" id="problem-mastery-value" value="<?php echo $option_value; ?>" class="textbox form-control gs-default-form-width">
      </div>

      <div class="mb-4">
        <button class="btn btn-success" onclick="save_problem_mastery()">Save</button>
      </div>

    </div><!-- Col 6 - End -->
  </div><!-- Row 6- End -->


  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 7 - Start -->
    <div class="col-12"><!-- Col 7 - Start -->
      <div class="lead page-name mb-3">Points Needed Per Level</div>

      <div class="mb-3">
        <select name="level-points" id="level-points" data-desc="1" class="select-green">
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
          <option value="7">7</option>
          <option value="8">8</option>
          <option value="9">9</option>
          <option value="10">10</option>
        </select>
      </div>

      <div class="mb-3">
        <?php
        /* get level points value from config table */
        $option_name = 'level_points';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        if ($option_value == "") {
          $value = "";
        } else {
          $level_array = unserialize($option_value);
          if (isset($level_array["level_1"])) {
            $value = $level_array["level_1"];
          } else {
            $value = "";
          }
        }
        ?>
        <input type="text" id="level-points-value" value="<?php echo $value; ?>" class="textbox form-control gs-default-form-width">
      </div>

      <div class="mb-4">
        <button class="btn btn-success" onclick="save_level_points()">Save</button>
      </div>


    </div><!-- Col 7 - End -->
  </div><!-- Row 7 - End -->


  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 8 - Start -->
    <div class="col-12"><!-- Col 8 - Start -->
      <div class="lead page-name mb-3">All Reports Email</div>

      <div class="mb-3">
        <?php
        /* get all_reports value from config table(email) */
        $option_name = 'all_reports';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <input type="text" class="textbox form-control" style="width: 250px;" id="all_reports" value="<?php echo $option_value; ?>">
      </div>

      <div class="mb-4">
        <button class="btn btn-success" onclick="save_all_reports()">Save</button>
      </div>


    </div><!-- Col 8 - End -->
  </div><!-- Row 8 - End -->

  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 9 - Start -->
    <div class="col-12"><!-- Col 9 - Start -->
      <div class="lead page-name mb-3">Free Days Before Paid</div>

      <div class="mb-3">
        <?php
        /* get free days value from config table */
        $option_name = 'free_days';
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($option_value);
        $stmt->fetch();
        $stmt->free_result();
        ?>
        <input type="text" class="textbox form-control gs-default-form-width" id="free_days" value="<?php echo $option_value; ?>">
      </div>

      <div class="mb-4">
        <button class="btn btn-success" onclick="save_free_days()">Save</button>
      </div>

    </div><!-- Col 9 - End -->
  </div><!-- Row 9 - End -->


  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 10 - Start -->
    <div class="col-12"><!-- Col 10 - Start -->
      <div class="lead page-name mb-3">Games Defined</div>

      <div class="mb-3">
        <button class="btn btn-success btn-add-games" onclick="add_game()">Add</button>
      </div>



            <?php
            /* get games_defined value from config table */
            $option_name = 'games_defined';
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($option_value);
            $stmt->fetch();
            $stmt->free_result();
            ?>
            <script type="text/javascript">
                var members = [];
            </script>
            <?php
            if ($option_value != "") {
              $games_defined = unserialize($option_value);
              foreach ($games_defined as $game) {
                ?>
                <script type="text/javascript">
                    members.push(['<?php echo $game['name']; ?>', '<?php echo $game['exe']; ?>', '<?php echo $game['link']; ?>', '<?php echo $game['style']; ?>']);
                </script>
                <?php
              }
            }
            ?>

      <div>
        <table class="table table-striped table-tests gs-small-table-fontsize">
          <thead>
          <tr>
            <th class="pd30-left">Name</th>
            <th class="left-align hidden-sm-down">Executable</th>
            <th class="left-align hidden-sm-down">Link</th>
            <th class="left-align hidden-sm-down">Style</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody id="games-table"></tbody><!-- Table Body Contents from js -->
        </table>
      </div>


    </div><!-- Col 10 - End -->
  </div><!-- Row 10 - End -->



  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 11 - Start -->
    <div class="col-12"><!-- Col 11 - Start -->
      <div class="lead page-name mb-3">Compliments</div>

      <div class="mb-3">
        <button class="btn btn-success btn-add-games" onclick="add_compliment()">Add</button>
      </div>

      <div class="mb-4">
        <script type="text/javascript">
            var compliments = [];
        </script>
        <?php
        $dirname = "upload/compliments";
        if (is_dir($dirname)) {
          $dir_handle = opendir($dirname);
        }
        /** get compliments files from compliments directory / sub directory
         * if sub directory is empty, delete sub directory
         */
        if ($dir_handle) {
          while ($sub_dir = readdir($dir_handle)) {
            if ($sub_dir != "." && $sub_dir != ".." && is_dir($dirname . "/" . $sub_dir)) {
              $sub_dir_handle = opendir($dirname . "/" . $sub_dir);
              if ($sub_dir_handle) {
                $tmp_image = "";
                $tmp_audio = "";
                while ($file = readdir($sub_dir_handle)) {
                  if (is_file($dirname . "/" . $sub_dir . "/" . $file)) {
                    $image_formats = array("png", "jpg");
                    $audio_formats = array("mp3", "MP3");
                    $name_array = explode(".", $file);
                    $ext = end($name_array);
                    if (in_array($ext, $image_formats)) {
                      $tmp_image = $dirname . "/" . $sub_dir . "/" . $file;
                    } else if (in_array($ext, $audio_formats)) {
                      $tmp_audio = $dirname . "/" . $sub_dir . "/" . $file;
                    }
                  }
                }
                if ($tmp_image == "" && $tmp_audio == "") {
                  deleteDirectory($dirname . "/" . $sub_dir);
                } else {
                  ?>
                  <script type="text/javascript">
                      compliments.push(['<?php echo $sub_dir; ?>', '<?php echo $tmp_image; ?>', '<?php echo $tmp_audio; ?>']);
                  </script>
                  <?php
                }
                closedir($sub_dir_handle);
              }
            }
          }
          closedir($dir_handle);
        }
        ?>
        <form class="input-file" id="attach_form_compliment" method="post" enctype="multipart/form-data"
              action="ajax/async-uploadCompliment.php">
          <input type="file" name="complimentUpload" id="attach_compliment" onchange="upload_compliment()">
          <input type="hidden" name="compliment_type" id="compliment_type" value="">
          <input type="hidden" name="sub_directory" id="sub_directory" value="">
          <input type="hidden" name="old_file" id="old_file" value="">
        </form>
        <table class="table table-striped table-tests gs-small-table-fontsize">
          <thead>
          <tr>
            <th width="42%" class="pd30-left">Image</th>
            <th width="42%" class="left-align">Audio</th>
            <th width="16%">Actions</th>
          </tr>
          </thead>

          <tbody id="compliments-table"></tbody><!-- Table Body Contents from js -->

        </table>
      </div>

    </div><!-- Col 11 - End -->
  </div><!-- Row 11 - End -->



  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 12 - Start -->
    <div class="col-12"><!-- Col 12 - Start -->
      <div class="lead page-name mb-3">Audio Alerts</div>

      <div class="mb-3">
        <button class="btn btn-success btn-add-games" onclick="addAudioAlert()">Add</button>
      </div>

      <div class="mb-4">
        <script type="text/javascript">
            var audioAlerts = [];
        </script>
        <?php
        /* get audio alerts list from audio alerts table and its files from audio-alert directory */
        $dirname = "upload/audio-alerts";
        $dir_handle = false;
        $sql = "SELECT * FROM audio_alerts";
        use Illuminate\Support\Facades\DB;
        $audioAlerts = DB::select($sql);

        if ($audioAlerts) {
            $audioAlerts = json_decode(json_encode($audioAlerts), true);
          foreach ($audioAlerts as $audioAlert) {
            $dirName = 'upload/audio-alerts/' . $audioAlert['id'] . '/';
            $imgFile =  ($audioAlert['img_file'] != '') ? $dirName . $audioAlert['img_file'] : '';
            $audioFile =  ($audioAlert['audio_file'] != '') ? $dirName . $audioAlert['audio_file'] : '';
            ?>
            <script type="text/javascript">
                audioAlerts.push(['<?php echo $audioAlert['id']; ?>',
                    '<?php echo $imgFile; ?>',
                    '<?php echo $audioFile ?>',
                    '<?php echo $audioAlert['audio_time']; ?>']);
            </script>
          <?php }
        } ?>
        <form class="input-file" id="attach_form_aa" method="post" enctype="multipart/form-data"
              action="ajax/async-upload-audio-alert.php">
          <input type="file" name="audio_alert_upload" id="attach_audio_alert" onchange="upload_audio_alert()">
          <input type="hidden" name="audio_alert_id" id="audio_alert_id" value="">
          <input type="hidden" name="type" id="type" value="">
          <input type="hidden" name="aa_old_file" id="aa_old_file" value="">
        </form>
        <table class="table table-striped table-tests gs-small-table-fontsize">
          <thead>
          <tr>
            <th>Time</th>
            <th>Audio</th>
            <th>Actions</th>
          </tr>
          </thead>

          <tbody id="audio_alerts_table"></tbody><!-- Table Body Contents from js -->

        </table>
      </div>

    </div><!-- Col 12 - End -->
  </div><!-- Row 12 - End -->


  <div class="row justify-content-center mb-3" style="background-color: #ebe7e5;"><!-- Row 13 - Start -->
    <div class="col-12"><!-- Col 13 - Start -->
      <div class="lead page-name mb-3">1st Person Games</div>




  <div class="mb-3">
    <button class="btn btn-success btn-add-games" onclick="add_person_game()">Add</button>
  </div>

  <div class="mb-4">
    <?php
    /* get 1st_person_game value from config table */
    $option_name = 'first_person_games';
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($option_value);
    $stmt->fetch();
    $stmt->free_result();
    ?>
    <script type="text/javascript">
        var persons = [];
    </script>
    <?php
    if ($option_value != "") {
      $first_person_games = unserialize($option_value);
      foreach ($first_person_games as $game) {
        /* adds list to persons array to show as table using jquery */
        ?>
        <script type="text/javascript">
            persons.push(['<?php echo $game['name']; ?>', '<?php echo $game['exe']; ?>']);
        </script>
        <?php
      }
    }
    ?>
    <table class="table table-striped table-tests gs-small-table-fontsize">
      <thead>
      <tr>
        <th width="42%" class="pd30-left">Name</th>
        <th width="42%" class="left-align">Executable</th>
        <th width="16%">Actions</th>
      </tr>
      </thead>

      <tbody id="person-games-table"></tbody>

    </table>
  </div>

    </div><!-- Col 13 - End -->
  </div><!-- Row 13 - End -->



</div><!------------ Container  End ----------------->
@endsection