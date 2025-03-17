<div class="mb-1">
    <label for="name" class="form-label">Nome </label>
    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', isset($revista) ? $revista->name
        : '') }}" placeholder="Revista..." required></input>
</div>

<div class="mb-1">
    <label for="centro_despesa" class="form-label">Centro de Despesa </label>
    @if (isset($revista))
        <select class="form-control select2" name="centro_despesa" id="centro_despesa">
            <option value="{{ $revista->centro_despesa }}" selected>{{ $revista->centro_despesa }}</option>
            @else
            <select class="form-control select2" name="centro_despesa" id="centro_despesa" placeholder="Centro de Despesa..." required>
                <option value="" selected>Centro de despesa...</option>
                @endif
                @foreach ($centros_despesas as $centro_despesa)
                    <option value="{{ $centro_despesa['etrhie'] }}">{{ $centro_despesa['etrhie'] }}</option>
                @endforeach
            </select>
</div>

<div class="container mt-5 col">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
    <a href="{{ route('revistas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>