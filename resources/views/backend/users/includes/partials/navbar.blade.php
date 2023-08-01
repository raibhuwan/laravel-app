<div class="m-portlet__head">
    <div class="m-portlet__head-tools">
        <ul class="nav nav-tabs m-tabs m-tabs-line   m-tabs-line--left m-tabs-line--primary"
            role="tablist">
            <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link  {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/user/*/edit'),'active') }}"
                   href="{{ route('user.edit', $userDetails->user_detail_id) }}"
                   role="tab">
                    <i class="flaticon-share m--hide"></i>
                    {{ trans('strings.backend.users.edit_user') }}
                </a>
            </li>

            <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/user/*/edit/image'),'active') }}"
                   href="{{ route('image.edit', $userDetails->user_detail_id) }}"
                   role="tab">
                    {{ trans('strings.backend.images.image') }}
                </a>
            </li>
            <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/user/*/edit/setting'),'active') }}"
                   href="{{ route('setting.edit', $userDetails->user_detail_id) }}"
                   role="tab">
                    {{ trans('strings.backend.settings.setting') }}
                </a>
            </li>
            <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/user/*/edit/location'),'active') }}"
                   href="{{ route('location.edit', $userDetails->user_detail_id) }}"
                   role="tab">
                    {{ trans('strings.backend.locations.location') }}
                </a>
            </li>
            <li class="nav-item m-tabs__item">
                <a class="nav-link m-tabs__link {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/user/*/edit/*/subscription'),'active') }}
                {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/user/*/subscription/*'),'active') }}
                {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/user/*/create/subscription'),'active') }}
                {{ active_class(Active::checkUriPattern(config('backend.admin_backend_url').'/user/*/subscriptions'),'active') }}"
                   href="{{ route('user.subscriptions', $userDetails->user_detail_id) }}"
                   role="tab">
                    {{ trans('strings.backend.plan_subscription.subscription') }}
                </a>
            </li>
        </ul>
    </div>
</div>
