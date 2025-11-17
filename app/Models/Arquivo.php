<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Arquivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'nome',
        'descricao',
        'caminho',
    ];
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Faz o upload e cria o registro do arquivo.
     */
    public static function salvarArquivo($file, $descricao, $pedidoId)
    {
        // Normaliza o nome do arquivo com data-hora-nomeoriginal
        $nomeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $timestamp = now()->format('d-m-Y_H-i');
        $nomeFinal = "{$timestamp}_{$nomeOriginal}";

        // Salva o arquivo na pasta 'arquivos' no disco 'public'
        $caminho = $file->storeAs('arquivos', $nomeFinal, 'public');

        // Cria o registro no banco
        return self::create([
            'pedido_id' => $pedidoId,
            'nome' => $nomeFinal,
            'descricao' => $descricao,
            'caminho' => $caminho,
        ]);
    }

    /**
     * Remove o arquivo físico e o registro no banco.
     */
    public function excluirArquivo()
    {
        if ($this->caminho && Storage::disk('public')->exists($this->caminho)) {
            Storage::disk('public')->delete($this->caminho);
        }
        $this->delete();
    }
}
