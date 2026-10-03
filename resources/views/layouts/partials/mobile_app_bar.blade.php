{{-- Bottom app bar: injected on mobile only so desktop never renders these icons --}}
<template id="appBottomBarTpl">
<nav class="app-bottom-bar" aria-label="Mobile app navigation">
    <a href="{{ route('dashboard') }}" data-route="dashboard">
        <i class="fa-solid fa-house"></i>
        Home
    </a>
    @if(auth()->user()->user_type == 'Admin')
        <a href="{{ route('admin.import_export') }}" data-route="import">
            <i class="fa-solid fa-file-import"></i>
            Import
        </a>
        <a href="{{ route('admin.reports.fault') }}" data-route="fault">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Faults
        </a>
        <a href="{{ route('admin.reports.generation') }}" data-route="generation">
            <i class="fa-solid fa-chart-column"></i>
            Gen.
        </a>
    @else
        <a href="{{ route('user.fault.index') }}" data-route="fault">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Faults
        </a>
        <a href="{{ route('user.grid.index') }}" data-route="grid">
            <i class="fa-solid fa-chart-column"></i>
            Grid
        </a>
        <a href="{{ route('user.meter-reading.index') }}" data-route="meter">
            <i class="fa-solid fa-gauge"></i>
            Meter
        </a>
    @endif
    <a href="javascript:void(0)" class="app-more-toggle" id="appMoreToggle">
        <i class="fa-solid fa-bars"></i>
        More
    </a>
</nav>
</template>
<script>
(function () {
    function mountBottomBar() {
        if (!window.matchMedia('(max-width: 991.98px)').matches) {
            var existing = document.querySelector('.app-bottom-bar');
            if (existing) existing.remove();
            return;
        }
        if (document.querySelector('.app-bottom-bar')) return;

        var tpl = document.getElementById('appBottomBarTpl');
        if (!tpl) return;
        var node = tpl.content.cloneNode(true);
        document.body.appendChild(node);

        var path = window.location.pathname;
        document.querySelectorAll('.app-bottom-bar a[data-route]').forEach(function (a) {
            var key = a.getAttribute('data-route');
            var on = false;
            if (key === 'dashboard' && /\/dashboard\/?$/.test(path)) on = true;
            if (key === 'import' && /import-export|import_|export_/.test(path)) on = true;
            if (key === 'fault' && (/reports\/fault/.test(path) || /\/fault(\/|$)/.test(path))) on = true;
            if (key === 'generation' && /reports\/generation/.test(path)) on = true;
            if (key === 'grid' && /\/grid(\/|$)/.test(path)) on = true;
            if (key === 'meter' && /meter-reading/.test(path)) on = true;
            if (on) a.classList.add('on');
        });

        var more = document.getElementById('appMoreToggle');
        if (more) {
            more.addEventListener('click', function () {
                var toggler = document.querySelector('.nav-toggler');
                if (toggler) toggler.click();
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mountBottomBar);
    } else {
        mountBottomBar();
    }
    window.addEventListener('resize', mountBottomBar);
})();
</script>
