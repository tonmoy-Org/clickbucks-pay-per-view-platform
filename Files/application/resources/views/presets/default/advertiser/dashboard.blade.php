@extends($activeTemplate.'layouts.advertiser.master')
@section('content')
<div class="body-wrapper">
    <div class="table-content">
        <div class="row gy-4 mb-4">

            <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                <div class="dash-card">
                    <a href="javascript:void(0)" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('Balance')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">{{$general->cur_sym}}{{showAmount(authAdvertiser()->balance)}}</span>
                            </div>
                        </div>
                        <div class="icon my-auto">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                <div class="dash-card">
                    <a href="{{route('advertiser.ptc.index')}}" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('Total Ads')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">{{$data['total_ads']}}</span>
                            </div>

                        </div>
                        <div class="icon my-auto">
                            <i class="fas fa-ad"></i>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                <div class="dash-card">
                    <a href="{{route('advertiser.ptc.pending')}}" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('Pending Ads')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">{{$data['pending_ads']}}</span>
                            </div>

                        </div>
                        <div class="icon my-auto">
                            <i class="fas fa-ad"></i>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                <div class="dash-card">
                    <a href="{{route('advertiser.get.packages')}}" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('Ad Create Points')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">{{$data['total_points']}}</span>
                            </div>

                        </div>
                        <div class="icon my-auto">
                            <i class="fas fa-plus"></i>
                        </div>
                    </a>
                </div>
            </div>

        </div>

        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="chart">
                    <div class="chart-bg">
                        <h4>@lang('Monthly Deposit & Withdrawal Reports')</h4>
                        <div id="account-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="m-0">
            <div class="list-card">
                <div class="header-title-list">
                    <h4 class="pb-0">@lang('Latest Ads')</h4>
                </div>
                <div class="row justify-content-center">
                    <div class="col-xl-12">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Duration')</th>
                                    <th>@lang('Max Show')</th>
                                    <th>@lang('Showed')</th>
                                    <th>@lang('Remain')</th>
                                    <th>@lang('Image/Link/Script')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ptcs as $ptc)
                                <tr>
                                    <td  data-label="@lang('Title')">{{__($ptc->title)}}</td>
                                    <td  data-label="@lang('Duration')">{{$ptc->duration}} </td>
                                    <td  data-label="@lang('Max Show')">{{$ptc->max_show}}</td>
                                    <td  data-label="@lang('Showed')">{{$ptc->showed}}</td>
                                    <td  data-label="@lang('Remain')">{{$ptc->remain}}</td>

                                    @if(in_array($ptc->ads_type, [1, 4]))
                                    <td data-label="@lang('Image/Link/Script')"><span class="badge badge--success" title="{{$ptc->ads_body}}"><i class="las la-link"></i></span></td>
                                    @endif

                                    @if($ptc->ads_type == 2)
                                        <td data-label="@lang('Image/Link/Script')"><img class="rounded" src="{{ getImage(getFilePath('ptc').'/'.$ptc->ads_body) }}" alt="" style="width:50px"></td>
                                    @endif

                                    @if($ptc->ads_type == 3)
                                        <td  data-label="@lang('Image/Link/Script')"><span class="badge badge--success" title="{{$ptc->ads_body}}"><i class="las la-comment-alt"></i></span></td>
                                    @endif

                                    <td  data-label="@lang('Status')">
                                        @php echo $ptc->statusBadge($ptc->status); @endphp
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <a href="{{route('advertiser.ptc.edit',$ptc->id)}}" class="btn btn--sm btn--base" title="Edit"><i class="las la-edit"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td data-label="@lang('Ad Table')" class="text-muted text-center" colspan="100%">{{__($emptyMessage) }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('script')
<script src="{{asset('assets/admin/js/apexcharts.min.js')}}"></script>
<script>
   (function () {
    'use strict';
        var options = {
            chart: {
                type: 'area',
                stacked: false,
                height: '310px'
            },
            stroke: {
                width: [0, 3],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%'
                }
            },
            colors: ['#04DA8D', '#ee6f11'],
            series: [{
                name: '@lang("Withdrawals")',
                type: 'column',
                data: @json($withdrawalsChart['values'])
    }, {
        name: '@lang("Deposits")',
        type: 'area',
        data: @json($depositsChart['values'])
    }],
    fill: {
        opacity: [0.85, 1],
                },
    labels: @json($depositsChart['labels']),
    markers: {
        size: 0
    },
    xaxis: {
        type: 'text'
    },
    yaxis: {
        min: 0
    },
    tooltip: {
        shared: true,
            intersect: false,
                y: {
            formatter: function (y) {
                if (typeof y !== "undefined") {
                    return "$ " + y.toFixed(0);
                }
                return y;

            }
        }
    },
    legend: {
        labels: {
            useSeriesColors: true
        },
        markers: {
            customHTML: [
                function () {
                    return ''
                },
                function () {
                    return ''
                }
            ]
        }
    }
            }
    var chart = new ApexCharts(
        document.querySelector("#account-chart"),
        options
    );
    chart.render();
        }) ();
</script>
@endpush


