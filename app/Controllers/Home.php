<?php

namespace App\Controllers;

use App\Models\Solicitacao;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Dashboard (Sprint 4) — home do sistema.
 *
 * Exibe a tabela "Suas solicitações em aberto" e três gráficos Chart.js.
 */
class Home extends BaseController
{
    protected Solicitacao $solicitacoes;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        helper(['url', 'funcoes']);

        $this->solicitacoes = new Solicitacao();
    }

    public function index(): string
    {
        $userId = (int) session()->get('user_id');

        // Tabela de solicitações em aberto do agente logado.
        $solicitacoesAbertas = $this->solicitacoes->solPorAgenteAberta($userId);

        // Gráficos de barras (último ano). A exclusão de users 76/74 ocorre no Model.
        $porCliente        = $this->solicitacoes->solPorCliente(365);
        $graficoPercentual = grafico_barras($porCliente);
        $graficoAbsoluto   = grafico_barras_absoluto($porCliente);

        // Gráfico doughnut (últimos 30 dias).
        $porSituacao = $this->solicitacoes->solPorSituacao(30);

        return view('dashboard/index', [
            'title'               => 'Dashboard',
            'solicitacoesAbertas' => $solicitacoesAbertas,
            'graficoPercentual'   => $graficoPercentual,
            'graficoAbsoluto'     => $graficoAbsoluto,
            'porSituacao'         => $porSituacao,
        ]);
    }
}
