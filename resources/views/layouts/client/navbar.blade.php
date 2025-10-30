@include('layouts.client.navbar-bottom')

{{-- top navigation --}}
<div class="navbar bg-base-300 fixed z-20 py-3 md:px-7">
    <div class="flex-1">
        <a href="{{ route('client.home') }}" @class([
            'ml-2 sm:mr-2 before:w-20',
            'tooltip tooltip-primary tooltip-bottom' => !Request::routeIs(
                'client.home'),
        ]) data-tip="Halaman Utama">
            <img src="{{ asset('assets/images/logo.webp') }}" alt="logo" width="60px" />
        </a>
        <div class="tooltip tooltip-primary tooltip-right" data-tip="Daftar Kategori">
            <div tabindex="0" role="button" class="btn btn-ghost outline-none text-lg"
                onclick="menuKategori.showModal()">
                <span class="fa fa-list"></span> <span class="hidden sm:inline">Kategori</span>
            </div>
        </div>
        @include('layouts.client.menu')
    </div>
    <div class="flex-none">
        {{-- searching --}}
        <div class="form-control mr-2">
            <div class="relative">
                <input id="search-input" type="text" placeholder="Cari sesuatu"
                    class="input input-bordered h-auto w-40 text-sm sm:w-60 md:w-80 lg:w-96 md:text-base" />
                <button id="search-button"
                    class="!absolute right-2 top-1/2 transform -translate-y-1/2 hover:bg-primary/20 rounded-full btn-sm">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
        {{-- <button class="btn btn-ghost btn-circle md:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button> --}}

        {{-- notifications --}}
        <!-- <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                <div class="indicator">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="badge badge-xs badge-primary indicator-item"></span>
                </div>
            </div>
            <div tabindex="0" class="mt-3 z-[1] card card-compact dropdown-content w-52 bg-base-100 shadow">
                <div class="card-body gap-0">
                    <span class="font-bold text-lg mb-2">Notifikasi</span>
                    <span class="text-info py-1 px-2 bg-primary/5">Subtotal: $999</span>
                    <span class="text-info py-1 px-2 bg-primary/5">Subtotal: $999</span>
                    <span class="text-info py-1 px-2">Subtotal: $999</span>
                    <span class="text-info py-1 px-2">Subtotal: $999</span>
                    <span class="text-info py-1 px-2">Subtotal: $999</span>
                    <div class="card-actions mt-2">
                        {{-- <a href="{{ route('client.notification') }}" class="link mx-auto">Lihat semua</a> --}}
                    </div>
                </div>
            </div>
        </div> -->

        {{-- cart --}}
        @auth
            @cannot('is-admin')
                @include('layouts.client.cart')
            @endcan
        @endauth

        {{-- user icon --}}
        {{-- user section --}}
        <div class="hidden lg:block dropdown dropdown-end">
            @if (auth()->check())
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="flex mx-3">
                        <span class="fa fa-person-running mr-2"></span><span>Keluar</span>
                    </button>
                </form>
            @else
                {{-- Not logged in (lg) → show Masuk + Buat Akun --}}
                <div class="mx-3 flex gap-4">
                    <a href="{{ route('login') }}" class="flex items-center gap-2">
                        <span class="fa fa-user-o"></span><span>Masuk</span>
                    </a>
                    <a href="{{ route('register') }}"
                        class="btn btn-primary text-white py-3 px-10 rounded-full whitespace-nowrap">
                        Buat Akun
                    </a>
                </div>
            @endif
        </div>

        {{-- profile icon --}}
        <div class="dropdown dropdown-end {{ auth()->check() ? '' : 'lg:hidden' }}">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                <div class="w-10 rounded-full">
                    @auth
                        <img alt="User profile photo"
                            src="{{ auth()->user()->img ? Storage::url(auth()->user()->img) : asset('assets/images/image-not-found.webp') }}" />
                    @else
                        <div class="flex items-center justify-center h-full bg-gray-200">
                            <span class="fa fa-user text-gray-600 text-xl"></span>
                        </div>
                    @endauth
                </div>
            </div>
            <ul tabindex="0"
                class="menu menu-sm dropdown-content mt-3 z-[1] py-4 px-4 shadow bg-base-100 rounded-box">

                @auth
                    <li class="text-center text-xs">{{ auth()->user()->email }}</li>
                    <li class="text-center text-sm my-1 font-bold">{{ auth()->user()->name }}</li>
                    <li>
                        @can('is-member')
                            <a class="mx-auto" href="{{ route('client.profile') }}"><span class="fa fa-user"></span> Profil</a>
                        @endcan
                        @can('is-admin')
                            <a href="{{ route('admin-profile.index') }}"><span class="fa fa-user"></span> Profil</a>
                        @endcan
                    </li>
                    <div class="divider my-0"></div>
                    <li class="mx-auto" x-data>
                        <a href="{{ route('client.profile') }}?open=notif">
                            <button class="relative flex items-center">
                                <span class="fa fa-envelope mr-2 text-xl"></span>
                                <span>Pesan</span>

                                <!-- 🔔 Badge jumlah notifikasi -->
                                <span x-show="$store.notification.unreadCount > 0" x-text="$store.notification.unreadCount"
                                    class="absolute bg-red-600 text-white text-[8px] w-[16px] h-[16px] flex items-center justify-center font-semibold rounded-full px-1.5 py-0.5" style="left: 10px; top: -5px">
                                </span>
                            </button>
                        </a>
                    </li>
                    <div class="lg:hidden divider my-0"></div>
                    <li class="lg:hidden mx-auto">
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button class="flex">
                                <span class="fa fa-person-running mr-2"></span><span>Keluar</span>
                            </button>
                        </form>
                    </li>
                @else
                    {{-- Not logged in (< lg) → Masuk + Buat Akun inside dropdown --}}
                    <li class="mx-auto">
                        <a href="{{ route('login') }}" class="flex items-center gap-2">
                            <span class="fa fa-right-to-bracket"></span>
                            <span>Masuk</span>
                        </a>
                    </li>
                    <div class="divider my-0"></div>
                    <li class="mx-auto mt-2">
                        <a href="{{ route('register') }}"
                            class="btn btn-primary text-center text-white rounded-full inline-flex items-center justify-center px-6 whitespace-nowrap">
                            Buat Akun
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</div>
