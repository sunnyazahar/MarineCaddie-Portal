<?php

namespace Tests\Concerns;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;

trait RendersMigratedBladeViews
{
    protected function renderMigratedView(string $view, array $data = [], ?Authenticatable $user = null): string
    {
        $user ??= $this->createAdminUser();

        $this->actingAsVerified($user);

        View::share('errors', session()->get('errors') ?? new ViewErrorBag());

        return view($view, $data)->render();
    }

    protected function emptyPaginator(int $perPage = 25): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, $perPage);
    }

    protected function assertHtmlContainsAll(string $html, array $needles): void
    {
        foreach ($needles as $needle) {
            $this->assertStringContainsString($needle, $html, "Expected migrated view HTML to contain: {$needle}");
        }
    }

    protected function assertPortSelectPresent(string $html, string $fieldName): void
    {
        $this->assertMatchesRegularExpression(
            '/name="' . preg_quote($fieldName, '/') . '"[^>]*data-port-select="1"/',
            $html,
            "Expected port-select field [{$fieldName}] with data-port-select."
        );
    }

    protected function assertCountrySelectPresent(string $html, string $fieldName): void
    {
        $this->assertMatchesRegularExpression(
            '/name="' . preg_quote($fieldName, '/') . '"[^>]*data-country-select="1"/',
            $html,
            "Expected country-select field [{$fieldName}] with data-country-select."
        );
    }

}
