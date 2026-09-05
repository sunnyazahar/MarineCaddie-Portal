<?php

namespace Tests\Unit;

use App\Support\MailBodyHtml;
use Tests\TestCase;

class MailBodyHtmlTest extends TestCase
{
    public function test_preserves_font_color_and_highlight_spans_in_hybrid_plain(): void
    {
        $html = MailBodyHtml::fromComposeBody(
            "Hello\n<span style=\"color:#dc2626\">red</span>\n<font color=\"#2563eb\">blue</font>\n<span style=\"background-color:#fff59d\">mark</span>"
        );

        $this->assertStringContainsString('style="color:#dc2626"', $html);
        $this->assertStringContainsString('red', $html);
        $this->assertStringContainsString('style="color:#2563eb"', $html);
        $this->assertStringContainsString('blue', $html);
        $this->assertStringContainsString('background-color:#fff59d', $html);
        $this->assertStringContainsString('<br>', $html);
        $this->assertStringNotContainsString('&lt;span', $html);
        $this->assertStringContainsString('font-family:Arial, Helvetica, sans-serif', $html);
    }

    public function test_editor_html_preserves_rgb_span_colors_and_flattens_divs(): void
    {
        $html = MailBodyHtml::fromComposeBody(
            '<div>Hello</div><div><span style="color: rgb(220, 38, 38);">red</span></div>'
        );

        $this->assertStringContainsString('style="color:#dc2626"', $html);
        $this->assertStringContainsString('Hello<br>', $html);
        $this->assertStringContainsString('red', $html);
        $this->assertStringNotContainsString('<div>Hello', $html);
        $this->assertMatchesRegularExpression('/^<div style="font-size:13px/', $html);
    }

    public function test_legacy_font_color_becomes_span_style(): void
    {
        $html = MailBodyHtml::fromComposeBody(
            '<div><font color="#2563eb">blue</font></div>'
        );

        $this->assertStringContainsString('<span style="color:#2563eb">blue</span>', $html);
        $this->assertStringNotContainsString('<font', $html);
    }

    public function test_preserves_table_borders_and_uses_consistent_font(): void
    {
        $html = MailBodyHtml::fromComposeBody(
            "Cargo\n<table style=\"width:100%;border-collapse:collapse;font-size:inherit\">"
            . '<thead><tr><th style="border:0.5px solid #ccc;background:#f3f4f6">Supplier</th></tr></thead>'
            . '<tbody><tr><td style="border:0.5px solid #ccc;font-size:inherit">Acme</td></tr></tbody>'
            . '</table>'
        );

        $this->assertStringContainsString('<table', $html);
        $this->assertStringContainsString('border-collapse:collapse', $html);
        $this->assertStringContainsString('border:0.5px solid #ccc', $html);
        $this->assertStringContainsString('Acme', $html);
        $this->assertStringContainsString('font-family:Arial, Helvetica, sans-serif', $html);
        $this->assertStringContainsString('font-size:13px', $html);
        $this->assertStringNotContainsString('font-size:inherit', $html);
    }

    public function test_converts_font_size_and_face_to_span_styles(): void
    {
        $html = MailBodyHtml::fromComposeBody(
            '<div><font size="4" face="Georgia">Large</font></div>'
        );

        $this->assertStringContainsString('font-size:16px', $html);
        $this->assertStringContainsString('font-family:Georgia', $html);
        $this->assertStringContainsString('Large', $html);
        $this->assertStringNotContainsString('<font', $html);
    }
}
