@extends('layouts.app')

@section('title','Hak Paten')

@section('content')

@php $activeStep = 1; @endphp
@include('hakpaten.isiformulir.menuformulir')

<section class="section-full section-content">
    <div class="section-inner">
        <div class="content-box">
            <div class="draft-paten">
            <h2>Draft Paten *</h2>
            </div>
            <div class="hero-buttons-start">
                <div class="button-unduh">
                    <a href="{{ route('download.template.draftpaten')}}" class="btn-template-draft-paten">Unduh Template Draft Paten</a>
                </div>
            </div>
        </div>
        <div class="next">
            <a id="nextLink" href="{{ route('hakpaten.isiformulir') }}" class="btn-selanjutnya is-disabled">Selanjutnya &raquo;</a>
        </div>
    </div>
</section>
@endsection
