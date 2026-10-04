<?php

namespace Tests\Feature;

use Tests\RegressionTestCase;

class AssetUrlTest extends RegressionTestCase
{
    public function test_asset_urls_do_not_expose_the_public_folder(): void
    {
        $url = asset('files/assets/pages/advance-elements/bootstrap-datetimepicker.min.js');

        $this->assertSame(
            rtrim((string) config('app.asset_url'), '/') . '/files/assets/pages/advance-elements/bootstrap-datetimepicker.min.js',
            $url
        );
        $this->assertStringNotContainsString('/public/', $url);
    }

    public function test_login_page_assets_do_not_expose_the_public_folder(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('/public/', false);
    }
}
