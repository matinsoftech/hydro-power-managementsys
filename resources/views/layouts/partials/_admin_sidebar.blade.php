{{-- Import / Export — top section, no drawer --}}
<li class="nav-small-cap"><span class="hide-menu">Import / Export</span></li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.import_export', ['tab' => 'failure']) }}" aria-expanded="false">
        <i class="fa-solid fa-bolt feather-icon"></i>
        <span class="hide-menu">Failure Import/Export</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.import_export', ['tab' => 'generation']) }}" aria-expanded="false">
        <i class="fa-solid fa-chart-line feather-icon"></i>
        <span class="hide-menu">Generation Import/Export</span>
    </a>
</li>

<li class="list-divider"></li>

{{-- Reports — second section, no drawer --}}
<li class="nav-small-cap"><span class="hide-menu">Reports</span></li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.reports.fault') }}" aria-expanded="false">
        <i data-feather="alert-triangle" class="feather-icon"></i>
        <span class="hide-menu">Fault Report</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.reports.generation') }}" aria-expanded="false">
        <i data-feather="bar-chart-2" class="feather-icon"></i>
        <span class="hide-menu">Generation Report</span>
    </a>
</li>

<li class="list-divider"></li>

{{-- Old Software — legacy menus, no drawer --}}
<li class="nav-small-cap"><span class="hide-menu">Old Software</span></li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.user.index') }}" aria-expanded="false">
        <i data-feather="users" class="feather-icon"></i>
        <span class="hide-menu">Manage Users</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.holiday.index') }}" aria-expanded="false">
        <i data-feather="tag" class="feather-icon"></i>
        <span class="hide-menu">Holiday List</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.grid.index') }}" aria-expanded="false">
        <i data-feather="message-square" class="feather-icon"></i>
        <span class="hide-menu">Grid</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.fault.index') }}" aria-expanded="false">
        <i data-feather="calendar" class="feather-icon"></i>
        <span class="hide-menu">Faults</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.meter-reading.create') }}" aria-expanded="false">
        <i data-feather="tv" class="feather-icon"></i>
        <span class="hide-menu">Add Meter Reading</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.meter-reading.index') }}" aria-expanded="false">
        <i data-feather="list" class="feather-icon"></i>
        <span class="hide-menu">Meter Reading Entries</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.meter-reading.detail') }}" aria-expanded="false">
        <i data-feather="clock" class="feather-icon"></i>
        <span class="hide-menu">Meter Reading Detail</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.station.index') }}" aria-expanded="false">
        <i class="fa-solid fa-network-wired feather-icon"></i>
        <span class="hide-menu">Stations</span>
    </a>
</li>

<li class="list-divider"></li>
