<?php

namespace e2e;

use env;
use Nav;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class NavigationTest extends TestCase
{
    protected function log(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        fwrite(STDOUT, "[$timestamp] $message\n");
    }

    public static function provideUrls(): array
    {
        $pages = array_merge(
            Nav::get_berichte(),
            Nav::get_info(),
            Nav::get_liga(),
            Nav::get_modus(),
            Nav::get_oc_start(),
            Nav::get_organisation(),

        );

        # Only start
        $filteredPages = array_filter($pages, function ($item) {
            return $item[0] !== env::LINK_DISCORD;
        });
        $urlsOnly = array_map(fn($item) => [$item[0]], $filteredPages);
        return array_values($urlsOnly);
    }

    public static function provideProductionURLs(): array
    {
        $urls = NavigationTest::provideUrls();
        $productionUrls = [];
        foreach ($urls as $url) {
            $productionUrls[] = [str_replace(
                search: Env::BASE_URL,
                replace: "https://einrad.hockey",
                subject: $url[0]
            )];
        }
        return $productionUrls;
    }


    public static function provideNavLinks(): array
    {

        // Using ReflectionClass
        $reflection = new ReflectionClass(Nav::class);
        $constants = $reflection->getConstants();

        $data = [];
        foreach ($constants as $_ => $value) {
            $data[] = [$value];
        }
        return $data;
    }

    protected function fetchUrl(string $url): array
    {
        $ch = curl_init($url);

        // Set options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 second timeout

        // Execute the request
        $html = curl_exec($ch);

        // Get HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close cURL
        curl_close($ch);
        $this->log($httpCode . " <-- " . $url);
        return [$httpCode, $html];
    }


    #[DataProvider("provideUrls")]
    public function testLocalhostURLs(string $url): void
    {
        [$httpCode, $html] = $this->fetchUrl($url);

        $this->assertEquals(
            200,
            $httpCode,
            "Website $url is not reachable. HTTP code: $httpCode"
        );
        $this->assertStringContainsString(
            '</footer>',
            $html,
            "Website $url does not appear to have a footer"
        );
    }

    #[DataProvider("provideProductionURLs")]
    public function testProductionUrls(string $url): void
    {
        [$httpCode, $html] = $this->fetchUrl($url);

        $this->assertEquals(
            200,
            $httpCode,
            "Website $url is not reachable. HTTP code: $httpCode"
        );
        $this->assertStringContainsString(
            '</footer>',
            $html,
            "Website $url does not appear to have a footer"
        );

        sleep(0.5); # Do not kill https://einrad.hockey
    }


    #[DataProvider("provideNavLinks")]
    public function testNavLinks(string $url): void
    {
        [$httpCode, $_] = $this->fetchUrl($url);

        $this->assertEquals(
            200,
            $httpCode,
            "Website $url is not reachable. HTTP code: $httpCode"
        );
    }

}