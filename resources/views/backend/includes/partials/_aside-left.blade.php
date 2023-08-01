<!-- BEGIN: Left Aside -->
<button class="m-aside-left-close  m-aside-left-close--skin-dark " id="m_aside_left_close_btn">
    <i
            class="la la-close"></i>
</button>
<div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">
    <!-- BEGIN: Aside Menu -->
    <div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark "
         m-menu-vertical="1" m-menu-scrollable="1" m-menu-dropdown-timeout="500" style="position: relative;">
        <ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
            <li class="m-menu__item {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/dashboard'),'m-menu__item--active') }}" aria-haspopup="true">
                <a href="{{ route('home') }}"
                   class="m-menu__link ">
                    <i
                            class="m-menu__link-icon flaticon-line-graph"></i>
                    <span class="m-menu__link-title">
						<span
                                class="m-menu__link-wrap">
							<span
                                    class="m-menu__link-text">{{  trans('strings.backend.dashboard.dashboard') }}</span>
							<span
                                    class="m-menu__link-badge">
							</span>
						</span>
					</span>
                </a>
            </li>
            <li class="m-menu__section ">
                <h4 class="m-menu__section-text">Components</h4>
                <i
                        class="m-menu__section-icon flaticon-more-v2"></i>
            </li>
            <li class="m-menu__item  m-menu__item--submenu {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/user*'),'m-menu__item--open m-menu__item--expanded') }}" aria-haspopup="true">
                <a href="javascript:;"
                   class="m-menu__link m-menu__toggle">
                    <i
                            class="m-menu__link-icon flaticon-user"></i>
                    <span class="m-menu__link-text">{{  trans('strings.backend.users.users') }}</span>
                    <i
                            class="m-menu__ver-arrow la la-angle-right"></i>
                </a>
                <div class="m-menu__submenu">
                    <span class="m-menu__arrow"></span>
                    <ul class="m-menu__subnav">
                        <li class="m-menu__item {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/user'),'m-menu__item--active') }}" aria-haspopup="true">
                            <a href="{{ route('user.index') }}"
                               class="m-menu__link ">
                                <i
                                        class="m-menu__link-bullet m-menu__link-bullet--dot">
                                    <span></span>
                                </i>
                                <span
                                        class="m-menu__link-text">{{  trans('strings.backend.users.all_users') }}</span>
                            </a>
                        </li>
                        <li class="m-menu__item {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/user/create'),'m-menu__item--active') }}" aria-haspopup="true">
                            <a href="{{ route('user.create') }}"
                               class="m-menu__link ">
                                <i
                                        class="m-menu__link-bullet m-menu__link-bullet--dot">
                                    <span></span>
                                </i>
                                <span
                                        class="m-menu__link-text">{{  trans('strings.backend.users.add_new') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="m-menu__item  m-menu__item--submenu {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/report*'),'m-menu__item--open m-menu__item--expanded') }}" aria-haspopup="true">
                <a href="javascript:;"
                   class="m-menu__link m-menu__toggle">
                    <i
                            class="m-menu__link-icon flaticon-user"></i>
                    <span class="m-menu__link-text">{{  trans('strings.backend.reports.reports') }}</span>
                    <i
                            class="m-menu__ver-arrow la la-angle-right"></i>
                </a>
                <div class="m-menu__submenu">
                    <span class="m-menu__arrow"></span>
                    <ul class="m-menu__subnav">
                        <li class="m-menu__item {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/report/user'),'m-menu__item--active') }}" aria-haspopup="true">
                            <a href="{{ route('report.user.index') }}"
                               class="m-menu__link ">
                                <i
                                        class="m-menu__link-bullet m-menu__link-bullet--dot">
                                    <span></span>
                                </i>
                                <span
                                        class="m-menu__link-text">{{  trans('strings.backend.reports.users') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="m-menu__item  m-menu__item--submenu {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/plan*'),'m-menu__item--open m-menu__item--expanded') }}" aria-haspopup="true">
                <a href="javascript:;"
                   class="m-menu__link m-menu__toggle">
                    <i
                            class="m-menu__link-icon flaticon-user"></i>
                    <span class="m-menu__link-text">{{  trans('strings.backend.plans.plans') }}</span>
                    <i
                            class="m-menu__ver-arrow la la-angle-right"></i>
                </a>
                <div class="m-menu__submenu">
                    <span class="m-menu__arrow"></span>
                    <ul class="m-menu__subnav">
                        <li class="m-menu__item {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/plan'),'m-menu__item--active') }}" aria-haspopup="true">
                            <a href="{{ route('plan.index') }}"
                               class="m-menu__link ">
                                <i
                                        class="m-menu__link-bullet m-menu__link-bullet--dot">
                                    <span></span>
                                </i>
                                <span
                                        class="m-menu__link-text">{{  trans('strings.backend.plans.all_plans') }}</span>
                            </a>
                        </li>
                        <li class="m-menu__item {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/plan/create'),'m-menu__item--active') }}" aria-haspopup="true">
                            <a href="{{ route('plan.create') }}"
                               class="m-menu__link ">
                                <i
                                        class="m-menu__link-bullet m-menu__link-bullet--dot">
                                    <span></span>
                                </i>
                                <span
                                        class="m-menu__link-text">{{  trans('strings.backend.plans.add_new') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <!-- END: Aside Menu -->
</div>
<!-- END: Left Aside -->