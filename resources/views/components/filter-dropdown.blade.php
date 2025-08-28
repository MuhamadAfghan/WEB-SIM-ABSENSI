<div id="filterDropdown" class="absolute right-0 mt-2 bg-white rounded-lg shadow-lg p-3 w-56 hidden z-50">

    <!-- List Filter -->
    <ul class="max-h-60 overflow-y-auto">
        @php
            $filters = [
                'PPLG', 'TJKT', 'DKV', 'KULINER', 'HOTEL', 'PMN', 'MPLB', 'MATEMATIKA', 'PKK',
                'PJOK', 'SEJARAH', 'B INGGRIS', 'B INDONESIA', 'PP', 'PABP', 'B SUNDA', 'INFORMATIKA'
            ];
        @endphp

        @foreach ($filters as $item)
            <li class="bg-blue-400 text-white px-3 py-1 rounded mb-1 cursor-pointer hover:bg-blue-500 text-center">
                {{ $item }}
            </li>
        @endforeach
    </ul>

</div>
