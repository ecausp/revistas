@if ($pedido->arquivos->count() > 0)
<div class="card mt-4">
    <div class="card-header h5">
        <div class="d-flex align-items-center">
            Arquivos Anexados
        </div>
    </div>
<div class="card-body p-2">
</div>
    <x-table-simples>
        <thead>
            <tr>
                <th>Arquivo</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pedido->arquivos as $arquivo)
            <tr style="font-size: .85em">
                <td>{{ $arquivo->nome }}</td>
                <td>{{ $arquivo->descricao }}</td>
                <td>
                    <a href="{{ asset('storage/' . $arquivo->caminho) }}" target="_blank"
                        style="width: 30px; height: 30px;"
                        class="btn btn-primary btn-sm d-inline-flex align-items-center justify-content-center"> 
                        <i class="fas fa-eye"></i>
                    </a>

                    @php
                        // Verifica se a ROTA ATUAL é a de EDIÇÃO (pedidos.edit)
                        $isEditPage = Route::is('pedidos.edit');
                    @endphp

                    @if($isEditPage && $pedido->status == "em_elaboracao")
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('financeiro') || auth()->user()->can('acesso-revista'))
                    
                    <form action="{{ route('arquivos.destroy', $arquivo) }}" method="POST"
                        onsubmit="return confirm('Tem certeza que deseja excluir este arquivo?')"
                        class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button style="width: 30px; height: 30px;"
                            class="btn btn-danger btn-sm d-inline-flex align-items-center justify-content-center">
                            <i class="fa fa-times"></i>
                        </button>
                    </form>
                        @endif
                    @endif
                    </td>
            </tr>
            @endforeach
        </tbody>
    </x-table-simples>

</div>
@endif