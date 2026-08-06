@extends('admin.layouts.app')
@section('panel')
<div class="d-flex flex-wrap justify-content-end mb-3">
    <div class="d-inline">
        <div class="input-group justify-content-end">
            <input type="text" name="search_table" class="form-control bg--white"
                placeholder="@lang('Search Plan')...">
            <button class="btn btn--primary input-group-text"><i class="fa fa-search"></i></button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two custom-data-table">
                        <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Referral Commission')</th>
                                <th>@lang('Points')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plans as $plan)
                            <tr>
                                <td>{{__($plan->name)}}</td>
                                <td>{{$general->cur_sym}}{{showAmount($plan->price) }}</td>
                                <td>{{__($plan->ref_level)}}</td>
                                <td>{{__($plan->point)}} @lang('Points')</td>
                                <td>
                                    @php echo $plan->statusBadge($plan->status); @endphp
                                </td>
                                <td>
                                    <div class="button--group">
                                        <button type="button" class="btn btn-sm btn--primary editBtn"
                                            data-id="{{ $plan->id }}" data-name="{{ $plan->name }}"
                                            data-price="{{ $plan->price }}"
                                            data-status="{{ $plan->status }}"
                                            data-point="{{ $plan->point}}"><i class="las la-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{__($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($plans->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($plans) }}
            </div>
            @endif
        </div><!-- card end -->
    </div>
</div>
{{-- Add modal --}}
<div id="addModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> @lang('Add Plan')</h5>
                <button type="button" class="close btn btn--danger" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.plan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name"> @lang('Name'):</label>
                        <input type="text" class="form-control" name="name" placeholder="@lang('Plan Name')" required>
                    </div>
                    <div class="form-group">
                        <label> @lang('Price') :</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="price" placeholder="@lang('Price of Plan')" required>
                            <div class="input-group-text">{{ $general->cur_text }}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label> @lang('Point') </label>
                        <input type="number" class="form-control" name="point" placeholder="@lang('Point')"
                            required>
                    </div>
                    <div class="form-group">
                        <label> @lang('Status')</label>
                        <label class="switch m-0">
                            <input type="checkbox" class="toggle-switch" name="status">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-global">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- edit modal --}}
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> @lang('Update')</h5>
                <button type="button" class="close btn btn--danger" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.plan.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name"> @lang('Name'):</label>
                        <input type="text" class="form-control" name="name" placeholder="@lang('Plan Name')" required>
                    </div>
                    <div class="form-group">
                        <label> @lang('Price') :</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" name="price" placeholder="@lang('Price of Plan')" required>
                              <div class="input-group-text">{{ $general->cur_text }}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label> @lang('Point') </label>
                        <input type="number" class="form-control" name="point" placeholder="@lang('Point')"
                            required>
                    </div>
                    <div class="form-group">
                        <label> @lang('Status')</label>
                        <label class="switch m-0">
                            <input type="checkbox" class="toggle-switch" name="status">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-global">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('breadcrumb-plugins')
<button type="button" class="btn btn-sm btn--primary addBtn"><i class="las la-plus"></i>@lang('Add New')</button>
@endpush
@push('script')
<script>
    (function($){
        "use strict";
        $('.addBtn').on('click', function() {
            var modal = $('#addModal');
            modal.modal('show');
        });

        $('.editBtn').on('click', function() {
            var modal = $('#editModal');
            modal.find('.act').text($(this).data('act'));
            modal.find('input[name=id]').val($(this).data('id'));
            modal.find('input[name=name]').val($(this).data('name'));
            modal.find('input[name=price]').val($(this).data('price'));
            modal.find('input[name=point]').val($(this).data('point'));
            modal.find('input[name=status]').prop('checked', $(this).data('status') == 1 ? true : false );
            modal.find('input[name=status]').val($(this).data('status') == 1 ? 1 : 0);
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush
