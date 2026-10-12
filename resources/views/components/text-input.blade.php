@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border border-gray-200 focus:border-gray-400 focus:ring-4 focus:ring-gray-100 rounded-md shadow-sm transition-all text-sm px-3 py-2']) }}>
