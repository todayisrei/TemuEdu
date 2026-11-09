@extends('layouts.admin')

@section('title', 'Tambah Konsultan')
@section('heading', 'Tambah Konsultan')

@section('content')
  <div class="card" style="max-width:860px">
    <form method="POST" action="{{ route('admin.consultants.store') }}" enctype="multipart/form-data">
      @csrf
      @include('admin.consultants._form')
    </form>
  </div>
@endsection
