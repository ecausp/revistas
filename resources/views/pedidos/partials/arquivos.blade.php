@if ($pedido->arquivos->count() > 0)
<div class="card mt-4">
<div class="card-header">
    <div class="d-flex align-items-center">
        <h5 class="mb-0 me-2 mr-5">Arquivos Anexados</h5>

    </div>
</div>
    <table class="table datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive mb-0">
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
                <td class="d-flex gap-2">
                    <!-- eh necessario rodar o comando php artisan storage:link antes -->
                    <a href="{{ asset('storage/' . $arquivo->path) }}" target="_blank"
                        class="btn btn-primary d-flex ml-2 mr-2 justify-content-center">
                        <i class="fas fa-eye"></i>
                    </a>

                    <!-- o arquivo só pode ser deletado se o pedido ainda não tiver sido enviado-->
                    @if($pedido->status != "em_analise_empresa")
                    <form action="{{ route('arquivos.destroy', $arquivo) }}" method="POST"
                        onsubmit="return confirm('Tem certeza que deseja excluir este arquivo?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger d-flex justify-content-center">
                            <i class="fa fa-times"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif