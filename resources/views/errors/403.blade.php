@extends('errors.layout')

@section('title', 'Forbidden')
@section('code', '403')
@section('message', 'Restricted Area')
@section('description', 'You do not have the necessary clearance to access this part of the archive. Authorized personnel only.')

@section('stamp')
    TOP<br>SECRET
@endsection
