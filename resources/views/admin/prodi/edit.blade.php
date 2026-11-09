@extends('layouts.admin')

@section('title', 'Edit Program Studi')
@section('heading', 'Edit Program Studi')

@section('content')
  <div class="card" style="max-width:640px">
    <form method="POST" action="{{ route('admin.prodi.update', $prodi) }}">
      @csrf
      @method('PUT')
      @include('admin.prodi._form')
    </form>
  </div>
@endsection
