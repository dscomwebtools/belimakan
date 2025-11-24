<x-app-layout>
    <div class="sm:mx-6 lg:mx-8 p-6 py-10 bg-secondary text-secondary-content rounded overflow-x-auto">

        <div class="flex flex-col sm:flex-row items-center sm:items-start p-4 gap-8 sm:gap-2">

            {{-- LEFT PROFILE --}}
            <div class="card w-80 bg-base-200 shadow-xl dark:text-base-content">
                <figure class="px-10 pt-10 pb-2">
                    <div class="avatar">
                        <div class="w-full rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                            <img src="{{ isset($data->img) ? Storage::url($data->img) : asset('assets/images/user_not_found.png') }}"
                                alt="profile picture" />
                        </div>
                    </div>
                </figure>

                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ $data->fullname }}</h2>
                    <p>{{ $data->status }}</p>
                </div>
            </div>

            {{-- RIGHT CONTENT --}}
            <div class="flex-1 bg-base-200 shadow-xl card dark:text-base-content" x-data="{ activeTab: 1 }">

                <!-- TAB BUTTONS -->
                <div class="flex bg-base-300 rounded-t-2xl">

                    <button class="uppercase px-8 py-3 -mb-px text-sm rounded-t-2xl"
                        :class="activeTab === 1 ? 'border-base-200 bg-base-200 font-bold' : 'text-gray-600'"
                        @click="activeTab = 1">
                        DATA MEMBER
                    </button>

                    <button class="uppercase px-8 py-3 -mb-px text-sm rounded-t-2xl"
                        :class="activeTab === 2 ? 'border-base-200 bg-base-200 font-bold' : 'text-gray-600'"
                        @click="activeTab = 2">
                        ALAMAT
                    </button>

                </div>

                <!-- TAB CONTENT -->
                <div class="p-6 bg-base-200 rounded text-sm">

                    <!-- TAB 1 - DATA MEMBER -->
                    <div x-show="activeTab === 1">

                        <h2 class="font-bold uppercase mb-2">Data Diri</h2>

                        <div class="flex gap-2 leading-10">
                            <span class="opacity-75">Nama Lengkap :</span>
                            <span>{{ $data->fullname }}</span>
                        </div>

                        <div class="flex gap-2 leading-10">
                            <span class="opacity-75">Nama Panggilan :</span>
                            <span>{{ $data->name }}</span>
                        </div>

                        <div class="flex gap-2 leading-10">
                            <span class="opacity-75">Tanggal Lahir :</span>
                            <span>{{ $data->birthday ? $data->birthday->isoFormat('LL') : '-' }}</span>
                        </div>

                        <div class="flex gap-2 leading-10">
                            <span class="opacity-75">Jenis Kelamin :</span>
                            <span>
                                @if ($data->gender === 'm')
                                    Laki
                                @elseif ($data->gender === 'f')
                                    Perempuan
                                @else
                                    -
                                @endif
                            </span>
                        </div>

                        <div class="flex gap-2 leading-10">
                            <span class="opacity-75">Status Pernikahan :</span>
                            <span>{{ $data['status-pernikahan'] }}</span>
                        </div>

                        <div class="flex gap-2 leading-10">
                            <span class="opacity-75">Pekerjaan :</span>
                            <span>{{ $data->occupation }}</span>
                        </div>

                        <div class="flex gap-2 leading-10">
                            <span class="opacity-75">Pendidikan :</span>
                            <span>{{ $data->education }}</span>
                        </div>

                        <h2 class="font-bold uppercase mb-2 mt-4">Kontak</h2>

                        <div class="flex gap-2 leading-10">
                            <span class="opacity-75">Nomor HP :</span>
                            <span>{{ $data->hp }}</span>
                        </div>

                        <div class="flex gap-2 leading-10">
                            <span class="opacity-75">Email :</span>
                            <span>{{ $data->email }}</span>
                        </div>

                        <div class="flex items-center mt-4">
                            <div class="flex gap-2 leading-10 opacity-50">
                                <span>Tanggal bergabung :</span>
                                <span>{{ $data->created_at->isoFormat('LL') }}</span>
                            </div>
                        </div>

                        <a href="{{ route('admin-member.index') }}"
                            class="py-2 px-4 bg-gray-500 text-gray-50 text-center mt-6 rounded inline-block">
                            {{ __('Kembali') }}
                        </a>

                    </div>

                    <!-- TAB 2 - ALAMAT -->
                    <div x-show="activeTab === 2">

                        @forelse ($data->address as $item)
                            <div class="bg-base-100 border border-base-300 rounded-xl p-5 mb-4 shadow-sm">

                                <div class="flex items-center mb-3">
                                    <h2 class="font-semibold text-lg">{{ $item->name }}</h2>
                                    @if ($item->isMain)
                                        <span class="ml-3 badge badge-primary badge-sm">Utama</span>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-700">{{ $item->address }}</p>

                                @if ($item->noteForCurrier)
                                    <p class="text-xs text-gray-500 mt-1">
                                        ({{ $item->noteForCurrier }})
                                    </p>
                                @endif

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4 text-sm">
                                    <div><span class="opacity-60">Provinsi:</span> <span
                                            class="font-medium">{{ $item->province }}</span></div>
                                    <div><span class="opacity-60">Kota / Kabupaten:</span> <span
                                            class="font-medium">{{ $item->city }}</span></div>
                                    <div><span class="opacity-60">Kecamatan:</span> <span
                                            class="font-medium">{{ $item->district }}</span></div>
                                    <div><span class="opacity-60">Kode POS:</span> <span
                                            class="font-medium">{{ $item->postal_code }}</span></div>
                                    <div><span class="opacity-60">Penerima:</span> <span
                                            class="font-medium">{{ $item->recipient }}</span></div>
                                    <div><span class="opacity-60">HP:</span> <span
                                            class="font-medium">{{ $item->hp }}</span></div>
                                </div>

                            </div>
                        @empty
                            <p class="text-center text-gray-500 italic">Belum ada alamat.</p>
                        @endforelse

                    </div>

                </div>

            </div>

        </div>

    </div>
</x-app-layout>
