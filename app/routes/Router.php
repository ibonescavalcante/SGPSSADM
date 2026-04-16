<?php

namespace App\routes;

use App\helpers\Request;
use App\helpers\Uri;
use App\middleware\SessionSecurity;

class Router
{

  const CONTROLLER_NAMESPACE = 'App\\controllers';

  private static function appDebug(): bool
  {
    $v = $_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG');
    if ($v === false || $v === null || $v === '') {
      return false;
    }
    return filter_var($v, FILTER_VALIDATE_BOOLEAN);
  }

  /**
   * Rotas que não exigem sessão do painel (`$_SESSION['user']`).
   */
  private static function dashboardRequerAutenticacao(string $method, string $uri): bool
  {
    $publicGet = ['/', '/login', '/dashboard/login', '/dashboard/logout'];
    if ($method === 'get' && in_array($uri, $publicGet, true)) {
      return false;
    }
    if ($method === 'post' && $uri === '/dashboard/login') {
      return false;
    }
    return true;
  }

  private static function isApiRequest(string $uri): bool
  {
    return str_starts_with($uri, '/api/');
  }

  private static function responderNaoAutenticado(string $uri): void
  {
    if (self::isApiRequest($uri)) {
      http_response_code(401);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['erro' => 'Não autenticado.']);
      exit;
    }
    header('Location: /dashboard/login');
    exit;
  }

  /**
   * Sessão do painel substituída por outro login (mesma conta).
   */
  private static function responderSessaoDashboardSubstituida(string $uri): void
  {
    if (self::isApiRequest($uri)) {
      http_response_code(401);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['erro' => 'Sessão encerrada. A conta foi acessada em outro local.']);
      exit;
    }
    header('Location: /dashboard/login?sessao=substituida');
    exit;
  }

  private static function responderCsrfInvalido(string $uri): void
  {
    if (self::isApiRequest($uri)) {
      http_response_code(403);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['erro' => 'Token de segurança inválido ou ausente.']);
      exit;
    }
    $_SESSION['erro'] = 'Sessão de segurança expirada. Atualize a página e tente novamente.';
    $back = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
    header('Location: ' . $back);
    exit;
  }

  public static function load(string $controller, string $method, ...$params)
  {
    try {
      $controllerNamespace = self::CONTROLLER_NAMESPACE . '\\' . $controller;
      if (!class_exists($controllerNamespace)) {
        throw new \Exception("O controller {$controller} não existe");
      }

      $controllerInstance = new $controllerNamespace;

      if (!method_exists($controllerInstance, $method)) {
        throw new \Exception("O method {$method} não existe");
      }
      $controllerInstance->$method(...$params);
    } catch (\Throwable $th) {
      error_log('Router::load ' . $th->getMessage());
      if (self::appDebug()) {
        echo $th->getMessage();
      } else {
        http_response_code(500);
        echo 'Erro interno do servidor.';
      }
    }
  }

  public static function routes(): array
  {
    return [
      'get' => [
        '/' => fn() => self::load('LoginDashboardController', 'index'),
        '/login' => fn() => self::load('LoginDashboardController', 'index'),
        '/dashboard' => fn() => self::load('DashboardController', 'index'),
        '/dashboard/processos' => fn() => self::load('DashboardController', 'processos'),
        '/dashboard/processos/novo' => fn() => self::load('DashboardController', 'processos_novo'),
        '/inscricoes' => fn() => self::load('DashboardController', 'inscricoes'),
        '/recursos' => fn() => self::load('DashboardController', 'recursos'),
        '/dashboard/inscricoes/detalhes/(\d+)' => fn($id) => self::load('DashboardController', 'detalhes', $id),
        '/dashboard/recursos/detalhes/(\d+)' => fn($id) => self::load('DashboardController', 'detalhes_recursos', $id),
        '/dashboard/relatorios' => fn() => self::load('DashboardController', 'relatorios'),
        '/configuracoes' => fn() => self::load('DashboardController', 'configuracoes'),
        '/dashboard/login' => fn() => self::load('LoginDashboardController', 'index'),
        '/dashboard/logout' => fn() => self::load('LoginDashboardController', 'logout'),
        '/api/cargos/(\d+)' => fn($id) => self::load('ApiController', 'get_cargos_by_pss_id', $id),
        '/api/status/(\d+)' => fn($id) => self::load('ApiController', 'get_processo_status', $id),
        '/dashboard/relatorios/gerar' => fn() => self::load('DashboardController', 'gerarRelatorio'),

      ],
      'post' => [

        '/dashboard/login' => fn() => self::load('LoginDashboardController', 'logar'),
        '/dashboard/inscricoes/detalhes/(\d+)' => fn($id) => self::load('ApiController', 'set_inscricao_status', $id),
        '/dashboard/recursos/detalhes/(\d+)' => fn($id) => self::load('ApiController', 'set_recursos_status', $id),
        '/dashboard/inscricoes/detalhes/pontuacao' => fn() => self::load('ApiController', 'set_inscricao_pontuacao'),
        '/dashboard/inscricoes/pontuacao/excluir' => fn() => self::load('ApiController', 'excluirPontuacao'),
        '/dashboard/inscricoes/documento/alterar' => fn() => self::load('ApiController', 'alterarDocumento'),
        '/dashboard/relatorios/api' => fn() => self::load('DashboardController', 'relatorios_api'),
        '/configuracoes/usuario' => fn() => self::load('DashboardController', 'criar_usuario'),
        '/configuracoes/usuario/atualizar' => fn() => self::load('DashboardController', 'atualizar_usuario'),
        '/api/alterar-senha' => fn() => self::load('ApiController', 'alterarSenha'),
        '/api/inscricoes' => fn() => self::load('ApiController', 'get_inscricoes'),
        '/api/recursos' => fn() => self::load('ApiController', 'get_recursos'),
        '/api/set-inscricoes' => fn() => self::load('ApiController', 'set_inscricao_status'),
        '/api/avaliacao-titulo' => fn() => self::load('ApiController', 'setAvaliacaoTitulo'),

      ]
    ];
  }

  public static function execute()
  {
    try {
      $routes = self::routes();
      $request = Request::method();
      $uri = Uri::get('path');
      error_log("URI recebida: " . $uri);
      error_log("Método da requisição: " . $request);

      if (str_starts_with($uri, '/requisitos/uploads/documentos/') || str_starts_with($uri, '/titulos/uploads/documentos/')) {
        $relativePath = substr($uri, strpos($uri, '/documentos/') + strlen('/documentos/'));
        $rootDir = realpath(__DIR__ . '/../../');
        $filePath = $rootDir . '/uploads/documentos/' . $relativePath;
        $realFilePath = realpath($filePath);
        $uploadsDir = realpath($rootDir . '/uploads/');
        if ($realFilePath !== false && str_starts_with($realFilePath, $uploadsDir) && is_file($realFilePath)) {
          $mime = mime_content_type($realFilePath) ?: 'application/octet-stream';
          header("Content-Type: {$mime}");
          header('Content-Length: ' . filesize($realFilePath));
          readfile($realFilePath);
          exit;
        } else {
          http_response_code(404);
          throw new \Exception("Arquivo não encontrado ou acesso negado.");
        }
      }

      if (!isset($routes[$request])) {
        error_log("Erro: Método de requisição '" . $request . "' não encontrado nas rotas.");
        throw new \Exception("A rota não existe!");
      }

      if (self::dashboardRequerAutenticacao($request, $uri) && !SessionSecurity::estaLogadoDashboard()) {
        self::responderNaoAutenticado($uri);
      }

      if (
        self::dashboardRequerAutenticacao($request, $uri)
        && SessionSecurity::estaLogadoDashboard()
        && !SessionSecurity::validarVinculoSessaoDashboard()
      ) {
        SessionSecurity::destruirSessao();
        self::responderSessaoDashboardSubstituida($uri);
      }

      if (
        $request === 'post'
        && $uri !== '/dashboard/login'
        && SessionSecurity::estaLogadoDashboard()
        && !SessionSecurity::validarDashboardCsrf()
      ) {
        self::responderCsrfInvalido($uri);
      }

      if (array_key_exists($uri, $routes[$request])) {
        error_log("Rota exata encontrada para URI: " . $uri);
        $router = $routes[$request][$uri];
        if (is_callable($router)) {
          return $router();
        }
      }

      foreach ($routes[$request] as $route => $handler) {
        $pattern = preg_replace("/{([a-zA-Z0-9_]+)}/", "([a-zA-Z0-9_]+)", $route);
        $pattern = preg_replace("/\\{(\\w+):(\\w+)\\/}/", "($2)", $pattern);
        $pattern = str_replace("(\\d+)", "(\\d+)", $pattern);
        $pattern = str_replace("(\\w+)", "(\\w+)", $pattern);
        $pattern = str_replace("([a-zA-Z0-9_]+)", "([a-zA-Z0-9_]+)", $pattern);
        error_log("Tentando regex match para rota: " . $route . " com padrão: " . $pattern);
        if (preg_match("#^$pattern$#", $uri, $matches)) {
          error_log("Regex match encontrado para rota: " . $route);
          array_shift($matches);
          if (is_callable($handler)) {
            return $handler(...$matches);
          }
        }
      }

      error_log("Erro: Nenhuma rota encontrada para URI: " . $uri);
      throw new \Exception("A rota não existe!.");
    } catch (\Throwable $th) {
      error_log("Erro geral no roteador: " . $th->getMessage());
      if (self::appDebug()) {
        echo $th->getMessage();
        return;
      }
      $msg = $th->getMessage();
      $is404 = str_contains($msg, 'Arquivo não encontrado')
        || str_contains($msg, 'A rota não existe');
      http_response_code($is404 ? 404 : 500);
      echo $is404 ? 'Página não encontrada.' : 'Ocorreu um erro ao processar a solicitação.';
    }
  }
}
