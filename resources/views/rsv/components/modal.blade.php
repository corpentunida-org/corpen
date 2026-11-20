@php
    $mId        = $id ?? 'rsvModalGenerico';
    $mTitulo    = $titulo ?? 'Ventana';
    $mSubtitulo = $subtitulo ?? null;
    $mSize      = $size ?? 'modal-md';
@endphp
<div class="modal fade" id="{{ $mId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $mSize }}">
        <div class="modal-content border shadow-sm" style="border-color: #e9eef4; border-radius: 6px;">
            <div class="modal-header bg-light border-bottom d-flex justify-content-between align-items-center" style="border-color: #f1f5f9; padding: 9px 14px;">
                <div>
                    <h6 class="modal-title mb-0 fw-bold" style="font-size: 11px; color: #334155;">{{ $mTitulo }}</h6>
                    @if($mSubtitulo)
                        <small class="text-muted" style="font-size: 8px;">{{ $mSubtitulo }}</small>
                    @endif
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="font-size: 9px;"></button>
            </div>
            <div class="modal-body" style="padding: 12px 14px;">
                {{ $body ?? '' }}
            </div>
            @if(isset($footer))
                <div class="modal-footer bg-light border-top" style="border-color: #f1f5f9; padding: 9px 14px;">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
