<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js">
    </script>
</head>

<body>

    <div class="container">
        <div class="card mt-5">
            <div class="card-body">

                <form action="#" method="POST" enctype="multipart/form-data" class="mt-2">
                    @csrf

                    <input class="typeahead form-control" id="search" type="text">
                    <!-- Campos para preencher automaticamente -->
                    <input type="email" id="email" class="form-control mt-3" placeholder="E-mail" readonly>
                    <input type="text" id="telefone" class="form-control mt-3" placeholder="Telefone" readonly>


                    <div class="mb-3 mt-3">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>

                </form>
            </div>
        </div>

    </div>

    <script type="text/javascript">
    var path = "{{ route('autocomplete') }}";

    $('#search').typeahead({
        source: function(query, process) {
            return $.get("{{ route('autocomplete') }}", {
                query: query
            }, function(data) {
                return process(data.map(function(item) {
                    return {
                        name: item.name,
                        email: item.email,
                        telefone: item.telefone,
                        display: item.name + ' - ' + item
                            .email // Apenas o que será mostrado
                    };
                }));
            });
        },
        displayText: function(item) {
            return item.display; // Mostra apenas "nome - email"
        },
        updater: function(item) {
            $('#search').val(item.name); // Apenas o nome no campo de busca
            $('#email').val(item.email).prop('readonly', true); // Preenche e-mail
            $('#telefone').val(item.telefone).prop('readonly', true); // Preenche telefone
            return item.name; // Retorna apenas o nome no campo de busca
        }
    });
    </script>

    <script type="text/javascript">
    $(document).ready(function() {
        // Limpar os campos de e-mail e telefone e torná-los editáveis no carregamento da página
        $('#name').val('').prop('readonly', false);
        $('#email').val('').prop('readonly', false);
        $('#telefone').val('').prop('readonly', false);
    });
    </script>
</body>

</html>