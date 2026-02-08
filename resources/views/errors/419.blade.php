@extends('errors::layout')

@section('title', 'Page Expired')
@section('code', '419')
@section('message', 'Stamp Expired')
@section('description', 'Your session stamp has lost its adhesive. Please refresh the page and try sticking it again.')

@section('stamp')
    VOID<br>STAMP
@endsection
