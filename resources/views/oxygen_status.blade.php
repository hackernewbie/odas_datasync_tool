@extends('layouts.master')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@section('title') Oxygen Status - ODAS Data Sync @endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Dashboards @endslot
        @slot('title') Oxygen Status @endslot
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
                        <table id="infrastructure" class="table table-striped display compact" style="width:100%">
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
                                    {{-- <th class="align-middle" width="20%">View Details</th> --}}
                                    <th class="align-middle" width="20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allInOxygenStatus as $item)
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
                                            @else
                                                <p>Facility ID Generated</p>
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
                            <a href="{{route('syncdata')}}" class="btn btn-lg btn-warning">
                                Load Oxygen Data
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


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
            $('#infrastructure').DataTable();
            //alert('hi');
        } );
    </script>
@endsection