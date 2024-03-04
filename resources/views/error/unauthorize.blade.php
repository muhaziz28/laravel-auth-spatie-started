@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">

        </div>
    </section>

    <section class="content">
        <div class="error-page d-flex">
            <h2 class="headline text-warning"> 401</h2>
            <div class="error-content ">
                <h3><i class="fas fa-exclamation-triangle text-warning"></i> Unauthorized</h3>
                <p>
                    You are unauthorized to access this page.
                    Meanwhile, you may <a href="{{ route('home') }}">return to dashboard</a>.
                </p>
            </div>

        </div>

    </section>
</div>
@endsection