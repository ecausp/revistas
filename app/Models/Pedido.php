<?php

namespace App\Models;

use App\States\StatusPedido;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

use App\Models\PedidoServico;
class Pedido extends Model
{
    use HasFactory;
    use HasStates;

    protected $fillable = [
    'solicitante_id',
    'revista_id',
    'licitacao_id',
    'observacoes',
    'status',
];
    public function arquivos()
    {

        return $this->hasMany(Arquivo::class);

    }

    public function revista()
    {
        return $this->belongsTo(Revista::class);
    }

    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }

    public function licitacao()
    {
        return $this->belongsTo(Licitacao::class);
    }

    public function servicos()
    {
        return $this->belongsToMany(Servico::class, 'pedido_servico')
                    ->withPivot('quantidade', 'valor_unitario');
    }


    public function historicos()
    {
        return $this->hasMany(PedidoHistorico::class, 'id_pedido');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    protected function casts(): array
    {
        return [
            'status' => StatusPedido::class,
        ];
    }

    // Helper para criar um novo  historico a cada vez que o pedido for alterado;
    public function adicionarHistorico(string $status, ?string $observacao = null)
    {
        return $this->historicos()->create([
            'status_pedido' => $status,
            'id_pessoa'     => auth()->id(), // pega automaticamente o usuário logado
            'observacao'    => $observacao,
        ]);
    }


    public function valorTotalPedido()
    {
        $servicos = PedidoServico::where('pedido_id', $this->id)->get();
        $valor_total = 0;
        foreach($servicos as $servico)
        {
            $valor_total = $valor_total + ($servico->quantidade * $servico->valor_unitario);
        }
        return $valor_total;

    }

}