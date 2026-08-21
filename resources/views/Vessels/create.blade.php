@extends('layouts.app')

@section('styles')
    <!-- Data Table Css -->

    <style>
        #offices-table tbody td {
            padding: 4px 5px !important;
            vertical-align: middle !important;
            font-size: 12px;
            white-space: normal !important;
        }
        .btn-teal {
            background-color: #008080;
            border-color: #008080;
            color: white;
        }
        .btn-teal:hover {
            background-color: #006666;
            border-color: #006666;
        }
        .btn-outline-teal {
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-outline-teal:hover {
            background-color: #008080;
            color: white;
        }
        .filter-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 2px;
            display: block;
        }
        .filter-input {
            height: 32px;
            font-size: 13px;
            border-radius: 2px;
        }
        .clear-filters {
            font-size: 12px;
            color: #ff5252;
            text-decoration: none;
            cursor: pointer;
            margin-top: 25px;
            display: inline-block;
        }
        .card-header-actions .btn {
            font-size: 12px;
            padding: 6px 15px;
            border-radius: 2px;
        }
        .custom-row {
            margin-right: -10px;
            margin-left: -10px;
        }
        .custom-col {
            padding-right: 10px;
            padding-left: 10px;
            flex: 0 0 11.5%;
            max-width: 11.5%;
        }
        @media (max-width: 992px) {
            .custom-col {
                flex: 0 0 33.33%;
                max-width: 33.33%;
            }
        }
        @media (max-width: 768px) {
            .custom-col {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
        .filter-input {
            height: 30px;
            font-size: 11px;
            border-radius: 2px;
        }
        
        /* Bootstrap Multiselect Custom Styling */
        .multiselect-native-select .btn-group {
            width: 100%;
        }
        .multiselect-native-select .multiselect {
            width: 100%;
            text-align: left;
            height: 30px;
            padding: 4px 10px;
            font-size: 11px;
            background-color: #fff;
            border: 1px solid #ced4da;
            color: #495057;
        }
        .multiselect-native-select .multiselect-container {
            width: 235px;
            font-size: 11px;
        }
        .multiselect-native-select .multiselect-container li a label {
            padding: 5px 10px 5px 0;
            display: block;
            margin: 0;
            cursor: pointer;
        }
        .multiselect-native-select .multiselect-selected .form-check-label {
            color: #008080;
            font-weight: bold;
        }
        .multiselect-item.multiselect-all label {
            font-weight: bold;
            color: #333;
        }
        input.form-control.multiselect-search {
            font-size: 11px;
        }
        select.form-control {
            font-size: 11px;
        }
        input.form-control {
            font-size: 11px;
        }
        .multiselect-container .input-group {
            margin: 2px;
        }
        .input-group-addon {
            background-color: #01a9ac;
            color: #fff;
            max-height: 31px;
        }
        .multiselect-container>li {
            padding: 0px 5px;
        }
        .multiselect-item .input-group {
            width: 114%;
        }
        /* Select2 Custom Styling */
        .select2-container .select2-selection--single {
            height: 30px !important;
            font-size: 11px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.25 !important;
            padding: 2px 8px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #008080;
            border: 1px solid #006666;
            color: #fff;
            font-size: 10px;
            margin-top: 2px;
        }
        .select2-container--default .select2-selection--multiple {
            min-height: 30px;
            border: 1px solid #ced4da;
            border-radius: 2px;
        }
        /* Filter Toggle Button Styling */
        .btn-filter-toggle {
            height: 30px;
            padding: 4px 10px;
            font-size: 14px;
            color: #008080;
            border-color: #008080;
            background-color: transparent;
        }
        .btn-filter-toggle:hover, .btn-filter-toggle:focus, .btn-filter-toggle:active {
            background-color: #008080 !important;
            color: white !important;
            border-color: #008080 !important;
        }
        .card .card-header .card-title {
            font-weight: 600;
            color: #1f4356;
            border-bottom: 2px solid #c8c8c8;
            padding: 7px 0px;
        }

        /* Reduce gap/margin between sidebar and content */
        .pcoded-inner-content {
            padding: 5px !important;
        }
        .main-body .page-wrapper {
            padding: 5px !important;
        }
       
    </style>
@endsection

@section('content')
<!-- Pre-loader start -->
    <div class="theme-loader">
        <div class="ball-scale">
            <div class='contain'>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
                <div class="ring">
                    <div class="frame"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pre-loader end -->
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">

          @include('layouts.top-menu')
                @include('layouts.left-menu')
                     <!-- Page-body start -->
                      <br>
                      <div class="pcoded-content">
                        <div class="pcoded-inner-content">
                        <!-- Main-body start -->
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <!-- Page-header start -->
                                    <div class="page-header">
                                        
                                    </div>
                                    <!-- Page-header end -->

                                    <!-- Page-body start -->
                                    <div class="page-body">
                                        <!-- Base Style - Compact start -->
                                        <div class="card">
                                            <div class="card-block">
                                                <div class="d-flex justify-content-between align-items-start pt-2">
                                                    <div style="width: 80%;">
                                                       
                                                    </div>
                                                     <div class="text-right" style="width: 20%; padding-top: 18px;">
                                                     <a class="btn btn-teal ml-2" href="{{ route('vessels.index') }}">Vessel Lists</a>
                                                </div>
                                                </div>
                                                 <hr>
                                                <div class="dt-responsive">
                                                    <div class="view-info">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <div class="form-radio">
                                                    <div class="radio radiofill radio-inline">
                                                        <h4 class="sub-title">Vessel Type</h4>
                                                        <label>
                                                            <input type="radio" name="vessel_type" checked="checked" class="vesselwithimo" value="vesselwithimo">
                                                            <i class="helper"></i>Vessel with IMO
                                                        </label>
                                                    </div>
                                                    <div class="radio radiofill radio-inline">
                                                        <label>
                                                            <input type="radio" name="vessel_type" class="vesselwithbuilding" value="vesselwithbuilding">
                                                            <i class="helper"></i>New building
                                                        </label>
                                                    </div>
                                                    </div>
                                            </div>
                                        </div>
                                            <div class="row" id="vesselwithimoRow">
                                                <div class="col-md-3 form-group">
                                                    <label for="vesselwithimo" class="form-label">Add vessel with IMO</label>
                                                    <input type="text" class="form-control" id="vesselwithimo">
                                                </div>
                                                
                                                <div class="col-md-3 form-group">
                                                    <label for="officeshortnameOfficeDetails" class="form-label"></label>
                                                    <input type="submit" class="btn btn-primary mt-4" value="Add Vessel">
                                                </div>
                                                
                                            </div>

                                            <div class="row" id="vesselwithBuilding" style="display: none;">
                                                <div class="col-md-3 form-group">
                                                    <label for="officenameOfficeDetails" class="form-label">Vessel name</label>
                                                    <input type="text" class="form-control" id="officenameOfficeDetails">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label for="companyidOfficeDetails" class="form-label">Shipyard</label>
                                                    <input type="text" class="form-control" id="companyidOfficeDetails">
                                                    <div data-lastpass-icon-root="" style="position: relative !important; height: 0px !important; width: 0px !important; display: initial !important; float: left !important;"></div>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label for="officeshortnameOfficeDetails" class="form-label">Office Shipyard location</label>
                                                    <input type="text" class="form-control" id="officeshortnameOfficeDetails">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label for="customernumberFMOfficeDetails" class="form-label">Hull number</label>
                                                    <input type="text" class="form-control" id="customernumberFMOfficeDetails">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label for="phonenumbercountrycodeOfficeDetails" class="form-label">Vessel owner</label>
                                                    <input type="text" class="form-control" id="phonenumbercountrycodeOfficeDetails">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label for="emailOfficeDetails" class="form-label">Remarks</label>
                                                    <textarea name="remarks" id="remarks" class="form-control" rows="1"></textarea>
                                                </div>
                                                <div class="col-md-1 form-group">
                                                    <label for="officeshortnameOfficeDetails" class="form-label"></label>
                                                    <input type="submit" class="btn btn-primary mt-2" value="Add Vessel">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                           
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Base Style - Compact end -->
                                    </div>
                                    <!-- Page-body end -->
                                </div>
                            </div>
                            <div id="styleSelector">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            var radios = $('input[type=radio][name=vessel_type]');
            radios.change(function() {
                if (this.value == 'vesselwithimo') {
                    $('#vesselwithimoRow').show();
                    $('#vesselwithBuilding').hide();
                }
                else if (this.value == 'vesselwithbuilding') {
                    $('#vesselwithBuilding').show();
                    $('#vesselwithimoRow').hide();
                }
            });
            // Force default checked state and trigger change on load
            $('.vesselwithimo').prop('checked', true).trigger('change');
        });
    </script>
@endsection
