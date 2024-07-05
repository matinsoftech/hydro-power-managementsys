<a href="{{ route('profile') }}" class="btn-nav-primary @if(request()->routeIs('profile')) active @endif">Account</a>
@if(auth()->user()->user_type=='Admin')

    <a href="{{ route('admin.site-setting.index') }}" class="btn-nav-primary @if (request()->routeIs('admin.site-setting.*')) active @endif">Site Setup</a>
    <a href="{{ route('admin.mail-setting.index') }}" class="btn-nav-primary @if (request()->routeIs('admin.mail-setting.*')) active @endif">Mail Setup</a>

@endif
