<?php $this->layout('template') ?>
<section class="max-w-lg mx-auto mt-40 bg-white shadow-md rounded-lg p-6 border border-gray-200">
    <h2 class="text-2xl font-semibold text-green-800 mb-4">Digite sua senha</h2>
    <?php if (isset($erro)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars($erro) ?></span>
        </div>
    <?php endif; ?>
    <form action="/verificar-senha" method="POST" class="space-y-4 mt-4">
        <input type="hidden" name="cpf" value="<?= htmlspecialchars($cpf) ?>">
        <div>
            <label for="senha" class="block text-sm font-medium text-gray-700 mb-1">Senha:</label>
            <input type="password" id="senha" name="senha" required
                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent">
        </div>
        <button type="submit" class="bg-green-800 text-white px-4 py-2 rounded hover:bg-green-900 transition">
            Entrar
        </button>
    </form>
</section>