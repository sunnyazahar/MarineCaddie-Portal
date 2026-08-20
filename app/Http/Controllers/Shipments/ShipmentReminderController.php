<?php

namespace App\Http\Controllers\Shipments;

use App\Services\PreAlertReminderMailService;
use App\Services\ShipmentMailDispatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentReminderController extends BaseShipmentController
{
    public function preAlertReminderMailPreview($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = $this->shipmentRepository->findWithRelationsOrFail((int) $id, [
            'crrs.packages',
            'crrs.customerVessel.customer',
            'accountManager.office',
            'creator',
        ]);

        if ($shipment->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed shipments cannot receive pre-alert reminders.',
            ], 422);
        }

        try {
            $preview = $reminderMailService->buildPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'eml_url' => route('shipments.pre-alert-reminder-mail', $shipment->id),
            'eml_filename' => 'pre-alert-reminder-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function sendPreAlertReminderMail(Request $request, $id, ShipmentMailDispatchService $mailDispatchService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        if ($shipment->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed shipments cannot receive pre-alert reminders.',
            ], 422);
        }

        $validated = $request->validate([
            'to' => ['required', 'string', 'max:2000'],
            'cc' => ['nullable', 'string', 'max:2000'],
            'bcc' => ['nullable', 'string', 'max:2000'],
            'subject' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp,eml,msg'],
        ]);

        $mailDispatchService->dispatchAfterResponse(
            $request,
            $shipment,
            'pre_alert_reminder',
            [
                'to' => $validated['to'],
                'cc' => $validated['cc'] ?? '',
                'bcc' => $validated['bcc'] ?? '',
                'subject' => $validated['subject'],
                'body' => $validated['body'] ?? '',
            ],
            recordReminderSend: true,
        );

        return response()->json([
            'success' => true,
            'message' => 'Reminder email sent successfully.',
            'queued' => true,
            'reminder_sent_count' => $shipment->preAlertReminderSends()->count() + 1,
        ]);
    }

    public function preAlertReminderMail($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        try {
            $eml = $reminderMailService->buildEml(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            Log::error('Pre-alert reminder mail draft failed: ' . $e->getMessage());
            abort(400, $e->getMessage());
        }

        $filename = 'pre-alert-reminder-' . $shipment->shipment_number . '.eml';

        return response($eml, 200, [
            'Content-Type' => 'message/rfc822',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function deliveryStatusReminderMailPreview($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        if ($shipment->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed shipments cannot receive delivery status reminders.',
            ], 422);
        }

        try {
            $preview = $reminderMailService->buildDeliveryStatusPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'eml_url' => route('shipments.delivery-status-reminder-mail', $shipment->id),
            'eml_filename' => 'delivery-status-request-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function sendDeliveryStatusReminderMail(Request $request, $id, ShipmentMailDispatchService $mailDispatchService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        if ($shipment->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed shipments cannot receive delivery status reminders.',
            ], 422);
        }

        $validated = $request->validate([
            'to' => ['required', 'string', 'max:2000'],
            'cc' => ['nullable', 'string', 'max:2000'],
            'bcc' => ['nullable', 'string', 'max:2000'],
            'subject' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp,eml,msg'],
        ]);

        $mailDispatchService->dispatchAfterResponse(
            $request,
            $shipment,
            'delivery_status',
            [
                'to' => $validated['to'],
                'cc' => $validated['cc'] ?? '',
                'bcc' => $validated['bcc'] ?? '',
                'subject' => $validated['subject'],
                'body' => $validated['body'] ?? '',
            ],
            recordReminderSend: true,
        );

        return response()->json([
            'success' => true,
            'message' => 'Reminder email sent successfully.',
            'queued' => true,
        ]);
    }

    public function deliveryStatusReminderMail($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        try {
            $eml = $reminderMailService->buildDeliveryStatusEml(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            Log::error('Delivery status reminder mail draft failed: ' . $e->getMessage());
            abort(400, $e->getMessage());
        }

        $filename = 'delivery-status-request-' . $shipment->shipment_number . '.eml';

        return response($eml, 200, [
            'Content-Type' => 'message/rfc822',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function invoiceRequestMailPreview($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        try {
            $preview = $reminderMailService->buildInvoiceRequestPreview(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'eml_url' => route('shipments.invoice-request-mail', $shipment->id),
            'eml_filename' => 'invoice-request-' . $shipment->shipment_number . '.eml',
        ]);
    }

    public function sendInvoiceRequestMail(Request $request, $id, ShipmentMailDispatchService $mailDispatchService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);
        $validated = $request->validate([
            'to' => ['required', 'string', 'max:2000'],
            'cc' => ['nullable', 'string', 'max:2000'],
            'bcc' => ['nullable', 'string', 'max:2000'],
            'subject' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp,eml,msg'],
        ]);

        $mailDispatchService->dispatchAfterResponse(
            $request,
            $shipment,
            'invoice_request',
            [
                'to' => $validated['to'],
                'cc' => $validated['cc'] ?? '',
                'bcc' => $validated['bcc'] ?? '',
                'subject' => $validated['subject'],
                'body' => $validated['body'] ?? '',
            ],
            recordReminderSend: true,
        );

        return response()->json([
            'success' => true,
            'message' => 'Invoice request email sent successfully.',
            'queued' => true,
            'reminder_sent_count' => $shipment->preAlertReminderSends()->count() + 1,
        ]);
    }

    public function invoiceRequestMail($id, PreAlertReminderMailService $reminderMailService)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        try {
            $eml = $reminderMailService->buildInvoiceRequestEml(
                $shipment,
                auth()->user()?->name,
                auth()->user()?->email
            );
        } catch (\Throwable $e) {
            Log::error('Invoice request mail draft failed: ' . $e->getMessage());
            abort(400, $e->getMessage());
        }

        $filename = 'invoice-request-' . $shipment->shipment_number . '.eml';

        return response($eml, 200, [
            'Content-Type' => 'message/rfc822',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function recordPreAlertReminderSend($id)
    {
        $shipment = $this->shipmentRepository->findOrFail((int) $id);

        if ($shipment->status === 'Completed') {
            return response()->json([
                'success' => false,
                'message' => 'Completed shipments cannot receive pre-alert reminders.',
            ], 422);
        }

        $this->shipmentRepository->createPreAlertReminderSend($shipment->id, auth()->id());

        return response()->json([
            'success' => true,
            'reminder_sent_count' => $shipment->preAlertReminderSends()->count(),
        ]);
    }

}
