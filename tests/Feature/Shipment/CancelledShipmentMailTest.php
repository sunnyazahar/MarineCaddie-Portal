<?php

namespace Tests\Feature\Shipment;

use App\Mail\CancelledShipmentMail;
use App\Models\Agent;
use App\Models\Contact;
use App\Models\Crr;
use App\Models\CrrPackage;
use App\Models\Port;
use App\Models\Shipment;
use App\Models\ShipmentFlight;
use Illuminate\Support\Facades\Mail;
use Tests\RegressionTestCase;

class CancelledShipmentMailTest extends RegressionTestCase
{
    public function test_cancelled_status_sends_email_to_departure_party_with_account_manager_cc(): void
    {
        Mail::fake();

        $user = $this->createAdminUser();

        $accountManager = Contact::create([
            'name' => 'Jayaram Konar',
            'email' => 'jayaram@marinecaddie.test',
            'phone_number' => '+91 77770 40575',
        ]);

        $agent = Agent::create([
            'agent_name' => 'Oakland Agent',
            'code' => 'OAK-AG',
            'email' => 'oakland.agent@marinecaddie.test',
            'city' => 'Oakland',
            'is_active' => true,
        ]);

        Port::create([
            'type' => 'airport',
            'iata_code' => 'OAK',
            'city' => 'Oakland',
            'country_name' => 'United States',
            'is_active' => true,
        ]);

        $crr = Crr::create([
            'stock_number' => 'OAK-CXL-1',
            'vessel_name' => 'Test Vessel',
            'content' => 'Shipspares',
            'status' => Crr::STATUS_IN_PROGRESS,
            'accept' => false,
        ]);

        CrrPackage::create([
            'crr_id' => $crr->id,
            'weight' => 265,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'MOR6429949-826',
            'status' => 'In process',
            'service' => 'Airfreight',
            'additional_service' => 'Express',
            'departure' => 'agent:' . $agent->id,
            'departure_port_code' => 'OAK',
            'consignee_port_code' => 'LHR',
            'consignee_city' => 'London',
            'account_manager_id' => $accountManager->id,
        ]);
        $shipment->crrs()->attach($crr->id);

        ShipmentFlight::create([
            'shipment_id' => $shipment->id,
            'sort_order' => 0,
            'leg_reference' => 'AWB123456',
            'flight_number' => 'BA286',
            'arrival_date' => '2026-09-01',
        ]);

        $this->actingAsVerified($user)->postJson(route('shipments.update-status', $shipment->id), [
            'status' => 'Cancelled',
        ])->assertOk()->assertJson([
            'success' => true,
            'status' => 'Cancelled',
        ]);

        Mail::assertSent(CancelledShipmentMail::class, function (CancelledShipmentMail $mail) use ($accountManager, $agent) {
            $this->assertTrue($mail->hasTo($agent->email));
            $this->assertTrue($mail->hasCc($accountManager->email));

            $expectedSubject = 'Cancelled shipment MOR6429949-826/ From Oakland to London/ Airfreight';
            $this->assertSame($expectedSubject, $mail->mailSubject);

            $body = preg_replace("/\r\n|\r|\n/", "\n", $mail->body) ?? '';

            $this->assertStringContainsString('This is to notify Oakland Agent, regarding shipment from OAK, Oakland.', $body);
            $this->assertStringContainsString('The shipment has been cancelled.', $body);
            $this->assertStringContainsString('Vessel: M/V Test Vessel', $body);
            $this->assertStringContainsString('Service: Airfreight, Express', $body);
            $this->assertStringContainsString('Airway bill: AWB123456', $body);
            $this->assertStringContainsString('Flight number: BA286', $body);
            $this->assertStringContainsString('Total pieces: 1', $body);
            $this->assertStringContainsString('Total weight: 265 kg', $body);
            $this->assertStringContainsString('Jayaram Konar', $body);
            $this->assertStringContainsString('+91 77770 40575', $body);

            return true;
        });
    }

    public function test_cancelled_status_does_not_resend_when_already_cancelled(): void
    {
        Mail::fake();

        $user = $this->createAdminUser();

        $accountManager = Contact::create([
            'name' => 'Jayaram Konar',
            'email' => 'jayaram@marinecaddie.test',
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'MOR-CXL-2',
            'status' => 'Cancelled',
            'service' => 'Airfreight',
            'account_manager_id' => $accountManager->id,
        ]);

        $this->actingAsVerified($user)->postJson(route('shipments.update-status', $shipment->id), [
            'status' => 'Cancelled',
        ])->assertOk();

        Mail::assertNothingSent();
    }

    public function test_cancelled_status_skips_email_when_departure_party_email_missing(): void
    {
        Mail::fake();

        $user = $this->createAdminUser();

        $accountManager = Contact::create([
            'name' => 'Jayaram Konar',
            'email' => 'jayaram@marinecaddie.test',
        ]);

        $agent = Agent::create([
            'agent_name' => 'No Email Agent',
            'code' => 'NO-EMAIL',
            'is_active' => true,
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'MOR-CXL-3',
            'status' => 'In process',
            'service' => 'Airfreight',
            'departure' => 'agent:' . $agent->id,
            'account_manager_id' => $accountManager->id,
        ]);

        $this->actingAsVerified($user)->postJson(route('shipments.update-status', $shipment->id), [
            'status' => 'Cancelled',
        ])->assertOk();

        Mail::assertNothingSent();
    }
}
