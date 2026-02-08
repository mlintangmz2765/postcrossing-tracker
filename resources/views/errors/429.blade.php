@extends('errors::layout')

@section('title', 'Too Many Requests')
@section('code', '429')
@section('message', 'Postbox Full')
@section('description', 'You are sending too many letters at once! Please wait a moment while we clear the chute before sending more requests.')

@section('stamp')
    OVER<br>LIMIT
@endsection
