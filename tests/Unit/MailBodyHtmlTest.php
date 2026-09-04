<?php

namespace Tests\Unit;

use App\Support\MailBodyHtml;
use Tests\TestCase;

class MailBodyHtmlTest extends TestCase
{
    public function test_preserves_font_color_and_highlight_spans(): void
    {
        $html = MailBodyHtml::fromComposeBody(
            "Hello\n<span style=\"color:#dc2626\">red</span>\n<font color=\"#2563eb\">blue</font>\n<span style=\"background-color:#fff59d\">mark</span>"
        );

        $this->assertStringContainsString('style="color:#dc2626"', $html);
        $this->assertStringContainsString('red', $html);
        $this->assertStringContainsString('color="#2563eb"', $html);
        $this->assertStringContainsString('background-color:#fff59d', $html);
        $this->assertStringNotContainsString('&lt;span', $html);
    }
}
