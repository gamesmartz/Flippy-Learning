<div style="display: flex; justify-content: center; background-color: #29a4ed; width: 100%; height: 40px;">

    <div style="position: absolute; left: 40px; top: 4px;" class="hide-on-sm-1200">
        <a href="/">
            <img src="/assets/images/logo-beta-transparent.png" alt="GameSmartz logo">
        </a>
    </div>

    <div style="display: none; position: absolute; left: 40px; top: 4px;" class="show-on-sm-1200 hide-on-sm-500">
        <a href="/">
            <img src="/assets/images/gs-small-box.png" alt="GameSmartz logo" style="border-radius: 3px;">
        </a>
    </div>

    <div style="display: flex; justify-content: space-evenly; font-size: 1.3em;" class="menu-responsive-properties">

        <a style="color: #fff; font-weight: bold;" href="/queue">
            <div style="display: flex; justify-content: center; align-items: center; width: 125px; padding-bottom: 5px; ">Queue</div>
        </a>


        <div class="dropdown" style="display: flex; justify-content: center; width: 100px; color: #fff; font-weight: bold; font-size: 1.3rem; cursor: pointer;">
            <div class="dropdown-toggle" data-display="static" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Nav</div>
            <div class="dropdown-menu dropdown-menu-right" style="background-color: #29a4ed; ">
                <a class="dropdown-item" style="padding: 5px 15px; font-size: 1.3rem; font-weight: bold; color: #fff !important;" href="/help-how-gs-works">How to Use</a>
                <a class="dropdown-item" style="padding: 5px 15px; font-size: 1.3rem; font-weight: bold; color: #fff !important;" href="/vocab-search">Find Flashcards</a>
                <a class="dropdown-item" style="padding: 5px 15px; font-size: 1.3rem; font-weight: bold; color: #fff !important;" href="/choose-tests">Choose Flashcards</a>
                <a class="dropdown-item" style="padding: 5px 15px; font-size: 1.3rem; font-weight: bold; color: #fff !important;" href="/download">In-Game Learning</a>
                <a class="dropdown-item" style="padding: 5px 15px; font-size: 1.3rem; font-weight: bold; color: #fff !important;" href="/all-subjects">Visual Subjects</a>
                <a class="dropdown-item" style="padding: 5px 15px; font-size: 1.3rem; font-weight: bold; color: #fff !important;" href="/help">Help</a>
                <a class="dropdown-item" style="padding: 5px 15px; font-size: 1.3rem; font-weight: bold; color: #fff !important;" href="/options">Options</a>
                <a class="dropdown-item" style="padding: 5px 15px; font-size: 1.3rem; font-weight: bold; color: #fff !important;" href="/history">History</a>
                <a class="dropdown-item" style="padding: 5px 15px; font-size: 1.3rem; font-weight: bold; color: #fff !important;" href="/update-contact">Update Email</a>
                <a class="dropdown-item" style="padding: 5px 15px; font-size: 1.3rem; font-weight: bold; color: #fff !important;" href="/change-password">Update Password</a>

                @auth
                <a class="dropdown-item" style="padding: 5px 15px; font-size: 1.3rem; font-weight: bold; color: #fff !important;" href="javascript:void" onclick="$('#logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Expired Modal needed for all pages -->
<div class="modal-wrapper expire-modal-wrapper">
    <div class="b-close"></div>
    <p class="lead">Login Expired</p>
    <h4>Please Login Again</h4>
    <div class="mt25">
        <br>
        <button class="btn btn-green" onclick="close_expire()">go</button>
    </div>
</div>