@extends('layouts.master')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@section('title') Facilities - ODAS Data Sync @endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Dashboards @endslot
        @slot('title') Facilities @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            @include('messages')
            {{-- <div class="alert alert-success alert-dismissable">
                <button type="button" class="close btn-close" data-bs-dismiss="alert" aria-bs-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{session('success')}}
            </div> --}}

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Facility Details</h4>
                    <div class="table-responsive">
                        <table id="facilities" class="table table-striped display compact" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th class="align-middle" width="10%">ODAS ID</th>
                                    <th class="align-middle" width="10px">Name</th>
                                    <th class="align-middle" max-width="10%">Address</th>
                                    <th class="align-middle" width="10%">City LGD</th>
                                    <th class="align-middle" width="10%">District LGD</th>
                                    <th class="align-middle" width="10%">Nodal Officer</th>
                                    <th class="align-middle" width="10%">Ownership Type</th>
                                    <th class="align-middle" width="10%">Facility Type</th>
                                    <th class="align-middle" width="20%">Last Updated</th>
                                    <th class="align-middle" width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allFacilities as $item)
                                    <tr>
                                        <td class="{{$item->odas_facility_id ? 'facility_id_column_green ' : 'facility_id_column_red'}}">{{$item->odas_facility_id ? $item->odas_facility_id : 'Not Updated'}}</td>
                                        <td>{{$item->facility_name}}</td>
                                        <td>
                                            {{$item->address_line_1}},<br/>{{$item->address_line_2}}<br/>
                                            PIN - {{$item->pincode}}
                                        </td>
                                        <td>
                                            {{$item->city_lgd_code}}
                                        </td>
                                        <td>
                                            {{$item->district_lgd_code}}
                                            {{-- <span class="badge badge-pill badge-soft-success font-size-11">Paid</span> --}}
                                        </td>
                                        <td>
                                            {{-- {{dd($item->FacilityNodalOfficer->officer_name)}} --}}
                                            {{$item->FacilityNodalOfficer ? $item->FacilityNodalOfficer->officer_name : 'NA'}}
                                        </td>
                                        <td>
                                            {{-- <i class="fab fa-cc-mastercard me-1"></i> Mastercard --}}
                                            {{$item->ownership_type}}
                                            ({{$item->ownership_subtype}})
                                        </td>
                                        <td>
                                            {{$item->facility_type}}
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($item->updated_at)->format('d, M y H:i:s')}}
                                        </td>
                                        {{-- <td>
                                            <!-- Button trigger modal -->
                                            <button type="button"
                                                class="btn btn-primary btn-sm btn-rounded waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target=".transaction-detailModal">
                                                View Details
                                            </button>
                                        </td> --}}
                                        <td>
                                            @if ($item->odas_facility_id == null)
                                                <a href="{{route('odas.facilityid.get',$item->facility_name)}}" class="btn btn-sm btn-warning">
                                                    Fetch ODAS Facility Ids
                                                </a>
                                                &nbsp;

                                            @else
                                                <p>Facility ID Generated</p>
                                                <a href="{{route('facility.bedinfo.update',$item->facility_name)}}" class="btn btn-sm btn-danger">
                                                    Update Beds
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    {{-- Nothing To Show --}}
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- end table-responsive -->
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row mx-auto">
                        <div class="col-10">
                            <a href="{{route('facilities.get')}}" class="btn btn-lg btn-warning">
                                Load Facilities
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- Transaction Modal -->
    <div class="modal fade transaction-detailModal" tabindex="-1" role="dialog"
        aria-labelledby="transaction-detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transaction-detailModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Product id: <span class="text-primary">#SK2540</span></p>
                    <p class="mb-4">Billing Name: <span class="text-primary">Neal Matthews</span></p>

                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <div>
                                            <img src="{{ URL::asset('/assets/images/product/img-7.png') }}" alt="" class="avatar-sm">
                                        </div>
                                    </th>
                                    <td>
                                        <div>
                                            <h5 class="text-truncate font-size-14">Wireless Headphone (Black)</h5>
                                            <p class="text-muted mb-0">$ 225 x 1</p>
                                        </div>
                                    </td>
                                    <td>$ 255</td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div>
                                            <img src="{{ URL::asset('/assets/images/product/img-4.png') }}" alt="" class="avatar-sm">
                                        </div>
                                    </th>
                                    <td>
                                        <div>
                                            <h5 class="text-truncate font-size-14">Phone patterned cases</h5>
                                            <p class="text-muted mb-0">$ 145 x 1</p>
                                        </div>
                                    </td>
                                    <td>$ 145</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <h6 class="m-0 text-right">Sub Total:</h6>
                                    </td>
                                    <td>
                                        $ 400
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <h6 class="m-0 text-right">Shipping:</h6>
                                    </td>
                                    <td>
                                        Free
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <h6 class="m-0 text-right">Total:</h6>
                                    </td>
                                    <td>
                                        $ 400
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end modal -->

    <!-- subscribeModal -->
    {{-- <div class="modal fade" id="subscribeModal" tabindex="-1" aria-labelledby="subscribeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="avatar-md mx-auto mb-4">
                            <div class="avatar-title bg-light rounded-circle text-primary h1">
                                <i class="mdi mdi-email-open"></i>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-xl-10">
                                <h4 class="text-primary">Subscribe !</h4>
                                <p class="text-muted font-size-14 mb-4">Subscribe our newletter and get notification to stay
                                    update.</p>

                                <div class="input-group bg-light rounded">
                                    <input type="email" class="form-control bg-transparent border-0"
                                        placeholder="Enter Email address" aria-label="Recipient's username"
                                        aria-describedby="button-addon2">

                                    <button class="btn btn-primary" type="button" id="button-addon2">
                                        <i class="bx bxs-paper-plane"></i>
                                    </button>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- end modal -->

@endsection
@section('script')
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- dashboard init -->
    <script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#facilities').DataTable();
            //alert('hi');
        } );
    </script>
@endsection
