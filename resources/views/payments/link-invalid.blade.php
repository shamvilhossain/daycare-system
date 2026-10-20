@extends('legal.layout')

@section('title', 'Link Expired')

@section('content')
    <div class="pp-card pp-empty">
        <i class="bi bi-link-45deg"></i>
        <div class="fw-bold text-dark mb-1">This link is invalid or has expired</div>
        <div>{{ $message ?? 'Please ask the daycare office to send you a new payment link, or pay from the parent app.' }}</div>
    </div>
@endsection
