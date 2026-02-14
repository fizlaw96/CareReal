<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class SerpApiMapsService
{
    /**
     * @return array{results: array<int, array<string, mixed>>, error: string|null}
     */
    public function searchNearby(string $query, int $limit = 8, ?string $whatsAppMessage = null): array
    {
        $apiKey = (string) config('services.serpapi.key');
        $endpoint = (string) config('services.serpapi.endpoint', 'https://serpapi.com/search.json');

        if (blank($apiKey)) {
            return [
                'results' => [],
                'error' => 'SERPAPI_KEY belum ditetapkan.',
            ];
        }

        try {
            $response = Http::timeout(15)
                ->retry(1, 250)
                ->get($endpoint, [
                    'engine' => 'google_maps',
                    'q' => $query,
                    'hl' => 'ms',
                    'gl' => 'my',
                    'api_key' => $apiKey,
                ]);

            if (! $response->ok()) {
                return [
                    'results' => [],
                    'error' => 'Permintaan Google Maps gagal. Cuba semula sebentar lagi.',
                ];
            }

            $json = $response->json();
            if (isset($json['error'])) {
                return [
                    'results' => [],
                    'error' => (string) $json['error'],
                ];
            }

            $results = collect($json['local_results'] ?? [])
                ->take($limit)
                ->map(function (array $item) use ($whatsAppMessage): array {
                    $lat = $item['gps_coordinates']['latitude'] ?? null;
                    $lng = $item['gps_coordinates']['longitude'] ?? null;

                    return [
                        'name' => $item['title'] ?? null,
                        'display_name' => $this->buildDisplayName($item['title'] ?? null),
                        'type' => $item['type'] ?? null,
                        'address' => $item['address'] ?? null,
                        'phone' => $item['phone'] ?? null,
                        'rating' => $item['rating'] ?? null,
                        'reviews' => $item['reviews'] ?? null,
                        'website' => $item['website'] ?? null,
                        'coordinates' => [
                            'lat' => $lat,
                            'lng' => $lng,
                        ],
                        'embed_url' => $this->buildEmbedUrl(
                            $item['title'] ?? null,
                            $item['address'] ?? null,
                            $lat,
                            $lng
                        ),
                        'maps_url' => $this->buildMapsUrl(
                            $item['title'] ?? null,
                            $item['address'] ?? null,
                            $lat,
                            $lng
                        ),
                        'whatsapp_url' => $this->buildWhatsAppUrl($item['phone'] ?? null, $whatsAppMessage),
                    ];
                })
                ->filter(fn (array $row) => filled($row['name']))
                ->values()
                ->all();

            return [
                'results' => $results,
                'error' => null,
            ];
        } catch (Throwable) {
            return [
                'results' => [],
                'error' => 'Tidak dapat hubungi SerpApi buat masa ini.',
            ];
        }
    }

    private function buildMapsUrl(?string $name, ?string $address, mixed $lat, mixed $lng): string
    {
        if (is_numeric($lat) && is_numeric($lng)) {
            return 'https://www.google.com/maps/search/?api=1&query='.urlencode($lat.','.$lng);
        }

        $query = trim(implode(' ', array_filter([$name, $address])));

        return 'https://www.google.com/maps/search/?api=1&query='.urlencode($query);
    }

    private function buildEmbedUrl(?string $name, ?string $address, mixed $lat, mixed $lng): string
    {
        if (is_numeric($lat) && is_numeric($lng)) {
            return 'https://www.google.com/maps?q='.urlencode($lat.','.$lng).'&output=embed';
        }

        $query = trim(implode(' ', array_filter([$name, $address])));

        return 'https://www.google.com/maps?q='.urlencode($query).'&output=embed';
    }

    private function buildDisplayName(?string $name): ?string
    {
        if (blank($name)) {
            return null;
        }

        $primary = trim((string) preg_split('/[|,]/', $name)[0]);

        return Str::limit($primary, 48);
    }

    private function buildWhatsAppUrl(?string $phone, ?string $message = null): ?string
    {
        if (blank($phone)) {
            return null;
        }

        $normalized = preg_replace('/[^0-9+]/', '', $phone);
        if (! is_string($normalized) || $normalized === '') {
            return null;
        }

        if (str_starts_with($normalized, '+')) {
            $normalized = ltrim($normalized, '+');
        }

        if (str_starts_with($normalized, '0')) {
            $normalized = '60'.ltrim($normalized, '0');
        } elseif (! str_starts_with($normalized, '60')) {
            $normalized = '60'.$normalized;
        }

        if (strlen($normalized) < 10) {
            return null;
        }

        $baseUrl = 'https://wa.me/'.$normalized;

        if (blank($message)) {
            return $baseUrl;
        }

        return $baseUrl.'?text='.rawurlencode((string) $message);
    }
}
