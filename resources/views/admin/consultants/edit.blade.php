@extends('layouts.admin')

@section('title', 'Edit Konsultan')
@section('heading', 'Edit Konsultan')

@section('content')
  <div class="card" style="max-width:860px">
    <form method="POST" action="{{ route('admin.consultants.update', $consultant) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('admin.consultants._form')
    </form>
  </div>
@endsection
