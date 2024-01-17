@extends('layouts.admin')

@section('title', "Options - GameSmartz")
@section('description', "GameSmartz Options")

@push('head')
<script src="{{ asset('assets/js/page/extras.js') }}"></script>
<style>
    body {
        background-color: #29a4ed;
    }
</style>
@endpush

@section('content')
<?php // Changes Saved Wrapper - Start 
?>
<div class="modal fade" id="changes-saved-modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">

    <div class="modal-dialog" style="display: flex; height: 100vh; justify-content: center; align-items: center; ">
        <div style="width: 220px; height: 140px;" class="modal-content">
            <div class="">
                <h5 id="exampleModalLongTitle">Changes Saved!</h5>
            </div>
            <div class="modal-body text-center">
                <button type="button" class="btn" style="background-color: #f3821f; color: #fff; font-weight: bold; padding: .6rem 1.5rem; font-size: 1.1rem;" data-dismiss="modal">ok</button>
            </div>
        </div>
    </div>


</div>
<?php // Changes Saved Wrapper - End 
?>
<?php // page specific DB vars
if ($loggedUser->reports_option != "") {
    $reports_option = unserialize($loggedUser['reports_option']);
}
if ($loggedUser->reports_time != "") {
    $reports_time = unserialize($loggedUser->reports_time);
}
?>
<?php // set reports interval from DB
if ($loggedUser->reports_interval != "") {
?>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#sms-interval .dd-selected-value").val('<?php echo $loggedUser->reports_interval; ?>');
            $("#sms-interval .dd-selected").text('<?php echo $loggedUser->reports_interval . ' min'; ?>');
        });
    </script>
<?php }  ?>

<div class="container-fluid" style="font-family: Raleway, sans-serif; color: #fff;">

    <div style="display: flex; justify-content: center;">

        <div>

            <div style="margin: 25px 0 10px 0; font-size: 1.3em;">
                <div style="display: flex; justify-content: center;">recieve a text message when</div>
                <div style="display: flex; justify-content: center;">studying starts or stops</div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0 10px 0; font-size: 0.9em;">
                <div>
                    <div>Receive a text message after a </div>
                    <div>question has not been answered for: </div>
                </div>
                <div style="margin-left: 3px; color: #000;">
                    <select name="sms-interval" id="sms-interval" data-desc="20" class="select-interval">
                        <option value="15">15 min</option>
                        <option value="20">20 min</option>
                        <option value="30">30 min</option>
                    </select>
                </div>
            </div>


            <div style="display: flex; justify-content: center;">
                <div class="error-message" id="phone-error" style="display: none; width: 300px; justify-content: center; font-family: Raleway, sans-serif; padding: 10px 20px; font-size: 1rem; line-height: 1.5; background-color: rgb(0, 172, 193); color: rgb(255, 255, 255); border-radius: 10px; margin-top: 10px;">
                    <span>Enter a Valid Phone Number Format: 555-555-5555</span>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: center;">
                <input type="text" class="form-control" id="report-phone" value="<?php echo $loggedUser->reports_phone; ?>" placeholder="555-555-5555">
            </div>

            <div id="submit-wrapper" style="display: flex; justify-content: center; margin-top: 40px;">
                <button type="submit" class="btn" style="background-color: #f3821f; color: #fff; font-weight: bold; padding: .6rem 1.5rem; font-size: 1.1rem;" onclick="submit_changes()">update</button>
            </div>

        </div>

    </div>
</div>
@endsection