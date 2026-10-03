@extends('layouts.app')

@section('title', 'Import / Export')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nepali-bs-date-picker/dist/nepali-date-picker.min.css">
<style>
    .ie-page {
        --ie-accent: #0aa1aa;
        --ie-accent-dark: #08848c;
        --ie-fail: #c45c26;
        --ie-fail-soft: #fdf4ef;
        --ie-gen: #0aa1aa;
        --ie-gen-soft: #eef9fa;
        --ie-border: #e6ecee;
        --ie-muted: #6b7c82;
        max-width: 100%;
        overflow-x: clip;
    }

    .ie-page .page-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 1.25rem;
    }

    .ie-page .page-head h4 {
        margin: 0;
        font-weight: 600;
    }

    .ie-page .page-head p {
        margin: 4px 0 0;
        color: var(--ie-muted);
        font-size: 0.92rem;
    }

    .ie-tabs {
        display: flex;
        gap: 8px;
        border-bottom: 1px solid var(--ie-border);
        margin-bottom: 1.25rem;
    }

    .ie-tabs .nav-link {
        border: none;
        background: transparent;
        color: var(--ie-muted);
        font-weight: 600;
        padding: 0.7rem 1rem;
        border-bottom: 3px solid transparent;
        border-radius: 0;
    }

    .ie-tabs .nav-link.active {
        color: var(--ie-accent-dark);
        border-bottom-color: var(--ie-accent);
        background: transparent;
    }

    .ie-tabs .nav-link .badge {
        font-weight: 500;
        font-size: 0.7rem;
        vertical-align: middle;
        margin-left: 6px;
    }

    .ie-panel {
        border: 1px solid var(--ie-border);
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .ie-panel > .ie-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        max-width: 100%;
    }

    .ie-table-wrap {
        padding: 0 1rem 1rem;
    }

    .ie-table-wrap .table {
        margin-bottom: 0;
        width: 100%;
        min-width: 720px;
        table-layout: fixed;
    }

    .ie-table-wrap thead th {
        white-space: nowrap;
        background: #f5f8f9;
        font-size: 0.78rem;
        color: #3a4a50;
        border-bottom-width: 1px;
        padding: 0.55rem 0.45rem;
        font-weight: 600;
    }

    .ie-table-wrap tbody td {
        font-size: 0.84rem;
        vertical-align: middle;
        padding: 0.5rem 0.45rem;
    }

    .ie-fail-table th:nth-child(1),
    .ie-fail-table td:nth-child(1) { width: 44px; text-align: center; }
    .ie-fail-table th:nth-child(2),
    .ie-fail-table td:nth-child(2) { width: 72px; }
    .ie-fail-table th:nth-child(3),
    .ie-fail-table td:nth-child(3) { width: 100px; }
    .ie-fail-table th:nth-child(4),
    .ie-fail-table td:nth-child(4),
    .ie-fail-table th:nth-child(5),
    .ie-fail-table td:nth-child(5),
    .ie-fail-table th:nth-child(6),
    .ie-fail-table td:nth-child(6),
    .ie-fail-table th:nth-child(7),
    .ie-fail-table td:nth-child(7) {
        width: 78px;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    .ie-fail-table th:nth-child(8),
    .ie-fail-table td:nth-child(8) { width: auto; }
    .ie-fail-table th:nth-child(9),
    .ie-fail-table td:nth-child(9) {
        width: 72px;
        text-align: center;
        white-space: nowrap;
    }

    .ie-fail-table .ie-reason {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 100%;
    }

    .ie-fail-table .ie-reason:hover {
        white-space: normal;
        overflow: visible;
    }

    .ie-fail-table .btn-ie-del {
        padding: 0.2rem 0.45rem;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    .ie-status {
        display: inline-block;
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .ie-day-total td {
        font-size: 0.8rem;
        padding: 0.45rem;
    }

    .ie-day-total .ie-day-total-meta {
        font-weight: 500;
        color: #7a5a45;
        white-space: nowrap;
    }

    .ie-th {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .ie-info-btn {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        border: 1px solid #6b7c82;
        background: #fff;
        color: #6b7c82;
        opacity: 0.55;
        font-size: 0.58rem;
        font-weight: 700;
        line-height: 1;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
    }

    .ie-info-btn:hover,
    .ie-info-btn:focus {
        opacity: 1;
        outline: none;
        color: var(--ie-accent);
        border-color: var(--ie-accent);
        background: #fff;
    }

    @media (max-width: 1200px) {
        .ie-table-wrap .table {
            min-width: 860px;
        }
    }

    .ie-panel-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--ie-border);
    }

    .ie-panel-head.failure {
        background: linear-gradient(90deg, var(--ie-fail-soft), #fff 55%);
    }

    .ie-panel-head.generation {
        background: linear-gradient(90deg, var(--ie-gen-soft), #fff 55%);
    }

    .ie-panel-head h5 {
        margin: 0;
        font-weight: 600;
    }

    .ie-panel-head span.hint {
        display: block;
        color: var(--ie-muted);
        font-size: 0.85rem;
        margin-top: 2px;
    }

    .ie-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ie-dropzone {
        margin: 1.25rem;
        border: 2px dashed #c9d6da;
        border-radius: 12px;
        padding: 1.5rem 1.25rem;
        text-align: center;
        background: #fafcfc;
        transition: border-color 0.2s ease, background 0.2s ease;
        cursor: pointer;
    }

    .ie-dropzone:hover,
    .ie-dropzone.is-dragover {
        border-color: var(--ie-accent);
        background: var(--ie-gen-soft);
    }

    .ie-dropzone.failure:hover,
    .ie-dropzone.failure.is-dragover {
        border-color: var(--ie-fail);
        background: var(--ie-fail-soft);
    }

    .ie-dropzone i {
        font-size: 1.75rem;
        color: var(--ie-accent);
        margin-bottom: 0.5rem;
    }

    .ie-dropzone.failure i {
        color: var(--ie-fail);
    }

    .ie-dropzone strong {
        display: block;
        margin-bottom: 0.25rem;
    }

    .ie-dropzone small {
        color: var(--ie-muted);
    }

    .ie-dropzone .file-name {
        display: none;
        margin-top: 0.75rem;
        font-weight: 600;
        color: #1f2d32;
    }

    .ie-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: end;
        padding: 0 1.25rem 1rem;
    }

    .ie-toolbar .form-label {
        font-size: 0.8rem;
        color: var(--ie-muted);
        margin-bottom: 4px;
    }

    .ie-status.solved {
        background: #e7f7ee;
        color: #1b7a45;
    }

    .ie-status.unsolved {
        background: #fdeceb;
        color: #b42318;
    }

    .ie-status.online {
        background: #e7f7ee;
        color: #1b7a45;
    }

    .ie-status.offline {
        background: #fff4e5;
        color: #b54708;
    }

    .ie-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--ie-muted);
    }

    .ie-day-total {
        background: #f7f1ec;
        font-weight: 600;
    }

    .ie-day-total td {
        border-top: 2px solid #e0cfc3;
        color: #5c3a22;
    }

    .ie-day-total .ie-day-total-label {
        letter-spacing: 0.02em;
        text-transform: uppercase;
        font-size: 0.78rem;
    }

    .ie-note {
        margin: 0 1.25rem 1.25rem;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        background: #f7fafb;
        border: 1px solid var(--ie-border);
        color: var(--ie-muted);
        font-size: 0.85rem;
    }

    .btn-ie-primary {
        background: var(--ie-accent);
        border-color: var(--ie-accent);
        color: #fff;
        border-radius: 8px;
    }

    .btn-ie-primary:hover {
        background: var(--ie-accent-dark);
        border-color: var(--ie-accent-dark);
        color: #fff;
    }

    .btn-ie-fail {
        background: var(--ie-fail);
        border-color: var(--ie-fail);
        color: #fff;
        border-radius: 8px;
    }

    .btn-ie-fail:hover {
        background: #a84b1d;
        border-color: #a84b1d;
        color: #fff;
    }

    .btn-ie-outline {
        border-radius: 8px;
    }

    .ie-history {
        margin: 0 1.25rem 1.25rem;
        border: 1px solid var(--ie-border);
        border-radius: 10px;
        overflow: hidden;
    }

    .ie-history-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0.85rem 1rem;
        background: #f8fafa;
        border-bottom: 1px solid var(--ie-border);
    }

    .ie-history-head h6 {
        margin: 0;
        font-weight: 600;
    }

    .ie-history .table {
        margin-bottom: 0;
    }

    .ie-badge-active {
        background: #e7f7ee;
        color: #1b7a45;
        border-radius: 999px;
        padding: 0.2rem 0.55rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .ie-badge-undone {
        background: #fdeceb;
        color: #b42318;
        border-radius: 999px;
        padding: 0.2rem 0.55rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .ie-import-overlay {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 28, 0.55);
        backdrop-filter: blur(2px);
        padding: 1rem;
    }

    .ie-import-overlay.is-open {
        display: flex;
    }

    .ie-import-modal {
        width: min(420px, 100%);
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 18px 50px rgba(0, 0, 0, 0.22);
        padding: 1.75rem 1.5rem 1.5rem;
        text-align: center;
    }

    .ie-import-spinner {
        width: 52px;
        height: 52px;
        margin: 0 auto 1rem;
        border: 4px solid #f1e5de;
        border-top-color: var(--ie-fail);
        border-radius: 50%;
        animation: ie-spin 0.8s linear infinite;
    }

    @keyframes ie-spin {
        to { transform: rotate(360deg); }
    }

    .ie-import-modal h5 {
        margin: 0 0 0.35rem;
        font-weight: 700;
        color: #243036;
    }

    .ie-import-modal p {
        margin: 0;
        color: var(--ie-muted);
        font-size: 0.92rem;
    }

    .ie-import-timer {
        margin-top: 1rem;
        padding: 0.65rem 0.85rem;
        border-radius: 10px;
        background: #f8fafa;
        border: 1px solid var(--ie-border);
        font-size: 0.9rem;
        color: #243036;
    }

    .ie-import-timer strong {
        font-variant-numeric: tabular-nums;
        color: var(--ie-fail);
        font-size: 1.05rem;
    }

    .ie-import-filename {
        margin-top: 0.75rem;
        font-size: 0.82rem;
        color: var(--ie-muted);
        word-break: break-all;
    }

    .ie-page .nepali-datepicker {
        background: #fff;
        cursor: pointer;
    }

    .ie-gen-table .table,
    .ie-fail-table {
        /* keep */
    }

    .ie-gen-table {
        min-width: 980px !important;
    }

    .ie-gen-table th,
    .ie-gen-table td {
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    .ie-gen-table th:nth-child(1),
    .ie-gen-table td:nth-child(1) { width: 44px; text-align: center; }

    .ie-gen-table .ie-num {
        text-align: right;
    }

    .ie-summary-strip {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 0 1rem 0.85rem;
    }

    .ie-summary-chip {
        background: #eef9fa;
        border: 1px solid #d5eef0;
        color: #0a6b72;
        border-radius: 999px;
        padding: 0.3rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .ie-day-total.generation {
        background: #eef9fa;
    }

    .ie-day-total.generation td {
        border-top-color: #c5e4e7;
        color: #0a5c62;
    }

    .ie-import-overlay.generation .ie-import-spinner {
        border-top-color: var(--ie-gen);
    }

    .ie-import-overlay.generation .ie-import-timer strong {
        color: var(--ie-gen);
    }

    /* Mobile / tablet card layout — hidden on desktop */
    .ie-mobile-cards { display: none; }
    .ie-desktop-only { display: block; }
    .ie-btn-short { display: none; }
    .ie-tab-short { display: none; }

    @media (max-width: 991.98px) {
        .ie-desktop-only { display: none !important; }
        .ie-mobile-cards { display: block; }

        .ie-page {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            overflow-x: hidden;
        }
        .ie-page .page-head {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 0.85rem;
        }
        .ie-page .page-head h4 { font-size: 1.15rem; }
        .ie-page .page-head p { font-size: 0.8rem; }
        .ie-page .page-head .badge {
            font-size: 0.68rem;
            white-space: normal;
            text-align: left;
            line-height: 1.3;
            max-width: 100%;
        }

        /* Keep Failure / Generation tabs side-by-side like mobile design */
        .ie-tabs {
            display: flex;
            flex-wrap: nowrap;
            gap: 8px;
            border-bottom: none;
            margin-bottom: 0.85rem;
            width: 100%;
        }
        .ie-tabs .nav-item {
            flex: 1 1 0;
            min-width: 0;
            width: 50%;
        }
        .ie-tabs .nav-link {
            width: 100%;
            text-align: center;
            padding: 0.65rem 0.35rem;
            font-size: 0.78rem;
            border: 1px solid var(--ie-border);
            border-radius: 12px;
            background: #fff;
            color: var(--ie-muted);
            border-bottom: 1px solid var(--ie-border);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ie-tabs .nav-link .badge {
            margin-left: 4px;
            font-size: 0.62rem;
            padding: 0.18em 0.45em;
        }
        .ie-tabs .nav-link.active {
            background: var(--ie-accent);
            border-color: var(--ie-accent);
            color: #fff;
            border-bottom-color: var(--ie-accent);
        }
        .ie-tabs .nav-link.active .badge {
            background: rgba(255,255,255,0.28) !important;
            color: #fff !important;
        }

        .ie-panel {
            border-radius: 14px;
            max-width: 100%;
        }
        .ie-panel-head {
            flex-direction: column;
            align-items: stretch;
            padding: 0.85rem 0.85rem 0.65rem;
            gap: 10px;
        }
        .ie-panel-head h5 { font-size: 0.95rem; }
        .ie-panel-head span.hint { font-size: 0.75rem; }
        .ie-actions {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .ie-actions .btn {
            flex: none;
            width: 100%;
            justify-content: center;
            white-space: nowrap;
        }

        .ie-dropzone { margin: 0.85rem; padding: 1rem 0.75rem; }
        .ie-dropzone strong { font-size: 0.88rem; }
        .ie-dropzone small { font-size: 0.7rem; }
        .ie-toolbar {
            padding: 0 0.85rem 0.85rem;
            gap: 8px;
        }
        .ie-toolbar > div {
            flex: 1 1 100%;
            min-width: 0;
        }
        .ie-toolbar .btn,
        .ie-toolbar a.btn {
            flex: 1 1 calc(50% - 4px);
            text-align: center;
        }
        .ie-summary-strip { padding: 0 0.85rem 0.75rem; }
        .ie-note { margin: 0 0.85rem 1rem; font-size: 0.78rem; }
        .ie-history {
            margin: 0 0.85rem 1rem;
            border-radius: 14px;
            overflow: hidden;
        }
        .ie-history .table-responsive { display: none; }
        .ie-history-head { padding: 0.75rem 0.85rem; }

        .ie-m-day {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 0.85rem 0.85rem 0.45rem;
        }
        .ie-m-day h3 {
            margin: 0;
            font-size: 0.88rem;
            font-weight: 700;
            color: #1f2d32;
        }
        .ie-m-tot {
            font-size: 0.68rem;
            font-weight: 700;
            background: #fdeee6;
            color: #d9622b;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            white-space: nowrap;
        }
        .ie-m-tot.gen {
            background: #eef9fa;
            color: #0aa1aa;
        }
        .ie-m-ev {
            background: #fff;
            border: 1px solid var(--ie-border);
            border-radius: 14px;
            margin: 0 0.85rem 0.55rem;
            padding: 0.75rem;
        }
        .ie-m-times {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
            margin-bottom: 0.55rem;
        }
        .ie-m-times.four { grid-template-columns: repeat(2, 1fr); }
        .ie-m-times div {
            font-weight: 700;
            font-size: 0.8rem;
            color: #1f2d32;
        }
        .ie-m-times small {
            display: block;
            color: var(--ie-muted);
            font-weight: 500;
            font-size: 0.65rem;
            margin-top: 1px;
        }
        .ie-m-why {
            font-size: 0.8rem;
            margin-bottom: 0.65rem;
            overflow-wrap: anywhere;
            color: #243036;
            line-height: 1.4;
        }
        .ie-m-units {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        .ie-m-u {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.3rem 0.65rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .ie-m-u i {
            font-style: normal;
            opacity: 0.8;
            font-weight: 600;
        }
        .ie-m-u.u1 { background: #e2f6ea; color: #1b9a55; }
        .ie-m-u.u2 { background: #fdeee6; color: #d9622b; }
        .ie-m-del {
            margin-left: auto;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #fde8eb;
            color: #d6334a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            padding: 0;
        }
        .ie-m-empty {
            text-align: center;
            color: var(--ie-muted);
            padding: 1.25rem 0.85rem;
            margin: 0 0.85rem 1rem;
            border: 1px dashed var(--ie-border);
            border-radius: 14px;
            background: #fff;
            font-size: 0.82rem;
        }
        .ie-m-hist { padding: 0.75rem 0.85rem 1rem; }
        .ie-m-h {
            background: #fff;
            border: 1px solid var(--ie-border);
            border-radius: 14px;
            padding: 0.85rem;
            margin-bottom: 0.55rem;
        }
        .ie-m-h .nm { font-weight: 700; font-size: 0.82rem; word-break: break-word; }
        .ie-m-h p { color: var(--ie-muted); font-size: 0.72rem; margin: 0.2rem 0 0.55rem; }
        .ie-m-chips { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 0.65rem; }
        .ie-m-c {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 999px;
            background: #eef2f4;
            color: #6b7c82;
        }
        .ie-m-c.g { background: #e2f6ea; color: #12a150; }
        .ie-m-c.o { background: #fdeee6; color: #d9622b; }
        .ie-m-c.r { background: #fde8eb; color: #d6334a; }
        .ie-m-h .row-btns { display: flex; gap: 8px; }
        .ie-m-h .row-btns .btn { flex: 1; }
        .ie-m-pager { padding: 0.5rem 0.85rem 1rem; }
        .ie-m-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 0.55rem;
        }
        .ie-m-meta .box {
            background: #f8fafa;
            border: 1px solid var(--ie-border);
            border-radius: 10px;
            padding: 0.5rem 0.55rem;
        }
        .ie-m-meta .box small { display: block; color: var(--ie-muted); font-size: 0.65rem; }
        .ie-m-meta .box strong { font-size: 0.78rem; font-variant-numeric: tabular-nums; word-break: break-word; }
    }

    /* Extra-tight phones (320px) */
    @media (max-width: 360px) {
        .ie-page {
            padding-left: 0.35rem !important;
            padding-right: 0.35rem !important;
        }
        .ie-tabs .nav-link {
            font-size: 0.72rem;
            padding: 0.55rem 0.25rem;
        }
        .ie-tabs .nav-link .ie-tab-full { display: none; }
        .ie-tabs .nav-link .ie-tab-short { display: inline; }
        .ie-tabs .nav-link .badge {
            display: inline-block;
            margin-left: 2px;
            font-size: 0.58rem;
        }
        .ie-btn-full { display: none; }
        .ie-btn-short { display: inline; }
        .ie-panel-head { padding: 0.75rem; }
        .ie-panel-head h5 { font-size: 0.9rem; }
        .ie-m-ev { margin-left: 0.75rem; margin-right: 0.75rem; }
        .ie-m-day { padding-left: 0.75rem; padding-right: 0.75rem; }
        .ie-dropzone { margin: 0.75rem; }
        .ie-history { margin-left: 0.75rem; margin-right: 0.75rem; }
        .ie-note { margin-left: 0.75rem; margin-right: 0.75rem; }
        .ie-m-times div { font-size: 0.74rem; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid ie-page">
    <div class="page-head">
        <div>
            <h4>Import / Export</h4>
            <p>Manage failure records and generation readings from CSV or Excel files.</p>
        </div>
        <span class="badge bg-secondary">Failure + Generation import live</span>
    </div>

    @if(session('success') && !session('import_result'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error') && !session('import_result'))
        <div class="alert alert-danger">
            <strong>{{ session('error') }}</strong>
            @if(session('import_errors'))
                <ul class="mb-0 mt-2">
                    @foreach(session('import_errors') as $importError)
                        @if($importError)
                            <li>{{ $importError }}</li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Upload validation</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <ul class="nav ie-tabs" id="ieTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ request('tab', 'failure') !== 'generation' ? 'active' : '' }}" id="failure-tab" data-bs-toggle="tab" data-bs-target="#failurePane"
                type="button" role="tab" aria-controls="failurePane" aria-selected="{{ request('tab', 'failure') !== 'generation' ? 'true' : 'false' }}">
                <span class="ie-tab-full">Failure Data</span>
                <span class="ie-tab-short">Failure</span>
                <span class="badge bg-warning text-dark">{{ $failureRows->total() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ request('tab') === 'generation' ? 'active' : '' }}" id="generation-tab" data-bs-toggle="tab" data-bs-target="#generationPane"
                type="button" role="tab" aria-controls="generationPane" aria-selected="{{ request('tab') === 'generation' ? 'true' : 'false' }}">
                <span class="ie-tab-full">Generation Data</span>
                <span class="ie-tab-short">Generation</span>
                <span class="badge bg-info text-dark">{{ $generationRows->total() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="ieTabContent">
        {{-- Failure Data --}}
        <div class="tab-pane fade {{ request('tab', 'failure') !== 'generation' ? 'show active' : '' }}" id="failurePane" role="tabpanel" aria-labelledby="failure-tab">
            <div class="ie-panel">
                <div class="ie-panel-head failure">
                    <div>
                        <h5>Grid Failure &amp; Force Outage</h5>
                        <span class="hint">Import your GRID FAIL Excel (Unit 1 left · Unit 2 right)</span>
                    </div>
                    <div class="ie-actions">
                        <a href="{{ route('admin.failure_template') }}" class="btn btn-sm btn-outline-secondary btn-ie-outline">
                            <i class="fa fa-download me-1"></i>
                            <span class="ie-btn-full">Download Template</span>
                            <span class="ie-btn-short">Template</span>
                        </a>
                        <a href="{{ route('admin.export_failure', request()->only(['failure_unit', 'failure_start', 'failure_end'])) }}"
                           class="btn btn-sm btn-ie-fail">
                            <i class="fa fa-file-export me-1"></i>
                            <span class="ie-btn-full">Export Failures</span>
                            <span class="ie-btn-short">Export</span>
                        </a>
                    </div>
                </div>

                <form id="failureImportForm" action="{{ route('admin.import_failure') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="ie-dropzone failure" id="failureDropzone" data-target="failureFileInput">
                        <i class="fa fa-cloud-arrow-up d-block"></i>
                        <strong>Drop GRID FAIL Excel here, or click to browse</strong>
                        <small>Accepted: .xlsx / .xls · DATE · FROM · TO · SYNCH · DURATION · REASON/REMARKS</small>
                        <div class="file-name" id="failureFileName"></div>
                        <input type="file" name="import_file" id="failureFileInput" class="d-none" accept=".csv,.xlsx,.xls" required>
                    </div>

                    <div class="ie-toolbar">
                        <button type="submit" class="btn btn-sm btn-ie-fail" id="failureImportBtn" disabled>
                            <i class="fa fa-upload me-1"></i> Import File
                        </button>
                    </div>
                </form>

                <form action="{{ route('admin.import_export') }}" method="GET" class="ie-toolbar">
                    <input type="hidden" name="tab" value="failure">
                    <div>
                        <label class="form-label" for="failureUnit">Unit</label>
                        <select name="failure_unit" id="failureUnit" class="form-select form-select-sm">
                            <option value="">All units</option>
                            <option value="1" @selected(request('failure_unit') == '1')>Unit 1</option>
                            <option value="2" @selected(request('failure_unit') == '2')>Unit 2</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="failureStart">Start date (BS)</label>
                        <input type="text" name="failure_start" id="failureStart" class="form-control form-control-sm nepali-datepicker"
                               placeholder="2083-05-01" value="{{ request('failure_start') }}" autocomplete="off" readonly>
                    </div>
                    <div>
                        <label class="form-label" for="failureEnd">End date (BS)</label>
                        <input type="text" name="failure_end" id="failureEnd" class="form-control form-control-sm nepali-datepicker"
                               placeholder="2083-05-31" value="{{ request('failure_end') }}" autocomplete="off" readonly>
                    </div>
                    <button type="submit" class="btn btn-sm btn-ie-fail">Filter</button>
                    <a href="{{ route('admin.import_export', ['tab' => 'failure']) }}" class="btn btn-sm btn-outline-secondary btn-ie-outline">Reset</a>
                </form>

                <div class="ie-table-wrap table-responsive ie-desktop-only">
                    <table class="table table-hover table-bordered align-middle ie-fail-table">
                        <thead>
                            <tr>
                                <th>
                                    <span class="ie-th">#
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Row serial number on this page.">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">Unit
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Generation unit affected: Unit 1 or Unit 2.">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">Date
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Nepali (Bikram Sambat) event date from the GRID FAIL sheet (YYYY-MM-DD).">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">From
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="FROM HRS — time when the unit tripped or was stopped.">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">To
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="TO HRS — intermediate / recovery checkpoint time from the sheet (may be blank).">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">Synch
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="SYNCH HRS — time when the unit was synchronized back to the grid.">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">Dur.
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="DURATION HRS — outage length for this event (usually Synch − From), shown as H:MM:SS.">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">Reason / Remarks
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Operator remark explaining why the unit tripped or was stopped. Hover a cell to see the full text.">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Delete this single failure record. Import History Undo removes a whole batch instead.">i</button>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $durationHelper = app(\App\Services\GridFailureReportService::class);
                                $failureSerial = $failureRows->firstItem() ?? 1;
                                $failureDayGroups = $failureRows->getCollection()->groupBy('date');
                            @endphp
                            @forelse($failureDayGroups as $dayDate => $dayRows)
                                @foreach($dayRows as $row)
                                    <tr>
                                        <td>{{ $failureSerial++ }}</td>
                                        <td>
                                            <span class="ie-status {{ (int) $row->unit === 1 ? 'online' : 'offline' }}">Unit {{ $row->unit }}</span>
                                        </td>
                                        <td>{{ $row->date }}</td>
                                        <td>{{ $row->from_hrs ?: '—' }}</td>
                                        <td>{{ $row->to_hrs ?: '—' }}</td>
                                        <td>{{ $row->synch_hrs ?: '—' }}</td>
                                        <td>{{ $row->duration_hrs ?: '—' }}</td>
                                        <td>
                                            <span class="ie-reason" title="{{ $row->reason ?: '' }}">{{ $row->reason ?: '—' }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.grid_failure.destroy', $row) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this failure record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-ie-del" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($dayRows->count() >= 2)
                                    @php
                                        $daySeconds = $dayRows->sum(function ($r) use ($durationHelper) {
                                            return $durationHelper->durationToSeconds($r->duration_hrs);
                                        });
                                        $dayTotal = $durationHelper->formatDuration((int) $daySeconds);
                                        $unit1Count = $dayRows->where('unit', 1)->count();
                                        $unit2Count = $dayRows->where('unit', 2)->count();
                                        $unitBits = [];
                                        if ($unit1Count) {
                                            $unitBits[] = "Unit 1: {$unit1Count}";
                                        }
                                        if ($unit2Count) {
                                            $unitBits[] = "Unit 2: {$unit2Count}";
                                        }
                                    @endphp
                                    <tr class="ie-day-total">
                                        <td></td>
                                        <td colspan="2">
                                            <span class="ie-day-total-label">TOTAL</span>
                                            <span class="text-muted fw-normal ms-1">{{ $dayDate }}</span>
                                        </td>
                                        <td colspan="3">
                                            <span class="ie-day-total-meta">
                                                {{ $dayRows->count() }} events
                                                @if(count($unitBits))
                                                    · {{ implode(' · ', $unitBits) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $dayTotal }}</td>
                                        <td class="text-muted fw-normal">Daily total</td>
                                        <td></td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="9" class="ie-empty">No failure records yet. Upload your GRID FAIL Excel to get started.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile / tablet card list --}}
                <div class="ie-mobile-cards">
                    @php
                        $durationHelper = $durationHelper ?? app(\App\Services\GridFailureReportService::class);
                        $failureDayGroups = $failureRows->getCollection()->groupBy('date');
                    @endphp
                    @forelse($failureDayGroups as $dayDate => $dayRows)
                        @php
                            $daySeconds = $dayRows->sum(fn ($r) => $durationHelper->durationToSeconds($r->duration_hrs));
                            $dayTotal = $durationHelper->formatDuration((int) $daySeconds);
                        @endphp
                        <div class="ie-m-day">
                            <h3>{{ $dayDate }}</h3>
                            <span class="ie-m-tot">{{ $dayTotal }} · {{ $dayRows->count() }} {{ $dayRows->count() === 1 ? 'event' : 'events' }}</span>
                        </div>
                        @foreach($dayRows as $row)
                            <div class="ie-m-ev">
                                <div class="ie-m-times">
                                    <div>{{ $row->from_hrs ?: '—' }}<small>From</small></div>
                                    <div>{{ $row->to_hrs ?: '—' }}<small>To</small></div>
                                    <div>{{ $row->synch_hrs ?: '—' }}<small>Synch</small></div>
                                </div>
                                <div class="ie-m-why">{{ $row->reason ?: '—' }}</div>
                                <div class="ie-m-units">
                                    <span class="ie-m-u {{ (int) $row->unit === 1 ? 'u1' : 'u2' }}">
                                        Unit {{ $row->unit }}
                                        <i>{{ $row->duration_hrs ?: '—' }}</i>
                                    </span>
                                    <form action="{{ route('admin.grid_failure.destroy', $row) }}" method="POST" class="d-inline ms-auto"
                                          onsubmit="return confirm('Delete this failure record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ie-m-del" title="Delete" aria-label="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="ie-m-empty">No failure records yet. Upload your GRID FAIL Excel to get started.</div>
                    @endforelse
                </div>

                @if($failureRows->hasPages())
                    <div class="px-3 pb-3 ie-m-pager">
                        {{ $failureRows->links('pagination::bootstrap-5') }}
                    </div>
                @endif

                <div class="ie-history">
                    <div class="ie-history-head">
                        <h6><i class="fa fa-clock-rotate-left me-1"></i> Import History</h6>
                        <small class="text-muted">Undo removes only that import’s rows</small>
                    </div>
                    <div class="table-responsive ie-desktop-only">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File</th>
                                    <th>Imported at</th>
                                    <th>New events saved</th>
                                    <th title="Excel TOTAL / summary rows ignored (not outages)">Excel TOTAL ignored</th>
                                    <th title="Already in database">Already existed</th>
                                    <th>By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($failureImports as $batch)
                                    <tr>
                                        <td>{{ $failureImports->firstItem() + $loop->index }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $batch->original_filename }}</div>
                                            @if($batch->notes)
                                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($batch->notes, 60) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $batch->imported_at?->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ $batch->record_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary" title="Excel TOTAL / summary rows ignored (these are daily totals, not outages)">
                                                {{ $batch->skipped_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark" title="Same event already existed in the database">
                                                {{ $batch->duplicate_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td>{{ $batch->importer->name ?? '—' }}</td>
                                        <td>
                                            @if($batch->isUndone())
                                                <span class="ie-badge-undone">Undone {{ $batch->undone_at?->format('Y-m-d H:i') }}</span>
                                            @else
                                                <span class="ie-badge-active">Active</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            @if($batch->stored_path)
                                                <a href="{{ route('admin.import_batch.download', $batch) }}"
                                                   class="btn btn-sm btn-outline-secondary btn-ie-outline">
                                                    File
                                                </a>
                                            @endif
                                            @if($batch->canUndo())
                                                <form action="{{ route('admin.import_batch.undo', $batch) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Undo this import?\n\nFile: {{ $batch->original_filename }}\nThis will delete {{ $batch->record_count }} record(s) from that import only.');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Undo</button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Undo</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="ie-empty">No imports yet. Upload a GRID FAIL Excel to create history.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="ie-mobile-cards ie-m-hist">
                        @forelse($failureImports as $batch)
                            <div class="ie-m-h">
                                <div class="nm">{{ $batch->original_filename }}</div>
                                <p>Imported {{ $batch->imported_at?->format('Y-m-d H:i') }} · {{ $batch->importer->name ?? '—' }}</p>
                                <div class="ie-m-chips">
                                    <span class="ie-m-c g">{{ $batch->record_count }} saved</span>
                                    <span class="ie-m-c">{{ $batch->skipped_count ?? 0 }} TOTAL ignored</span>
                                    <span class="ie-m-c o">{{ $batch->duplicate_count ?? 0 }} existed</span>
                                    @if($batch->isUndone())
                                        <span class="ie-m-c r">Undone {{ $batch->undone_at?->format('m-d H:i') }}</span>
                                    @else
                                        <span class="ie-m-c g">Active</span>
                                    @endif
                                </div>
                                <div class="row-btns">
                                    @if($batch->stored_path)
                                        <a href="{{ route('admin.import_batch.download', $batch) }}" class="btn btn-sm btn-outline-secondary">File</a>
                                    @endif
                                    @if($batch->canUndo())
                                        <form action="{{ route('admin.import_batch.undo', $batch) }}" method="POST" class="flex-fill"
                                              onsubmit="return confirm('Undo this import?\n\nFile: {{ $batch->original_filename }}\nThis will delete {{ $batch->record_count }} record(s) from that import only.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">Undo</button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" disabled>Undo</button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="ie-m-empty">No imports yet. Upload a GRID FAIL Excel to create history.</div>
                        @endforelse
                    </div>
                    @if($failureImports->hasPages())
                        <div class="p-3">
                            {{ $failureImports->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>

                <div class="ie-note">
                    Each upload is saved as a batch with the original file.
                    <strong>Already existed</strong> means the same outage (unit + date + times + reason) was already in the database.
                    <strong>Excel TOTAL ignored</strong> means summary TOTAL rows from the sheet were ignored — they are daily totals, not outage events.
                    <strong>Undo</strong> removes only that batch’s imported rows — history stays for audit.
                </div>
            </div>
        </div>

        {{-- Generation Data --}}
        <div class="tab-pane fade {{ request('tab') === 'generation' ? 'show active' : '' }}" id="generationPane" role="tabpanel" aria-labelledby="generation-tab">
            <div class="ie-panel">
                <div class="ie-panel-head generation">
                    <div>
                        <h5>24 Hours Generation</h5>
                        <span class="hint">Import monthly generation Excel (AD / BS dates · Main meter · Check meter)</span>
                    </div>
                    <div class="ie-actions">
                        <a href="{{ route('admin.generation_template') }}" class="btn btn-sm btn-outline-secondary btn-ie-outline">
                            <i class="fa fa-download me-1"></i>
                            <span class="ie-btn-full">Download Template</span>
                            <span class="ie-btn-short">Template</span>
                        </a>
                        <a href="{{ route('admin.export_generation', request()->only(['generation_start', 'generation_end'])) }}"
                           class="btn btn-sm btn-ie-primary">
                            <i class="fa fa-file-export me-1"></i>
                            <span class="ie-btn-full">Export Generation</span>
                            <span class="ie-btn-short">Export</span>
                        </a>
                    </div>
                </div>

                <form id="generationImportForm" action="{{ route('admin.import_generation') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="ie-dropzone" id="generationDropzone">
                        <i class="fa fa-cloud-arrow-up d-block"></i>
                        <strong>Drop 24 HOURS GENERATION Excel here, or click to browse</strong>
                        <small>Accepted: .xlsx / .xls · DATE (AD+BS) · MAIN METER · CHECK METER · daily rows from row 6</small>
                        <div class="file-name" id="generationFileName"></div>
                        <input type="file" name="import_file" id="generationFileInput" class="d-none" accept=".csv,.xlsx,.xls" required>
                    </div>

                    <div class="ie-toolbar">
                        <button type="submit" class="btn btn-sm btn-ie-primary" id="generationImportBtn" disabled>
                            <i class="fa fa-upload me-1"></i> Import File
                        </button>
                    </div>
                </form>

                <form action="{{ route('admin.import_export') }}" method="GET" class="ie-toolbar">
                    <input type="hidden" name="tab" value="generation">
                    <div>
                        <label class="form-label" for="generationStart">Start date (BS)</label>
                        <input type="text" name="generation_start" id="generationStart" class="form-control form-control-sm nepali-datepicker"
                               placeholder="2083-05-01" value="{{ request('generation_start') }}" autocomplete="off" readonly>
                    </div>
                    <div>
                        <label class="form-label" for="generationEnd">End date (BS)</label>
                        <input type="text" name="generation_end" id="generationEnd" class="form-control form-control-sm nepali-datepicker"
                               placeholder="2083-05-31" value="{{ request('generation_end') }}" autocomplete="off" readonly>
                    </div>
                    <button type="submit" class="btn btn-sm btn-ie-primary">Filter</button>
                    <a href="{{ route('admin.import_export', ['tab' => 'generation']) }}" class="btn btn-sm btn-outline-secondary btn-ie-outline">Reset</a>
                </form>

                <div class="ie-summary-strip">
                    <span class="ie-summary-chip">Days: {{ number_format($generationTotals['days'] ?? 0) }}</span>
                    <span class="ie-summary-chip">Main gen: {{ number_format($generationTotals['main_kwh'] ?? 0, 0) }} kWh</span>
                    <span class="ie-summary-chip">Check gen: {{ number_format($generationTotals['check_kwh'] ?? 0, 0) }} kWh</span>
                </div>

                <div class="ie-table-wrap table-responsive ie-desktop-only">
                    <table class="table table-hover table-bordered align-middle ie-gen-table">
                        <thead>
                            <tr>
                                <th>
                                    <span class="ie-th">#
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Row serial number on this page.">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">BS Initial
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Nepali (BS) start date of the 24-hour period (12AM).">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">BS Final
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Nepali (BS) end date of the 24-hour period (next day 12AM).">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">AD Initial
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Gregorian (A.D.) start date of the reading day.">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">AD Final
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Gregorian (A.D.) end date of the reading day.">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">Main Init
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Main meter initial reading at 12AM.">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">Main Final
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Main meter final reading at next 12AM.">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">Main kWh
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Main meter generation for the day (Final − Initial).">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">Check Init
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Check meter initial reading (may be blank).">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">Check Final
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Check meter final reading (may be blank).">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">Check kWh
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Check meter generation for the day (Final − Initial).">i</button>
                                    </span>
                                </th>
                                <th>
                                    <span class="ie-th">
                                        <button type="button" class="ie-info-btn" data-bs-toggle="tooltip" title="Delete this single daily reading. Import History Undo removes a whole batch.">i</button>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $genSerial = $generationRows->firstItem() ?? 1;
                                $pageMain = 0.0;
                                $pageCheck = 0.0;
                            @endphp
                            @forelse($generationRows as $row)
                                @php
                                    $pageMain += (float) ($row->main_generation_kwh ?? 0);
                                    $pageCheck += (float) ($row->check_generation_kwh ?? 0);
                                @endphp
                                <tr>
                                    <td>{{ $genSerial++ }}</td>
                                    <td>{{ $row->bs_initial_date }}</td>
                                    <td>{{ $row->bs_final_date ?: '—' }}</td>
                                    <td>{{ $row->ad_initial_date ?: '—' }}</td>
                                    <td>{{ $row->ad_final_date ?: '—' }}</td>
                                    <td class="ie-num">{{ $row->main_initial !== null ? number_format($row->main_initial, 0) : '—' }}</td>
                                    <td class="ie-num">{{ $row->main_final !== null ? number_format($row->main_final, 0) : '—' }}</td>
                                    <td class="ie-num"><strong>{{ $row->main_generation_kwh !== null ? number_format($row->main_generation_kwh, 0) : '—' }}</strong></td>
                                    <td class="ie-num">{{ $row->check_initial !== null ? number_format($row->check_initial, 0) : '—' }}</td>
                                    <td class="ie-num">{{ $row->check_final !== null ? number_format($row->check_final, 0) : '—' }}</td>
                                    <td class="ie-num">{{ $row->check_generation_kwh !== null ? number_format($row->check_generation_kwh, 0) : '—' }}</td>
                                    <td>
                                        <form action="{{ route('admin.generation_reading.destroy', $row) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this generation reading?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-ie-del" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="ie-empty">No generation records yet. Upload your 24 HOURS GENERATION Excel to get started.</td>
                                </tr>
                            @endforelse
                            @if($generationRows->count() > 0)
                                <tr class="ie-day-total generation">
                                    <td></td>
                                    <td colspan="6">
                                        <span class="ie-day-total-label">TOTAL</span>
                                        <span class="text-muted fw-normal ms-1">this page</span>
                                    </td>
                                    <td class="ie-num">{{ number_format($pageMain, 0) }}</td>
                                    <td colspan="2"></td>
                                    <td class="ie-num">{{ number_format($pageCheck, 0) }}</td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="ie-mobile-cards">
                    @forelse($generationRows as $row)
                        <div class="ie-m-day">
                            <h3>{{ $row->bs_initial_date }} → {{ $row->bs_final_date ?: '—' }}</h3>
                            <span class="ie-m-tot gen">{{ $row->main_generation_kwh !== null ? number_format($row->main_generation_kwh, 0) : '0' }} kWh main</span>
                        </div>
                        <div class="ie-m-ev">
                            <div class="ie-m-meta">
                                <div class="box"><small>AD Initial</small><strong>{{ $row->ad_initial_date ?: '—' }}</strong></div>
                                <div class="box"><small>AD Final</small><strong>{{ $row->ad_final_date ?: '—' }}</strong></div>
                                <div class="box"><small>Main Init → Final</small><strong>{{ $row->main_initial !== null ? number_format($row->main_initial, 0) : '—' }} → {{ $row->main_final !== null ? number_format($row->main_final, 0) : '—' }}</strong></div>
                                <div class="box"><small>Main Generation</small><strong>{{ $row->main_generation_kwh !== null ? number_format($row->main_generation_kwh, 0) : '—' }} kWh</strong></div>
                                <div class="box"><small>Check Init → Final</small><strong>{{ $row->check_initial !== null ? number_format($row->check_initial, 0) : '—' }} → {{ $row->check_final !== null ? number_format($row->check_final, 0) : '—' }}</strong></div>
                                <div class="box"><small>Check Generation</small><strong>{{ $row->check_generation_kwh !== null ? number_format($row->check_generation_kwh, 0) : '—' }} kWh</strong></div>
                            </div>
                            <div class="ie-m-units">
                                <form action="{{ route('admin.generation_reading.destroy', $row) }}" method="POST" class="d-inline ms-auto"
                                      onsubmit="return confirm('Delete this generation reading?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ie-m-del" title="Delete" aria-label="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="ie-m-empty">No generation records yet. Upload your 24 HOURS GENERATION Excel to get started.</div>
                    @endforelse
                </div>

                @if($generationRows->hasPages())
                    <div class="px-3 pb-3 ie-m-pager">
                        {{ $generationRows->links('pagination::bootstrap-5') }}
                    </div>
                @endif

                <div class="ie-history">
                    <div class="ie-history-head">
                        <h6><i class="fa fa-clock-rotate-left me-1"></i> Import History</h6>
                        <small class="text-muted">Undo removes only that import’s rows</small>
                    </div>
                    <div class="table-responsive ie-desktop-only">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File</th>
                                    <th>Imported at</th>
                                    <th>New days saved</th>
                                    <th title="Excel TOTAL rows ignored">Excel TOTAL ignored</th>
                                    <th title="Already in database">Already existed</th>
                                    <th>By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($generationImports as $batch)
                                    <tr>
                                        <td>{{ $generationImports->firstItem() + $loop->index }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $batch->original_filename }}</div>
                                            @if($batch->notes)
                                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($batch->notes, 60) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $batch->imported_at?->format('Y-m-d H:i') }}</td>
                                        <td><span class="badge bg-success">{{ $batch->record_count }}</span></td>
                                        <td>
                                            <span class="badge bg-secondary" title="Excel TOTAL / summary rows ignored">
                                                {{ $batch->skipped_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark" title="Same day reading already existed">
                                                {{ $batch->duplicate_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td>{{ $batch->importer->name ?? '—' }}</td>
                                        <td>
                                            @if($batch->isUndone())
                                                <span class="ie-badge-undone">Undone {{ $batch->undone_at?->format('Y-m-d H:i') }}</span>
                                            @else
                                                <span class="ie-badge-active">Active</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            @if($batch->stored_path)
                                                <a href="{{ route('admin.import_batch.download', $batch) }}"
                                                   class="btn btn-sm btn-outline-secondary btn-ie-outline">File</a>
                                            @endif
                                            @if($batch->canUndo())
                                                <form action="{{ route('admin.import_batch.undo', $batch) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Undo this import?\n\nFile: {{ $batch->original_filename }}\nThis will delete {{ $batch->record_count }} record(s) from that import only.');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Undo</button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Undo</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="ie-empty">No imports yet. Upload a generation Excel to create history.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="ie-mobile-cards ie-m-hist">
                        @forelse($generationImports as $batch)
                            <div class="ie-m-h">
                                <div class="nm">{{ $batch->original_filename }}</div>
                                <p>Imported {{ $batch->imported_at?->format('Y-m-d H:i') }} · {{ $batch->importer->name ?? '—' }}</p>
                                <div class="ie-m-chips">
                                    <span class="ie-m-c g">{{ $batch->record_count }} saved</span>
                                    <span class="ie-m-c">{{ $batch->skipped_count ?? 0 }} TOTAL ignored</span>
                                    <span class="ie-m-c o">{{ $batch->duplicate_count ?? 0 }} existed</span>
                                    @if($batch->isUndone())
                                        <span class="ie-m-c r">Undone {{ $batch->undone_at?->format('m-d H:i') }}</span>
                                    @else
                                        <span class="ie-m-c g">Active</span>
                                    @endif
                                </div>
                                <div class="row-btns">
                                    @if($batch->stored_path)
                                        <a href="{{ route('admin.import_batch.download', $batch) }}" class="btn btn-sm btn-outline-secondary">File</a>
                                    @endif
                                    @if($batch->canUndo())
                                        <form action="{{ route('admin.import_batch.undo', $batch) }}" method="POST" class="flex-fill"
                                              onsubmit="return confirm('Undo this import?\n\nFile: {{ $batch->original_filename }}\nThis will delete {{ $batch->record_count }} record(s) from that import only.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">Undo</button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" disabled>Undo</button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="ie-m-empty">No imports yet. Upload a generation Excel to create history.</div>
                        @endforelse
                    </div>
                    @if($generationImports->hasPages())
                        <div class="p-3">
                            {{ $generationImports->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>

                <div class="ie-note">
                    Each upload is saved as a batch with the original file.
                    <strong>Already existed</strong> means the same day readings (BS dates + meter values) were already in the database.
                    <strong>Excel TOTAL ignored</strong> means the sheet’s TOTAL GENERATION row was skipped.
                    <strong>Undo</strong> removes only that batch’s imported rows — history stays for audit.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Import loading modal (shared) --}}
<div class="ie-import-overlay" id="importOverlay" aria-hidden="true">
    <div class="ie-import-modal" role="dialog" aria-modal="true" aria-labelledby="importModalTitle">
        <div class="ie-import-spinner" aria-hidden="true"></div>
        <h5 id="importModalTitle">Importing data…</h5>
        <p id="importModalSubtitle">Please wait while we read and save your Excel file.</p>
        <div class="ie-import-timer">
            Elapsed time: <strong id="importElapsed">0.0</strong> s
        </div>
        <div class="ie-import-filename" id="importModalFile"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/nepali-bs-date-picker/dist/nepali-date-picker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function () {
        document.querySelectorAll('.ie-info-btn').forEach(function (el) {
            if (window.bootstrap && bootstrap.Tooltip) {
                new bootstrap.Tooltip(el);
            } else if (window.jQuery && jQuery.fn.tooltip) {
                jQuery(el).tooltip();
            }
        });

        if (window.NepaliDatePicker && typeof NepaliDatePicker.attach === 'function') {
            ['#failureStart', '#failureEnd', '#generationStart', '#generationEnd'].forEach(function (selector) {
                var el = document.querySelector(selector);
                if (!el) return;
                NepaliDatePicker.attach(selector, {
                    language: 'en',
                    onChange: function (date) {
                        if (date && typeof date.format === 'function') {
                            el.value = date.format('YYYY-MM-DD');
                        }
                    },
                });
            });
        }

        var tabButtons = document.querySelectorAll('#ieTabs [data-bs-toggle="tab"]');
        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var targetId = btn.getAttribute('data-bs-target');
                document.querySelectorAll('#ieTabs .nav-link').forEach(function (el) {
                    el.classList.remove('active');
                    el.setAttribute('aria-selected', 'false');
                });
                document.querySelectorAll('#ieTabContent .tab-pane').forEach(function (pane) {
                    pane.classList.remove('show', 'active');
                });
                btn.classList.add('active');
                btn.setAttribute('aria-selected', 'true');
                var pane = document.querySelector(targetId);
                if (pane) {
                    pane.classList.add('show', 'active');
                }
            });
        });

        var overlay = document.getElementById('importOverlay');
        var elapsedEl = document.getElementById('importElapsed');
        var modalFileEl = document.getElementById('importModalFile');
        var modalTitleEl = document.getElementById('importModalTitle');
        var modalSubtitleEl = document.getElementById('importModalSubtitle');
        var timerId = null;
        var startedAt = 0;

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = text == null ? '' : String(text);
            return div.innerHTML;
        }

        function detailsHtml(details) {
            if (!details || !details.length) return '';
            var items = details.map(function (line) {
                return '<li style="text-align:left;margin:0.25rem 0;">' + escapeHtml(line) + '</li>';
            }).join('');
            return '<ul style="margin:0.75rem 0 0;padding-left:1.15rem;font-size:0.92rem;color:#445055;">' + items + '</ul>';
        }

        function showImportAlert(payload) {
            var ok = !!(payload && payload.success);
            var title = (payload && payload.title) || (ok ? 'Import Successful' : 'Import Failed');
            var message = (payload && payload.message) || (ok ? 'Import completed.' : 'Import could not be completed.');
            var html = '<p style="margin:0;color:#445055;">' + escapeHtml(message) + '</p>' + detailsHtml(payload && payload.details);
            var isSoftFail = !ok && (
                title === 'Nothing New to Import' ||
                title === 'Invalid File' ||
                title === 'No File Selected'
            );

            return Swal.fire({
                icon: ok ? 'success' : (isSoftFail ? 'warning' : 'error'),
                title: title,
                html: html,
                confirmButtonText: ok ? 'OK, refresh list' : 'Close',
                confirmButtonColor: ok ? '#1b7a45' : '#c45c26',
                allowOutsideClick: false,
            });
        }

        function showImportModal(name, opts) {
            if (!overlay) return;
            opts = opts || {};
            startedAt = performance.now();
            if (elapsedEl) elapsedEl.textContent = '0.0';
            if (modalFileEl) modalFileEl.textContent = name ? ('File: ' + name) : '';
            if (modalTitleEl) modalTitleEl.textContent = opts.title || 'Importing data…';
            if (modalSubtitleEl) modalSubtitleEl.textContent = opts.subtitle || 'Please wait while we read and save your Excel file.';
            overlay.classList.toggle('generation', !!opts.generation);
            overlay.classList.add('is-open');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            if (timerId) clearInterval(timerId);
            timerId = setInterval(function () {
                var secs = ((performance.now() - startedAt) / 1000).toFixed(1);
                if (elapsedEl) elapsedEl.textContent = secs;
            }, 100);
        }

        function hideImportModal() {
            if (!overlay) return;
            overlay.classList.remove('is-open', 'generation');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            if (timerId) {
                clearInterval(timerId);
                timerId = null;
            }
        }

        @if(session('import_result'))
        showImportAlert(@json(session('import_result'))).then(function () {
            @if(session('import_result.success'))
            window.location.reload();
            @endif
        });
        @endif

        function bindDropzone(dropzoneId, fileInputId, fileNameId, importBtnId) {
            var dropzone = document.getElementById(dropzoneId);
            var fileInput = document.getElementById(fileInputId);
            var fileName = document.getElementById(fileNameId);
            var importBtn = document.getElementById(importBtnId);
            if (!dropzone || !fileInput) {
                return { fileInput: fileInput, importBtn: importBtn, defaultHtml: importBtn ? importBtn.innerHTML : '' };
            }

            function setFileLabel(name) {
                if (!fileName) return;
                fileName.style.display = 'block';
                fileName.textContent = name;
                if (importBtn) importBtn.disabled = false;
            }

            dropzone.addEventListener('click', function () { fileInput.click(); });
            fileInput.addEventListener('change', function () {
                if (fileInput.files && fileInput.files[0]) setFileLabel(fileInput.files[0].name);
            });
            ['dragenter', 'dragover'].forEach(function (evt) {
                dropzone.addEventListener(evt, function (e) {
                    e.preventDefault();
                    dropzone.classList.add('is-dragover');
                });
            });
            ['dragleave', 'drop'].forEach(function (evt) {
                dropzone.addEventListener(evt, function (e) {
                    e.preventDefault();
                    dropzone.classList.remove('is-dragover');
                });
            });
            dropzone.addEventListener('drop', function (e) {
                var files = e.dataTransfer.files;
                if (!files || !files.length) return;
                fileInput.files = files;
                setFileLabel(files[0].name);
            });

            return { fileInput: fileInput, importBtn: importBtn, defaultHtml: importBtn ? importBtn.innerHTML : '' };
        }

        function bindImportForm(config) {
            var form = document.getElementById(config.formId);
            if (!form) return;
            var bound = bindDropzone(config.dropzoneId, config.fileInputId, config.fileNameId, config.importBtnId);
            var fileInput = bound.fileInput;
            var importBtn = bound.importBtn;
            var defaultHtml = bound.defaultHtml || (importBtn ? importBtn.innerHTML : '');

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No File Selected',
                        text: config.noFileText,
                        confirmButtonColor: config.accent || '#c45c26',
                    });
                    return;
                }

                var selectedName = fileInput.files[0].name;
                var data = new FormData(form);

                importBtn.disabled = true;
                importBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Importing...';
                showImportModal(selectedName, {
                    title: config.modalTitle,
                    subtitle: config.modalSubtitle,
                    generation: !!config.generation,
                });

                fetch(form.action, {
                    method: 'POST',
                    body: data,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    credentials: 'same-origin',
                })
                .then(function (response) {
                    return response.json().then(function (json) {
                        return { ok: response.ok, json: json };
                    }).catch(function () {
                        return {
                            ok: false,
                            json: {
                                success: false,
                                title: 'Import Failed',
                                message: 'The server returned an unexpected response.',
                                details: [config.invalidFileHint],
                            },
                        };
                    });
                })
                .then(function (result) {
                    hideImportModal();
                    var payload = result.json || {};
                    if (typeof payload.success === 'undefined') {
                        payload.success = !!result.ok;
                    }
                    return showImportAlert(payload).then(function () {
                        if (payload.success) {
                            window.location.href = payload.redirect || config.redirect;
                        } else if (importBtn) {
                            importBtn.disabled = !(fileInput.files && fileInput.files[0]);
                            importBtn.innerHTML = defaultHtml;
                        }
                    });
                })
                .catch(function () {
                    hideImportModal();
                    showImportAlert({
                        success: false,
                        title: 'Connection Error',
                        message: 'Could not reach the server. Check your connection and try again.',
                        details: [],
                    }).then(function () {
                        if (importBtn) {
                            importBtn.disabled = !(fileInput.files && fileInput.files[0]);
                            importBtn.innerHTML = defaultHtml;
                        }
                    });
                });
            });
        }

        bindImportForm({
            formId: 'failureImportForm',
            dropzoneId: 'failureDropzone',
            fileInputId: 'failureFileInput',
            fileNameId: 'failureFileName',
            importBtnId: 'failureImportBtn',
            noFileText: 'Please choose a GRID FAIL Excel file first.',
            modalTitle: 'Importing failure data…',
            modalSubtitle: 'Please wait while we read and save your Excel file.',
            invalidFileHint: 'Please try again with a valid GRID FAIL Excel file.',
            redirect: '{{ route('admin.import_export', ['tab' => 'failure']) }}',
            accent: '#c45c26',
            generation: false,
        });

        bindImportForm({
            formId: 'generationImportForm',
            dropzoneId: 'generationDropzone',
            fileInputId: 'generationFileInput',
            fileNameId: 'generationFileName',
            importBtnId: 'generationImportBtn',
            noFileText: 'Please choose a 24 HOURS GENERATION Excel file first.',
            modalTitle: 'Importing generation data…',
            modalSubtitle: 'Please wait while we read and save your Excel file.',
            invalidFileHint: 'Please try again with a valid 24 HOURS GENERATION Excel file.',
            redirect: '{{ route('admin.import_export', ['tab' => 'generation']) }}',
            accent: '#0aa1aa',
            generation: true,
        });
    })();
</script>
@endsection
