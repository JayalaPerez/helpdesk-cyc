@props(['label' => null, 'name' => null, 'options' => []])

@if($label)
  <label for="{{ $name }}" class="text-sm font-medium text-zinc-700">{{ $label }}</label>
@endif
<select
  id="{{ $name }}"
  name="{{ $name }}"
  {{ $attributes->merge([
    'class' => 'w-full mt-1 rounded-xl border border-zinc-300 focus:border-zinc-500 focus:ring-2 focus:ring-zinc-400/40 px-3 py-2 bg-white'
  ]) }}
>
  {{ $slot }}
</select>
@error($name)
  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
