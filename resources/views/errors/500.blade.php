@extends('errors::standalone')

@section('title', __('jejak.errors.500.title'))
@section('message', __('jejak.errors.500.message'))
@section('action')
    <a href="{{ route('home') }}">{{ __('jejak.errors.home') }}</a>
@endsection
