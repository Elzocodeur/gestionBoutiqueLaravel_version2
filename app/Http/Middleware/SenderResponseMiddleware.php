<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\RepositoryException;
use App\Exceptions\ServiceException;
use Exception;

class SenderResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);

            if ($response instanceof JsonResponse) {
                $content = $response->getData(true);
            } else {
                $content = json_decode($response->getContent(), true) ?? $response->getContent();
            }
            if (is_string($content)) {
                if (strpos($content, '<!DOCTYPE html>') !== false) {
                    return $next($request);
                }
            }
            if (isset($content["exception"])) {
                unset($content["trace"]);
                $exception = $content["exception"];
                if ($exception ==  "Symfony\Component\HttpKernel\Exception\HttpException")
                    throw new Exception($content["message"]);
                if($exception == "Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException")
                    throw new Exception($content["message"]);
                throw new $exception($content["message"]);
            }
            $statusCode = $response->getStatusCode();
            if (is_null($content)) {
                $responseData = [
                    'data' => null,
                    'status' => "ECHEC",
                    'message' => 'Ressource non trouvée'
                ];
                return response()->json($responseData, 404);
            }
            $statusCode = $statusCode > 0 ? $statusCode : 200;
            if (is_array($content)) {
                $data = $content['data'] ?? $content;
                $statusCode = isset($content['status']) ? (int) $content['status'] : $statusCode;
                $message = $content['message'] ?? ($statusCode === 200 ? 'Ressource trouvée' : 'Ressource non trouvée');
                if (array_keys($content) === range(0, count($content) - 1)) {
                    $data = $content;
                }
            } else {
                $data = $content;
                $statusCode = (int)$statusCode;
                $message = ($statusCode === 200 || $statusCode == 201) ? 'Ressource trouvée' : 'Ressource non trouvée';
            }
            $status = ($statusCode == 200 || $statusCode == 201) ? "SUCCESS" : "ECHEC";
            if (isset($data["message"])) {
                $message = $data["message"];
                unset($data["message"]);
                if (!count($data))
                    $data = null;
            }
            if (isset($data["status"])) {
                $status = $data["status"];
                unset($data["status"]);
                if (!count($data))
                    $data = null;
            }
            $responseData = [
                'data' => $data,
                'status' => $status,
                'message' => $message,
            ];
            return response()->json($responseData)->setStatusCode($statusCode > 0 ? $statusCode : 200);
        } catch (ModelNotFoundException $e) {

            return $this->handleException($e, 404, 'Ressource non trouvée');
        } catch (RepositoryException $e) {

            return $this->handleException($e, 500, 'Erreur dans le repository');
        } catch (ServiceException $e) {

            return $this->handleException($e, 500, 'Erreur dans le service');
        } catch (\Exception $e) {

            return $this->handleException($e, 500, 'Erreur interne du serveur');
        } catch (\Error $e) {
            return $this->handleException($e, 500, 'Erreur interne du serveur');
        }
    }



    protected function handleException(\Exception|\Error $e, int $statusCode, string $defaultMessage): JsonResponse
    {
        $message = $e->getMessage() ?: $defaultMessage;
        $responseData = [
            'data' => null,
            'status' => 'ECHEC',
            'message' => $message,
        ];

        return response()->json($responseData, $statusCode);
    }
}
