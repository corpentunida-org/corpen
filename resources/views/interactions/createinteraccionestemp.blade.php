<x-base-layout>
    <div class="row">
        <div class="col-xl-8">
            <div class="card invoice-container">
                <div class="card-header">
                    <h5>CREAR INTERACCIÓN</h5>
                    <a class="btn btn-primary" href="">
                        <i class="feather feather-plus me-2"></i>
                        <span>Ir al Dashboard</span>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="px-4 pt-4">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Cliente:</label>
                                <input id="" class="form-control" placeholder="Issue date...">
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="form-label">Agente:</label>
                                <input id="issueDate" class="form-control" placeholder="...">
                            </div>
                            <div class="col-md-3 mb-4">
                                <label class="form-label">Fecha y Hora:</label>
                                <input id="dueDate" class="form-control" placeholder="...">
                            </div>

                        </div>
                    </div>
                    <hr class="border-dashed">
                    <div class="px-4 row justify-content-between">
                        <div class="col-xl-3">
                            <div class="form-group mb-3">
                                <label for="InvoiceLabel" class="form-label">Canal</label>
                                <input type="text" class="form-control" id="InvoiceLabel"
                                    placeholder="Duralux Invoice">
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="form-group mb-3">
                                <label for="InvoiceNumber" class="form-label">Tipo Interacción</label>
                                <input type="text" class="form-control" id="InvoiceNumber" placeholder="#NXL2023">
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="form-group mb-3">
                                <label for="InvoiceProduct" class="form-label">Duración</label>
                                <input type="text" class="form-control" id="InvoiceProduct"
                                    placeholder="Product Name">
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="form-group mb-3">
                                <label class="form-label">Resultado</label>
                                <input type="text" class="form-control" id="" placeholder="Product Name">
                            </div>
                        </div>
                    </div>
                    <div class="px-4 col-md-12 mb-4">
                        <label class="form-label">Observaciones:</label>
                        <input id="" class="form-control" placeholder="...">
                    </div>
                    <div class="px-4 row">
                        <div class="col-md-6">
                            <label for="attachments" class="form-label">Subir Archivos</label>
                            <input type="file" name="attachments[]" id="attachments" class="form-control"
                                multiple="">
                            <small class="form-text text-muted">Formato: PDF, JPG, PNG, DOCX, XLSX. Tamaño
                                máximo: 5MB.</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="interaction_url" class="form-label">URL (Grabación, Chat, etc.)</label>
                            <input type="url" name="interaction_url" id="interaction_url" class="form-control"
                                value=""
                                placeholder="Ej: https://link-a-grabacion.com/123 o enlace a chat/documento">
                            <small class="form-text text-muted">Enlace a grabaciones, chats de WhatsApp, o
                                recurso externo relevante.</small>
                        </div>
                    </div>

                    <hr class="border-dashed">
                    <div class="row px-4 justify-content-between">
                        <div class="col-xl-5 mb-4 mb-sm-0">
                            <div class="mb-4">
                                <h6 class="fw-bold">Invoice From:</h6>
                                <span class="fs-12 text-muted">Send an invoice and get paid</span>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="InvoiceName" class="col-sm-3 col-form-label">Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="InvoiceName"
                                        placeholder="Business Name">
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="InvoiceEmail" class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="InvoiceEmail"
                                        placeholder="Email Address">
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="InvoicePhone" class="col-sm-3 col-form-label">Phone</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="InvoicePhone"
                                        placeholder="Enter Phone">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="InvoiceAddress" class="col-sm-3 col-form-label">Address</label>
                                <div class="col-sm-9">
                                    <textarea rows="5" class="form-control" id="InvoiceAddress" placeholder="Enter Address"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5">
                            <div class="mb-4">
                                <h6 class="fw-bold">Invoice To:</h6>
                                <span class="fs-12 text-muted">Send an invoice and get paid</span>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="ClientName" class="col-sm-3 col-form-label">Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="ClientName"
                                        placeholder="Business Name">
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="ClientEmail" class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="ClientEmail"
                                        placeholder="Email Address">
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="ClientPhone" class="col-sm-3 col-form-label">Phone</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="ClientPhone"
                                        placeholder="Enter Phone">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ClientAddress" class="col-sm-3 col-form-label">Address</label>
                                <div class="col-sm-9">
                                    <textarea rows="5" class="form-control" id="ClientAddress" placeholder="Enter Address"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="border-dashed">
                    <div class="px-4 clearfix">
                        <div class="mb-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-bold">Add Items:</h6>
                                <span class="fs-12 text-muted">Add items to invoice</span>
                            </div>
                            <div class="avatar-text avatar-sm" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                title="Informations">
                                <i class="feather feather-info"></i>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered overflow-hidden" id="tab_logic">
                                <thead>
                                    <tr class="single-item">
                                        <th class="text-center">#</th>
                                        <th class="text-center wd-450">Product</th>
                                        <th class="text-center wd-150">Qty</th>
                                        <th class="text-center wd-150">Price</th>
                                        <th class="text-center wd-150">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="addr0">
                                        <td>1</td>
                                        <td><input type="text" name="product[]" placeholder="Enter Product Name"
                                                class="form-control"></td>
                                        <td><input type="number" name="qty[]" placeholder="Enter Qty"
                                                class="form-control qty" step="1" min="1"></td>
                                        <td><input type="number" name="price[]" placeholder="Enter Unit Price"
                                                class="form-control price" step="1.00" min="1"></td>
                                        <td><input type="number" name="total[]" placeholder="0.00"
                                                class="form-control total" readonly=""></td>
                                    </tr>
                                    <tr id="addr1">
                                        <td>2</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button id="delete_row" class="btn btn-sm bg-soft-danger text-danger">Delete</button>
                            <button id="add_row" class="btn btn-sm btn-primary">Add Items</button>
                        </div>
                    </div>
                    <hr class="border-dashed">
                    <div class="px-4 pb-4">
                        <div class="form-group">
                            <label for="InvoiceNote" class="form-label">Invoice Note:</label>
                            <textarea rows="6" class="form-control" id="InvoiceNote"
                                placeholder="It was a pleasure working with you and your team. We hope you will keep us in mind for future freelance projects. Thank You!"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="fw-bold">Grand Total:</h6>
                            <span class="fs-12 text-muted">Grand total invoice</span>
                        </div>
                        <div class="avatar-text avatar-sm" data-bs-toggle="tooltip" data-bs-trigger="hover"
                            title="Grand total invoice">
                            <i class="feather feather-info"></i>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tab_logic_total">
                            <tbody>
                                <tr class="single-item">
                                    <th class="text-dark fw-semibold">Sub Total</th>
                                    <td class="w-25"><input type="number" name="sub_total" placeholder="0.00"
                                            class="form-control border-0 bg-transparent p-0" id="sub_total"
                                            readonly=""></td>
                                </tr>
                                <tr class="single-item">
                                    <th class="text-dark fw-semibold">Tax</th>
                                    <td class="w-25">
                                        <div class="input-group mb-2 mb-sm-0">
                                            <input type="number" class="form-control border-0 bg-transparent p-0"
                                                id="tax" placeholder="0">
                                            <div class="input-group-addon">%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="single-item">
                                    <th class="text-dark fw-semibold">Tax Amount</th>
                                    <td class="w-25"><input type="number" name="tax_amount" id="tax_amount"
                                            placeholder="0.00" class="form-control border-0 bg-transparent p-0"
                                            readonly=""></td>
                                </tr>
                                <tr class="single-item">
                                    <th class="text-dark fw-semibold bg-gray-100">Grand Total</th>
                                    <td class="bg-gray-100 w-25"><input type="number" name="total_amount"
                                            id="total_amount" placeholder="0.00"
                                            class="form-control border-0 bg-transparent p-0 fw-bolder text-dark"
                                            readonly=""></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
