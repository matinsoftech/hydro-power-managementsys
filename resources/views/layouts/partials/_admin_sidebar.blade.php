{{-- Manage Users — directly under Dashboard --}}
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.user.index') }}" aria-expanded="false">
        <i data-feather="users" class="feather-icon"></i>
        <span class="hide-menu">Manage Users</span>
    </a>
</li>

<li class="list-divider"></li>

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
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.import_export', ['tab' => 'generator']) }}" aria-expanded="false">
        <i class="fa-solid fa-gears feather-icon"></i>
        <span class="hide-menu">Generator Meter Import</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.import_export', ['tab' => 'log']) }}" aria-expanded="false">
        <i class="fa-solid fa-book feather-icon"></i>
        <span class="hide-menu">Log Import</span>
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
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.reports.generator') }}" aria-expanded="false">
        <i data-feather="cpu" class="feather-icon"></i>
        <span class="hide-menu">Generator Meter Report</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('admin.reports.log') }}" aria-expanded="false">
        <i data-feather="file-text" class="feather-icon"></i>
        <span class="hide-menu">Log Report</span>
    </a>
</li>

<li class="list-divider"></li>

{{-- Old Software — collapsed dropdown --}}
<li class="sidebar-item">
    <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
        <i data-feather="archive" class="feather-icon"></i>
        <span class="hide-menu">Old Software</span>
    </a>
    <ul aria-expanded="false" class="collapse first-level base-level-line">
        <li class="sidebar-item">
            <a href="{{ route('admin.holiday.index') }}" class="sidebar-link">
                <span class="hide-menu">Holiday List</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="{{ route('admin.grid.index') }}" class="sidebar-link">
                <span class="hide-menu">Grid</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="{{ route('admin.fault.index') }}" class="sidebar-link">
                <span class="hide-menu">Faults</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="{{ route('admin.meter-reading.create') }}" class="sidebar-link">
                <span class="hide-menu">Add Meter Reading</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="{{ route('admin.meter-reading.index') }}" class="sidebar-link">
                <span class="hide-menu">Meter Reading Entries</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="{{ route('admin.meter-reading.detail') }}" class="sidebar-link">
                <span class="hide-menu">Meter Reading Detail</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="{{ route('admin.station.index') }}" class="sidebar-link">
                <span class="hide-menu">Stations</span>
            </a>
        </li>
    </ul>
</li>

<li class="list-divider"></li>
