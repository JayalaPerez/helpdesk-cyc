@props([
  'type' => 'button',
  'variant' => 'primary',  // primary | secondary | ghost | danger
  'size' => 'md',          // sm | md | lg
  'href' => null,
  'as' => null,            // 'a' para forzar <a>
])

@php
$base = 'inline-flex items-center justify-center rounded-xl font-medium transition shadow-sm';
$sizes = [
  'sm' => 'px-3 py-1.5 text-sm',
  'md' => 'px-4 py-2 text-sm',
  'lg' => 'px-5 py-2.5 text-base',
][$size];

$variants = [
  'primary'   => 'bg-zinc-900 text-white hover:bg-zinc-800 focus:ring-2 focus:ring-zinc-400/40',
  'secondary' => 'bg-white text-zinc-900 border border-zinc-200 hover:bg-zinc-50',
  'ghost'     => 'bg-transparent text-zinc-700 hover:bg-zinc-100',
  'danger'    => 'bg-red-600 text-white hover:bg-red-500 focus:ring-2 focus:ring-red-400/40',
][$variant];

$classes = "$base $sizes $variants";
@endphp

@if($href || $as === 'a')
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
  </a>
@else
  <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
  </button>
@endif
