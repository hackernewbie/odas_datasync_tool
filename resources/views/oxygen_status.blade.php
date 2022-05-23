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
                                    <th class="align-middle" width="10%">Name</th>
                                    <th class="align-middle" width="10%">Tybe B</th>
                                    <th class="align-middle" width="10%">Type D7</th>
                                    <th class="align-middle" width="10%">LMO Stock (MT)</th>
                                    <th class="align-middle" width="10%">LMO Capacity (MT)</th>
                                    <th class="align-middle" width="10%">PSA Gen Capacity (MT)</th>
                                    <th class="align-middle" width="10%">PSA Capacity (MT)</th>
                                    <th class="align-middle" width="10%">Gen Beds</th>
                                    <th class="align-middle" width="10%">HDU Beds</th>
                                    <th class="align-middle" width="10%">ICU Beds</th>
                                    <th class="align-middle" width="10%">O2 Concentrators</th>
                                    <th class="align-middle" width="10%">Vent Beds</th>

                                    <th class="align-middle" width="10%">Accuracy Remarks</th>
                                    <th class="align-middle" width="10%">Flag</th>
                                    <th class="align-middle" width="10%">Demand For Date</th>
                                    <th class="align-middle" width="10%">OE Demand By</th>
                                    <th class="align-middle" width="10%">Tot. Estimated Demand</th>
                                    <th class="align-middle" width="10%">UE Demand By</th>

                                    <th class="align-middle" width="10%">Consumption For Date</th>
                                    <th class="align-middle" width="10%">Consumption Updated Date</th>
                                    <th class="align-middle" width="10%">Total O2 Consumed</th>
                                    <th class="align-middle" width="10%">Total O2 Delivered</th>
                                    <th class="align-middle" width="10%">Total O2 Generated</th>

                                    <th class="align-middle" width="40%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allInOxygenStatus as $item)
                                    <tr>
                                        <td class="{{$item->odas_facility_id ? 'facility_id_column_green ' : 'facility_id_column_red'}}">{{$item->odas_facility_id ? $item->odas_facility_id : 'Not Updated'}}</td>
                                        <td>{{$item->facility_name}}</td>
                                        <td>
                                            {{$item->total_typeB_cylinders_available}}
                                        </td>
                                        <td>
                                            {{$item->total_typeD_cylinders}}
                                        </td>
                                        <td>
                                            {{$item->lmo_current_stock_in_MT}}
                                        </td>
                                        <td>
                                            {{$item->lmo_current_storage_capacity_in_MT}}
                                        </td>
                                        <td>
                                            {{$item->psa_gen_capacity_in_MT}}
                                        </td>
                                        <td>
                                            {{$item->psa_storage_capacity_in_MT}}
                                        </td>
                                        <td>{{$item->FacilityBedInfo ? $item->FacilityBedInfo->no_gen_beds : 'NA'}}</td>
                                        <td>{{$item->FacilityBedInfo ? $item->FacilityBedInfo->no_hdu_beds : 'NA'}}</td>
                                        <td>{{$item->FacilityBedInfo ? $item->FacilityBedInfo->no_icu_beds : 'NA'}}</td>
                                        <td>{{$item->FacilityBedInfo ? $item->FacilityBedInfo->no_o2_concentrators : 'NA'}}</td>
                                        <td>{{$item->FacilityBedInfo ? $item->FacilityBedInfo->no_vent_beds : 'NA'}}</td>

                                        <td>No Date</td>
                                        <td>No Date</td>
                                        <td>No Date</td>
                                        <td>No Date</td>
                                        <td>No Date</td>
                                        <td>No Date</td>

                                        <td>{{$item->FacilityOxygenConsumption ? $item->FacilityOxygenConsumption->consumption_for_date : 'NA'}}</td>
                                        <td>{{$item->FacilityOxygenConsumption ? $item->FacilityOxygenConsumption->consumption_updated_date : 'NA'}}</td>
                                        <td>{{$item->FacilityOxygenConsumption ? $item->FacilityOxygenConsumption->total_oxygen_consumed : 'NA'}}</td>
                                        <td>{{$item->FacilityOxygenConsumption ? $item->FacilityOxygenConsumption->total_oxygen_delivered : 'NA'}}</td>
                                        <td>{{$item->FacilityOxygenConsumption ? $item->FacilityOxygenConsumption->total_oxygen_generated : 'NA'}}</td>

                                        {{-- <td>
                                            <!-- Button trigger modal -->
                                            <button type="button"
                                                class="btn btn-primary btn-sm btn-rounded waves-effect waves-light"
                                                data-bs-toggle="modal" data-bs-target=".transaction-detailModal">
                                                View Details
                                            </button>
                                        </td> --}}
                                        <td>
                                            @if ($item->odas_facility_id !== null && $item->status == null)
                                                <a href="{{route('update.oxygen.data',$item->facility_name)}}" class="btn btn-sm btn-warning">
                                                    Push O2 Infra
                                                </a>
                                            @endif

                                            @if ($item->odas_facility_id !== null && $item->FacilityBedInfo->status == null)
                                                <a href="{{route('update.facility.bed.occupancy',$item->odas_facility_id)}}" class="btn btn-sm btn-success">
                                                    Push Bed Occupancy
                                                </a>
                                            @endif

                                            @if ($item->odas_facility_id !== null)
                                                <a href="{{route('update.oxygen.demand',$item->odas_facility_id)}}" class="btn btn-sm btn-info">
                                                    Push O2 Demand
                                                </a>
                                            @endif

                                            @if ($item->odas_facility_id !== null && $item->FacilityOxygenConsumption->status == null)
                                                <a href="{{route('update.facility.oxygen.consumption',$item->odas_facility_id)}}" class="btn btn-sm btn-danger">
                                                    Push O2 Consumption
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
                            <a href="{{route('fetch.oxygen.data')}}" class="btn btn-lg btn-warning">
                                Get Latest Oxygen Data
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
