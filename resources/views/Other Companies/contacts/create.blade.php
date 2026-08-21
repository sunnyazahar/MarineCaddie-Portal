@extends('layouts.app')

@section('styles')
    <style>
        /* Form Layout Styling */
        .form-pillar-container {
            display: flex;
            flex-direction: column;
            /* gap: 20px; */
            padding: 25px;
            background: #fff;
            max-width: 600px;
        }

        .form-group-custom {
            margin-bottom: 15px;
        }

        .form-label-custom {
            font-size: 13px;
            color: #3c485a;
            margin-bottom: 8px;
            display: block;
            font-weight: 600;
        }

        .form-input-custom {
            height: 35px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            width: 100%;
            padding: 5px 12px;
            font-size: 13px;
            color: #333;
            background: #fff;
        }

        .form-textarea-custom {
            border: 1px solid #d1d5db;
            border-radius: 3px;
            width: 100%;
            padding: 10px 12px;
            font-size: 13px;
            color: #333;
            resize: none;
            background: #fff;
        }

        /* Footer Styling */
        .form-footer {
            padding: 15px 30px;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            gap: 20px;
            border-top: 1px solid #dee2e6;
            position: fixed;
            bottom: 0;
            left: 240px;
            /* Adjust based on sidebar width */
            right: 0;
            z-index: 1000;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
        }

        .btn-save-custom {
            background: #1b5e6f;
            color: #fff;
            border: none;
            padding: 10px 30px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-save-custom:hover {
            background: #144653;
        }

        .btn-cancel-custom {
            color: #01a9ac;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-cancel-custom:hover {
            text-decoration: underline;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-group input {
            cursor: pointer;
        }

        .checkbox-label {
            font-size: 12px;
            color: #555;
            cursor: pointer;
        }

        /* Layout Adjustments */
        .pcoded-inner-content {
            padding: 0 !important;
            background: #fff;
        }

        .main-body {
            background: #fff;
        }

        .main-body .page-wrapper {
            padding: 20px 20px 80px 20px !important;
            background: #fff;
        }

        .input-group-append-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            right: 10px;
            color: #ccc;
            font-size: 14px;
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
                                <div class="card mt-5" style="border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                                    <form action="{{ route('other-companies.contacts.store', $otherCompany->id) }}"
                                        method="POST">
                                        @csrf
                                        <div class="form-pillar-container">
                                            <div class="form-group-custom">
                                                <label class="form-label-custom">Name</label>
                                                <div class="input-group-append-custom">
                                                    <input type="text" name="name" class="form-input-custom" required>
                                                    <i class="ti-more-alt input-icon"></i>
                                                </div>
                                            </div>

                                            <div class="form-group-custom">
                                                <label class="form-label-custom">Email</label>
                                                <input type="email" name="email" class="form-input-custom">
                                            </div>

                                            <div class="form-group-custom">
                                                <label class="form-label-custom">Phone number (with country code)</label>
                                                <input type="text" name="phone_number" class="form-input-custom">
                                            </div>

                                            <div class="form-group-custom">
                                                <label class="form-label-custom">Description</label>
                                                <textarea name="description" class="form-textarea-custom"
                                                    rows="4"></textarea>
                                            </div>

                                            <div class="checkbox-group">
                                                <input type="checkbox" name="is_main_contact" id="is_main_contact"
                                                    value="1">
                                                <label for="is_main_contact" class="checkbox-label">Is main contact</label>
                                            </div>
                                        </div>

                                        <div class="form-footer">
                                            <button type="submit" class="btn-save-custom">Save</button>
                                            <a href="{{ route('other-companies.edit', $otherCompany->id) }}#contacts"
                                                class="btn-cancel-custom">Cancel</a>
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

@endsection