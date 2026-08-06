@extends($activeTemplate.'layouts.advertiser.master')
@section('content')
<div class="body-wrapper">
    <div class="table-content">
        <div class="body-area">
            <div class="plan">
                <div class="row gy-4">
                    @foreach($packages as $package)
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="card">
                            <div class="content">
                                <h4>{{__($package->name)}}</h4>
                                <h3>
                                    {{$general->cur_sym}}{{showAmount($package->price)}} <span>/@lang('per ad')</span>
                                </h3>
                            </div>
                            <div>
                                <p>@lang('Ad Create Point'): {{$package->point}}</p>
                                <a href="{{route('advertiser.payment',$package->id)}}" class="btn btn--base">@lang('Get
                                    started')</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
