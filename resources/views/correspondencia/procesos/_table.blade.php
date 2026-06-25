<div class="table-responsive">
    <table class="table table-minimal mb-0">
        <thead>
            <tr>
                <th style="width: 40px;"><input type="checkbox" class="form-check-input" id="selectAll"></th>
                <th>Proceso</th>
                <th>Flujo</th>
                <th>Estado</th>
                <th>Asignados</th>
                <th>Fecha</th>
                <th class="text-end"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($procesos as $proceso)
            <tr>
                <td><input type="checkbox" name="ids[]" value="{{ $proceso->id }}" class="form-check-input row-checkbox"></td>
                <td>
                    <div class="fw-bold text-dark">{{ $proceso->nombre }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">ID: #{{ str_pad($proceso->id, 5, '0', STR_PAD_LEFT) }}</div>
                </td>
                <td class="text-muted small">{{ $proceso->flujo->nombre ?? '—' }}</td>
                <td>
                    <span class="d-flex align-items-center small text-dark">
                        <span class="status-dot {{ $proceso->activo ? 'active' : 'inactive' }}"></span>
                        {{ $proceso->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td>
                    <div class="avatar-group">
                        @forelse($proceso->usuariosAsignados->take(3) as $user)
                            <div class="avatar-mini" title="{{ $user->usuario->name ?? 'User' }}">
                                {{ strtoupper(substr($user->usuario->name ?? 'U', 0, 1)) }}
                            </div>
                        @empty
                            <span class="text-muted small">—</span>
                        @endforelse
                    </div>
                </td>
                <td class="text-muted small">{{ $proceso->created_at->format('d M, y') }}</td>
                <td class="text-end">
                    <a href="{{ route('correspondencia.procesos.show', $proceso) }}" class="text-muted text-decoration-none me-3 hover-text-dark">Ver</a>
                    <a href="#" class="text-muted text-decoration-none">···</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                    <i class="fas fa-inbox mb-2 opacity-50" style="font-size: 2rem;"></i>
                    <p class="mb-0 small">No hay procesos que coincidan con la búsqueda.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>