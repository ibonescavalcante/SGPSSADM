<?php

namespace App\routes;

use App\helpers\Request;
use App\helpers\Uri;
use App\middleware\SessionSecurity;

class Router
{

  const CONTROLLER_NAMESPACE = 'App\\controllers';

  // ✅ Rotas públicas (acessíveis sem login)
  protected static array $rotasPublicas = [
    '/login',
    '/',
    // '/buscarcidades',
    // '/cadastro',
    // '/pss/(\d+)',
    // '/pss/(\d+)/cargo/(\d+)',
    // '/pss/(\d+)/inscricao',
    // '/pss/(\d+)/zona/(\w+)',
    // '/consulta/protocolo',
    // '/consulta/recurso',
    '/dashboard/login',
    '/dashboard/logout',
    '/dashboard',
    '/dashboard/processos',
    '/dashboard/processos/novo',
    '/dashboard/inscricoes',
    '/dashboard/relatorios',
    '/dashboard/configuracoes',
    '/dashboard/inscricoes/detalhes/(\d+)',
    '/dashboard/recursos/detalhes/(\d+)',
    '/dashboard/recursos/detalhes',
    '/dashboard/inscricoes/detalhes',
    '/dashboard/inscricoes/detalhes/pontuacao',
    '/dashboard/inscricoes/pontuacao/excluir',
    '/verificar-senha',
    '/inscricao/comprovante/([A-Za-z0-9]+)',
    '/inscricao/comprovante/([A-Za-z0-9]+)/pdf',
    '/onboarding/(\d+)/zona',
    '/onboarding/(\d+)/microrregiao/([a-zA-Z0-9_]+)',
    '/pss/(\d+)/vagas',
    '/api/cidades-por-estado/(\d+)',
    '/api/cargos/(\d+)',
    '/api/inscricoes',
    '/api/recursos',
    '/api/set-inscricoes',
    '/api/status/(\d+)',
    '/api/avaliacao-titulo',
    '/dashboard/relatorios/gerar',
    '/api/alterar-senha',
    '/dashboard/recursos',
  ];

  public static function load(string $controller, string $method, ...$params)
  {
    // echo $controller;
    try {
      //Verifica se o controller existe
      $controllerNamespace = self::CONTROLLER_NAMESPACE . '\\' . $controller;
      // echo $controllerNamespace;
      if (!class_exists($controllerNamespace)) {
        throw new \Exception("O controller {$controller} não existe");
        //obs:\Exception utiliza essa contrabarra para que possa buscara o Exception global eo mesmo que usar 'use Exception' no inicio
      }

      $controllerInstance = new $controllerNamespace;

      if (!method_exists($controllerInstance, $method)) {
        throw new \Exception("O method {$method} não existe");
      }
      $controllerInstance->$method(...$params);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }
  public static function routes(): array
  {
    return [
      'get' => [
        // Rotas dashboard
        '/' => fn() => self::load('LoginDashboardController', 'index'),
        '/login' => fn() => self::load('LoginDashboardController', 'index'),
        '/dashboard' => fn() => self::load('DashboardController', 'index'),
        '/dashboard/processos' => fn() => self::load('DashboardController', 'processos'),
        '/dashboard/processos/novo' => fn() => self::load('DashboardController', 'processos_novo'),
        '/dashboard/inscricoes' => fn() => self::load('DashboardController', 'inscricoes'),
        '/dashboard/recursos' => fn() => self::load('DashboardController', 'recursos'),
        '/dashboard/inscricoes/detalhes/(\d+)' => fn($id) => self::load('DashboardController', 'detalhes', $id),
        '/dashboard/recursos/detalhes/(\d+)' => fn($id) => self::load('DashboardController', 'detalhes_recursos', $id),
        '/dashboard/relatorios' => fn() => self::load('DashboardController', 'relatorios'),
        '/dashboard/configuracoes' => fn() => self::load('DashboardController', 'configuracoes'),
        '/dashboard/login' => fn() => self::load('LoginDashboardController', 'index'),
        '/dashboard/logout' => fn() => self::load('LoginDashboardController', 'logout'),
        '/api/cargos/(\d+)' => fn($id) => self::load('ApiController', 'get_cargos_by_pss_id', $id),
        '/api/status/(\d+)' => fn($id) => self::load('ApiController', 'get_processo_status', $id),
        '/dashboard/relatorios/gerar' => fn() => self::load('DashboardController', 'gerarRelatorio'),

      ],
      'post' => [

        //rotas do dashboard
        '/dashboard/login' => fn() => self::load('LoginDashboardController', 'logar'),
        '/api/inscricoes' => fn() => self::load('ApiController', 'get_inscricoes'),
        '/api/recursos' => fn() => self::load('ApiController', 'get_recursos'),
        '/api/set-inscricoes' => fn() => self::load('ApiController', 'set_inscricao_status'),
        '/api/avaliacao-titulo' => fn() => self::load('ApiController', 'setAvaliacaoTitulo'),
        '/dashboard/inscricoes/detalhes/(\d+)' => fn($id) => self::load('ApiController', 'set_inscricao_status', $id),
        '/dashboard/recursos/detalhes/(\d+)' => fn($id) => self::load('ApiController', 'set_recursos_status', $id),
        '/dashboard/inscricoes/detalhes/pontuacao' => fn() => self::load('ApiController', 'set_inscricao_pontuacao'),
        '/dashboard/inscricoes/pontuacao/excluir' => fn() => self::load('ApiController', 'excluirPontuacao'),
        '/dashboard/inscricoes/documento/alterar' => fn() => self::load('ApiController', 'alterarDocumento'),
        '/dashboard/relatorios/api' => fn() => self::load('DashboardController', 'relatorios_api'),
        '/api/alterar-senha' => fn() => self::load('ApiController', 'alterarSenha'),

      ]
    ];
  }

  public static function execute()
  {
    // ✅ Usar middleware de segurança para sessões
    // SessionSecurity::iniciarSessao();

    try {
      $routes = self::routes();
      $request = Request::method();
      $uri = Uri::get('path');
      error_log("URI recebida: " . $uri);
      error_log("Método da requisição: " . $request);



      //trata visualização de documentos 
      if (str_starts_with($uri, '/requisitos/uploads/documentos/') || str_starts_with($uri, '/titulos/uploads/documentos/')) {
        $relativePath = substr($uri, strpos($uri, '/documentos/') + strlen('/documentos/'));
        // // Define o diretório raiz da aplicação
        $rootDir = realpath(__DIR__ . '/../../');
        // $filename = basename($uri);
        // Monta o caminho completo para o arquivo solicitado
        $filePath = $rootDir . '/uploads/documentos/' . $relativePath; //. $relativePath;
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

      // echo ($uri);
      // die;
      if (!isset($routes[$request])) {
        error_log("Erro: Método de requisição '" . $request . "' não encontrado nas rotas.");
        throw new \Exception("A rota não existe!");
      }

      // ✅ Se o usuário estiver logado e tentar acessar o /login, redireciona para o painel
      // if ($uri === '/login' && SessionSecurity::estaLogado()) {
      //   header("Location: /painel");
      //   exit;
      // }

      // ✅ Verifica se precisa estar logado
      $rotaPublica = false;

      // Verifica rotas públicas exatas e com regex
      foreach (self::$rotasPublicas as $rotaPublicaPattern) {
        // Para rotas exatas
        if ($uri === $rotaPublicaPattern) {
          $rotaPublica = true;
          break;
        }

        // Para rotas com parâmetros regex
        $pattern = str_replace('(\d+)', '\d+', $rotaPublicaPattern);
        $pattern = str_replace('([A-Za-z0-9]+)', '[A-Za-z0-9]+', $pattern);
        $pattern = str_replace('(\w+)', '\w+', $pattern);
        $pattern = str_replace('([a-zA-Z0-9_]+)', '[a-zA-Z0-9_]+', $pattern);
        if (preg_match("#^" . $pattern . "$#", $uri)) {
          $rotaPublica = true;
          break;
        }
      }






      // ✅ Verificação de autenticação com middleware de segurança
      // if (!$rotaPublica && !SessionSecurity::estaLogado()) {
      //   // Redireciona para o login se não estiver logado ou sessão inválida
      //   header("Location: /login");
      //   exit;
      // }

      // ✅ Verificar integridade da sessão para usuários logados
      // if (SessionSecurity::estaLogado() && !SessionSecurity::verificarIntegridade()) {
      //   header("Location: /login");
      //   exit;
      // }

      // Try exact match first
      if (array_key_exists($uri, $routes[$request])) {
        error_log("Rota exata encontrada para URI: " . $uri);
        $router = $routes[$request][$uri];
        if (is_callable($router)) {
          return $router();
        }
      }

      // Then try regex matches for routes with parameters
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
      echo $th->getMessage();
    }
  }
}