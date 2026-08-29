@extends('errors.layout')

@section('title', '401 Tidak Diotorisasi')
@section('code', '401')
@section('message', 'Autentikasi Diperlukan')
@section('description', 'Anda harus masuk ke akun Anda terlebih dahulu untuk mengakses halaman atau sumber daya ini.')

@section('icon')
<svg class="w-8 h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
</svg>
@endsection
