<style>
    /* Estilos base de los formularios y cards corporativos */
    .form-card-corp, .show-card-corp {
        background: white; border-radius: 16px; border: 1px solid var(--border-soft); padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }
    
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 24px; }
    
    .form-group-corp label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 8px; }
    
    .form-control-corp {
        width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border-soft); background: #fdfdfd; font-size: 0.95rem; transition: all 0.3s; color: var(--text-main);
    }
    .form-control-corp:focus { border-color: var(--brand-accent); background: white; outline: none; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
    
    .error-text { color: var(--prio-high); font-size: 0.8rem; margin-top: 5px; display: block; font-weight: 500;}
    
    .readonly-section { background: var(--bg-page); padding: 20px; border-radius: 12px; border: 1px dashed var(--border-soft); }
    .readonly-item .label { display: block; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;}
    .readonly-item strong { font-size: 1.1rem; color: var(--brand-dark); }
    
    .divider-corp { border: 0; height: 1px; background: var(--border-soft); margin: 30px 0; }

    /* Estilos Vista Show */
    .show-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 20px; margin-bottom: 20px; }
    .status-badge-large { padding: 8px 16px; border-radius: 30px; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; display: flex; align-items: center; gap: 8px;}
    .status-badge-large i { font-size: 0.6rem; }
    .show-header h2 { font-size: 2rem; color: var(--brand-dark); font-weight: 800; margin: 0; }
    
    .info-block h4 { font-size: 0.9rem; text-transform: uppercase; color: var(--text-muted); font-weight: 800; margin-bottom: 10px; }
    .info-block p { font-size: 1.1rem; color: var(--text-main); line-height: 1.6; }
    
    .detail-item { display: flex; align-items: flex-start; gap: 15px; }
    .icon-muted { font-size: 1.5rem; color: #cbd5e1; margin-top: 5px; }
    .detail-item .label { display: block; font-size: 0.8rem; color: var(--text-muted); font-weight: 600; margin-bottom: 3px; }
    
    .ecm-attachment-box { background: #f0f9ff; border: 1px solid #bae6fd; padding: 20px; border-radius: 12px; }
    .file-icon { font-size: 2.5rem; color: #0ea5e9; }

    @media (max-width: 768px) {
        .grid-2, .grid-3 { grid-template-columns: 1fr; gap: 15px; }
        .form-card-corp, .show-card-corp { padding: 20px; }
    }
</style>