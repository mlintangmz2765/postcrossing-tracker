@extends('errors.layout')

@section('title', 'Server Error')
@section('code', '500')
@section('message', 'Service Disruption')
@section('description', 'Our postal service is currently experiencing some technical difficulties. Our postmasters are working hard to sort it out.')

@section('stamp')
    DAMAGED<br>PARCEL
@endsection
