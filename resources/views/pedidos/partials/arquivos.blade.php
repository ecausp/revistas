@if ($pedido->arquivos->count() > 0)
<div class="card mt-4">
<div class="card-header">
    <div class="d-flex align-items-center">
        <h5 class="mb-0 me-2 mr-5">Arquivos Anexados</h5>

    </div>
</div>
    <table class="table table-hover datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive mb-0">
        <thead>
            <tr>
                <th style="width: 33%;">Arquivo</th>
                <th style="width: 33%;">Descrição</th>
                <th style="width: 33%;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pedido->arquivos as $arquivo)
            <tr>
                <td>{{ $arquivo->nome }}</td>
                <td>{{ $arquivo->descricao }}</td>
                <td class="d-flex">
                    <a href="{{ asset('storage/' . $arquivo->caminho) }}" target="_blank"
                        style="width: 30px; height: 30px;"
                        class="btn btn-primary btn-sm d-inline-flex align-items-center justify-content-center mr-2"> 
                        <i class="fas fa-eye"></i>
                    </a>

                    @if($pedido->status == "em_elaboracao")
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('financeiro') || auth()->user()->can('acesso-revista'))
                    <form action="{{ route('arquivos.destroy', $arquivo) }}" method="POST"
                        onsubmit="return confirm('Tem certeza que deseja excluir este arquivo?')">
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
    </table>
</div>
@endif