# Laravel WebSocket

## 📌 Sobre o Projeto
O **Laravel WebSocket** é um package para Laravel que fornece autenticação via API (usando Laravel Sanctum) e gerenciamento de planos com limites de requisições.

## ✨ Funcionalidades
- 🔑 **Pusher** com esse package você pode se conectar e gerenciar qualquer conexão com servidores pusher
---

## 🚀 Instalação

### 1️⃣ Requisitos
Antes de instalar, certifique-se de que seu projeto atenda aos seguintes requisitos:
- PHP >= 8.0
- Laravel >= 10
- Composer instalado

### 2️⃣ Instalação do Package
Execute o seguinte comando no terminal:
```bash
  composer require risetechapps/websocet-for-tenancy
```

### 3️⃣ Publicar Configurações
```bash
  php artisan vendor:publish --provider="RiseTechApps\WebSocket\WebSocketServiceProvider"
```

Isso criará o arquivo `config/apimanager.php`, onde você pode configurar os planos e limites.

### 4️⃣ Rodar Migrations
```bash
  php artisan migrate
```


---

## 🛠 Contribuição
Sinta-se à vontade para contribuir! Basta seguir estes passos:
1. Faça um fork do repositório
2. Crie uma branch (`feature/nova-funcionalidade`)
3. Faça um commit das suas alterações
4. Envie um Pull Request

---

## 📜 Licença
Este projeto é distribuído sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

💡 **Desenvolvido por [Rise Tech](https://risetech.com.br)**

