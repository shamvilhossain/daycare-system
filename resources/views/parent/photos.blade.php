@extends('parent.layouts.app')

@section('title', 'Photos')

@section('content')
    <div class="pp-page-title">Photos</div>
    <div class="pp-page-sub">Class activity photos from {{ $activeChild->first_name }}'s program.</div>

    @forelse ($grouped as $day => $items)
        @php $occurrences = $items->pluck('activityOccurrence')->unique('id'); @endphp
        <div class="pp-card">
            <div class="d-flex justify-content-between align-items-baseline mb-2">
                <div class="fw-bold">{{ \Carbon\Carbon::parse($day)->format('D, j M Y') }}</div>
                <span class="text-muted small">{{ $items->count() }} {{ Str::plural('item', $items->count()) }}</span>
            </div>
            <div class="d-flex flex-wrap gap-1 mb-2">
                @foreach ($occurrences as $occ)
                    <span class="pp-chip">{{ $occ->activity?->name ?? 'Activity' }}</span>
                @endforeach
            </div>
            <div class="pp-photo-grid">
                @foreach ($items as $item)
                    @php $caption = $item->caption ?: ($item->activityOccurrence->activity?->name ?? ''); @endphp
                    <a href="{{ asset($item->file_url) }}" data-pp-media="{{ $item->media_type }}" data-caption="{{ $caption }}">
                        @if ($item->media_type === 'video')
                            <video src="{{ asset($item->file_url) }}#t=0.1" preload="metadata" muted playsinline></video>
                            <i class="bi bi-play-circle-fill fs-4 pp-video-badge"></i>
                        @else
                            <img src="{{ asset($item->file_url) }}" alt="{{ $caption ?: 'Activity photo' }}" loading="lazy">
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @empty
        <div class="pp-card pp-empty">
            <i class="bi bi-images"></i>
            <div class="fw-bold text-dark mb-1">No photos yet</div>
            <div>Photos from class activities will appear here.</div>
        </div>
    @endforelse

    @if ($media && $media->hasPages())
        <div class="mt-3">{{ $media->links() }}</div>
    @endif

    {{-- Lightbox --}}
    <div class="modal fade pp-lightbox" id="ppLightbox" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <div class="pp-lightbox-caption text-white small"></div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex align-items-center pp-lightbox-body"></div>
            </div>
        </div>
    </div>
@endsection
