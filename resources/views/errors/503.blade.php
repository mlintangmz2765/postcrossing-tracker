@extends('errors.layout')

@section('title', 'Service Unavailable')
@section('code', '503')
@section('message', 'Post Office Closed')
@section('description', 'We are currently performing scheduled maintenance on our sorting machines. We will be back open for business shortly.')

@section('stamp')
    CLOSED<br>TEMP
@endsection
