<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="index3.html" class="brand-link">
        <img src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <div class="sidebar">

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="#" class="nav-link {{ request()->is('home') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @can('read-roles')
                <li class="nav-item">
                    <a href="{{ route('role.index') }}" class="nav-link {{ request()->is('role*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Role
                        </p>
                    </a>
                </li>
                @endcan
                @can('read-permissions')
                <li class="nav-item">
                    <a href="{{ route('permission.index') }}" class="nav-link {{ request()->is('permission*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Permission
                        </p>
                    </a>
                </li>
                @endcan
                @can('read-users')
                <li class="nav-item">
                    <a href="{{ route('user.index') }}" class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            User
                        </p>
                    </a>
                </li>
                @endcan
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Logout
                            </p>
                        </a>
                    </form>

                </li>
            </ul>
        </nav>

    </div>

</aside>