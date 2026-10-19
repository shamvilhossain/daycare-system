@extends('parent.layouts.app')

@section('title', 'Notices')

@section('content')
    <div class="pp-page-title">Notices</div>
    <div class="pp-page-sub">Announcements from the daycare.</div>

    @forelse ($notices as $notice)
        <div class="pp-card">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                <div class="fw-bold fs-6">{{ $notice->title }}</div>
                <span class="small text-muted text-nowrap">{{ ($notice->published_at ?? $notice->created_at)->format('j M') }}</span>
            </div>
            <div class="small" style="white-space: pre-line">{{ $notice->content }}</div>
            <div class="small text-muted mt-2">
                <i class="bi bi-person me-1"></i>{{ $notice->author_name }}
                @if ($notice->expires_at)
                    · Until {{ $notice->expires_at->format('j M Y') }}
                @endif
            </div>
        </div>
    @empty
        <div class="pp-card pp-empty">
            <i class="bi bi-megaphone"></i>
            <div class="fw-bold text-dark mb-1">No notices right now</div>
            <div>New announcements will show up here.</div>
        </div>
    @endforelse

    @if ($notices->hasPages())
        <div class="mt-3">{{ $notices->links() }}</div>
    @endif
@endsection
