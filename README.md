# Route Engine 🗺️

>### O Route Engine é um pacote para Laravel que automatiza o cálculo de fretes baseados em distância real. Ele orquestra três APIs diferentes para entregar precisão no cálculo logístico:

    1. ViaCEP: Localiza o endereço através do CEP.
    2. Nominatim (OSM): Converte o endereço em coordenadas geográficas (Latitude/Longitude).
    3. OpenRouteService: Calcula a rota rodoviária real entre os pontos.


## 🚀 Requisitos

* PHP: ^8.2 ou ^8.3
* Laravel: ^11.0 ou ^12.0
* GuzzleHTTP: ^7.9

## 📦 Instalação
Com o pacote registrado no Packagist, basta rodar o comando abaixo no seu projeto Laravel:
```
composer require jonatan-passo/route-engine
```
O Laravel utilizará o Package Discovery para registrar automaticamente o RouteEngineServiceProvider.

## ⚙️ Configuração

**1. Publicar Configurações e Migrations**

Execute o comando abaixo para publicar o arquivo de configuração e as migrations da tabela de parâmetros:
```
php artisan vendor:publish --tag=route-config
```
**2. Rodar Migrations**

O pacote exige uma tabela de parâmetros para o cálculo. Crie-a executando:
```
php artisan migrate
```

**3. Variáveis de Ambiente (.env)**

Adicione sua chave de API do OpenRouteService e configure sua origem padrão no arquivo .env:
```
ORS_API_KEY=sua_chave_aqui
ROUTE_ENGINE_ORIGIN_ZIP=01001000
```

## 🛠️ Como Usar

Tabela de Parâmetros
O pacote utiliza a tabela delivery_parameters. Certifique-se de popular os seguintes nomes para que o cálculo funcione:

>* base_freight: Valor fixo de saída.
>* cost_per_km: Valor cobrado por quilômetro rodado.

## Exemplo em um Controller
Você pode injetar o RouteService diretamente em seus métodos:

```
<?php

namespace App\Http\Controllers;

use JonatanPasso\RouteEngine\Services\RouteService;
use Illuminate\Http\JsonResponse;

class ShipController extends Controller
{
    public function calculate(RouteService $service): JsonResponse
    {
        try {
            $result = $service->calculate(
                originZip: '01001000', 
                destZip: '20040000'
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

You may be using [Markdown Live Preview](https://markdownlivepreview.com/).

## Retorno Esperado
```
{
  "distance_km": 432.5,
  "total_price": 875.00,
  "address": {
    "logradouro": "Praça da Sé",
    "localidade": "São Paulo",
    "uf": "SP"
  }
}
```

## 🧪 Testes
Para rodar os testes do pacote:
```
composer test
```

## 🤝 Contribuição

**1. Faça um Fork do projeto.**

**2. Crie uma Branch para sua feature (git checkout -b feature/nova-feature).**

**3. Dê um Commit nas suas alterações (git commit -m 'Add nova feature').**

**4. Dê um Push na Branch (git push origin feature/nova-feature).**

**5. Abra um Pull Request.**

## 📄 Licença

Este pacote é um software de código aberto licenciado sob a MIT license.
