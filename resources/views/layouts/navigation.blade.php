<ul class="sidebar-nav" data-coreui="navigation">
    @hasanyrole('Admin')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-speedometer') }}"></use>
            </svg>
            Dashboard
        </a>
    </li>
    @endhasanyrole

    @foreach($menus as $menu => $data)
        <li class="nav-group {{ in_array(request()->path(), array_column($data['submenu'], 'ruta')) ? 'nav-group-expanded' : '' }}">
            <a class="nav-link nav-group-toggle" href="#">
                <svg class="nav-icon">
                    <use xlink:href="{{ asset('icons/coreui.svg#cil-building') }}"></use>
                </svg>
                {{ $menu }}
            </a>

            @if(!empty($data['submenu']) && is_array($data['submenu']))
                <ul class="nav-group-items">
                    @foreach($data['submenu'] as $item)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is($item['ruta']) ? 'active' : '' }}" href="{{ url($item['ruta']) }}">
                                {{ $item['nombre'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>