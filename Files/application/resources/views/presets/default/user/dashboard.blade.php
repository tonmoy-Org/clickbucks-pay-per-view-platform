@extends($activeTemplate.'layouts.user.master')
@section('content')
<div class="body-wrapper">
    <div class="table-content">
        <div class="row gy-4 mb-4">

            <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                <div class="dash-card">
                    <a href="javascript:void(0)" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('Total Balance')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">{{$general->cur_sym}}{{showAmount(auth()->user()->balance)}}</span>
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
                    <a href="{{ route('user.ptc.index') }}" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('View Ads')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">
                                @if(($data['totalAds'] - $user->clicks->where('view_date', Date('Y-m-d'))->count()) <= 0)
                                    0
                                @else
                                    {{ $data['totalAds'] - $user->clicks->where('view_date', Date('Y-m-d'))->count() }}
                                @endif
                                </span>
                            </div>

                        </div>
                        <div class="icon my-auto">
                            <i class="fas fa-eye"></i>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-4 col-12">
                <div class="dash-card">
                    <a href="{{route('user.ptc.today.click')}}" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('Today Clicks')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">
                                    {{$user->clicks->where('view_date',Date('Y-m-d'))->count()}}
                                </span>
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
                    <a href="{{route('user.ptc.today.click')}}" class="d-flex justify-content-between">
                        <div>
                            <div>
                                <p>@lang('Total Clicks')</p>
                            </div>
                            <div class="content">
                                <span class="text-uppercase">
                                    {{$user->clicks->count()}}
                                </span>
                            </div>

                        </div>
                        <div class="icon my-auto">
                            <i class="fas fa-ad"></i>
                        </div>
                    </a>
                </div>
            </div>

        </div>

        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="chart">
                    <div class="chart-bg">
                        <h4>@lang('Click & Earn Reports')</h4>
                        <div id="apex-bar-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="chart">
                    <div class="chart-bg">
                        <h4>@lang('Monthly Withdrawal Reports')</h4>
                        <div id="account-chart"></div>
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
(function ($) {
    "use strict";

    var options = {
      series: [{
      name: 'Clicks',
      data: [
        @foreach($chart['click'] as $key => $click)
            {{ $click }},
        @endforeach
      ]
    }, {
      name: 'Earn Amount',
      data: [
            @foreach($chart['amount'] as $key => $amount)
                {{ $amount }},
            @endforeach
      ]
    }],
      chart: {
      type: 'bar',
      height: 350,
      toolbar: {
        show: false
      }
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '20%',
        endingShape: 'rounded'
      },
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      show: true,
      width:4,
      colors: ['transparent'],
    },
    xaxis: {
      categories: [
      @foreach($chart['amount'] as $key => $amount)
                '{{ $key }}',
            @endforeach
    ],
    },

    fill: {
      opacity: 1
    },
    tooltip: {
      y: {
        formatter: function (val) {
          return val
        }
      }
    }
    };
    var chart = new ApexCharts(document.querySelector("#apex-bar-chart"), options);
    chart.render();
})(jQuery);


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
    }],
    fill: {
        opacity: [0.85, 1],
                },
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

