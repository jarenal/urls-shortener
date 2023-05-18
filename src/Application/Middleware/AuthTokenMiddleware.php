<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class AuthTokenMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $this->getToken($request);

        if (!$this->isValidToken($token)) {
            throw new RuntimeException('Invalid token');
        }
        return $handler->handle($request->withAttribute('token', $token));
    }

    private function getToken(ServerRequestInterface $request): string
    {
        $header = $request->getHeaderLine('Authorization');
        preg_match('/Bearer\s+(.+)/', $header, $matches);
        return $matches[1] ?? '';
    }

    /**
     * El siguiente método comprueba que el token sea válido.
     *
     * Un token será considerado válido si:
     * - Está vacío
     *
     * O si se cumplen las siguientes condiciones:
     * - Tiene la misma cantidad de símbolos de apertura que de cierre
     * - Los símbolos de apertura y cierre están balanceados (mismo orden de apertura y cierre)
     * - Los símbolos de apertura y cierre están balanceados en cada nivel de anidamiento
     *
     * Para validar el balanceo de los símbolos se utiliza una tabla de símbolos con su valor numérico.
     * Los símbolos de apertura tienen valor positivo y los de cierre negativo.
     * Cada vez que se abre un símbolo aumenta el nivel de anidamiento y cada vez que se cierra disminuye.
     * Esto permite validar que los símbolos de cierre estén balanceados con los de apertura.
     *
     * Ejemplos:
     *  - {[]} es válido porque tiene la misma cantidad de símbolos de apertura que de cierre y están balanceados
     *  - {[]]} no es válido porque tiene más símbolos de cierre que de apertura
     *  - {[]()} es válido porque tiene la misma cantidad de símbolos de apertura que de cierre y están balanceados
     *  - {[]() no es válido porque no tiene la misma cantidad de símbolos de apertura que de cierre
     *
     * @param string $token
     * @return bool
     */
    private function isValidToken(string $token): bool
    {
        if (empty($token)) {
            return true;
        }

        $symbolsValue = [
            "{" => 10,
            "[" => 100,
            "(" => 1000,
            "}" => -10,
            "]" => -100,
            ")" => -1000,
        ];

        $pairSymbolsTable = [
            "}" => "{",
            "]" => "[",
            ")" => "(",
        ];

        $arrToken = str_split($token);

        $total = 0;
        $lastSymbolResult = [];
        $level = 0;
        foreach ($arrToken as $currentIndex => $symbol) {
            if (!isset($symbolsValue[$symbol])) {
                return false;
            }

            $position = $currentIndex;

            if ($symbolsValue[$symbol] < 0) {
                $pairSymbol = $pairSymbolsTable[$symbol];
                if (array_key_exists(md5($pairSymbol), $lastSymbolResult[$level])) {
                    $result = $lastSymbolResult[$level][md5($pairSymbol)] * -1;
                    $level--;
                } else {
                    return false;
                }
            } else {
                $level++;
                $result = $symbolsValue[$symbol] + $position;
                $lastSymbolResult[$level][md5($symbol)] = $result;
            }

            $total += $result;
        }

        return $total === 0;
    }
}
