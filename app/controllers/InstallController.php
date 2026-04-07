<?php

namespace App\controllers;

use App\core\Database;

class InstallController extends Controller
{

    public function instal()
    {
        $sql = "INSERT INTO `estados` (`id`, `sigla`, `estado`) VALUES
                (1, 'AC', 'Acre'),
                (2, 'AL', 'Alagoas'),
                (3, 'AP', 'Amapá'),
                (4, 'AM', 'Amazonas'),
                (5, 'BA', 'Bahia'),
                (6, 'CE', 'Ceará'),
                (7, 'DF', 'Distrito Federal'),
                (8, 'ES', 'Espírito Santo'),
                (9, 'GO', 'Goiás'),
                (10, 'MA', 'Maranhão'),
                (11, 'MT', 'Mato Grosso'),
                (12, 'MS', 'Mato Grosso do Sul'),
                (13, 'MG', 'Minas Gerais'),
                (14, 'PA', 'Pará'),
                (15, 'PB', 'Paraíba'),
                (16, 'PR', 'Paraná'),
                (17, 'PE', 'Pernambuco'),
                (18, 'PI', 'Piauí'),
                (19, 'RJ', 'Rio de Janeiro'),
                (20, 'RN', 'Rio Grande do Norte'),
                (21, 'RS', 'Rio Grande do Sul'),
                (22, 'RO', 'Rondônia'),
                (23, 'RR', 'Roraima'),
                (24, 'SC', 'Santa Catarina'),
                (25, 'SP', 'São Paulo'),
                (26, 'SE', 'Sergipe'),
                (27, 'TO', 'Tocantins');";
    }
    public function index()
    {

        $pdo  = Database::getInstance();

        $tabela = 'ControleAcesso';

        try {
            $stmt = $pdo->prepare("SHOW TABLES LIKE :tabela");
            $stmt->execute([':tabela' => $tabela]);

            if ($stmt->rowCount() > 0) {
                echo "⚠️ A tabela '$tabela' já existe. Nenhuma alteração foi feita.";
            } else {
                $sql = "
                    CREATE TABLE `$tabela` (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        nome VARCHAR(100) NOT NULL,
                        email VARCHAR(100) NOT NULL UNIQUE,
                        senha VARCHAR(255) NOT NULL,
                        email_token VARCHAR(100),
                        email_token_expira DATETIME,
                        status ENUM('ativo', 'inativo') DEFAULT 'inativo',
                        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        token_acesso TEXT
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ";

                $pdo->exec($sql);
                echo "✅ Tabela '$tabela' criada com sucesso.";
            }
        } catch (\PDOException $e) {
            echo "❌ Erro ao criar a tabela: " . $e->getMessage();
        }
    }
}
