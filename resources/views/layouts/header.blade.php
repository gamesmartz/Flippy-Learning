<nav class="navbar navbar-expand-lg navbar-light progress-nav">
    <div class="container py-1">
        <div>
            <button type="button" class="btn position-relative">
                <a href="/">
                    <img style="width: 33px; height: 33px;" src="{{ asset('assets/images/light-bulb-gs-logo.png') }}" class="nav-bar-btn" alt="Home" title="Home">
                </a>
            </button>
        </div>
        <button class="navbar-toggler float-end" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarText">
            <ul class="navbar-nav mt-2 mt-lg-0 main-nav">
                <li class="nav-item me-2">
                    <a class="nav-link" aria-current="page" href="/progress?chapter=1st-4th%20Grade%20Science">
                        <img src="{{ asset('assets/images/queue.png') }}" alt="Chapters" title="Chapters" class="me-2">
                        <span>Chapters</span>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="/download">
                        <img src="{{ asset('assets/images/icon-game.png') }}" alt="Games" title="Games" class="me-2">
                        <span>Games</span>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="/queue">
                        <img src="{{ asset('assets/images/icon-game.png') }}" alt="Game Queue" title="In-Game Queue" class="me-2">
                        <span>Game Queue</span>
                    </a>
                </li>               
                <li class="nav-item me-2">
                    <a class="nav-link" href="/history">
                        <img src="{{ asset('assets/images/queue.png') }}" alt="History" title="History" class="me-2">
                        <span>Reports</span>
                    </a>
                </li>
            </ul>
            <div style="display: flex; justify-content: center;">
                <ul class="navbar-nav  main-nav" style="margin-top: 4px; margin-left: 12px;">
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('assets/images/icon-profile.png') }}" class="nav-bar-btn" alt="Profile Icon" title="<?php if( !empty($loggedUser->user_name) ) { echo $loggedUser->user_name; } else { echo 'Profile Icon'; }  ?>">
                        </a>
                        <ul class="dropdown-menu py-0" aria-labelledby="navbarDropdown">
                            @guest
                                <li>
                                    <a class="dropdown-item py-3 border-bottom" href="{{ route('login') }}">
                                        <img src="{{ asset('assets/images/icon-logout.png') }}" alt="Login" title="Login" class="me-3">
                                        Login
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-3" href="{{ route('register') }}">
                                        <img src="{{ asset('assets/images/icon-logout.png') }}" alt="Create Account" title="Create Account" class="me-3">
                                        Create Account
                                    </a>
                                </li>
                            @endguest
                            @auth
                            <li>
                                <a class="dropdown-item py-3" href="javascript:void" onclick="$('#logout-form').submit();">
                                    <img src="{{ asset('assets/images/icon-logout.png') }}" alt="Logout" title="Logout" class="me-3">
                                    Logout
                                </a>
                                {{-- <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> --}}
                                    @csrf
                                </form>
                            </li>
                            @endauth
                        </ul>
                    </li>
                </ul>
                <button type="button" class="btn position-relative">
                    <img style="width: 30px; height: 30px;" src="{{ asset('assets/images/icon-crown-yellow.png') }}" alt="Yellow Crown Icon" title="Yellow Crown Icon" class="nav-bar-btn"> <span class="position-absolute top-0 start-100 translate-middle badge" style="color:#ffc800;">0</span>
                </button>
                <button type="button" class="btn position-relative">
                    <img src="{{ asset('assets/images/icon-fire.png') }}" alt="Fire Icon" title="Fire Icon" class="nav-bar-btn"> <span class="position-absolute top-0 start-100 translate-middle badge" style="color:#b3b2b2">0</span>
                </button>
                <button type="button" class="btn position-relative">
                    <img src="{{ asset('assets/images/icon-gem-red.png') }}" alt="Red Gem Icon" title="Red Gem Icon" class="nav-bar-btn"> <span class="position-absolute top-0 start-100 translate-middle badge" style="color:#fc4848"><?php if ( isset($loggedUser->total_points) && $loggedUser->total_points != 0 ) { echo number_format( $loggedUser->total_points ); } else { echo '0';} ?></span>
                </button>                
            </div>
        </div>
    </div>
</nav>