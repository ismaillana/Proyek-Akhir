<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="index.html">SIPAM</a>
      </div>
      <div class="sidebar-brand sidebar-brand-sm">
        <a href="index.html">SIPAM</a>
      </div>
      <ul class="sidebar-menu">
          <li class="menu-header">Dashboard</li>
          <li class="nav-item active">
            <a href="{{route('home')}}" class="nav-link"><i class="fas fa-fire"></i>
                <span>Dashboard</span></a>
          </li>
      <li class="menu-header">Master Data</li>
          <li class="nav-item dropdown {{ request()->is('menu-admin/jurusan*') ? 'active' : ''}} ||
            {{ request()->is('menu-admin/prodi*') ? 'active' : ''}} ||
            {{ request()->is('menu-admin/ijazah*') ? 'active' : ''}} ||
            {{ request()->is('menu-admin/tempat-pkl*') ? 'active' : ''}} ">
            <a class="nav-link has-dropdown" data-toggle="dropdown">
              <i class="fas fa-columns"></i> 
                <span>Master Data</span>
            </a>
            <ul class="dropdown-menu">
              <li class="{{ request()->is('menu-admin/jurusan*') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('jurusan.index')}}">
                  Jurusan
                </a>
              </li>
              <li class="{{ request()->is('menu-admin/prodi*') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('prodi.index')}}">
                  Program Studi
                </a>
              </li>
              <li class="{{ request()->is('menu-admin/ijazah*') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('ijazah.index')}}">
                  Ijazah
                </a>
              </li>
              <li class="{{ request()->is('menu-admin/tempat-pkl*') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('tempat-pkl.index')}}">
                  Tempat PKL
                </a>
              </li>
            </ul>
          </li>
      <li class="menu-header">Manajemen User</li>
          <li class="nav-item dropdown 
            {{ request()->is('menu-admin/mahasiswa*') ? 'active' : ''}} ||
            {{ request()->is('menu-admin/alumni*') ? 'active' : ''}} ||
            {{ request()->is('menu-admin/instansi*') ? 'active' : ''}} ||
            {{ request()->is('menu-admin/koor-pkl*') ? 'active' : ''}} ||
            {{ request()->is('menu-admin/admin-jurusan*') ? 'active' : ''}} ||
            {{ request()->is('menu-admin/manajemen-user*') ? 'active' : ''}} ||">
  
            <a class="nav-link has-dropdown" data-toggle="dropdown">
              <i class="far fa-user"></i> 
              <span>Manajemen User</span>
            </a>
            <ul class="dropdown-menu">
              <li class="{{ request()->is('menu-admin/manajemen-user*') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('manajemen-user.index')}}">
                  User
                </a>
              </li>
              <li class="{{ request()->is('menu-admin/admin-jurusan*') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('admin-jurusan.index')}}">
                  Admin Jurusan
                </a>
              </li>
              <li class="{{ request()->is('menu-admin/koor-pkl*') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('koor-pkl.index')}}">
                  Koor.PKL Jurusan
                </a>
              </li>
              <li class="{{ request()->is('menu-admin/mahasiswa*') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('mahasiswa.index')}}">
                  Mahasiswa
                </a>
              </li>
              <li class="{{ request()->is('menu-admin/alumni*') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('alumni.index')}}">
                  Alumni
                </a>
              </li>
              <li class="{{ request()->is('menu-admin/instansi*') ? 'active' : ''}}">
                <a class="nav-link" href="{{route('instansi.index')}}">
                  Instansi
                </a>
              </li>
            </ul>
          </li>
      <li class="menu-header">Pengajuan</li>
          <li class="nav-item dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fas fa-th-large"></i> <span>Pengajuan</span></a>
            <ul class="dropdown-menu">
              <li><a class="nav-link" href="components-article.html">Surat Aktif Kuliah</a></li>
              <li><a class="nav-link beep beep-sidebar" href="components-avatar.html">Legalisir</a></li>
              <li><a class="nav-link" href="components-chat-box.html">Izin Penelitian</a></li>
              <li><a class="nav-link beep beep-sidebar" href="components-empty-state.html">Pengantar PKL</a></li>
              <li><a class="nav-link" href="components-gallery.html">Cek Ijazah</a></li>
              <li><a class="nav-link beep beep-sidebar" href="components-hero.html">Surat Dispensasi</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="far fa-file-alt"></i> <span>Riwayat Pengajuan</span></a>
            <ul class="dropdown-menu">
              <li><a class="nav-link" href="components-article.html">Surat Aktif Kuliah</a></li>
              <li><a class="nav-link beep beep-sidebar" href="components-avatar.html">Legalisir</a></li>
              <li><a class="nav-link" href="components-chat-box.html">Izin Penelitian</a></li>
              <li><a class="nav-link beep beep-sidebar" href="components-empty-state.html">Pengantar PKL</a></li>
              <li><a class="nav-link" href="components-gallery.html">Cek Ijazah</a></li>
              <li><a class="nav-link beep beep-sidebar" href="components-hero.html">Surat Dispensasi</a></li>
            </ul>
          </li>

        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
          <a href="https://getstisla.com/docs" class="btn btn-primary btn-lg btn-block btn-icon-split">
            <i class="fas fa-rocket"></i> Documentation
          </a>
        </div>
    </aside>
  </div>