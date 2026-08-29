@extends('layouts.app')

@section('styles')
    <style>
        body.billing-page .pcoded-inner-content,
        body.billing-page .main-body,
        body.billing-page .page-wrapper,
        body.billing-page .page-body {
            margin: 0 !important;
            padding: 0 !important;
        }
        .billing-placeholder-card {
            margin: 0 !important;
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
            min-height: calc(100vh - 64px);
        }
        .billing-placeholder-card > .card-block {
            padding: 16px 20px !important;
        }
        .billing-empty-state {
            margin-top: 24px;
            padding: 48px 24px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
            border: 1px dashed #d6e3ee;
            border-radius: 8px;
            background: #f8fafc;
        }
    </style>
@endsection

@section('content')
    <script>document.body.classList.add('billing-page');</script>

    @include('layouts.partials.pcoded-shell-start', ['pageWrapperClass' => 'p-0'])

    <div class="card billing-placeholder-card">
        <div class="card-block">
            <x-lists.page-header
                title="Debit note"
                subtitle="Manage debit notes for billing adjustments"
                icon="ti-minus"
            />

            <div class="billing-empty-state">
                Debit note list will appear here.
            </div>
        </div>
    </div>

    @include('layouts.partials.pcoded-shell-end')
@endsection
