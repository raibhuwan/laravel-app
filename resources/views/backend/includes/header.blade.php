<header class="main-header">
    <!-- Logo -->
    <a href="index2.html" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>A</b>LT</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>Love</b>Lock</span>
    </a>
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">{{ trans('labels.general.toggle_navigation') }}</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="https://adminlte.io/themes/AdminLTE/dist/img/user2-160x160.jpg"
                             class="user-image" alt="User Avatar"/>
                        <span class="hidden-xs">Simon Shrestha</span>
                    </a>

                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <img src="https://adminlte.io/themes/AdminLTE/dist/img/user2-160x160.jpg" class="img-circle"
                                 alt="User Avatar"/>
                            <p>
                                Developer
                            </p>
                        </li>
                        <li class="user-body">
                            <div class="col-xs-4 text-center">
                                {{ link_to('#', 'Link') }}
                            </div>
                            <div class="col-xs-4 text-center">
                                {{ link_to('#', 'Link') }}
                            </div>
                            <div class="col-xs-4 text-center">
                                {{ link_to('#', 'Link') }}
                            </div>
                        </li>

                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="" class="btn btn-default btn-flat">
                                    <i class="fa fa-home"></i>
                                    {{ trans('navs.general.home') }}
                                </a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ route('logout') }}" class="btn btn-default btn-flat" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ trans('navs.general.logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </li>

                    </ul>

                </li>
            </ul>
        </div>
    </nav>
</header>