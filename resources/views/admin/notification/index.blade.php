<x-app-layout>
    <div class="p-6 py-10 rounded overflow-x-auto">
        <div class="flex flex-col md:flex-row">
            <a href="{{ route('notification.create') }}"
                class="bg-primary text-primary-content w-fit h-fit rounded px-4 py-2 mb-4 inline-block">
                <i class="fa fa-plus"></i> Tambah Notifikasi
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table bg-base-200">
                <thead>
                    <tr class="bg-base-300">
                        <th>#</th>
                        <th>Judul</th>
                        <th>Pesan</th>
                        <th>Tipe</th>
                        <th>Broadcast</th>
                        <th>Tanggal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notifications as $index => $item)
                        <tr class="odd:bg-primary/5">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ Str::limit($item->message, 50) }}</td>
                            <td>{{ $item->type ?? '-' }}</td>
                            <td>
                                @if ($item->is_broadcast)
                                    <span class="text-green-600 font-bold">Yes</span>
                                @else
                                    <span class="text-gray-500">No</span>
                                @endif
                            </td>
                            <td>{{ $item->created_at->isoFormat('D MMMM Y HH:mm') }}</td>
                            <td class="flex justify-evenly">
                                <form action="{{ route('notification.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600"><i class="fa fa-trash"></i></button>
                                </form>
                                <a href="{{ route('notification.edit', $item->id) }}">
                                    <i class="fa fa-pen text-blue-600"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
