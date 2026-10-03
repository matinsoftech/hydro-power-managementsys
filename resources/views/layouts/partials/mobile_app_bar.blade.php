{{-- Fixed bottom app bar — mobile / tablet only --}}
<nav class="app-bottom-bar" aria-label="Mobile app navigation">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}">
        <svg viewBox="0 0 24 24"><path d="M4 11l8-7 8 7v9H4z"/></svg>
        Home
    </a>
    @if(auth()->user()->user_type == 'Admin')
        <a href="{{ route('admin.import_export') }}" class="{{ request()->routeIs('admin.import_export') || request()->routeIs('admin.import_*') || request()->routeIs('admin.export_*') ? 'on' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M12 4v11M8 11l4 4 4-4M5 19h14"/></svg>
            Import
        </a>
        <a href="{{ route('admin.reports.fault') }}" class="{{ request()->routeIs('admin.reports.fault*') ? 'on' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M12 3l9 16H3zM12 10v4M12 17v.5"/></svg>
            Faults
        </a>
        <a href="{{ route('admin.reports.generation') }}" class="{{ request()->routeIs('admin.reports.generation*') ? 'on' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M6 20V10M12 20V4M18 20v-7"/></svg>
            Gen.
        </a>
    @else
        <a href="{{ route('user.fault.index') }}" class="{{ request()->routeIs('user.fault*') ? 'on' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M12 3l9 16H3zM12 10v4M12 17v.5"/></svg>
            Faults
        </a>
        <a href="{{ route('user.grid.index') }}" class="{{ request()->routeIs('user.grid*') ? 'on' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M6 20V10M12 20V4M18 20v-7"/></svg>
            Grid
        </a>
        <a href="{{ route('user.meter-reading.index') }}" class="{{ request()->routeIs('user.meter-reading*') ? 'on' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M12 4v11M8 11l4 4 4-4M5 19h14"/></svg>
            Meter
        </a>
    @endif
    <a href="javascript:void(0)" class="app-more-toggle" id="appMoreToggle">
        <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        More
    </a>
</nav>
<script>
(function () {
    var more = document.getElementById('appMoreToggle');
    if (!more) return;
    more.addEventListener('click', function () {
        var toggler = document.querySelector('.nav-toggler');
        if (toggler) toggler.click();
    });
})();
</script>
