@extends('layouts.app')

@section('styles')
    <style>
        /* Base wrapper adjustments */
        .pcoded-inner-content {
            padding: 0 !important;
        }
        .main-body .page-wrapper {
            padding: 20px !important;
            background: #f6f7f9;
        }
        
        /* --- Premium Form Styling --- */
        .contact-card {
            background: #fff;
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 120px);
            position: relative;
        }

        .edit-header-summary {
            padding: 20px 30px;
            display: flex;
            gap: 50px;
            border-bottom: 1px solid #f0f2f5;
        }
        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .summary-label {
            font-size: 10px;
            color: #8da2b5;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .summary-value {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-content-area {
            padding: 30px;
            flex-grow: 1;
            max-width: 100%;
        }

        .form-pillar {
            display: flex;
            flex-direction: column;
            gap: 25px;
            max-width: 100%;
        }

        .form-group-custom {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label-custom {
            font-size: 12px;
            font-weight: 500;
            color: #5a7184;
            margin-bottom: 0;
        }

        .form-input-custom {
            height: 38px;
            padding: 8px 12px;
            font-size: 13px;
            border: 1px solid #e1e8ed;
            border-radius: 4px;
            width: 100%;
            outline: none;
            color: #2c3e50;
            transition: border-color 0.2s;
        }
        .form-input-custom:focus {
            border-color: #01a9ac;
        }

        .form-textarea-custom {
            padding: 12px;
            font-size: 13px;
            border: 1px solid #e1e8ed;
            border-radius: 4px;
            width: 100%;
            min-height: 100px;
            resize: vertical;
            outline: none;
            color: #2c3e50;
        }

        .input-group-custom {
            display: flex;
            position: relative;
            width: 100%;
        }
        .input-group-custom .form-input-custom {
            padding-right: 40px;
        }
        .btn-input-append {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 38px;
            background: #f1f4f6;
            border: none;
            border-left: 1px solid #e1e8ed;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #aab8c2;
            cursor: pointer;
            border-radius: 0 4px 4px 0;
        }

        /* Footer Styling - Fixed at the bottom */
        .form-footer-container {
            padding: 15px 30px;
            background: rgba(255, 255, 255, 0.98);
            border-top: 1px solid #f0f2f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            bottom: 0;
            left: 240px; /* This matches the standard sidebar width */
            right: 0;
            z-index: 1000;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.05);
        }

        @media (max-width: 991px) {
            .form-footer-container {
                left: 0;
            }
        }

        .footer-left-actions {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .btn-saved-disabled {
            background-color: #e6e9ed;
            color: #8da2b5;
            border: none;
            padding: 8px 24px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            cursor: default;
        }
        
        /* Save button active state */
        .btn-teal {
            background-color: #01a9ac;
            color: white;
            border: none;
            padding: 8px 24px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(1, 169, 172, 0.2);
        }

        .btn-cancel-link {
            color: #01a9ac;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }
        .btn-cancel-link:hover {
            text-decoration: underline;
        }

        .footer-metadata {
            text-align: right;
            font-size: 11px;
            color: #9cb1c1;
            line-height: 1.5;
        }
        
        /* Padding for content area to not be hidden by footer */
        .page-body {
            padding-bottom: 80px !important;
        }

        /* Custom Checkbox */
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 5px;
        }
        .checkbox-container input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        /* Validation Styling */
        .error-message {
            color: #e74c3c;
            font-size: 11px;
            margin-top: 4px;
        }
    </style>
@endsection

@section('content')
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            @include('layouts.top-menu')
            @include('layouts.left-menu')

            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <div class="main-body">
                        <div class="page-wrapper">
                            <div class="page-body">
                                <div class="contact-card mt-5">
                                    <!-- Summary Header -->
                                    <div class="edit-header-summary">
                                        <div class="summary-item">
                                            <div class="summary-label">Hub Name</div>
                                            <div class="summary-value">{{ $hub->hub_name }}</div>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-label">Code</div>
                                            <div class="summary-value">{{ $hub->code }}</div>
                                        </div>
                                    </div>

                                    <form id="contactForm" action="{{ route('hub.contacts.store', $hub->id) }}" method="POST">
                                        @csrf
                                        <div class="form-content-area">
                                            <div class="form-pillar">
                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Name</label>
                                                    <div class="input-group-custom">
                                                        <input type="text" name="name" class="form-input-custom" value="" required>
                                                        <button type="button" class="btn-input-append"><i class="ti-more-alt"></i></button>
                                                    </div>
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Email</label>
                                                    <input type="email" name="email" class="form-input-custom" value="">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Phone number (with country code)</label>
                                                    <input type="text" name="phone_number" class="form-input-custom" value="">
                                                </div>

                                                <div class="form-group-custom">
                                                    <label class="form-label-custom">Description</label>
                                                    <textarea name="description" class="form-textarea-custom"></textarea>
                                                </div>

                                                <div class="checkbox-container">
                                                    <input type="checkbox" name="is_main_contact" id="is_main_contact" value="1">
                                                    <label class="form-label-custom" for="is_main_contact">Is main contact</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-footer-container">
                                            <div class="footer-left-actions">
                                                <button type="submit" id="saveButton" class="btn-saved-disabled">All changes saved</button>
                                                <a href="{{ route('hub.show', $hub->id) }}#contacts" class="btn-cancel-link">Cancel</a>
                                            </div>
                                            
                                            <div class="footer-metadata"></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Required Jquery -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/popper.js/dist/umd/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/modernizr.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/modernizr/feature-detects/css-scrollbars.js') }}"></script>

    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            // Logic to change button style when input changes
            $('#contactForm input, #contactForm textarea').on('input change', function() {
                $('#saveButton').removeClass('btn-saved-disabled').addClass('btn-teal').text('Save Contact').css('cursor', 'pointer');
            });

            $('#contactForm').validate({
                rules: {
                    name: { required: true, minlength: 2 },
                    email: { email: true }
                },
                errorElement: 'div',
                errorClass: 'error-message',
                errorPlacement: function(error, element) {
                    if (element.parent('.input-group-custom').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        });
    </script>
@endsection
