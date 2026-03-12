<div onclick="openModal(
'{{ $title }}',
'{{ $category }}',
'{{ $image }}',
'{{ $longDescription }}'
)"
class="group cursor-pointer bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">

<div class="relative h-56 overflow-hidden">

<img src="{{ $image }}"
class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">

<div class="absolute top-4 left-4">

<span class="bg-blue-600 text-white text-[10px] font-bold uppercase px-3 py-1 rounded-full shadow-lg">
{{ $category }}
</span>

</div>

</div>

<div class="p-6">

<h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-blue-600">
{{ $title }}
</h3>

<p class="text-gray-600 text-sm mb-4">
{{ $description }}
</p>

<span class="text-blue-600 font-bold text-xs uppercase">
Detail Program →
</span>

</div>

</div>