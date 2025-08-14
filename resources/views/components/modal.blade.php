{{-- <div class="modal fade-scale" id="ModalConfirmacionEliminar" aria-hidden="true"
    aria-labelledby="languageSelectModalLabel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered ">
        <div class="modal-content bg-soft-primary border-0">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>¿Está seguro que desea eliminar?</h4>
            </div>
            <div class="modal-footer hstack justify-content-evenly">
                <div class="text-center">
                    <a href="" class="fs-16 fw-bold" aria-label="Close">CANCELAR</a>
                </div>
                <span class="vr"></span>
                <div class="text-center">
                    <a class="fs-16 fw-bold" id="botonSiModal">SI</a>
                </div>
            </div>
        </div>
    </div>
</div>
--}}
<script>
    document.querySelectorAll('.btnAbrirModalDestroy').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const formulario = btn.closest('form');
            let Text = btn.getAttribute('data-text');
            Swal.fire({
                title: `¿Está seguro de eliminar el ${Text}?`,
                text: `Una vez eliminado, no podrá deshacer esta acción. `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-danger mx-1',
                    cancelButton: 'btn btn-secondary mx-1'
                },
                buttonsStyling: false,
                showClass: {
                    popup: 'animate__animated animate__zoomIn'
                },
                hideClass: {
                    popup: 'animate__animated animate__zoomOut'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formulario.submit();
                }
            });
        });
    });
</script>