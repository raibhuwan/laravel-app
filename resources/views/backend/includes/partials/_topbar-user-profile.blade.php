<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"
    m-dropdown-toggle="click">
    <a href="#" class="m-nav__link m-dropdown__toggle">
		<span class="m-topbar__userpic">
			<img
                    src="{{ session('profilePic') ? session('profilePic') : HelperFunctions::getBlankProfileImagePath() }}"
                    class="m--img-rounded m--marginless"
                    alt="profilePic"/>
		</span>

        <span class="m-topbar__username m--hide">{{session('currentUser')->name ? session('currentUser')->name  : 'John Doe'}}</span>
    </a>
    <div class="m-dropdown__wrapper">
		<span
                class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
        <div class="m-dropdown__inner">
            <div class="m-dropdown__header m--align-center"
                 style="background: url(/assets/app/media/img/misc/user_profile_bg.jpg); background-size: cover;">
                <div class="m-card-user m-card-user--skin-dark">
                    <div class="m-card-user__pic">
                        <img src="{{ session('profilePic') ? session('profilePic') : HelperFunctions::getBlankProfileImagePath() }}"
                             class="m--img-rounded m--marginless" alt=""/>
                        <!--						<span class="m-type m-type--lg m--bg-danger"><span class="m--font-light">S<span><span>						-->
                    </div>
                    <div class="m-card-user__details">
						<span
                                class="m-card-user__name m--font-weight-500">{{session('currentUser')->name ? session('currentUser')->name : 'John Doe'}}</span>
                        <a href=""
                           class="m-card-user__email m--font-weight-300 m-link">
                            {{session('currentUser')->email ? session('currentUser')->email : 'johndoe@email.com' }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="m-dropdown__body">
                <div class="m-dropdown__content">
                    <ul class="m-nav m-nav--skin-light">
                        <li class="m-nav__section m--hide">
                            <span class="m-nav__section-text">Section</span>
                        </li>
                        <li class="m-nav__item">
                            <a href="{{session('currentUser')->id ? route('user.edit',['id' => session('currentUser')->id]) : ''}}"
                               class="m-nav__link">
                                <i
                                        class="m-nav__link-icon flaticon-profile-1"></i>
                                <span
                                        class="m-nav__link-title">
									<span
                                            class="m-nav__link-wrap">
										<span
                                                class="m-nav__link-text">My Profile</span>
										<span
                                                class="m-nav__link-badge">
											<span
                                                    class="m-badge m-badge--success">2</span>
										</span>
									</span>
								</span>
                            </a>
                        </li>
                        <li class="m-nav__separator m-nav__separator--fit"></li>
                        <li class="m-nav__item">
                            <a href="{{ route('logout') }}"
                               class="btn m-btn--pill    btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{  trans('menus.backend.topbar-user-profile.logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                  style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</li>