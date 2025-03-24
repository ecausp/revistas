<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js">
</script>


<input class="typeahead form-control" id="search" type="text" name="name" placeholder="Nome completo" required></input>


<script type="text/javascript">
var path = "{{ route('autocomplete') }}";

$('#search').typeahead({
    source: function(query, process) {
        return $.get(path, {
            query: query
        }, function(data) {
            return process(data);
        });
    }
});
</script>