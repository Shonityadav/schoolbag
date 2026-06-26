@extends('layouts.admin')
@section('admin_page_title', 'ID Card Designer')

@section('admin_content')
<style>
    /* Designer Layout CSS */
    .designer-container {
        display: flex;
        height: calc(100vh - 120px);
        background: #f3f4f6;
        border: 1px solid #ddd;
        font-family: 'Roboto', sans-serif;
    }
    .designer-sidebar-left, .designer-sidebar-right {
        width: 300px;
        background: #fff;
        border-right: 1px solid #ddd;
        overflow-y: auto;
        padding: 15px;
    }
    .designer-sidebar-right {
        border-right: none;
        border-left: 1px solid #ddd;
    }
    .designer-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .designer-toolbar {
        height: 50px;
        background: #fff;
        border-bottom: 1px solid #ddd;
        display: flex;
        align-items: center;
        padding: 0 15px;
        gap: 10px;
    }
    .designer-canvas-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: auto;
        background: #e9ecef;
        position: relative;
    }
    .canvas-wrapper {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        background: #fff;
    }
    /* Toolbar buttons */
    .toolbar-btn {
        background: #f8f9fa;
        border: 1px solid #ccc;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    .toolbar-btn:hover { background: #e2e6ea; }
    
    /* Variables List */
    .var-badge {
        display: inline-block;
        padding: 5px 8px;
        background: #eef2f5;
        border: 1px solid #d1d9e0;
        border-radius: 4px;
        margin: 4px 2px;
        cursor: pointer;
        font-size: 12px;
        user-select: none;
    }
    .var-badge:hover { background: #cce5ff; border-color: #b8daff; }
    
    /* Section Headers */
    .sidebar-section-title {
        font-size: 14px;
        font-weight: bold;
        margin-top: 15px;
        margin-bottom: 10px;
        text-transform: uppercase;
        color: #555;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }
    
    /* Form Group overrides */
    .prop-group { margin-bottom: 10px; }
    .prop-group label { font-size: 12px; color: #666; margin-bottom: 2px; display: block; }
    .prop-group input, .prop-group select { font-size: 13px; padding: 4px 8px; height: auto; }
</style>

<!-- Load Fabric.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<div class="container-fluid px-0">
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center p-2 bg-white border-bottom">
        <h4 class="mb-0 ms-2"><i class="bi bi-person-vcard"></i> {{ $template->name }} <span class="badge bg-secondary ms-2" id="save-status">Saved</span></h4>
        <div>
            <div class="btn-group me-3" role="group">
                <input type="radio" class="btn-check" name="sideSwitch" id="btn-front" autocomplete="off" checked onclick="switchSide('front')">
                <label class="btn btn-outline-primary" for="btn-front">Front Side</label>

                <input type="radio" class="btn-check" name="sideSwitch" id="btn-back" autocomplete="off" onclick="switchSide('back')">
                <label class="btn btn-outline-primary" for="btn-back">Back Side</label>
            </div>
            <button class="btn btn-success me-2" onclick="saveLayoutNow()">Save Layout</button>
            <a href="{{ route('admin.id_cards.index') }}" class="btn btn-outline-secondary">Exit</a>
        </div>
    </div>

    <div class="designer-container">
        <!-- LEFT SIDEBAR -->
        <div class="designer-sidebar-left">
            <div class="sidebar-section-title">Add Elements</div>
            <button class="btn btn-sm btn-outline-secondary w-100 mb-2" onclick="addText()">+ Add Text</button>
            <button class="btn btn-sm btn-outline-secondary w-100 mb-2" onclick="addRect()">+ Add Rectangle</button>
            <button class="btn btn-sm btn-outline-secondary w-100 mb-2" onclick="addCircle()">+ Add Circle</button>
            
            <div class="sidebar-section-title mt-4">System Elements</div>
            <button class="btn btn-sm btn-outline-primary w-100 mb-2" onclick="addSystemElement('photo')"><i class="bi bi-person-circle"></i> User Photo Space</button>
            <button class="btn btn-sm btn-outline-primary w-100 mb-2" onclick="addSystemElement('qr_code')"><i class="bi bi-qr-code"></i> QR Code</button>
            <button class="btn btn-sm btn-outline-primary w-100 mb-2" onclick="addSystemElement('barcode')"><i class="bi bi-upc-scan"></i> Barcode</button>
            
            <div class="sidebar-section-title mt-4">Dynamic Variables</div>
            <p class="text-muted" style="font-size:11px;">Click to add to canvas</p>
            <div>
                @foreach($variables as $var)
                    @if($var['type'] === 'both' || $var['type'] === $template->type)
                        <span class="var-badge" onclick="addDynamicText('{{ $var['tag'] }}')" title="{{ $var['label'] }}">{{ $var['tag'] }}</span>
                    @endif
                @endforeach
            </div>

            <div class="sidebar-section-title mt-4">Institute Assets</div>
            <!-- Assets will go here -->
            <button class="btn btn-sm btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#assetModal">Manage Assets</button>
            <div class="mt-2 row g-1">
                @foreach($assets as $asset)
                    <div class="col-4 text-center">
                        <img src="{{ asset($asset->file_path) }}" alt="{{ $asset->name }}" class="img-fluid border" style="cursor:pointer; max-height: 50px;" onclick="addImage('{{ asset($asset->file_path) }}', '{{ $asset->type }}')">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- CENTER CANVAS -->
        <div class="designer-main">
            <div class="designer-toolbar">
                <button class="toolbar-btn" onclick="deleteSelected()" title="Delete Selected"><i class="bi bi-trash"></i> Delete</button>
                <button class="toolbar-btn" onclick="duplicateSelected()" title="Duplicate"><i class="bi bi-files"></i> Clone</button>
                <div class="vr mx-1"></div>
                <button class="toolbar-btn" onclick="bringForward()" title="Bring Forward"><i class="bi bi-layer-forward"></i> Front</button>
                <button class="toolbar-btn" onclick="sendBackward()" title="Send Backward"><i class="bi bi-layer-backward"></i> Back</button>
                <div class="vr mx-1"></div>
                <button class="toolbar-btn" onclick="toggleLock()" id="btn-lock" title="Lock/Unlock"><i class="bi bi-unlock"></i> Lock</button>
                <div class="vr mx-1"></div>
                <label class="d-flex align-items-center mb-0" style="font-size:13px; gap:5px;">
                    Snap:
                    <select id="snap-grid" class="form-select form-select-sm" style="width:70px;">
                        <option value="0">Off</option>
                        <option value="5">5px</option>
                        <option value="10" selected>10px</option>
                        <option value="15">15px</option>
                    </select>
                </label>
                <div class="ms-auto">
                    <button class="toolbar-btn text-primary" onclick="livePreview()"><i class="bi bi-eye"></i> Live Preview</button>
                </div>
            </div>
            <div class="designer-canvas-container" id="canvas-container">
                <div class="canvas-wrapper">
                    <!-- Fabric Canvas -->
                    <canvas id="idcard-canvas"></canvas>
                </div>
                <!-- Safe Area Overlays (CSS Based, pointer-events: none) -->
                <div id="safe-area-guides" style="position: absolute; pointer-events: none; border: 1px dashed red; opacity: 0.5; display: none;"></div>
            </div>
        </div>

        <!-- RIGHT SIDEBAR (PROPERTIES) -->
        <div class="designer-sidebar-right" id="properties-panel" style="display:none;">
            <div class="sidebar-section-title">Properties</div>
            
            <div class="prop-group">
                <label>Element ID/Name</label>
                <input type="text" id="prop-name" class="form-control" onchange="updateSelectedProp('name', this.value)">
            </div>
            
            <div class="row">
                <div class="col-6 prop-group">
                    <label>X Position</label>
                    <input type="number" id="prop-left" class="form-control" onchange="updateSelectedProp('left', parseInt(this.value))">
                </div>
                <div class="col-6 prop-group">
                    <label>Y Position</label>
                    <input type="number" id="prop-top" class="form-control" onchange="updateSelectedProp('top', parseInt(this.value))">
                </div>
                <div class="col-6 prop-group">
                    <label>Width</label>
                    <input type="number" id="prop-width" class="form-control" onchange="updateSelectedPropScaled('width', parseInt(this.value))">
                </div>
                <div class="col-6 prop-group">
                    <label>Height</label>
                    <input type="number" id="prop-height" class="form-control" onchange="updateSelectedPropScaled('height', parseInt(this.value))">
                </div>
                <div class="col-6 prop-group">
                    <label>Rotation</label>
                    <input type="number" id="prop-angle" class="form-control" onchange="updateSelectedProp('angle', parseInt(this.value))">
                </div>
                <div class="col-6 prop-group">
                    <label>Opacity</label>
                    <input type="number" id="prop-opacity" class="form-control" min="0" max="1" step="0.1" onchange="updateSelectedProp('opacity', parseFloat(this.value))">
                </div>
            </div>

            <div id="text-properties" style="display:none;">
                <div class="sidebar-section-title mt-2">Text Styling</div>
                <div class="prop-group">
                    <label>Content</label>
                    <textarea id="prop-text" class="form-control" rows="2" oninput="updateSelectedProp('text', this.value)"></textarea>
                </div>
                <div class="prop-group">
                    <label>Font Family</label>
                    <select id="prop-fontFamily" class="form-select" onchange="updateSelectedProp('fontFamily', this.value)">
                        <option value="Arial">Arial</option>
                        <option value="Roboto">Roboto</option>
                        <option value="Poppins">Poppins</option>
                        <option value="Times New Roman">Times New Roman</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-6 prop-group">
                        <label>Font Size</label>
                        <input type="number" id="prop-fontSize" class="form-control" onchange="updateSelectedProp('fontSize', parseInt(this.value))">
                    </div>
                    <div class="col-6 prop-group">
                        <label>Text Color</label>
                        <input type="color" id="prop-fill" class="form-control form-control-color w-100" onchange="updateSelectedProp('fill', this.value)">
                    </div>
                </div>
                <div class="btn-group w-100 mt-2 mb-2" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-bold" onclick="toggleTextProp('fontWeight', 'bold', 'normal')"><i class="bi bi-type-bold"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-italic" onclick="toggleTextProp('fontStyle', 'italic', 'normal')"><i class="bi bi-type-italic"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-underline" onclick="toggleTextProp('underline', true, false)"><i class="bi bi-type-underline"></i></button>
                </div>
            </div>
            
            <div id="shape-properties" style="display:none;">
                <div class="sidebar-section-title mt-2">Shape Styling</div>
                <div class="row">
                    <div class="col-6 prop-group">
                        <label>Fill Color</label>
                        <input type="color" id="prop-shape-fill" class="form-control form-control-color w-100" onchange="updateSelectedProp('fill', this.value)">
                    </div>
                    <div class="col-6 prop-group">
                        <label>Border Color</label>
                        <input type="color" id="prop-stroke" class="form-control form-control-color w-100" onchange="updateSelectedProp('stroke', this.value)">
                    </div>
                    <div class="col-6 prop-group">
                        <label>Border Width</label>
                        <input type="number" id="prop-strokeWidth" class="form-control" onchange="updateSelectedProp('strokeWidth', parseInt(this.value))">
                    </div>
                    <div class="col-6 prop-group">
                        <label>Border Radius</label>
                        <input type="number" id="prop-rx" class="form-control" min="0" oninput="updateRadius(Math.max(0, parseInt(this.value) || 0))">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Live Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light" id="preview-content">
                <div class="text-center py-5"><i class="bi bi-arrow-repeat spin fa-3x text-primary" style="animation: spin 1s linear infinite;"></i><br>Generating Preview...</div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    // Initial Data
    const fabricFront = {!! json_encode($fabricFront) !!};
    const fabricBack = {!! json_encode($fabricBack) !!};
    let currentSide = 'front';
    
    // Canvas Initialization
    const canvas = new fabric.Canvas('idcard-canvas', {
        preserveObjectStacking: true, // Keep z-index on selection
        backgroundColor: '#ffffff'
    });

    // We store the canvas instances in memory
    let layouts = {
        front: fabricFront,
        back: fabricBack
    };

    // Load initial side
    function loadCanvasData(side) {
        canvas.clear();
        canvas.loadFromJSON(layouts[side], function() {
            canvas.setWidth(layouts[side].width || 1012);
            canvas.setHeight(layouts[side].height || 638);
            canvas.setBackgroundColor(layouts[side].background || '#ffffff', canvas.renderAll.bind(canvas));
            canvas.renderAll();
            updateGuides();
        });
    }

    function switchSide(side) {
        // Save current canvas state to memory
        layouts[currentSide] = canvas.toJSON(['id', 'name', 'selectable', 'lockMovementX', 'lockMovementY', 'lockRotation', 'lockScalingX', 'lockScalingY', 'hasControls']);
        layouts[currentSide].width = canvas.width;
        layouts[currentSide].height = canvas.height;
        layouts[currentSide].background = canvas.backgroundColor;
        
        // Load new side
        currentSide = side;
        loadCanvasData(side);
        hideProperties();
    }

    // Initialize
    loadCanvasData('front');

    // --- Snapping Logic ---
    canvas.on('object:moving', function(options) {
        let snap = parseInt(document.getElementById('snap-grid').value);
        if (snap > 0) {
            options.target.set({
                left: Math.round(options.target.left / snap) * snap,
                top: Math.round(options.target.top / snap) * snap
            });
        }
        triggerAutoSave();
    });

    // --- Events & Properties Panel ---
    canvas.on('selection:created', showProperties);
    canvas.on('selection:updated', showProperties);
    canvas.on('selection:cleared', hideProperties);
    canvas.on('object:modified', function() {
        showProperties();
        triggerAutoSave();
    });
    canvas.on('object:added', triggerAutoSave);
    canvas.on('object:removed', triggerAutoSave);

    function showProperties() {
        let obj = canvas.getActiveObject();
        if(!obj) return;
        
        document.getElementById('properties-panel').style.display = 'block';
        document.getElementById('prop-name').value = obj.name || '';
        document.getElementById('prop-left').value = Math.round(obj.left);
        document.getElementById('prop-top').value = Math.round(obj.top);
        document.getElementById('prop-width').value = Math.round(obj.width * obj.scaleX);
        document.getElementById('prop-height').value = Math.round(obj.height * obj.scaleY);
        document.getElementById('prop-angle').value = Math.round(obj.angle);
        document.getElementById('prop-opacity').value = obj.opacity;

        // Lock status
        document.getElementById('btn-lock').innerHTML = !obj.lockMovementX ? '<i class="bi bi-unlock"></i> Lock' : '<i class="bi bi-lock"></i> Unlock';

        // Type specific panels
        document.getElementById('text-properties').style.display = obj.type === 'i-text' ? 'block' : 'none';
        document.getElementById('shape-properties').style.display = (obj.type === 'rect' || obj.type === 'circle') ? 'block' : 'none';

        if(obj.type === 'i-text') {
            document.getElementById('prop-text').value = obj.text;
            document.getElementById('prop-fontFamily').value = obj.fontFamily;
            document.getElementById('prop-fontSize').value = obj.fontSize;
            document.getElementById('prop-fill').value = obj.fill;
        }
        if(obj.type === 'rect' || obj.type === 'circle') {
            document.getElementById('prop-shape-fill').value = obj.fill === 'transparent' ? '#ffffff' : obj.fill; // handling transparent might need work
            document.getElementById('prop-stroke').value = obj.stroke || '#000000';
            document.getElementById('prop-strokeWidth').value = obj.strokeWidth || 0;
            document.getElementById('prop-rx').value = obj.rx || obj.radius || 0;
        }
    }

    function hideProperties() {
        document.getElementById('properties-panel').style.display = 'none';
    }

    // --- Update Properties ---
    function updateSelectedProp(key, value) {
        let obj = canvas.getActiveObject();
        if(obj) {
            if(typeof value === 'number' && isNaN(value)) value = 0;
            obj.set(key, value);
            canvas.renderAll();
            triggerAutoSave();
        }
    }
    
    function updateSelectedPropScaled(key, value) {
        let obj = canvas.getActiveObject();
        if(obj) {
            if(typeof value === 'number' && isNaN(value)) value = 10;
            if(key === 'width') { obj.set('scaleX', 1); obj.set('width', value); }
            if(key === 'height') { obj.set('scaleY', 1); obj.set('height', value); }
            canvas.renderAll();
            triggerAutoSave();
        }
    }

    function toggleTextProp(key, trueVal, falseVal) {
        let obj = canvas.getActiveObject();
        if(obj && obj.type === 'i-text') {
            let current = obj.get(key);
            obj.set(key, current === trueVal ? falseVal : trueVal);
            canvas.renderAll();
            triggerAutoSave();
        }
    }

    function updateRadius(val) {
        let obj = canvas.getActiveObject();
        if(obj) {
            if(obj.type === 'rect') {
                obj.set('rx', val);
                obj.set('ry', val);
            } else if (obj.type === 'circle') {
                obj.set('radius', val);
            }
            canvas.renderAll();
            triggerAutoSave();
        }
    }

    // --- Tools ---
    function deleteSelected() {
        let activeObjects = canvas.getActiveObjects();
        if (activeObjects.length) {
            canvas.discardActiveObject();
            activeObjects.forEach(function(object) {
                if(object.selectable !== false) {
                    canvas.remove(object);
                }
            });
        }
    }

    function duplicateSelected() {
        let obj = canvas.getActiveObject();
        if (!obj) return;
        obj.clone(function(clone) {
            clone.set({
                left: obj.left + 20,
                top: obj.top + 20,
                id: 'el_' + new Date().getTime()
            });
            canvas.add(clone);
            canvas.setActiveObject(clone);
            triggerAutoSave();
        }, ['id', 'name']);
    }

    function bringForward() {
        let obj = canvas.getActiveObject();
        if(obj) { canvas.bringForward(obj); triggerAutoSave(); }
    }

    function sendBackward() {
        let obj = canvas.getActiveObject();
        if(obj) { canvas.sendBackwards(obj); triggerAutoSave(); }
    }

    function toggleLock() {
        let obj = canvas.getActiveObject();
        if(obj) {
            let isLocked = obj.lockMovementX;
            obj.set({
                lockMovementX: !isLocked,
                lockMovementY: !isLocked,
                lockRotation: !isLocked,
                lockScalingX: !isLocked,
                lockScalingY: !isLocked,
                hasControls: isLocked,
                borderColor: !isLocked ? 'red' : 'rgba(102,153,255,0.75)'
            });
            canvas.renderAll();
            showProperties();
            triggerAutoSave();
        }
    }

    // --- Add Elements ---
    function addText() {
        var text = new fabric.IText('Double Click to Edit', {
            left: (canvas.width / 2) - 80, top: (canvas.height / 2) - 10, fontSize: 20, fontFamily: 'Arial', fill: '#000000',
            id: 'el_' + new Date().getTime(), name: 'text_' + new Date().getTime()
        });
        canvas.add(text);
        canvas.setActiveObject(text);
    }

    function addDynamicText(tag) {
        var text = new fabric.IText(tag, {
            left: (canvas.width / 2) - 60, top: (canvas.height / 2) - 10, fontSize: 20, fontFamily: 'Arial', fill: '#000000',
            id: 'el_' + new Date().getTime(), name: 'var_' + new Date().getTime()
        });
        canvas.add(text);
        canvas.setActiveObject(text);
    }

    function addRect() {
        var rect = new fabric.Rect({
            left: (canvas.width / 2) - 50, top: (canvas.height / 2) - 50, width: 100, height: 100, fill: '#007bff',
            id: 'el_' + new Date().getTime(), name: 'rect_' + new Date().getTime()
        });
        canvas.add(rect);
        canvas.setActiveObject(rect);
    }

    function addCircle() {
        var circle = new fabric.Circle({
            left: (canvas.width / 2) - 50, top: (canvas.height / 2) - 50, radius: 50, fill: '#28a745',
            id: 'el_' + new Date().getTime(), name: 'circle_' + new Date().getTime()
        });
        canvas.add(circle);
        canvas.setActiveObject(circle);
    }

    function addImage(url, typeName) {
        fabric.Image.fromURL(url, function(img) {
            img.set({
                left: (canvas.width / 2) - 75, top: (canvas.height / 2) - 75,
                id: 'el_' + new Date().getTime(), name: typeName + '_' + new Date().getTime()
            });
            img.scaleToWidth(150);
            canvas.add(img);
            canvas.setActiveObject(img);
        });
    }

    function addSystemElement(type) {
        // Creates a placeholder rectangle for system elements like photo, qr, barcode
        var rect = new fabric.Rect({
            left: 50, top: 50, width: 100, height: 100, fill: '#e9ecef', stroke: '#6c757d', strokeWidth: 2, strokeDashArray: [5, 5],
            id: 'el_' + new Date().getTime(), name: type + '_' + new Date().getTime()
        });
        
        // Add a text label to it
        var text = new fabric.Text(type.toUpperCase(), {
            fontSize: 14, fill: '#6c757d', originX: 'center', originY: 'center',
            left: rect.left + rect.width/2, top: rect.top + rect.height/2
        });

        var group = new fabric.Group([rect, text], {
            left: (canvas.width / 2) - 50, top: (canvas.height / 2) - 50,
            id: 'el_' + new Date().getTime(), name: type
        });

        canvas.add(group);
        canvas.setActiveObject(group);
    }

    // --- Guides / Safe Area ---
    function updateGuides() {
        let guides = document.getElementById('safe-area-guides');
        // Simple overlay 10px inside
        guides.style.width = (canvas.width - 20) + 'px';
        guides.style.height = (canvas.height - 20) + 'px';
        guides.style.display = 'block';
    }

    // --- Auto Save & AJAX ---
    let autoSaveTimer;
    function triggerAutoSave() {
        document.getElementById('save-status').innerText = 'Unsaved Changes';
        document.getElementById('save-status').className = 'badge bg-warning ms-2';
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(saveLayoutNow, 10000); // Save after 10s of inactivity
    }

    function serializeCanvas(canvasInstance) {
        let jsonOutput = {
            version: "5.3.0",
            objects: [],
            background: canvasInstance.backgroundColor || '#ffffff'
        };
        let props = ['id', 'name', 'selectable', 'lockMovementX', 'lockMovementY', 'lockRotation', 'lockScalingX', 'lockScalingY', 'hasControls'];
        
        canvasInstance.getObjects().forEach(obj => {
            try {
                jsonOutput.objects.push(obj.toJSON(props));
            } catch (e) {
                console.warn("Fabric toJSON failed for object, using manual fallback:", e);
                let fb = {
                    type: obj.type, left: obj.left || 0, top: obj.top || 0,
                    width: obj.width || 100, height: obj.height || 100,
                    scaleX: obj.scaleX || 1, scaleY: obj.scaleY || 1, angle: obj.angle || 0,
                    fill: obj.fill || '#000000', stroke: obj.stroke || null, strokeWidth: obj.strokeWidth || 0,
                    opacity: obj.opacity !== undefined ? obj.opacity : 1
                };
                if (obj.type === 'i-text' || obj.type === 'text') {
                    fb.text = obj.text || ''; fb.fontSize = obj.fontSize || 20;
                    fb.fontFamily = obj.fontFamily || 'Arial'; fb.fontWeight = obj.fontWeight || 'normal';
                    fb.fontStyle = obj.fontStyle || 'normal'; fb.textAlign = obj.textAlign || 'left';
                    fb.underline = obj.underline || false;
                }
                if (obj.type === 'rect') { fb.rx = obj.rx || 0; fb.ry = obj.ry || 0; }
                if (obj.type === 'circle') { fb.radius = obj.radius || 0; }
                props.forEach(p => { if (obj[p] !== undefined) fb[p] = obj[p]; });
                jsonOutput.objects.push(fb);
            }
        });
        return jsonOutput;
    }

    function saveLayoutNow() {
        try {
            document.getElementById('save-status').innerText = 'Saving...';
            document.getElementById('save-status').className = 'badge bg-warning text-dark ms-2';
            
            // 1. Serialize Canvas using resilient wrapper
            let jsonOutput;
            try {
                jsonOutput = serializeCanvas(canvas);
            } catch (e) {
                throw new Error("serializeCanvas failed: " + e.message);
            }

            // 2. Assign Properties
            try {
                layouts[currentSide] = jsonOutput;
                layouts[currentSide].width = canvas.getWidth ? canvas.getWidth() : canvas.width;
                layouts[currentSide].height = canvas.getHeight ? canvas.getHeight() : canvas.height;
                layouts[currentSide].background = canvas.backgroundColor || '#ffffff';
            } catch (e) {
                throw new Error("Property assignment failed: " + e.message);
            }

            // 3. Stringify
            let bodyData;
            try {
                bodyData = JSON.stringify({
                    front_layout: layouts.front || {objects:[]},
                    back_layout: layouts.back || {objects:[]}
                });
            } catch (e) {
                throw new Error("JSON.stringify failed: " + e.message);
            }

            // 4. Fetch
            fetch("{{ route('admin.id_cards.save_layout', $template->uuid) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: bodyData
            })
            .then(async response => {
                if (!response.ok) {
                    let errText = await response.text();
                    throw new Error(`HTTP error! status: ${response.status}, body: ${errText.substring(0,100)}`);
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    document.getElementById('save-status').innerText = 'Saved';
                    document.getElementById('save-status').className = 'badge bg-success ms-2';
                } else {
                    throw new Error(data.message || 'Unknown error from server');
                }
            }).catch(err => {
                console.error("Save Layout Error:", err);
                document.getElementById('save-status').innerText = 'Failed';
                document.getElementById('save-status').className = 'badge bg-danger ms-2';
                alert('Save failed: ' + err.message);
            });
        } catch (error) {
            console.error("Synchronous Error in saveLayoutNow:", error);
            document.getElementById('save-status').innerText = 'Failed';
            document.getElementById('save-status').className = 'badge bg-danger ms-2';
            alert('Error preparing layout: ' + error.message);
        }
    }

    function livePreview() {
        try {
            // Show modal and loading state
            var previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            document.getElementById('preview-content').innerHTML = '<div class="text-center py-5"><i class="bi bi-arrow-repeat spin fa-3x text-primary" style="animation: spin 1s linear infinite;"></i><br>Generating Preview...</div>';
            previewModal.show();

            // Update current side before sending
            layouts[currentSide] = serializeCanvas(canvas);
            layouts[currentSide].width = canvas.getWidth ? canvas.getWidth() : canvas.width;
            layouts[currentSide].height = canvas.getHeight ? canvas.getHeight() : canvas.height;
            layouts[currentSide].background = canvas.backgroundColor || '#ffffff';

            fetch("{{ route('admin.id_cards.preview', $template->uuid) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    front_layout: layouts.front,
                    back_layout: layouts.back
                })
            })
            .then(async response => {
                if(!response.ok) {
                    let errText = await response.text();
                    throw new Error(`HTTP ${response.status}: ${errText.substring(0,100)}`);
                }
                return response.json();
            })
            .then(data => {
                if(data.html) {
                    document.getElementById('preview-content').innerHTML = data.html;
                } else {
                    document.getElementById('preview-content').innerHTML = '<div class="alert alert-danger">Error generating preview.</div>';
                }
            }).catch(err => {
                console.error("Preview Error:", err);
                document.getElementById('preview-content').innerHTML = `<div class="alert alert-danger">Failed to generate preview: ${err.message}</div>`;
            });
        } catch(error) {
            console.error("Sync Error in preview:", error);
            document.getElementById('preview-content').innerHTML = `<div class="alert alert-danger">Error preparing preview: ${error.message}</div>`;
        }
    }

</script>
@endsection
