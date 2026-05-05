<style>
    .transaction-shell {
        padding: 24px 0 54px;
        background:
            radial-gradient(circle at top left, rgba(var(--secondary-color-rgb), 0.18), transparent 34%),
            linear-gradient(180deg, rgba(var(--primary-color-rgb), 0.06), rgba(255, 255, 255, 0) 320px);
    }
    .transaction-stack,
    .transaction-summary-grid,
    .transaction-ledger-mobile,
    .transaction-note-list,
    .transaction-step-list {
        display: grid;
        gap: 24px;
    }
    .transaction-overview,
    .transaction-form-grid {
        display: grid;
        gap: 24px;
        grid-template-columns: minmax(0, 1.02fr) minmax(0, 0.98fr);
    }
    .transaction-panel,
    .transaction-surface,
    .transaction-summary-card,
    .transaction-ledger-wrap,
    .transaction-ledger-mobile-card,
    .transaction-empty {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 24px 52px rgba(15, 23, 42, 0.08);
    }
    .transaction-panel::before,
    .transaction-surface::before,
    .transaction-summary-card::before,
    .transaction-ledger-wrap::before,
    .transaction-ledger-mobile-card::before,
    .transaction-empty::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb), 0.08), rgba(var(--primary-color-rgb), 0.04));
        pointer-events: none;
    }
    .transaction-panel > *,
    .transaction-surface > *,
    .transaction-summary-card > *,
    .transaction-ledger-wrap > *,
    .transaction-ledger-mobile-card > *,
    .transaction-empty > * {
        position: relative;
        z-index: 1;
    }
    .transaction-panel,
    .transaction-empty {
        padding: 28px;
    }
    .transaction-panel--compact {
        padding: 24px;
    }
    .transaction-meta-label {
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
    }
    .transaction-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }
    .transaction-section-title {
        margin: 0;
        font-size: clamp(24px, 3vw, 30px);
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
    }
    .transaction-section-copy,
    .transaction-helper,
    .transaction-subcopy,
    .transaction-ledger-subtext,
    .transaction-mobile-meta span,
    .transaction-note-list li,
    .transaction-step-list li {
        color: #64748b;
    }
    .transaction-section-copy {
        margin: 8px 0 0;
        font-size: 15px;
        line-height: 1.7;
    }
    .transaction-icon {
        width: 54px;
        height: 54px;
        flex: 0 0 54px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #ffffff;
        background: linear-gradient(135deg, #f0a83a, #6f33cc);
        box-shadow: 0 14px 26px rgba(111, 51, 204, 0.2);
    }
    .transaction-action-grid,
    .transaction-summary-grid,
    .transaction-toggle-group,
    .transaction-address-row {
        display: grid;
        gap: 12px;
    }
    .transaction-action-grid,
    .transaction-summary-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .transaction-action-grid {
        margin: 24px 0;
    }
    .transaction-action-btn {
        width: 100%;
        min-height: 50px;
        padding: 12px 16px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 800;
        text-align: center;
    }
    .transaction-summary-card,
    .transaction-surface {
        padding: 18px;
    }
    .transaction-summary-value,
    .transaction-surface-value,
    .transaction-ledger-type,
    .transaction-table-title,
    .transaction-mobile-meta strong {
        color: #0f172a;
        font-weight: 800;
        word-break: break-word;
    }
    .transaction-summary-value {
        font-size: clamp(20px, 3vw, 28px);
        line-height: 1.08;
        margin: 0;
    }
    .transaction-surface-value {
        font-size: 18px;
        line-height: 1.35;
    }
    .transaction-toggle-group {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-bottom: 18px;
    }
    .transaction-toggle-btn {
        min-height: 48px;
        border-radius: 14px;
        border: 1px solid rgba(15, 23, 42, 0.12);
        background: rgba(255, 255, 255, 0.7);
        color: #475569;
        font-size: 14px;
        font-weight: 800;
        transition: 0.2s ease;
    }
    .transaction-toggle-btn.is-active {
        border-color: transparent;
        color: #ffffff;
        background: linear-gradient(135deg, #f0a83a, #6f33cc);
        box-shadow: 0 16px 28px rgba(111, 51, 204, 0.2);
    }
    .transaction-qr-card {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 278px;
    }
    .transaction-qr-card svg,
    .transaction-qr-card img {
        width: min(100%, 220px);
        height: auto;
    }
    .transaction-address-row {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
    }
    .transaction-address-row .form-control,
    .transaction-form .form-control,
    .transaction-form .form-select {
        min-height: 52px;
        border-radius: 16px;
        border-color: rgba(15, 23, 42, 0.12);
        background: rgba(255, 255, 255, 0.88);
        box-shadow: none;
    }
    .transaction-address-row .form-control {
        font-size: 14px;
        font-weight: 700;
    }
    .transaction-form {
        display: grid;
        gap: 18px;
    }
    .transaction-form label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }
    .transaction-form .btn-main {
        min-height: 54px;
        border-radius: 16px;
    }
    .transaction-step-list,
    .transaction-note-list {
        margin: 0;
        padding-left: 18px;
        gap: 10px;
    }
    .transaction-note-list {
        padding-left: 20px;
    }
    .transaction-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .transaction-status-badge.is-pending {
        background: rgba(245, 158, 11, 0.16);
        color: #b45309;
    }
    .transaction-status-badge.is-approved,
    .transaction-status-badge.is-completed {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
    }
    .transaction-status-badge.is-rejected {
        background: rgba(239, 68, 68, 0.12);
        color: #b91c1c;
    }
    .transaction-status-badge.is-expired,
    .transaction-status-badge.is-default {
        background: rgba(148, 163, 184, 0.16);
        color: #475569;
    }
    .transaction-guide-modal {
        position: fixed;
        inset: 0;
        z-index: 1055;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(2, 6, 23, 0.72);
        backdrop-filter: blur(8px);
    }
    .transaction-guide-modal.is-open {
        display: flex;
    }
    .transaction-guide-dialog {
        position: relative;
        width: min(100%, 680px);
        max-height: min(86vh, 760px);
        overflow-y: auto;
        padding: 28px;
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: #101116;
        box-shadow: 0 28px 70px rgba(0, 0, 0, 0.42);
        color: #f8fafc;
    }
    .transaction-guide-head {
        margin: -28px -28px 20px;
        padding: 26px 78px 20px 28px;
        border-radius: 24px 24px 0 0;
        background: #ffffff;
        color: #0f172a;
    }
    .transaction-guide-head .transaction-meta-label {
        color: #475569;
    }
    .transaction-guide-head .transaction-section-title {
        color: #0f172a;
    }
    .transaction-guide-dialog .transaction-section-title,
    .transaction-guide-dialog .transaction-summary-value {
        color: #f8fafc;
    }
    .transaction-guide-dialog .transaction-guide-head .transaction-section-title {
        color: #0f172a;
    }
    .transaction-guide-dialog .transaction-section-copy,
    .transaction-guide-dialog .transaction-step-list li,
    .transaction-guide-dialog .transaction-note-list li {
        color: #bfdbfe;
    }
    .transaction-guide-close {
        position: absolute;
        top: 18px;
        right: 18px;
        width: 38px;
        height: 38px;
        border: 0;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #7f1d1d;
        background: #fee2e2;
        box-shadow: 0 10px 24px rgba(127, 29, 29, 0.16);
    }
    .transaction-guide-close:hover,
    .transaction-guide-close:focus {
        color: #ffffff;
        background: #7f1d1d;
    }
    .transaction-guide-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 22px;
    }
    .transaction-guide-actions .btn-main,
    .transaction-guide-actions .btn-light {
        min-height: 46px;
        border-radius: 14px;
    }
    .transaction-ledger-wrap {
        padding: 0;
    }
    .transaction-ledger-table {
        margin-bottom: 0;
    }
    .transaction-ledger-table thead th {
        padding: 16px 18px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.3);
    }
    .transaction-ledger-table tbody td {
        padding: 18px;
        vertical-align: middle;
        border-color: rgba(15, 23, 42, 0.08);
    }
    .transaction-ledger-amount {
        font-size: 16px;
        font-weight: 800;
    }
    .transaction-ledger-amount.is-credit {
        color: #059669;
    }
    .transaction-ledger-amount.is-debit {
        color: #dc2626;
    }
    .transaction-ledger-mobile {
        display: none;
        gap: 14px;
    }
    .transaction-ledger-mobile-card {
        padding: 18px;
    }
    .transaction-mobile-top,
    .transaction-mobile-meta {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }
    .transaction-mobile-meta {
        padding-top: 10px;
        margin-top: 10px;
        border-top: 1px solid rgba(15, 23, 42, 0.08);
        font-size: 14px;
    }
    .transaction-empty {
        text-align: center;
        color: #64748b;
        border-style: dashed;
    }
    .dark-scheme .transaction-shell {
        background:
            radial-gradient(circle at top left, rgba(var(--secondary-color-rgb), 0.18), transparent 34%),
            linear-gradient(180deg, rgba(10, 14, 22, 0.8), rgba(10, 14, 22, 0) 360px);
    }
    .dark-scheme .transaction-panel,
    .dark-scheme .transaction-surface,
    .dark-scheme .transaction-summary-card,
    .dark-scheme .transaction-ledger-wrap,
    .dark-scheme .transaction-ledger-mobile-card,
    .dark-scheme .transaction-empty {
        background: rgba(10, 14, 22, 0.86);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 26px 54px rgba(0, 0, 0, 0.34);
    }
    .dark-scheme .transaction-meta-label,
    .dark-scheme .transaction-section-copy,
    .dark-scheme .transaction-helper,
    .dark-scheme .transaction-subcopy,
    .dark-scheme .transaction-ledger-subtext,
    .dark-scheme .transaction-mobile-meta span,
    .dark-scheme .transaction-note-list li,
    .dark-scheme .transaction-step-list li,
    .dark-scheme .transaction-ledger-table thead th {
        color: #94a3b8;
    }
    .dark-scheme .transaction-section-title,
    .dark-scheme .transaction-summary-value,
    .dark-scheme .transaction-surface-value,
    .dark-scheme .transaction-form label,
    .dark-scheme .transaction-ledger-type,
    .dark-scheme .transaction-table-title,
    .dark-scheme .transaction-mobile-meta strong {
        color: #f8fafc;
    }
    .dark-scheme .transaction-toggle-btn {
        color: #cbd5e1;
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.05);
    }
    .dark-scheme .transaction-address-row .form-control,
    .dark-scheme .transaction-form .form-control,
    .dark-scheme .transaction-form .form-select {
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.06);
    }
    .dark-scheme .transaction-ledger-table thead th {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .transaction-ledger-table tbody td,
    .dark-scheme .transaction-mobile-meta {
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .transaction-status-badge.is-pending {
        color: #fbbf24;
        background: rgba(245, 158, 11, 0.18);
    }
    .dark-scheme .transaction-status-badge.is-approved,
    .dark-scheme .transaction-status-badge.is-completed {
        color: #86efac;
        background: rgba(16, 185, 129, 0.16);
    }
    .dark-scheme .transaction-status-badge.is-rejected {
        color: #fca5a5;
        background: rgba(239, 68, 68, 0.18);
    }
    .dark-scheme .transaction-status-badge.is-expired,
    .dark-scheme .transaction-status-badge.is-default {
        color: #cbd5e1;
        background: rgba(148, 163, 184, 0.18);
    }
    .dark-scheme .transaction-guide-head .transaction-meta-label {
        color: #475569;
    }
    .dark-scheme .transaction-guide-head .transaction-section-title {
        color: #0f172a;
    }
    @media (max-width: 991.98px) {
        .transaction-overview,
        .transaction-form-grid {
            grid-template-columns: 1fr;
        }
        .transaction-panel,
        .transaction-empty {
            padding: 24px;
            border-radius: 24px;
        }
    }
    @media (max-width: 767.98px) {
        .transaction-shell {
            padding-bottom: 106px;
        }
        .transaction-action-grid,
        .transaction-summary-grid,
        .transaction-address-row {
            grid-template-columns: 1fr;
        }
        .transaction-section-head {
            flex-direction: column;
        }
        .transaction-ledger-wrap {
            display: none;
        }
        .transaction-ledger-mobile {
            display: grid;
        }
        .transaction-guide-dialog {
            padding: 24px 20px;
        }
        .transaction-guide-head {
            margin: -24px -20px 18px;
            padding: 24px 70px 18px 20px;
        }
    }
    @media (max-width: 575.98px) {
        .transaction-panel,
        .transaction-empty {
            padding: 20px;
            border-radius: 22px;
        }
        .transaction-mobile-top,
        .transaction-mobile-meta {
            flex-direction: column;
        }
        .transaction-guide-modal {
            padding: 14px;
        }
        .transaction-guide-dialog {
            border-radius: 20px;
        }
        .transaction-guide-head {
            border-radius: 20px 20px 0 0;
        }
        .transaction-guide-actions {
            display: grid;
        }
    }
</style>
