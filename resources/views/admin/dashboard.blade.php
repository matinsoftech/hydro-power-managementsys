@extends('layouts.app')

@section('title','Dashboard')

@section('styles')
<style>
    .btn-lg-circle{
        height: 250px;
        width: 250px;
        border-radius: 100%;
        font-size: xx-large;
    }
</style>
<style>
    .main_div{
        position: relative;
    }
    #map {
        height: 100%;
        width: 100%;
    }
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    .control-panel {
        width: 100%;
        max-width: 600px;
        z-index: 1;
        background-color: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .control-panel input,
    .control-panel select {
        margin-bottom: 5px;
    }
    .control-panel h3{
      font-size: 24px;
      color: #000;
    }

    .control-panel input,
    .control-panel select{
     padding: 10px;
     width: 100%;
    }
    .control-panel label{
        display: block;
    }
</style>
@endsection

@section('content')
    {{-- <div class="page-breadcrumb">
        <div class="row">
            <div class="col-7 align-self-center">
                <h3 class="page-title text-truncate text-dark font-weight-medium mb-1">Good Morning Jason!</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-5 align-self-center">
                <div class="customize-input float-end">
                    <select
                        class="custom-select custom-select-set form-control bg-white border-0 custom-shadow custom-radius">
                        <option selected>Aug 23</option>
                        <option value="1">July 23</option>
                        <option value="2">Jun 23</option>
                    </select>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- ============================================================== -->
    <!-- End Bread crumb and right sidebar toggle -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        <!-- *************************************************************** -->
        <!-- Start First Cards -->
        <!-- *************************************************************** -->
        <div class="row">
            <div class="col-sm-6 col-lg-3">
                <div class="card border-end">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">{{ $TotalUsers }}</h2>
                                    {{-- <span class="badge bg-primary font-12 text-white font-weight-medium rounded-pill ms-2 d-lg-block d-md-none">+18.33%</span> --}}
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Total Number User
                                </h6>
                            </div>
                            <div class="ms-auto mt-md-3 mt-lg-0">
                                <span class="opacity-7 text-muted"><i data-feather="user-plus"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-end ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h2 class="text-dark mb-1 w-100 text-truncate font-weight-medium">
                                    {{-- <sup class="set-doller">$</sup> --}}
                                   {{ $TotalMeterReading }}</h2>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">
                                    Total meter reading
                                </h6>
                            </div>
                            <div class="ms-auto mt-md-3 mt-lg-0">
                                {{-- <span class="opacity-7 text-muted"><i data-feather="dollar-sign"></i></span> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-end ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="d-inline-flex align-items-center">
                                    <h2 class="text-dark mb-1 font-weight-medium">{{$TotalFault  }}</h2>
                                    {{-- <span
                                        class="badge bg-danger font-12 text-white font-weight-medium rounded-pill ms-2 d-md-none d-lg-block">-18.33%</span> --}}
                                </div>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">
                                    Total Number of fault
                                </h6>
                            </div>
                            <div class="ms-auto mt-md-3 mt-lg-0">
                                {{-- <span class="opacity-7 text-muted"><i data-feather="file-plus"></i></span> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-sm-6 col-lg-3">
                <div class="card ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <h2 class="text-dark mb-1 font-weight-medium">864</h2>
                                <h6 class="text-muted font-weight-normal mb-0 w-100 text-truncate">Projects</h6>
                            </div>
                            <div class="ms-auto mt-md-3 mt-lg-0">
                                <span class="opacity-7 text-muted"><i data-feather="globe"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
        <!-- *************************************************************** -->
        <!-- End First Cards -->
        <!-- *************************************************************** -->
        <!-- *************************************************************** -->
        <!-- Start Sales Charts Section -->
        <!-- *************************************************************** -->
        <div class="row">
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        <button id="checkInOut" class="btn btn-success btn-lg-circle">
                            Check In
                        </button>
                    </div>
                </div>
            </div>
            {{-- <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Net Income</h4>
                        <div class="net-income mt-4 position-relative" style="height:294px;"></div>
                        <ul class="list-inline text-center mt-5 mb-2">
                            <li class="list-inline-item text-muted fst-italic">Sales for this month</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Earning by Location</h4>
                        <div class="" style="height:180px">
                            <div id="visitbylocate" style="height:100%"></div>
                        </div>
                        <div class="row mb-3 align-items-center mt-5">
                            <div class="col-4 text-end">
                                <span class="text-muted font-14">India</span>
                            </div>
                            <div class="col-5">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col-3 text-end">
                                <span class="mb-0 font-14 text-dark font-weight-medium">28%</span>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-end">
                                <span class="text-muted font-14">UK</span>
                            </div>
                            <div class="col-5">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 74%"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col-3 text-end">
                                <span class="mb-0 font-14 text-dark font-weight-medium">21%</span>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-end">
                                <span class="text-muted font-14">USA</span>
                            </div>
                            <div class="col-5">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-cyan" role="progressbar" style="width: 60%"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col-3 text-end">
                                <span class="mb-0 font-14 text-dark font-weight-medium">18%</span>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-4 text-end">
                                <span class="text-muted font-14">China</span>
                            </div>
                            <div class="col-5">
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 50%"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col-3 text-end">
                                <span class="mb-0 font-14 text-dark font-weight-medium">12%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
        <!-- *************************************************************** -->
        <!-- End Sales Charts Section -->
        <!-- *************************************************************** -->
        <!-- *************************************************************** -->
        <!-- Start Location and Earnings Charts Section -->
        <!-- *************************************************************** -->
        {{-- <div class="row">
            <div class="col-md-6 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <h4 class="card-title mb-0">Earning Statistics</h4>
                            <div class="ms-auto">
                                <div class="dropdown sub-dropdown">
                                    <button class="btn btn-link text-muted dropdown-toggle" type="button" id="dd1"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i data-feather="more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dd1">
                                        <a class="dropdown-item" href="#">Insert</a>
                                        <a class="dropdown-item" href="#">Update</a>
                                        <a class="dropdown-item" href="#">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pl-4 mb-5">
                            <div class="stats ct-charts position-relative" style="height: 315px;"></div>
                        </div>
                        <ul class="list-inline text-center mt-4 mb-0">
                            <li class="list-inline-item text-muted fst-italic">Earnings for this month</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Recent Activity</h4>
                        <div class="mt-4 activity">
                            <div class="d-flex align-items-start border-left-line pb-3">
                                <div>
                                    <a href="javascript:void(0)" class="btn btn-info btn-circle mb-2 btn-item">
                                        <i data-feather="shopping-cart"></i>
                                    </a>
                                </div>
                                <div class="ms-3 mt-2">
                                    <h5 class="text-dark font-weight-medium mb-2">New Product Sold!</h5>
                                    <p class="font-14 mb-2 text-muted">John Musa just purchased <br> Cannon 5M
                                        Camera.
                                    </p>
                                    <span class="font-weight-light font-14 text-muted">10 Minutes Ago</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-start border-left-line pb-3">
                                <div>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-circle mb-2 btn-item">
                                        <i data-feather="message-square"></i>
                                    </a>
                                </div>
                                <div class="ms-3 mt-2">
                                    <h5 class="text-dark font-weight-medium mb-2">New Support Ticket</h5>
                                    <p class="font-14 mb-2 text-muted">Richardson just create support <br>
                                        ticket</p>
                                    <span class="font-weight-light font-14 text-muted">25 Minutes Ago</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-start border-left-line">
                                <div>
                                    <a href="javascript:void(0)" class="btn btn-cyan btn-circle mb-2 btn-item">
                                        <i data-feather="bell"></i>
                                    </a>
                                </div>
                                <div class="ms-3 mt-2">
                                    <h5 class="text-dark font-weight-medium mb-2">Notification Pending Order!
                                    </h5>
                                    <p class="font-14 mb-2 text-muted">One Pending order from Ryne <br> Doe</p>
                                    <span class="font-weight-light font-14 mb-1 d-block text-muted">2 Hours
                                        Ago</span>
                                    <a href="javascript:void(0)" class="font-14 border-bottom pb-1 border-info">Load
                                        More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <!-- *************************************************************** -->
        <!-- End Location and Earnings Charts Section -->
        <!-- *************************************************************** -->
        <!-- *************************************************************** -->
        <!-- Start Top Leader Table -->
        <!-- *************************************************************** -->
        {{-- <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <h4 class="card-title">Top Leaders</h4>
                            <div class="ms-auto">
                                <div class="dropdown sub-dropdown">
                                    <button class="btn btn-link text-muted dropdown-toggle" type="button" id="dd1"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i data-feather="more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd1">
                                        <a class="dropdown-item" href="#">Insert</a>
                                        <a class="dropdown-item" href="#">Update</a>
                                        <a class="dropdown-item" href="#">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table no-wrap v-middle mb-0">
                                <thead>
                                    <tr class="border-0">
                                        <th class="border-0 font-14 font-weight-medium text-muted">Team Lead
                                        </th>
                                        <th class="border-0 font-14 font-weight-medium text-muted px-2">Project
                                        </th>
                                        <th class="border-0 font-14 font-weight-medium text-muted">Team</th>
                                        <th class="border-0 font-14 font-weight-medium text-muted text-center">
                                            Status
                                        </th>
                                        <th class="border-0 font-14 font-weight-medium text-muted text-center">
                                            Weeks
                                        </th>
                                        <th class="border-0 font-14 font-weight-medium text-muted">Budget</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border-top-0 px-2 py-4">
                                            <div class="d-flex no-block align-items-center">
                                                <div class="me-3"><img
                                                        src="../assets/images/users/widget-table-pic1.jpg" alt="user"
                                                        class="rounded-circle" width="45" height="45" /></div>
                                                <div class="">
                                                    <h5 class="text-dark mb-0 font-16 font-weight-medium">Hanna
                                                        Gover</h5>
                                                    <span class="text-muted font-14">hgover@gmail.com</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="border-top-0 text-muted px-2 py-4 font-14">Elite Admin</td>
                                        <td class="border-top-0 px-2 py-4">
                                            <div class="popover-icon">
                                                <a class="btn btn-primary rounded-circle btn-circle font-12"
                                                    href="javascript:void(0)">DS</a>
                                                <a class="btn btn-danger rounded-circle btn-circle font-12 popover-item"
                                                    href="javascript:void(0)">SS</a>
                                                <a class="btn btn-cyan rounded-circle btn-circle font-12 popover-item"
                                                    href="javascript:void(0)">RP</a>
                                                <a class="btn btn-success text-white rounded-circle btn-circle font-20"
                                                    href="javascript:void(0)">+</a>
                                            </div>
                                        </td>
                                        <td class="border-top-0 text-center px-2 py-4"><i
                                                class="fa fa-circle text-primary font-12" data-bs-toggle="tooltip"
                                                data-placement="top" title="In Testing"></i></td>
                                        <td class="border-top-0 text-center font-weight-medium text-muted px-2 py-4">
                                            35
                                        </td>
                                        <td class="font-weight-medium text-dark border-top-0 px-2 py-4">$96K
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4">
                                            <div class="d-flex no-block align-items-center">
                                                <div class="me-3"><img
                                                        src="../assets/images/users/widget-table-pic2.jpg" alt="user"
                                                        class="rounded-circle" width="45" height="45" /></div>
                                                <div class="">
                                                    <h5 class="text-dark mb-0 font-16 font-weight-medium">Daniel
                                                        Kristeen
                                                    </h5>
                                                    <span class="text-muted font-14">Kristeen@gmail.com</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted px-2 py-4 font-14">Real Homes WP Theme</td>
                                        <td class="px-2 py-4">
                                            <div class="popover-icon">
                                                <a class="btn btn-primary rounded-circle btn-circle font-12"
                                                    href="javascript:void(0)">DS</a>
                                                <a class="btn btn-danger rounded-circle btn-circle font-12 popover-item"
                                                    href="javascript:void(0)">SS</a>
                                                <a class="btn btn-success text-white rounded-circle btn-circle font-20"
                                                    href="javascript:void(0)">+</a>
                                            </div>
                                        </td>
                                        <td class="text-center px-2 py-4"><i class="fa fa-circle text-success font-12"
                                                data-bs-toggle="tooltip" data-placement="top" title="Done"></i>
                                        </td>
                                        <td class="text-center text-muted font-weight-medium px-2 py-4">32</td>
                                        <td class="font-weight-medium text-dark px-2 py-4">$85K</td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-4">
                                            <div class="d-flex no-block align-items-center">
                                                <div class="me-3"><img
                                                        src="../assets/images/users/widget-table-pic3.jpg" alt="user"
                                                        class="rounded-circle" width="45" height="45" /></div>
                                                <div class="">
                                                    <h5 class="text-dark mb-0 font-16 font-weight-medium">Julian
                                                        Josephs
                                                    </h5>
                                                    <span class="text-muted font-14">Josephs@gmail.com</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted px-2 py-4 font-14">MedicalPro WP Theme</td>
                                        <td class="px-2 py-4">
                                            <div class="popover-icon">
                                                <a class="btn btn-primary rounded-circle btn-circle font-12"
                                                    href="javascript:void(0)">DS</a>
                                                <a class="btn btn-danger rounded-circle btn-circle font-12 popover-item"
                                                    href="javascript:void(0)">SS</a>
                                                <a class="btn btn-cyan rounded-circle btn-circle font-12 popover-item"
                                                    href="javascript:void(0)">RP</a>
                                                <a class="btn btn-success text-white rounded-circle btn-circle font-20"
                                                    href="javascript:void(0)">+</a>
                                            </div>
                                        </td>
                                        <td class="text-center px-2 py-4"><i class="fa fa-circle text-primary font-12"
                                                data-bs-toggle="tooltip" data-placement="top" title="Done"></i>
                                        </td>
                                        <td class="text-center text-muted font-weight-medium px-2 py-4">29</td>
                                        <td class="font-weight-medium text-dark px-2 py-4">$81K</td>
                                    </tr>
                                    <tr>
                                        <td class="border-bottom-0 px-2 py-4">
                                            <div class="d-flex no-block align-items-center">
                                                <div class="me-3"><img
                                                        src="../assets/images/users/widget-table-pic4.jpg" alt="user"
                                                        class="rounded-circle" width="45" height="45" /></div>
                                                <div class="">
                                                    <h5 class="text-dark mb-0 font-16 font-weight-medium">Jan
                                                        Petrovic
                                                    </h5>
                                                    <span class="text-muted font-14">hgover@gmail.com</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="border-bottom-0 text-muted px-2 py-4 font-14">Hosting Press
                                            HTML</td>
                                        <td class="border-bottom-0 px-2 py-4">
                                            <div class="popover-icon">
                                                <a class="btn btn-primary rounded-circle btn-circle font-12"
                                                    href="javascript:void(0)">DS</a>
                                                <a class="btn btn-success text-white font-20 rounded-circle btn-circle"
                                                    href="javascript:void(0)">+</a>
                                            </div>
                                        </td>
                                        <td class="border-bottom-0 text-center px-2 py-4"><i
                                                class="fa fa-circle text-danger font-12" data-bs-toggle="tooltip"
                                                data-placement="top" title="In Progress"></i></td>
                                        <td class="border-bottom-0 text-center text-muted font-weight-medium px-2 py-4">
                                            23</td>
                                        <td class="border-bottom-0 font-weight-medium text-dark px-2 py-4">$80K
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <!-- *************************************************************** -->
        <!-- End Top Leader Table -->
        <!-- *************************************************************** -->
        <div class="main_div" style="width: 100% ;height: 500px">
            <div id="map"></div>
        </div>
    </div>

    <div class="modal fade" id="stationModal" tabindex="-1" role="dialog" aria-labelledby="stationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stationModalLabel">Station Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Name:</strong> <span id="stationName"></span></p>
                    <p><strong>Level:</strong> <span id="stationLevel"></span></p>
                    <p><strong>Manager:</strong> <span id="stationManager"></span></p>
                    <p><strong>Line Man:</strong> <span id="stationLineMan"></span></p>
                    <p><strong>Capacity:</strong> <span id="stationCapacity"></span></p>
                    <p><strong>Start Date:</strong> <span id="stationStartDate"></span></p>
                    <p><strong>Latitude:</strong> <span id="stationLatitude"></span></p>
                    <p><strong>Longitude:</strong> <span id="stationLongitude"></span></p>
                </div>
                <div class="modal-footer">
                    <form id="deleteStation" action="#" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" data-dismiss="modal">Delete Station</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="{{ asset('assets/extra-libs/c3/d3.min.js') }}"></script>
<script src="{{ asset('assets/extra-libs/c3/c3.min.js') }}"></script>
<script src="{{ asset('assets/libs/chartist/dist/chartist.min.js') }}"></script>
<script src="{{ asset('assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js') }}"></script>
<script src="{{ asset('assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('assets/extra-libs/jvector/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('dist/js/pages/dashboards/dashboard1.min.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCjjf_h1Kin_CeaJiT8VanhcNz0-4lhdNQ&libraries=places" async
defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let map;
    let rootLatLng = new google.maps.LatLng({{ app('siteSetting')->latitude }}, {{ app('siteSetting')->longitude }});

    let mapOptions = {
        center: rootLatLng,
        zoom: 12
    };

    map = new google.maps.Map(document.getElementById('map'), mapOptions);

    let rootMarker = new google.maps.Marker({
        position: rootLatLng,
        map: map,
        icon: {
            url: "{{ asset('assets/map/root.png') }}",
            scaledSize: new google.maps.Size(60, 60)
        },
        title: "Root Station"
    });

    function addMarker(location, station, isParentStation = false) {
        let icon = null;

        if (isParentStation) {
            icon = {
                url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                scaledSize: new google.maps.Size(60, 60)
            };
        }

        let marker = new google.maps.Marker({
            position: location,
            map: map,
            icon: icon
        });

        marker.addListener('click', function() {
            $('#stationName').text(station.name);
            $('#stationLevel').text(station.station_level);
            $('#stationManager').text(station.manager);
            $('#stationLineMan').text(station.line_man_name);
            $('#stationCapacity').text(station.capacity);
            $('#stationStartDate').text(station.start_date);
            $('#stationLatitude').text(station.latitude);
            $('#stationLongitude').text(station.longitude);
            $('#deleteStation').attr('action', '/admin/station/' + station.id);
            $('#stationModal').modal('show');
        });
    }

    function drawLine(start, end) {
        let line = new google.maps.Polyline({
            path: [start, end],
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
        });
        line.setMap(map);
    }

    function plotStations(stations, parentLatLng) {
        stations.forEach(station => {
            let stationLatLng = new google.maps.LatLng(station.latitude, station.longitude);
            addMarker(stationLatLng, station, true);
            drawLine(parentLatLng, stationLatLng);

            if (station.childs && station.childs.length > 0) {
                plotStations(station.childs, stationLatLng);
            }
        });
    }

    // Initial plot with root station as the parent
    plotStations(@json($stations), rootLatLng);
});
</script>

<script>
    $(function () {
        $('#checkInOut').on('click', function () {
            $.ajax({
                url: "{{ route('user.entry-sys.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function (data) {
                    if(data.success == true){
                        if(data.data.check_out_time){
                            $('#checkInOut').removeClass('btn-danger').addClass('btn-success').text('Check In');
                        }else{
                            $('#checkInOut').removeClass('btn-success').addClass('btn-danger').text('Check Out');
                        }
                    }else{
                        toastr.error(data.message);
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });
    });
</script>
@endsection
