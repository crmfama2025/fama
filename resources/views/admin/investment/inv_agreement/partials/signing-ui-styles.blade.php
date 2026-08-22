<style>
    body {
        background: #eef0f2;
    }

    /*
     * Modal overlay
     */
    .sig-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;

        align-items: center;
        justify-content: center;

        padding: 20px;
        background: rgba(0, 0, 0, 0.55);
    }

    .sig-modal-overlay.active {
        display: flex;
    }

    /*
     * Modal box
     */
    .sig-modal {
        width: 480px;
        max-width: 95vw;
        max-height: 95vh;

        padding: 28px 28px 20px;

        overflow-y: auto;

        background: #fff;
        border-radius: 10px;

        box-shadow:
            0 8px 40px rgba(0, 0, 0, 0.25);

        font-family: Arial, sans-serif;
    }

    #sendModalOverlay .sig-modal {
        width: 360px;
    }

    .sig-modal h5 {
        margin: 0 0 16px;

        color: #333;
        font-size: 15px;
        font-weight: 700;
    }

    .sig-modal p {
        font-family: Arial, sans-serif;
    }

    /*
     * Signature tabs
     */
    .sig-tabs {
        display: flex;
        gap: 8px;

        margin-bottom: 14px;
    }

    .sig-tab {
        flex: 1;

        padding: 8px;

        color: #333;
        font-size: 13px;
        font-weight: 600;
        text-align: center;

        background: #f8f8f8;
        border: 1.5px solid #ccc;
        border-radius: 6px;

        cursor: pointer;

        transition:
            border-color 0.2s,
            background-color 0.2s,
            color 0.2s;
    }

    .sig-tab.active {
        color: #007bff;
        background: #e8f0fe;
        border-color: #007bff;
    }

    /*
     * Tab panels
     */
    .sig-panel {
        display: none;
    }

    .sig-panel.active {
        display: block;
    }

    /*
     * Signature canvas
     */
    #sig-canvas {
        display: block;

        width: 100%;

        background: #fafafa;
        border: 1.5px solid #ccc;
        border-radius: 6px;

        cursor: crosshair;
        touch-action: none;
    }

    .sig-canvas-hint {
        margin-top: 5px;

        color: #aaa;
        font-size: 11px;
        text-align: center;
    }

    /*
     * Signature upload
     */
    .sig-upload-area {
        padding: 28px 16px;

        text-align: center;

        border: 2px dashed #ccc;
        border-radius: 8px;

        cursor: pointer;

        transition: border-color 0.2s;
    }

    .sig-upload-area:hover {
        border-color: #007bff;
    }

    .sig-upload-area input[type="file"] {
        display: none;
    }

    .sig-upload-preview {
        display: none;
        margin-top: 12px;
    }

    .sig-upload-preview img {
        max-width: 100%;
        max-height: 80px;

        padding: 4px;

        border: 1px solid #eee;
        border-radius: 4px;
    }

    /*
     * Modal actions
     */
    .sig-actions {
        display: flex;
        gap: 8px;

        justify-content: flex-end;

        margin-top: 16px;
    }

    #sendModalOverlay .sig-actions {
        justify-content: center;
        gap: 12px;
    }

    .sig-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;

        padding: 8px 18px;

        color: #333;
        font-family: Arial, sans-serif;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.4;

        background: #f1f1f1;
        border: 0;
        border-radius: 6px;

        cursor: pointer;

        transition:
            opacity 0.2s,
            transform 0.2s;
    }

    .sig-btn:hover {
        opacity: 0.9;
    }

    .sig-btn:active {
        transform: translateY(1px);
    }

    .sig-btn-clear {
        color: #555;
        background: #f1f1f1;
    }

    .sig-btn-apply {
        color: #fff;
        background: #007bff;
    }

    .sig-btn-apply:disabled {
        background: #aaa;
        cursor: not-allowed;
    }

    .sig-btn-cancel {
        color: #888;
        background: #fff;
        border: 1px solid #ddd;
    }

    .sig-btn-whatsapp {
        color: #fff;
        background: #25d366;
    }

    .sig-btn-email {
        color: #fff;
        background: #007bff;
    }

    /*
     * Signature placement controls
     */
    .sig-placeholder-btn {
        position: absolute;
        z-index: 30;

        padding: 4px 10px;

        color: #fff;
        font-size: 8pt;
        font-weight: 700;

        background: #007bff;
        border: 0;
        border-radius: 4px;

        box-shadow:
            0 2px 6px rgba(0, 0, 0, 0.25);

        cursor: pointer;
    }

    .sig-placeholder-btn:hover {
        background: #0056b3;
    }

    .sig-placed-remove {
        position: absolute;
        top: -8px;
        right: -8px;
        z-index: 31;

        width: 18px;
        height: 18px;
        padding: 0;

        color: #fff;
        font-size: 11px;
        line-height: 18px;
        text-align: center;

        background: #dc3545;
        border: 0;
        border-radius: 50%;

        cursor: pointer;
    }

    /*
     * Recipient information
     */
    .sig-recipient-info {
        margin-bottom: 14px;
        padding: 10px 12px;

        color: #444;
        font-family: Arial, sans-serif;
        font-size: 13px;
        line-height: 1.6;

        background: #f7f8fa;
        border-radius: 8px;
    }



    @media (max-width: 575px) {
        .sig-modal-overlay {
            padding: 12px;
        }

        .sig-modal,
        #sendModalOverlay .sig-modal {
            width: 100%;
            max-width: 100%;
            padding: 20px 16px;
        }

        .sig-actions {
            flex-wrap: wrap;
        }
    }

    @media print {

        .sig-modal-overlay,
        .sig-placeholder-btn,
        .sig-placed-remove,
        .no-print {
            display: none !important;
        }
    }
</style>
