<?php $this->layout('admin_template', ['title' => 'Criar Novo PSS']) ?>

<div style="padding: 20px;">
    <h1>Criar Novo PSS - Teste</h1>
    <p>Se você está vendo esta mensagem, o controller e as rotas estão funcionando!</p>
    
    <div style="background: #f0f0f0; padding: 15px; margin: 20px 0; border-radius: 5px;">
        <h3>Status do Sistema:</h3>
        <ul>
            <li>✅ Controller carregado</li>
            <li>✅ Template engine funcionando</li>
            <li>✅ Rota configurada</li>
            <li>✅ Autenticação OK</li>
        </ul>
    </div>
    
    <a href="/admin/dashboard" class="btn btn-primary">Voltar ao Dashboard</a>
</div>

<style>
.btn {
    display: inline-block;
    padding: 10px 20px;
    background: #2E7D32;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin: 10px 0;
}
.btn:hover {
    background: #1B5E20;
}
</style>

