<li class="sidebar-item"> <a class="sidebar-link" href="{{ route('user.holiday.index') }}"
        aria-expanded="false"><i data-feather="tag" class="feather-icon"></i><span
            class="hide-menu">Holiday List
        </span></a>
</li>
<li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="{{ route('user.grid.index') }}"
        aria-expanded="false"><i data-feather="message-square" class="feather-icon"></i><span
            class="hide-menu">Grid</span></a></li>
<li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="{{ route('user.fault.index') }}"
        aria-expanded="false"><i data-feather="calendar" class="feather-icon"></i><span
            class="hide-menu">Faults</span></a></li>
<li class="sidebar-item"> <a class="sidebar-link has-arrow" href="javascript:void(0)"
    aria-expanded="false"><i data-feather="users" class="feather-icon"></i><span
        class="hide-menu">Meter Reading</span></a>
    <ul aria-expanded="false" class="collapse  first-level base-level-line">
        <li class="sidebar-item"><a href="{{ route('user.meter-reading.create')}}" class="sidebar-link"><span
                    class="hide-menu"> Add Meter Reading
                </span></a>
        </li>
        <li class="sidebar-item"><a href="{{ route('user.meter-reading.index')}}" class="sidebar-link"><span
                    class="hide-menu"> Meter Reading Entries
                </span></a>
        </li>
        <li class="sidebar-item"><a href="{{ route('user.meter-reading.detail')}}" class="sidebar-link"><span
            class="hide-menu"> Meter Reading Detail By date
                </span></a>
        </li>

    </ul>
</li>

<li class="list-divider"></li>
