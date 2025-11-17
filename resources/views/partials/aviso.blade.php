@if (config('revistas.aviso'))
  <div class="alert alert-{{ config('revistas.avisoTipo') }} alert-dismissible text-center rounded-0 m-0 p-2 border-0 mb-4" role="alert">
    {{ config('revistas.aviso') }}
  </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', () => {

    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade');
            alert.classList.add('show');
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';

            setTimeout(() => alert.remove(), 500);
        }, 6000); // 6 segundos
    });

});
</script>
