@extends('errors::layout')

@section('title', 'Unauthorized')
@section('code', '401')
@section('message', 'Identification Required')
@section('description', 'You must present valid identification to access this correspondence. Please sign in to proceed.')

@section('stamp')
    ID<br>REQUIRED
@endsection
