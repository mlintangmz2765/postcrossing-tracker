@extends('errors.layout')

@section('title', 'Not Found')
@section('code', '404')
@section('message', 'Address Unknown')
@section('description', 'The postcard you are looking for seems to have been lost in transit or the address is incorrect. Please check your map and try again.')

@section('stamp')
    RETURN<br>TO<br>SENDER
@endsection
