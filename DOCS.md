# Documentação da API - Rotas Disponíveis

## Endpoints de Pedidos

Informações importantes sobre o estoque:

Ao tentar criar um novo pedido, é feita uma solicitação para o banco de dados para verificar se é possível criar este pedido de acordo com quantidade de itens no estoque.

Após ser executada a migração, deverá ser criado três itens:

```bash
  DB::table('estoque')->insert([
            [
                'item_nome' => 'Pendrive Ultra Mega Blaster 16GB',
                'quantidade_disponivel' => 10,
            ],
            [
                'item_nome' => 'Mouse Gamer Ultra Mega Blaster 16GB',
                'quantidade_disponivel' => 20,
            ],
            [
                'item_nome' => 'TV 4K Ultra HD Quality Doubly 45"',
                'quantidade_disponivel' => 15,
            ],
        ]);
```

com id 1, 2 e 3 respectivamente. Para criar um pedido e ser descontado do estoque conforme acima, é necessário inserir o "produto_id" correto na requisição. Dito isto, segue os endpoints abaixo.

### 1. Criar Novo Pedido",
    "pedido_id_usuario" : 2,
    "pedido_itens": [
        {
            "produto_id": "456",
            "quantidade": 2,
            "valor_unitario_atTheTime": 99.90
        },
        {
            "produto_id": "456",
            "quantidade": 2,
            "valor_unitario_atTheTime": 99.90
        }
    ]

```

**Resposta de sucesso (201):**
```json
{
    "status": "success",
    "message": "Pedido criado com sucesso",
    "pedido_id": "uuid-gerado",
    "pedido_data_criacao": "2025-06-13 10:30:00",
    "pedido_status": "pendente"
}
```

### 2. Listar Pedidos
```http
GET /api/pedidos/list
GET /api/pedidos/list/{id}
```

**Parâmetros:**
- `id` (opcional): ID do pedido específico, para listar detalhes de um pedido.

**GET /api/pedidos/list**

**Resposta de sucesso (200):**
```json
{
    "status": "success",
    "message": "Pedidos listados com sucesso",
    "data": [
        {
            "id": "uuid-do-pedido",
            "id_usuario": "123",
            "data_pedido": "2025-06-13 10:30:00",
            "status": "pendente",
            "total_valor": 199.80,
            "itens_pedido": [...]
        }
    ]
}
```

**GET /api/pedidos/list{id}**

**Resposta de sucesso (200):**
```json
{
    "status": "success",
    "message": "Pedidos listados com sucesso",
    "data": [
        {
            "id": "uuid-do-pedido",
            "id_usuario": "123",
            "data_pedido": "2025-06-13 10:30:00",
            "status": "pendente",
            "total_valor": 199.80,
            "itens_pedido": [...]
        }
    ]
}
```

### 3. Atualizar Status do Pedido
```http
PUT /api/pedidos/update
```

**Corpo da requisição:**
```json
{
    "id": "uuid-do-pedido",
    "status": "processando"
}
```

**Resposta de sucesso (200):**
```json
{
    "status": "success",
    "message": "Status do pedido atualizado com sucesso",
    "data": {
        "id": "uuid-do-pedido",
        "status": "processando"
    }
}
```

## Códigos de Status

- `200` - Sucesso
- `201` - Criado com sucesso
- `404` - Pedido não encontrado
- `500` - Erro interno do servidor

## Status de Pedidos Disponíveis

- `pendente`
- `processando`
- `enviado`
- `entregue`
- `cancelado`

## Observações

1. Todas as respostas seguem o padrão:
```json
{
    "status": "success|error",
    "message": "Mensagem descritiva",
    "data": [] // opcional
}
```

2. Em caso de erro, a resposta incluirá detalhes específicos sobre o problema.

3. Para criar pedidos, certifique-se de que os produtos estão disponíveis em estoque. Ao tentar criar um pedido, ele é descontado do estoque. Vou adicionar mais um list para o estoque em breve.