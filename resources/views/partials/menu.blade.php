<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4" href="#">
            {{ trans('panel.site_title') }}
        </a>
    </div>

    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a href="{{ route("admin.home") }}" class="c-sidebar-nav-link">
                <i class="c-sidebar-nav-icon fas fa-fw fa-tachometer-alt">

                </i>
                {{ trans('global.dashboard') }}
            </a>
        </li>
        @can('manage_application_access')
            <li class="c-sidebar-nav-dropdown">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-mobile-alt c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.manageApplication.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('advertisement_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.mainpageimages.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/mainpageimages") || request()->is("admin/mainpageimages/*") ? "active" : "" }}">
                                <i class="fa-fw far fa-font-awesome-logo-full c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.advertisement.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('advertisement_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.item_advertisements.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/item_advertisements") || request()->is("admin/item_advertisements/*") ? "active" : "" }}">
                                <i class="fa-fw far fa-font-awesome-logo-full c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.item_advertisement.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('department_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.departments.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/departments") || request()->is("admin/departments/*") ? "active" : "" }}">
                                <i class="fa-fw fab fa-servicestack c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.department.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('offer_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.offers.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/offers") || request()->is("admin/offers/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-gift c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.offer.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('job_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.jobs.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/jobs") || request()->is("admin/jobs/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-user-md c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.job.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('news_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.news.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/news") || request()->is("admin/news/*") ? "active" : "" }}">
                                <i class="fa-fw far fa-newspaper c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.news.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('notification_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.notifications.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/notifications") || request()->is("admin/notifications/*") ? "active" : "" }}">
                                <i class="fa-fw far fa-bell c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.notification.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('job_offer_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.job-offers.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/job-offers") || request()->is("admin/job-offers/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.jobOffer.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('trader_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.traders.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/traders") || request()->is("admin/traders/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-dollar-sign c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.trader.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('product_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.products.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/products") || request()->is("admin/products/*") ? "active" : "" }}">
                                <i class="fa-fw fab fa-product-hunt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.product.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('coupon_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.coupons.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/coupons") || request()->is("admin/coupons/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-cut c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.coupon.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('order_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.orders.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/orders") || request()->is("admin/orders/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-plus-square c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.order.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('color_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.colors.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/colors") || request()->is("admin/colors/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-palette c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.color.title') }}
                            </a>
                        </li>
                    @endcan

                    @can('size_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.sizes.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/sizes") || request()->is("admin/sizes/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-expand-arrows-alt c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.size.title') }}
                            </a>
                        </li>
                    @endcan

                    @can('brand_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.brands.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/brands") || request()->is("admin/brands/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-copyright c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.brand.title') }}
                            </a>
                        </li>
                    @endcan

                </ul>
            </li>
        @endcan
        @can('branch_access')
            <li class="c-sidebar-nav-dropdown">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-code-branch c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.branch.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('city_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.cities.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/cities") || request()->is("admin/cities/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.city.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('category_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.categories.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/categories") || request()->is("admin/categories/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-th c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.category.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('category_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.sub_categories.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/sub_categories") || request()->is("admin/sub_categories/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-bezier-curve c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.sub_category.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('category_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.news_categories.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/news_categories") || request()->is("admin/news_categories/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-th c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.news_category.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('category_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.news_sub_categories.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/news_sub_categories") || request()->is("admin/news_categories/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-bezier-curve c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.news_sub_category.title') }}
                            </a>
                        </li>
                    @endcan

                        @can('category_access')
                            <li class="c-sidebar-nav-item">
                                <a href="{{ route("admin.main_product_types.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/main_product_types") || request()->is("admin/main_product_types/*") ? "active" : "" }}">
                                    <i class="fa-fw fas fa-th c-sidebar-nav-icon">

                                    </i>
                                    {{ trans('cruds.main_product_type.title') }}
                                </a>
                            </li>
                        @endcan

                        @can('category_access')
                            <li class="c-sidebar-nav-item">
                                <a href="{{ route("admin.sub_product_types.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/sub_product_types") || request()->is("admin/sub_categories/*") ? "active" : "" }}">
                                    <i class="fa-fw fas fa-bezier-curve c-sidebar-nav-icon">

                                    </i>
                                    {{ trans('cruds.sub_product_type.title') }}
                                </a>
                            </li>
                        @endcan


                        @can('category_access')
                            <li class="c-sidebar-nav-item">
                                <a href="{{ route("admin.main_product_service_types.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/main_product_service_types") || request()->is("admin/main_product_service_types/*") ? "active" : "" }}">
                                    <i class="fa-fw fas fa-th c-sidebar-nav-icon">

                                    </i>
                                    {{ trans('cruds.main_product_service_type.title') }}
                                </a>
                            </li>
                        @endcan

                        @can('category_access')
                            <li class="c-sidebar-nav-item">
                                <a href="{{ route("admin.sub_product_service_types.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/sub_product_service_types") || request()->is("admin/sub_categories/*") ? "active" : "" }}">
                                    <i class="fa-fw fas fa-bezier-curve c-sidebar-nav-icon">

                                    </i>
                                    {{ trans('cruds.sub_product_service_type.title') }}
                                </a>
                            </li>
                        @endcan

                    @can('specialization_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.specializations.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/specializations") || request()->is("admin/specializations/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-cogs c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.specialization.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @can('user_management_access')
            <li class="c-sidebar-nav-dropdown">
                <a class="c-sidebar-nav-dropdown-toggle" href="#">
                    <i class="fa-fw fas fa-users c-sidebar-nav-icon">

                    </i>
                    {{ trans('cruds.userManagement.title') }}
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
{{--                    @can('permission_access')--}}
{{--                        <li class="c-sidebar-nav-item">--}}
{{--                            <a href="{{ route("admin.permissions.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "active" : "" }}">--}}
{{--                                <i class="fa-fw fas fa-unlock-alt c-sidebar-nav-icon">--}}

{{--                                </i>--}}
{{--                                {{ trans('cruds.permission.title') }}--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endcan--}}
                    @can('role_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.roles.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-briefcase c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.role.title') }}
                            </a>
                        </li>
                    @endcan
                    @can('user_access')
                        <li class="c-sidebar-nav-item">
                            <a href="{{ route("admin.users.index") }}" class="c-sidebar-nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "active" : "" }}">
                                <i class="fa-fw fas fa-user c-sidebar-nav-icon">

                                </i>
                                {{ trans('cruds.user.title') }}
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan
        @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
            @can('profile_password_edit')
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}" href="{{ route('profile.password.edit') }}">
                        <i class="fa-fw fas fa-key c-sidebar-nav-icon">
                        </i>
                        {{ trans('global.change_password') }}
                    </a>
                </li>
            @endcan
        @endif
        <li class="c-sidebar-nav-item">
            <a href="#" class="c-sidebar-nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <i class="c-sidebar-nav-icon fas fa-fw fa-sign-out-alt">

                </i>
                {{ trans('global.logout') }}
            </a>
        </li>
    </ul>

</div>
