@extends('layouts.admin')

@section('title', "Manage Users - GameSmartz")
@section('description', "Manage Users")

@push('head')
<script src="{{ asset('assets/js/page/admin-users.js') }}"></script>
<?php
/* get all users order by login time desc */
$sql = "SELECT user_id, name, email, login_time, user_registered_time, level, subscription, total_points FROM users ORDER BY login_time DESC ";
$stmt = $db->prepare($sql);

$stmt->execute();
$stmt->store_result();
$row = $stmt->num_rows();
$stmt->bind_result($user_id, $user_name, $user_email, $login_time, $user_registered_time, $level, $subscription, $total_points);

if ($row != 0) { ?>
    <script type="text/javascript">
        var members = [];
    </script>
    <?php
    while ($stmt->fetch()) {
    ?>
        <script type="text/javascript">
            /* push users array to javascript member array to show on table using jquery */
            members.push(['<?php echo $user_id; ?>', '<?php echo $user_name; ?>', '<?php echo $user_email; ?>', '<?php echo date("m-d-y", strtotime($login_time)); ?>', '<?php echo date("m-d-y", strtotime($user_registered_time)); ?>', '<?php echo $level; ?>', '<?php echo $subscription; ?>', '<?php echo $total_points; ?>']);
        </script>
    <?php
    }
} else { ?>
    <script type="text/javascript">
        var members = [
            ['none', 'No users.', '', '', '', '', '', '']
        ];
    </script>
<?php
}

$stmt->free_result();
$stmt->close();

?>
@endpush

@section('content')
<!-- Modal Password - Start -->
<div class="modal-wrapper password-modal">
    <div class="b-close"></div>
    <p class="mt25" style="font-size: 18px;">
        change password
    </p>
    <input type="password" class="textbox" id="user_pass" value="">
    <div class="mt15">
        <button type="submit" class="btn btn-green" onclick="save_password()">ok</button>
    </div>
</div>
<!-- Modal Password - End -->

<!-- Modal Subscription - Start -->
<div class="modal-wrapper subscription-modal">
    <div class="b-close"></div>
    <p class="mt25" style="font-size: 18px;">
        subscription:
    </p>
    <div class="public-modal-wrapper">
        <select name="user_subscription" id="user_subscription" data-desc="no" class="user_subscription">
            <option value="0">no</option>
            <option value="1">yes</option>
        </select>
    </div>
    <div class="mt15">
        <button type="submit" class="btn btn-green" onclick="save_subscription()">ok</button>
    </div>
</div>
<!-- Modal Subscription - End -->

<!-- Modal Lock Account - Start -->
<div class="modal-wrapper account-modal">
    <div class="b-close"></div>
    <p class="mt25" style="font-size: 18px;">
        account lock:
    </p>
    <div class="public-modal-wrapper">
        <select name="user_status" id="user_status" data-desc="no" class="user_status">
            <option value="1">no</option>
            <option value="0">yes</option>
        </select>
    </div>
    <div class="mt15">
        <button type="submit" class="btn btn-green" onclick="save_status()">ok</button>
    </div>
</div>
<!-- Modal Lock Account - End -->

<!-- Modal User Privileges - Start -->
<div class="modal-wrapper admin-modal">
    <div class="b-close"></div>
    <p class="mt25" style="font-size: 18px;">
        user privileges:
    </p>
    <div class="public-modal-wrapper">
        <select name="user_role" id="user_role" data-desc="user" class="user_role">
            <option value="2">user</option>
            <option value="3">loading</option>
            <option value="1">admin</option>
        </select>
    </div>
    <div class="mt15">
        <button type="submit" class="btn btn-green" onclick="save_role()">ok</button>
    </div>
</div>
<!-- Modal User Privileges - End -->

<!-- Modal Leader Board - Start -->
<div class="modal-wrapper leader-modal">
    <div class="b-close"></div>
    <p class="mt25" style="font-size: 18px;">
        leader board:
    </p>
    <div class="public-modal-wrapper">
        <select name="show_leader" id="show_leader" data-desc="yes" class="show_leader">
            <option value="1">yes</option>
            <option value="0">no</option>
        </select>
    </div>
    <div class="mt15">
        <button type="submit" class="btn btn-green" onclick="save_leader()">ok</button>
    </div>
</div>
<!-- Modal Leader Board - End -->


<!-- Container 1 - Start -->
<div class="container-fluid">
    <div class="row justify-content-center" style="background-color: #ebe7e5;">
        <!-- Row 1 - Start -->
        <div class="col-12">
            <!-- Col 1 - Start -->
            <div class="lead page-name">Admin Users</div>

            <div class="float-right" style="float: right">
                <p>
                    <select name="admin-nav" id="admin-nav" data-desc="admin nav" class="admin-nav action-dropdown">
                        <option value="/admin/tests">admin-tests</option>                        
                            <option value="/admin/users">admin-users</option>
                            <option value="/admin/videos">admin-videos</option>
                            <option value="/admin/options">admin-options</option>
                    </select>
                </p>
            </div>

        </div><!-- Col 1 - End -->
    </div><!-- Row 1 - End -->
</div><!-- Container 1 - End -->


<!-- Container 2- Start -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <!-- Row 2 - Start -->
        <div class="col-12 gs-small-12-col">
            <!-- Col 2 - Start -->

            <table class="table table-striped table-tests gs-small-table-fontsize table-tests">
                <!-- Table Start -->
                <thead>
                    <tr>
                        <th>
                            <select class="select" id="sort-users" data-desc="Sort">
                                <option>last login</option>
                                <option>date joined</option>
                                <option>level</option>
                                <option>email a-z</option>
                                <option>name a-z</option>
                                <option>admin</option>
                            </select>
                        </th>
                        <th class="hidden-sm-down">Points</th>
                        <th class="hidden-sm-down">User Email</th>
                        <th class="">Last Login</th>
                        <th class="hidden-sm-down">Joined</th>
                        <th class="hidden-sm-down">Level</th>
                        <th class="hidden-sm-down">Subcript</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="choose-tests-table"></tbody><!-- Everything in Table Body in js -->

            </table><!-- Table End -->

            <div class="table-footer">
                <div id="Pagination" class="pagination" onclick="start_drag()"></div>
                <div class="view-per-page pull-left">
                    <span class="pull-left mr5 normal">View</span>
                    <select name="view-test" class="select select-whitetxt" id="view-test" data-desc="20">
                        <option>10</option>
                        <option>20</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                </div>
                <div class="clear"></div>
            </div>

        </div><!-- Col 2 - End -->
    </div><!-- Row 2 - End -->

</div> <!-- Container 2- End -->
@endsection