<x-default-layout>
    @section('title')
        Purchase Order Templates
    @endsection

    @push('styles')
        <style>
            :root {
                --po-template-bg: #eef3fb;
                --po-template-surface: #ffffff;
                --po-template-surface-soft: #f8fafc;
                --po-template-text: #172033;
                --po-template-muted: #6b7280;
                --po-template-line: #e5e7eb;
                --po-template-primary: #1d4ed8;
                --po-template-primary-soft: #dbeafe;
                --po-template-success: #16a34a;
                --po-template-success-soft: #dcfce7;
                --po-template-warning: #d97706;
                --po-template-warning-soft: #fef3c7;
                --po-template-danger: #dc2626;
                --po-template-danger-soft: #fee2e2;
                --po-template-shadow: 0 16px 30px rgba(23, 32, 51, 0.1);
            }

            .po-template-page {
                padding: 24px;
                border-radius: 24px;
                background: linear-gradient(180deg, #dde9ff 0%, var(--po-template-bg) 18%, #f7f9fd 100%);
            }

            .po-template-topbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                margin-bottom: 18px;
            }

            .po-template-topbar h2 {
                margin: 0;
                font-size: 28px;
                color: var(--po-template-text);
            }

            .po-template-topbar p {
                margin: 4px 0 0;
                color: var(--po-template-muted);
                font-size: 14px;
            }

            .po-template-badge {
                border-radius: 14px;
                border: 1px solid var(--po-template-line);
                background: #fff;
                padding: 12px 14px;
                box-shadow: var(--po-template-shadow);
                font-size: 13px;
                color: var(--po-template-muted);
                white-space: nowrap;
            }

            .po-template-summary {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 12px;
                margin-bottom: 16px;
            }

            .po-template-summary-box {
                padding: 14px;
                border-radius: 18px;
                background: var(--po-template-surface-soft);
                border: 1px solid var(--po-template-line);
            }

            .po-template-summary-box strong {
                display: block;
                font-size: 22px;
                margin-bottom: 4px;
                color: var(--po-template-text);
            }

            .po-template-summary-box span {
                color: var(--po-template-muted);
                font-size: 12px;
            }

            .po-template-layout {
                display: grid;
                grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
                gap: 18px;
            }

            .po-template-card {
                background: var(--po-template-surface);
                border-radius: 22px;
                box-shadow: var(--po-template-shadow);
                padding: 16px;
                border: 1px solid rgba(255, 255, 255, 0.65);
                margin-bottom: 16px;
            }

            .po-template-section-head {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
                margin-bottom: 12px;
            }

            .po-template-section-head h3 {
                margin: 0;
                font-size: 16px;
                color: var(--po-template-text);
            }

            .po-template-section-head span {
                color: var(--po-template-primary);
                font-size: 12px;
                font-weight: 800;
            }

            .po-template-grid-2,
            .po-template-grid-3 {
                display: grid;
                gap: 12px;
            }

            .po-template-grid-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .po-template-grid-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .po-template-field label {
                display: block;
                margin-bottom: 6px;
                font-size: 11px;
                color: var(--po-template-muted);
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }

            .po-template-input,
            .po-template-select,
            .po-template-textarea {
                width: 100%;
                border-radius: 14px;
                border: 1px solid var(--po-template-line);
                background: #fff;
                padding: 12px 13px;
                color: var(--po-template-text);
                font-size: 14px;
                outline: none;
                font-family: inherit;
            }

            .po-template-textarea {
                min-height: 92px;
                resize: vertical;
                background: var(--po-template-surface-soft);
            }

            .po-template-inline-add {
                display: grid;
                grid-template-columns: 1.2fr 0.8fr 0.7fr 0.7fr auto;
                gap: 10px;
                align-items: end;
            }

            .po-template-btn {
                border: none;
                border-radius: 14px;
                padding: 12px 14px;
                font-size: 13px;
                font-weight: 800;
                cursor: pointer;
                box-shadow: var(--po-template-shadow);
            }

            .po-template-btn-primary {
                background: var(--po-template-primary);
                color: #fff;
            }

            .po-template-btn-secondary {
                background: #fff;
                color: var(--po-template-text);
                border: 1px solid var(--po-template-line);
            }

            .po-template-btn-soft {
                background: var(--po-template-primary-soft);
                color: var(--po-template-primary);
            }

            .po-template-btn-danger {
                background: var(--po-template-danger-soft);
                color: var(--po-template-danger);
            }

            .po-template-search-box {
                display: flex;
                gap: 10px;
                align-items: center;
                background: #fff;
                border: 1px solid var(--po-template-line);
                border-radius: 16px;
                padding: 12px 14px;
                margin-bottom: 12px;
            }

            .po-template-search-box input {
                border: none;
                outline: none;
                width: 100%;
                font-size: 14px;
                background: transparent;
            }

            .po-template-chips {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                margin-bottom: 12px;
            }

            .po-template-chip {
                padding: 8px 12px;
                border-radius: 999px;
                background: #eef2ff;
                color: #3730a3;
                font-size: 12px;
                font-weight: 700;
                cursor: pointer;
                border: none;
            }

            .po-template-chip.active {
                background: var(--po-template-primary);
                color: #fff;
            }

            .po-template-catalog-list,
            .po-template-list {
                display: grid;
                gap: 10px;
            }

            .po-template-catalog-item,
            .po-template-item {
                border: 1px solid var(--po-template-line);
                border-radius: 16px;
                background: #fff;
                padding: 12px;
                display: flex;
                justify-content: space-between;
                gap: 12px;
                align-items: center;
            }

            .po-template-catalog-item.hidden {
                display: none;
            }

            .po-template-catalog-info h4,
            .po-template-item-info h4,
            .po-template-line h4 {
                margin: 0 0 4px;
                font-size: 14px;
                color: var(--po-template-text);
            }

            .po-template-catalog-info p,
            .po-template-item-info p,
            .po-template-line p {
                margin: 0;
                color: var(--po-template-muted);
                font-size: 12px;
                line-height: 1.45;
            }

            .po-template-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border-radius: 999px;
                padding: 7px 10px;
                font-size: 11px;
                font-weight: 800;
                white-space: nowrap;
            }

            .po-template-pill-blue {
                background: var(--po-template-primary-soft);
                color: var(--po-template-primary);
            }

            .po-template-template-items {
                display: grid;
                gap: 10px;
            }

            .po-template-line {
                border: 1px solid var(--po-template-line);
                border-radius: 16px;
                padding: 12px;
                background: var(--po-template-surface-soft);
            }

            .po-template-line-top {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                align-items: flex-start;
                margin-bottom: 8px;
            }

            .po-template-line-meta {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                margin-top: 8px;
                align-items: center;
            }

            .po-template-line-meta span {
                font-size: 11px;
                color: var(--po-template-muted);
                background: #fff;
                border: 1px solid var(--po-template-line);
                border-radius: 999px;
                padding: 6px 10px;
            }

            .po-template-inline-qty {
                width: 48px;
                border: 1px solid var(--po-template-line);
                border-radius: 8px;
                padding: 4px 6px;
                font-size: 11px;
                font-weight: 700;
                color: var(--po-template-text);
                background: #fff;
                outline: none;
                margin-left: 4px;
            }

            .po-template-result {
                display: none;
                margin-top: 12px;
                padding: 12px 14px;
                border-radius: 14px;
                font-size: 13px;
                line-height: 1.45;
                font-weight: 700;
            }

            .po-template-result.show {
                display: block;
            }

            .po-template-result-success {
                background: var(--po-template-success-soft);
                color: var(--po-template-success);
                border: 1px solid #86efac;
            }

            @media (max-width: 1180px) {
                .po-template-layout {
                    grid-template-columns: 1fr;
                }

                .po-template-inline-add,
                .po-template-grid-3,
                .po-template-summary {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .po-template-page {
                    padding: 16px;
                }

                .po-template-topbar,
                .po-template-line-top {
                    flex-direction: column;
                    align-items: stretch;
                }

                .po-template-grid-2,
                .po-template-inline-add {
                    grid-template-columns: 1fr;
                }

                .po-template-badge {
                    white-space: normal;
                }
            }
        </style>
    @endpush

    <div class="po-template-page">
        <div class="po-template-summary">
            <div class="po-template-summary-box">
                <strong id="templateCount">3</strong>
                <span>Existing templates</span>
            </div>
            <div class="po-template-summary-box">
                <strong id="currentItemCount">3</strong>
                <span>Items in current template</span>
            </div>
            <div class="po-template-summary-box">
                <strong id="currentDept">Kitchen</strong>
                <span>Selected department</span>
            </div>
        </div>

        <div class="po-template-layout">
            <section>
                <div class="po-template-card">
                    <div class="po-template-section-head">
                        <h3>Template Details</h3>
                        <span>Header Setup</span>
                    </div>

                    <div class="po-template-grid-2">
                        <div class="po-template-field">
                            <label for="templateName">Template Name</label>
                            <input id="templateName" class="po-template-input" value="Daily Kitchen Essentials">
                        </div>
                        <div class="po-template-field">
                            <label for="templateDept">Department</label>
                            <select id="templateDept" class="po-template-select">
                                <option>Kitchen</option>
                                <option>Housekeeping</option>
                                <option>Bar</option>
                                <option>Bakery</option>
                            </select>
                        </div>
                        <div class="po-template-field">
                            <label for="templatePriority">Priority Default</label>
                            <select id="templatePriority" class="po-template-select">
                                <option>Normal</option>
                                <option>Urgent</option>
                                <option>Low</option>
                            </select>
                        </div>
                        <div class="po-template-field">
                            <label for="templateStatus">Status</label>
                            <select id="templateStatus" class="po-template-select">
                                <option>Active</option>
                                <option>Draft</option>
                                <option>Archived</option>
                            </select>
                        </div>
                    </div>

                    <div class="po-template-field mt-3">
                        <label for="templateDescription">Description</label>
                        <textarea id="templateDescription" class="po-template-textarea">Reusable daily order template for vegetables, dairy, and morning kitchen prep items.</textarea>
                    </div>
                </div>

                <div class="po-template-card">
                    <div class="po-template-section-head">
                        <h3>Insert Items</h3>
                        <span>Manual Add</span>
                    </div>

                    <div class="po-template-inline-add">
                        <div class="po-template-field">
                            <label for="newItemName">Product Name</label>
                            <input id="newItemName" class="po-template-input" placeholder="e.g. Tomato">
                        </div>
                        <div class="po-template-field">
                            <label for="newItemCategory">Category</label>
                            <select id="newItemCategory" class="po-template-select">
                                <option>Vegetables</option>
                                <option>Dairy</option>
                                <option>Dry Goods</option>
                                <option>Cleaning</option>
                            </select>
                        </div>
                        <div class="po-template-field">
                            <label for="newItemQty">Default Qty</label>
                            <input id="newItemQty" class="po-template-input" value="1">
                        </div>
                        <div class="po-template-field">
                            <label for="newItemUnit">Unit</label>
                            <select id="newItemUnit" class="po-template-select">
                                <option>kg</option>
                                <option>pcs</option>
                                <option>pack</option>
                                <option>bottle</option>
                            </select>
                        </div>
                        <button id="addItemBtn" type="button" class="po-template-btn po-template-btn-primary">Add Item</button>
                    </div>

                    <div class="po-template-field mt-3">
                        <label for="newItemNote">Default Note</label>
                        <input id="newItemNote" class="po-template-input" placeholder="Optional note for this template item">
                    </div>
                </div>

                <div class="po-template-card">
                    <div class="po-template-section-head">
                        <h3>Current Template Items</h3>
                        <span id="lineCountLabel">3 lines</span>
                    </div>

                    <div id="templateItems" class="po-template-template-items">
                        <div class="po-template-line" data-category="Vegetables">
                            <div class="po-template-line-top">
                                <div>
                                    <h4>Tomato</h4>
                                    <p>Default supplier: FreshFarm</p>
                                </div>
                                <button type="button" class="po-template-btn po-template-btn-danger remove-line-btn">Remove</button>
                            </div>
                            <div class="po-template-line-meta">
                                <span>Category: Vegetables</span>
                                <span>Qty: <input class="po-template-inline-qty inline-qty-input" value="2.5"></span>
                                <span>Unit: kg</span>
                                <span>Note: ripe if possible</span>
                            </div>
                        </div>

                        <div class="po-template-line" data-category="Vegetables">
                            <div class="po-template-line-top">
                                <div>
                                    <h4>Onion</h4>
                                    <p>Default supplier: FreshFarm</p>
                                </div>
                                <button type="button" class="po-template-btn po-template-btn-danger remove-line-btn">Remove</button>
                            </div>
                            <div class="po-template-line-meta">
                                <span>Category: Vegetables</span>
                                <span>Qty: <input class="po-template-inline-qty inline-qty-input" value="3"></span>
                                <span>Unit: kg</span>
                                <span>Note: medium size</span>
                            </div>
                        </div>

                        <div class="po-template-line" data-category="Dairy">
                            <div class="po-template-line-top">
                                <div>
                                    <h4>Yogurt</h4>
                                    <p>Default supplier: DairyPlus</p>
                                </div>
                                <button type="button" class="po-template-btn po-template-btn-danger remove-line-btn">Remove</button>
                            </div>
                            <div class="po-template-line-meta">
                                <span>Category: Dairy</span>
                                <span>Qty: <input class="po-template-inline-qty inline-qty-input" value="8"></span>
                                <span>Unit: pcs</span>
                                <span>Note: strawberry flavor</span>
                            </div>
                        </div>
                    </div>

                    <div id="saveResult" class="po-template-result po-template-result-success">Template saved successfully.</div>
                </div>
            </section>

            <aside>

             <div class="po-template-card">
                    <div class="po-template-section-head">
                        <h3>Existing Templates</h3>
                        <span>Quick Load</span>
                    </div>

                    <div class="po-template-list">
                        <div class="po-template-item">
                            <div class="po-template-item-info">
                                <h4>Daily Kitchen Essentials</h4>
                                <p>Kitchen · 3 items · Active</p>
                            </div>
                            <div class="po-template-pill po-template-pill-blue">Current</div>
                        </div>

                        <div class="po-template-item">
                            <div class="po-template-item-info">
                                <h4>Weekend Prep Order</h4>
                                <p>Kitchen · 12 items · Active</p>
                            </div>
                            <button type="button" class="po-template-btn po-template-btn-secondary">Load</button>
                        </div>

                        <div class="po-template-item">
                            <div class="po-template-item-info">
                                <h4>Housekeeping Restock</h4>
                                <p>Housekeeping · 9 items · Draft</p>
                            </div>
                            <button type="button" class="po-template-btn po-template-btn-secondary">Load</button>
                        </div>
                    </div>
                </div>

                <div class="po-template-card">
                    <div class="po-template-section-head">
                        <h3>Actions</h3>
                        <span>Save / Duplicate</span>
                    </div>

                    <div class="po-template-grid-3">
                        <button id="saveTemplateBtn" type="button" class="po-template-btn po-template-btn-primary">Save Template</button>
                        <button type="button" class="po-template-btn po-template-btn-secondary">Duplicate</button>
                        <button type="button" class="po-template-btn po-template-btn-secondary">Archive</button>
                    </div>
                </div>
                <div class="po-template-card">
                    <div class="po-template-section-head">
                        <h3>Product Catalog</h3>
                        <span>Insert from Master Data</span>
                    </div>

                    <div class="po-template-search-box">
                        <span>Search</span>
                        <input id="catalogSearch" type="text" placeholder="Search product name or category...">
                    </div>

                    <div class="po-template-chips">
                        <button type="button" class="po-template-chip active" data-filter="all">All</button>
                        <button type="button" class="po-template-chip" data-filter="Vegetables">Vegetables</button>
                        <button type="button" class="po-template-chip" data-filter="Dairy">Dairy</button>
                        <button type="button" class="po-template-chip" data-filter="Dry Goods">Dry Goods</button>
                        <button type="button" class="po-template-chip" data-filter="Cleaning">Cleaning</button>
                    </div>

                    <div id="catalogList" class="po-template-catalog-list">
                        <div class="po-template-catalog-item" data-category="Vegetables" data-name="Tomato">
                            <div class="po-template-catalog-info">
                                <h4>Tomato</h4>
                                <p>Vegetables · Preferred supplier: FreshFarm · Unit: kg</p>
                            </div>
                            <button type="button" class="po-template-btn po-template-btn-soft add-from-catalog-btn" data-name="Tomato" data-category="Vegetables" data-qty="2.5" data-unit="kg" data-note="ripe if possible">Insert</button>
                        </div>

                        <div class="po-template-catalog-item" data-category="Vegetables" data-name="Cucumber">
                            <div class="po-template-catalog-info">
                                <h4>Cucumber</h4>
                                <p>Vegetables · Preferred supplier: FreshFarm · Unit: kg</p>
                            </div>
                            <button type="button" class="po-template-btn po-template-btn-soft add-from-catalog-btn" data-name="Cucumber" data-category="Vegetables" data-qty="2" data-unit="kg" data-note="fresh">Insert</button>
                        </div>

                        <div class="po-template-catalog-item" data-category="Dairy" data-name="Yogurt">
                            <div class="po-template-catalog-info">
                                <h4>Yogurt</h4>
                                <p>Dairy · Preferred supplier: DairyPlus · Unit: pcs</p>
                            </div>
                            <button type="button" class="po-template-btn po-template-btn-soft add-from-catalog-btn" data-name="Yogurt" data-category="Dairy" data-qty="8" data-unit="pcs" data-note="strawberry flavor">Insert</button>
                        </div>

                        <div class="po-template-catalog-item" data-category="Dry Goods" data-name="Flour">
                            <div class="po-template-catalog-info">
                                <h4>Flour</h4>
                                <p>Dry Goods · Preferred supplier: Baker Supply · Unit: kg</p>
                            </div>
                            <button type="button" class="po-template-btn po-template-btn-soft add-from-catalog-btn" data-name="Flour" data-category="Dry Goods" data-qty="1" data-unit="kg" data-note="type 00 if possible">Insert</button>
                        </div>

                        <div class="po-template-catalog-item" data-category="Cleaning" data-name="Paper Towels">
                            <div class="po-template-catalog-info">
                                <h4>Paper Towels</h4>
                                <p>Cleaning · Preferred supplier: CleanPro · Unit: pack</p>
                            </div>
                            <button type="button" class="po-template-btn po-template-btn-soft add-from-catalog-btn" data-name="Paper Towels" data-category="Cleaning" data-qty="2" data-unit="pack" data-note="large roll">Insert</button>
                        </div>
                    </div>
                </div>

               
            </aside>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const templateItems = document.getElementById('templateItems');
                const lineCountLabel = document.getElementById('lineCountLabel');
                const currentItemCount = document.getElementById('currentItemCount');
                const currentDept = document.getElementById('currentDept');
                const templateDept = document.getElementById('templateDept');
                const saveTemplateBtn = document.getElementById('saveTemplateBtn');
                const saveResult = document.getElementById('saveResult');
                const catalogSearch = document.getElementById('catalogSearch');
                const catalogList = document.getElementById('catalogList');
                const chips = Array.from(document.querySelectorAll('.po-template-chip'));
                const addItemBtn = document.getElementById('addItemBtn');
                const newItemName = document.getElementById('newItemName');
                const newItemCategory = document.getElementById('newItemCategory');
                const newItemQty = document.getElementById('newItemQty');
                const newItemUnit = document.getElementById('newItemUnit');
                const newItemNote = document.getElementById('newItemNote');

                let activeFilter = 'all';

                function updateCounts() {
                    const count = templateItems.querySelectorAll('.po-template-line').length;
                    lineCountLabel.textContent = `${count} lines`;
                    currentItemCount.textContent = String(count);
                    currentDept.textContent = templateDept.value;
                }

                function bindTemplateLineEvents() {
                    templateItems.querySelectorAll('.remove-line-btn').forEach((button) => {
                        button.onclick = function () {
                            const line = button.closest('.po-template-line');

                            if (line) {
                                line.remove();
                            }

                            updateCounts();
                        };
                    });

                    templateItems.querySelectorAll('.inline-qty-input').forEach((input) => {
                        input.onblur = function () {
                            const cleaned = input.value.trim();
                            const numeric = Number(cleaned);

                            input.value = !cleaned || Number.isNaN(numeric) || numeric <= 0 ? '1' : cleaned;
                        };
                    });
                }

                function createTemplateLine(name, category, quantity, unit, note) {
                    const line = document.createElement('div');
                    line.className = 'po-template-line';
                    line.dataset.category = category;
                    line.innerHTML = `
                        <div class="po-template-line-top">
                            <div>
                                <h4>${name}</h4>
                                <p>Inserted into template</p>
                            </div>
                            <button type="button" class="po-template-btn po-template-btn-danger remove-line-btn">Remove</button>
                        </div>
                        <div class="po-template-line-meta">
                            <span>Category: ${category}</span>
                            <span>Qty: <input class="po-template-inline-qty inline-qty-input" value="${quantity}"></span>
                            <span>Unit: ${unit}</span>
                            <span>Note: ${note || '-'}</span>
                        </div>
                    `;

                    templateItems.appendChild(line);
                    bindTemplateLineEvents();
                    updateCounts();
                }

                function applyCatalogFilter() {
                    const query = catalogSearch.value.trim().toLowerCase();

                    Array.from(catalogList.querySelectorAll('.po-template-catalog-item')).forEach((item) => {
                        const name = item.dataset.name.toLowerCase();
                        const category = item.dataset.category;
                        const matchesSearch = name.includes(query) || category.toLowerCase().includes(query);
                        const matchesFilter = activeFilter === 'all' || category === activeFilter;

                        item.classList.toggle('hidden', !(matchesSearch && matchesFilter));
                    });
                }

                chips.forEach((chip) => {
                    chip.addEventListener('click', function () {
                        chips.forEach((currentChip) => currentChip.classList.remove('active'));
                        chip.classList.add('active');
                        activeFilter = chip.dataset.filter;
                        applyCatalogFilter();
                    });
                });

                catalogSearch.addEventListener('input', applyCatalogFilter);

                document.querySelectorAll('.add-from-catalog-btn').forEach((button) => {
                    button.addEventListener('click', function () {
                        createTemplateLine(
                            button.dataset.name,
                            button.dataset.category,
                            button.dataset.qty,
                            button.dataset.unit,
                            button.dataset.note
                        );
                    });
                });

                addItemBtn.addEventListener('click', function () {
                    const name = newItemName.value.trim();

                    if (!name) {
                        return;
                    }

                    createTemplateLine(
                        name,
                        newItemCategory.value,
                        newItemQty.value || '1',
                        newItemUnit.value,
                        newItemNote.value.trim()
                    );

                    newItemName.value = '';
                    newItemQty.value = '1';
                    newItemNote.value = '';
                });

                templateDept.addEventListener('change', updateCounts);

                saveTemplateBtn.addEventListener('click', function () {
                    saveResult.classList.add('show');

                    window.setTimeout(function () {
                        saveResult.classList.remove('show');
                    }, 2000);
                });

                bindTemplateLineEvents();
                updateCounts();
                applyCatalogFilter();
            });
        </script>
    @endpush
</x-default-layout>
