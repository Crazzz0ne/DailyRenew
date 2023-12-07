<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-title">
                @lang('menus.backend.sidebar.general')
            </li>
            <li class="nav-item">
                <a class="nav-link {{
                    active_class(Active::checkUriPattern('/dashboard'))}}"
                   href="{{ route('dashboard.dashboard') }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    @lang('menus.backend.sidebar.dashboard')
                </a>
            </li>


            <li class="nav-item nav-dropdown ">
                <a class="nav-link nav-dropdown-toggle" href="#">
                    <i class="nav-icon fas fa-house-damage pb-2"></i>Lead
                </a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('dashboard.lead') }}"> <i
                                class="nav-icon fas fa-house-damage"></i>
                            Leads
                        </a>
                    </li>
                    @can('view lead')
                    <li class="nav-item" >
                        <a class="nav-link {{ active_class(Active::checkUriPattern('dashboard/chat'))
                                }}" href="{{ route('dashboard.chat') }}"> <i
                                class="nav-icon fas fa-comments"></i>
                            Notes
                        </a>
                    </li>
                    @endcan
                    <li class="nav-item">
                        <a class="nav-link {{ active_class(Active::checkUriPattern('dashboard/lead'))
                                }}" href="/dashboard/lead/queue">
                            <i class="nav-icon fas fa-business-time"></i>
                            Queue
                        </a>
                    </li>
                    @can('view reporting')
                        <li class="nav-item">
                            <a class="nav-link {{active_class(Active::checkUriPattern('dashboard/report'))}}"
                               href="{{ route('dashboard.report') }}">
                                <i class="nav-icon fas fa-file" aria-hidden="true"></i>
                                Reports
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>


            <li class="nav-item">
                <a class="nav-link {{
                    active_class(Active::checkUriPattern('/calender'))}}"
                   href="{{ route('dashboard.calender') }}">
                    <i class="nav-icon fas fa fa-calendar"></i>
                    Calender
                </a>
            </li>
            @can('view team')
            <li class="nav-item">
                <a class="nav-link {{
                    active_class(Active::checkUriPattern('/team'))}}"
                   href="{{ route('dashboard.team') }}">
                    <i class="nav-icon fas fa fa-people-group"></i>
                    My Team
                </a>
            </li>
            @endcan
            @can('view announcement')
                <li class="nav-item">
                    <a class="nav-link {{active_class(Active::checkUriPattern('dashboard/announcement'))}}"
                       href="{{ route('dashboard.announcement.index')}}">
                        @if ($unread_count > 0)
                            <span class="badge badge-danger"
                                  style="float: left;display: block;margin: -1px 16px 0 0;">{{ $unread_count }}</span>
                        @endif
                        <i class="nav-icon fas fa-bullhorn"></i>
                        Announcements
                    </a>
                </li>
            @endcan
            @can('view partnerlinks')
                <li class="nav-item">
                    <a class="nav-link {{
                    active_class(Active::checkUriPattern('dashboard/vendorlinks'))
                }}" href="{{ route('dashboard.vendorlinks.index') }}">
                        <i class="nav-icon fas fa-address-card"></i>
                        Partner Links
                    </a>
                </li>
            @endcan

            <li class="nav-item nav-dropdown ">
                <a class="nav-link nav-dropdown-toggle" href="#">
                    <i class="nav-icon fas fa-money-check pb-2"></i>Revenue
                </a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('dashboard.commission') }}"> <i
                                class="nav-icon fas fa-money-bill"></i>
                            Commissions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="https://docs.google.com/forms/d/e/1FAIpQLSd6ZZDHmWc3GLJe5c6ZzbrxAA_Au7tB4EaR3jndBB2Uf4CkZg/viewform?usp=sf_link"
                           target="_blank">
                            <i class="nav-icon fas fa-comments-dollar"></i>
                            Payroll Items
                        </a>
                    </li>
                </ul>
            </li>


            @can('view mastermind')
                <li class="nav-item">
                    <a class="nav-link {{
                    active_class(Active::checkUriPattern('dashboard/instructions'))
                }}" href="{{ route('dashboard.mastermind.all') }}">
                        <i class="nav-icon fas fa-brain"></i>
                        Mastermind
                    </a>
                </li>
            @endcan
            @can('view training')
                <li class="nav-item">
                    <a class="nav-link {{
                    active_class(Active::checkUriPattern('dashboard/training'))}}"
                       href="{{ route('dashboard.training.all') }}">
                        <i class="nav-icon fas fa-graduation-cap"></i>
                        Training
                    </a>
                </li>
            @endcan

            <li class="nav-item nav-dropdown ">
                <a class="nav-link nav-dropdown-toggle" href="#">
                    <i class="nav-icon fas fa-file-word pb-2"></i>Documents
                </a>
                <ul class="nav-dropdown-items">
                    @can('view printable')
                        <li class="nav-item">
                            <a class="nav-link {{active_class(Active::checkUriPattern('dashboard/printable'))}}"
                               href="{{ route('dashboard.printable.all') }}">
                                <i class="nav-icon fas fa-print"></i>
                                Printable
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>

            @hasanyrole('administrator|manager|executive')
            <li class="nav-title">
                @lang('menus.backend.sidebar.system')
            </li>
            <li class="nav-item nav-dropdown {{
                    active_class(Active::checkUriPattern('dashboard/auth/user'), 'open')}}">
                <a class="nav-link nav-dropdown-toggle {{active_class(Active::checkUriPattern('dashboard/auth/user'))}}"
                   href="#">

                    <i class="nav-icon fa fa-lock"></i>
                    @lang('menus.backend.access.title')
                </a>
                <ul class="nav-dropdown-items">
                    @hasanyrole('administrator|executive')
                    <li class="nav-item">
                        <a class="nav-link {{
                                active_class(Active::checkUriPattern('dashboard/auth/user*'))
                            }}" href="{{ route('dashboard.auth.user.index') }}"><i class="nav-icon far fa-user"></i>
                            OG User Management
                        </a>
                    </li>

                    @endhasanyrole
                    <li class="nav-item">
                        <a class="nav-link {{
                                active_class(Active::checkUriPattern('dashboard/user*'))
                            }}" href="/dashboard/user"><i class="nav-icon far fa-user"></i>
                            User Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{
                                active_class(Active::checkUriPattern('dashboard/unsignedRep'))
                            }}" href="/dashboard/unsignedRep"><i class="nav-icon far fa-question"></i>

                            Unassigned Appointments
                        </a>
                    </li>
                    {{--                        --}}
                    @hasanyrole('administrator|executive')
                    <li class="nav-item">
                        <a class="nav-link {{
                                active_class(Active::checkUriPattern('dashboard/round-robin*'))
                            }}" href="/dashboard/round-robin"><i class="nav-icon fas fa-kiwi-bird"></i>
                            Round Robin

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{
                                active_class(Active::checkUriPattern('dashboard/settings*'))
                            }}" href="/dashboard/settings/eligible-city"><i class="nav-icon fas fa-city"></i>
                            Eligible Cities
                        </a>
                    </li>
                    @endhasanyrole
                    {{--                        --}}
                    <li class="nav-item">
                        <a class="nav-link {{ active_class(Active::checkUriPattern('dashboard/dashboard'))
                                }}" href="{{ route('dashboard.office.index')}}">
                            <i class="nav-icon fas fa-building"></i>
                            Offices
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ active_class(Active::checkUriPattern('dashboard/dashboard'))
                                }}" href="{{ route('dashboard.managerefficiency.index')}}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            Manager Efficiency
                        </a>
                    </li>

                    @if ($logged_in_user->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{active_class(Active::checkUriPattern('dashboard/auth/role*'))}}"
                               href="{{ route('dashboard.auth.role.index') }}"
                               style="padding-left: 20px">
                                <i class="fa fa-universal-access" aria-hidden="true"
                                   style="margin-right: 20px"></i>
                                Role Management
                            </a>
                        </li>
                    @endif

                </ul>
            </li>
            @if ($logged_in_user->id === 1)
                <li class="divider"></li>
                <li class="nav-item nav-dropdown {{
                    active_class(Active::checkUriPattern('dashboard/log-viewer*'), 'open')}}">
                    <a class="nav-link nav-dropdown-toggle {{
                            active_class(Active::checkUriPattern('dashboard/log-viewer*'))}}" href="#">
                        <i class="nav-icon fas fa-list"></i>
                        Logs Viewer
                    </a>

                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a class="nav-link {{
                            active_class(Active::checkUriPattern('dashboard/log-viewer'))}}"
                               href="{{ route('log-viewer::dashboard') }}"
                               style="padding-left: 20px">
                                <i class="fas fa-tachometer-alt" style="margin-right: 20px"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{active_class(Active::checkUriPattern('dashboard/log-viewer/logs*'))}}"
                               href="{{ route('log-viewer::logs.list') }}"
                               style="padding-left: 20px">
                                <i class="fas fa-history" style="margin-right: 20px"></i>
                                Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{active_class(Active::checkUriPattern('dashboard/log-viewer/logs*'))}}"
                               href="{{ route('dashboard.auth.support.index') }}"
                               style="padding-left: 20px">
                                <i class="fas fa-ambulance" style="margin-right: 20px"></i>
                                Support
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @endhasanyrole

        </ul>

    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div><!--sidebar-->
