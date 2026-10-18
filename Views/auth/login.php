<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4">C</div>
            <h1 class="text-2xl font-bold text-gray-800">Acesso Restrito</h1>
            <p class="text-gray-500 text-sm">Insira suas credenciais para acessar o painel</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= e($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= url('login/authenticate') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">E-mail</label>
                <input type="email" id="email" name="email" required autofocus
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="admin@coninfoms.com.br">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Senha</label>
                <input type="password" id="password" name="password" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="********">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition-colors">
                Entrar
            </button>

            <div class="mt-4 text-center">
                <a href="<?= url() ?>" class="text-sm text-blue-500 hover:text-blue-800">Voltar para o site</a>
            </div>
        </form>
    </div>

</body>
</html>
