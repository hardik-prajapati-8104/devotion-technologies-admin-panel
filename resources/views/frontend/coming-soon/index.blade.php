@extends('frontend.layouts.app')

@section('title', 'Coming Soon')

@section('content')

<div class="page-wrap">

    <div class="md-content">

        <div class="hero md-skin-dark"
             style="background-image:url('{{ url('public/frontend/images/comming-soon-image.avif') }}')">

            @include('frontend.layouts.partials.header')

            <div class="container">

                <div class="hero__wrapper">

                    <div class="row">

                        <div class="col-lg-10 offset-lg-1">

                            <div class="hero__title_inner">
 
                                <h1 class="hero__title">
                                    We Are Almost Ready for Launch
                                </h1>

                                <p class="hero__text">
                                    Perfect and awesome template to present your future product or service.
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="countdown__module" data-date="2026-12-31T23:59:59">

                        <p>
                            <span id="days">00</span>
                            Days
                        </p>

                        <p>
                            <span id="hours">00</span>
                            Hours
                        </p>

                        <p>
                            <span id="minutes">00</span>
                            Minutes
                        </p>

                        <p>
                            <span id="seconds">00</span>
                            Seconds
                        </p>

                    </div>

                    @include('frontend.layouts.partials.services')

                </div>

            </div>

        </div>

    </div>

</div>

@endsection