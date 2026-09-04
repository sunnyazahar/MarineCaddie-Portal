<?php

namespace Tests\Unit;

use App\Models\Port;
use Tests\RegressionTestCase;

class PortSelectLabelTest extends RegressionTestCase
{
    public function test_select_label_for_code_uses_code_comma_city(): void
    {
        Port::create([
            'type' => Port::TYPE_AIRPORT,
            'iata_code' => 'AAC',
            'city' => 'Al Arish',
            'port_name' => 'Al Arish International',
            'is_active' => true,
        ]);

        $this->assertSame('AAC, Al Arish', Port::selectLabelForCode('AAC'));
    }

    public function test_select_label_normalizes_messy_city(): void
    {
        Port::create([
            'type' => Port::TYPE_AIRPORT,
            'iata_code' => 'BOM',
            'city' => 'Bombay (Mumbai)',
            'port_name' => 'Bombay (Mumbai) - CSIA',
            'is_active' => true,
        ]);

        $this->assertSame('BOM, Mumbai', Port::selectLabelForCode('BOM'));
    }
}
