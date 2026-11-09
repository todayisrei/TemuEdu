@extends('layouts.admin')

@section('title', 'Tambah Program Studi')
@section('heading', 'Tambah Program Studi')

@section('content')
  <div class="card" style="max-width:640px">
    <form method="POST" action="{{ route('admin.prodi.store') }}">
      @csrf
      @include('admin.prodi._form')
    </form>
  </div>
@endsection
