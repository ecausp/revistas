@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Redefinir Senha
    </div>

    <div class="card-body">
        <div class="mb-1">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control email" name="email" id="email" placeholder="email@exemplo.com"
                required></input>
        </div>

        <div class="mb-1">
            <label for="senha" class="form-label">Nova Senha</label>
            <input type="password" class="form-control" name="password" id="password" required></input>
        </div>

        <div class="mb-1">
            <label for="senha" class="form-label">Nova Senha</label>
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"
                required></input>
        </div>
        <br>

        <button type="submit" class="btn btn-primary">Alterar Senha</button>
        </form>
    </div>
    @endsection