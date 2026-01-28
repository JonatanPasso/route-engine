<?php

declare(strict_types=1);

namespace JonatanPasso\RouteEngine\Services;

use Illuminate\Support\Facades\Http;
use JonatanPasso\RouteEngine\Models\DeliveryParameter;
use Exception;

class RouteService
{
    public function calculate(string $originZip, string $destZip): array
    {
        // 1. VIA CEP - Busca endereço do destino
        $address = Http::get("https://viacep.com.br/ws/{$destZip}/json/")->json();
        if (isset($address['erro'])) throw new Exception("CEP de destino não encontrado.");

        // 2. NOMINATIM - Geocoding (Transforma endereço em Lat/Long)
        $queryString = "{$address['logradouro']}, {$address['localidade']}, {$address['uf']}, Brasil";
        $geo = Http::withHeaders(['User-Agent' => config('route-engine.user_agent')])
            ->get("https://nominatim.openstreetmap.org/search", [
                'q' => $queryString,
                'format' => 'json',
                'limit' => 1
            ])->json();

        if (empty($geo)) throw new Exception("Geolocalização não encontrada para o endereço.");
        
        $destCoords = ['lat' => $geo[0]['lat'], 'lon' => $geo[0]['lon']];

        // 3. OPEN ROUTE SERVICE - Calcula a rota (Exemplo: saindo de um ponto fixo ou CEP origem)
        // Nota: Você precisaria fazer o mesmo Geocoding para o $originZip aqui
        $distanceKm = $this->getDistance($destCoords); 

        // 4. PARÂMETROS - Busca na sua tabela de parâmetros
        $baseFreight = (float) DeliveryParameter::getByName('base_freight');
        $costPerKm = (float) DeliveryParameter::getByName('cost_per_km');

        return [
            'distancia_km' => round($distanceKm, 2),
            'valor_frete' => $baseFreight + ($distanceKm * $costPerKm),
            'endereco' => $address
        ];
    }

    private function getDistance(array $dest): float
    {
        // Exemplo de chamada ao ORS (Requer API Key no .env)
        $response = Http::withHeaders(['Authorization' => config('route-engine.ors_key')])
            ->get("https://api.openrouteservice.org/v2/directions/driving-car", [
                'start' => "-46.63, -23.55", // Long, Lat de exemplo (SP)
                'end' => "{$dest['lon']}, {$dest['lat']}"
            ]);

        return $response->json()['features'][0]['properties']['summary']['distance'] / 1000;
    }
}

