@extends('backend.index')

@section('content')
    <div class="row">
        <div class="col-xl-3 col-lg-4">
            <div class="m-portlet">
                <div class="m-portlet__body">
                    <div class="m-card-profile">
                        <div class="m-card-profile__pic">
                            <div class="m-card-profile__pic-wrapper">
                                <img src="{{  HelperFunctions::getImageLink($userDetails->image_detail_link, $userDetails->image_detail_path,  $userDetails->image_detail_name, $userDetails->user_detail_gender) }}"
                                     alt=""/>
                            </div>
                        </div>
                        <div class="m-card-profile__details">
                            <span class="m-card-profile__name">{{$userDetails->user_detail_name }}</span>
                            <a href="" class="m-card-profile__email m-link">{{$userDetails->user_detail_email }}</a>
                            <input type="hidden" name="user_id" value="{{$userDetails->user_detail_id}}"/>
                        </div>
                    </div>
                    <ul class="m-nav m-nav--hover-bg m-portlet-fit--sides">
                        <li class="m-nav__separator m-nav__separator--fit"></li>
                        <li class="m-nav__section m--hide">
                            <span class="m-nav__section-text">Section</span>
                        </li>
                        <li class="m-nav__item">
                            <a href="../header/profile&amp;demo=default.html" class="m-nav__link">
                                <i class="m-nav__link-icon flaticon-profile-1"></i>
                                <span class="m-nav__link-title">
								<span class="m-nav__link-wrap">
									<span class="m-nav__link-text">Profile</span>
								</span>
							</span>
                            </a>
                        </li>
                    </ul>
                    <div class="m-portlet__body-separator"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-8">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--tabs">
                @include('backend.users.includes.partials.navbar')
                @yield('users.layout')
            </div>
            <!--end::Portlet-->
        </div>
    </div>
@endsection