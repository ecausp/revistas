@php
    $isFinanceiro = Auth::user()->HasAnyRole(['financeiro','admin'])
@endphp

<div class="mb-1">
    <label for="name" class="form-label">Nome </label>
    <input type="text" class="form-control" name="name" id="name" 
           value="{{ old('name', isset($revista) ? $revista->name : '') }}" 
           placeholder="Revista..." 
           @unless($isFinanceiro) disabled @endunless
           required>
</div>

<div class="mb-1">
    <label for="email_revista" class="form-label">E-mail </label>
    <input type="text" class="form-control" name="email_revista" id="email_revista" 
           value="{{ old('email_revista', isset($revista) ? $revista->email_revista : '') }}" 
           placeholder="Email da Revista..." 
           @unless($isFinanceiro) disabled @endunless
           required>
</div>

<div class="mb-1">
    <label for="centro_despesa" class="form-label">Centro de Despesa </label>
    <select class="form-control select2" name="centro_despesa" id="centro_despesa" 
            @unless($isFinanceiro) disabled @endunless
            required>
        @if (isset($revista))
            <option value="{{ $revista->centro_despesa }}" selected>{{ $revista->centro_despesa }}</option>
        @else
            <option value="" selected>Centro de despesa...</option>
        @endif
        @foreach ($centros_despesas as $centro_despesa)
            <option value="{{ $centro_despesa['etrhie'] }}">{{ $centro_despesa['etrhie'] }}</option>
        @endforeach
    </select>
</div>

<div class="mb-1">
    <label for="responsavel_centro_despesa" class="form-label">Editor(es)</label>
    <select class="form-control select2" 
            name="responsavel_centro_despesa[]"
            id="responsavel_centro_despesa"
            multiple="multiple"
            @unless($isFinanceiro) disabled @endunless
            required>
        
        @if (isset($revista))
            @php
                $responsaveisSelecionados = explode(',', $revista->responsavel_centro_despesa);
            @endphp

            @foreach ($responsaveis_centro_despesa as $responsavel)
                <option value="{{ $responsavel['codpes'] }}"
                    @if(in_array($responsavel['codpes'], $responsaveisSelecionados)) selected @endif>
                    {{ $responsavel['nompesttd'] }}
                </option>
            @endforeach
        @else
            <option value="" disabled>Escolha os editores...</option>
            @foreach ($responsaveis_centro_despesa as $responsavel)
                <option value="{{ $responsavel['codpes'] }}">
                    {{ $responsavel['nompesttd'] }}
                </option>
            @endforeach
        @endif
    </select>
</div>

<div class="container mt-5 col">
    @if($isFinanceiro)
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
    @endif

    @if (isset($revista))
        <a href="{{ route('solicitantes.create',  $revista)}}" class="btn btn-warning"><i class="fas fa-user"></i>
            Solicitante(s)</a>
    @endif

    <a href="{{ route('revistas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>
