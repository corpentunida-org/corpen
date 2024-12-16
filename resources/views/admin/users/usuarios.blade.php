<x-base-layout>
@section('titlepage', 'Usuarios')
<div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body p-0">
                <div class="table-responsive">
                        <div class="col-sm-12">
                            <table class="table table-hover" id="customerList">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th class="hstack gap-2 justify-content-end">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registros as $r)
                                        <tr class="single-item">
                                            <td>
                                                {{ $r->fechaRegistro}} {{ $r->horaRegistro}}                                             
                                            </td>
                                            <td class="text-center">
                                                {{ $r->usuario}}                                                
                                            </td>
                                            <td class="hstack gap-2 justify-content-end">
                                                {{ $r->accion}}                                                
                                            </td>                                                                                      
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-base-layout>