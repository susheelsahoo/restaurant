@extends('frontend.layouts.app')

@section('meta_title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description ?? Str::limit(strip_tags($page->content), 160))
@section('meta_keywords', $page->meta_keywords ?? 'hotel, mediator, booking, management')

@section('content')
@include(config('settings.FRONTED_PAGE_DIR').'/layouts/_menu')
{!! $page->content !!}
</div>
@endsection