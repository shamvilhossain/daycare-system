{{-- Usage: @include('parent.partials.avatar', ['child' => $child, 'size' => '' | 'sm' | 'lg']) --}}
@php
    $sizeClass = !empty($size) ? 'pp-avatar-' . $size : '';
    $initials = strtoupper(mb_substr($child->first_name ?? '', 0, 1) . mb_substr($child->last_name ?? '', 0, 1));
@endphp
@if ($child->photo_url)
    <img src="{{ Storage::url($child->photo_url) }}" alt="{{ $child->full_name }}" class="pp-avatar {{ $sizeClass }}">
@else
    <span class="pp-avatar {{ $sizeClass }}" aria-hidden="true">{{ $initials ?: '?' }}</span>
@endif
