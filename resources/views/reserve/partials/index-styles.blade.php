<style>
    .reserve-shell {
        padding: 24px 0 54px;
        background:
            radial-gradient(circle at top left, rgba(var(--secondary-color-rgb), 0.18), transparent 34%),
            linear-gradient(180deg, rgba(var(--primary-color-rgb), 0.06), rgba(255, 255, 255, 0) 320px);
    }
    .reserve-stack,
    .reserve-summary-grid,
    .reserve-history-grid,
    .reserve-action-grid,
    .reserve-balance-grid,
    .reserve-glance-grid,
    .reserve-selector__row,
    .reserve-selector__summary,
    .reserve-selector__glance,
    .reserve-history-mobile,
    .reserve-sales-mobile {
        display: grid;
        gap: 24px;
    }
    .reserve-overview {
        display: grid;
        gap: 24px;
        grid-template-columns: minmax(0, 1.02fr) minmax(0, 0.98fr);
    }
    .reserve-history-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .reserve-panel,
    .reserve-card,
    .reserve-summary-card,
    .reserve-stat-card,
    .reserve-history-wrap,
    .reserve-mobile-card,
    .reserve-empty,
    .reserve-live-card,
    .reserve-pill-card {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 24px 52px rgba(15, 23, 42, 0.08);
    }
    .reserve-panel::before,
    .reserve-card::before,
    .reserve-summary-card::before,
    .reserve-stat-card::before,
    .reserve-history-wrap::before,
    .reserve-mobile-card::before,
    .reserve-empty::before,
    .reserve-live-card::before,
    .reserve-pill-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(var(--secondary-color-rgb), 0.08), rgba(var(--primary-color-rgb), 0.04));
        pointer-events: none;
    }
    .reserve-panel > *,
    .reserve-card > *,
    .reserve-summary-card > *,
    .reserve-stat-card > *,
    .reserve-history-wrap > *,
    .reserve-mobile-card > *,
    .reserve-empty > *,
    .reserve-live-card > *,
    .reserve-pill-card > * {
        position: relative;
        z-index: 1;
    }
    .reserve-panel,
    .reserve-empty {
        padding: 28px;
    }
    .reserve-card,
    .reserve-summary-card,
    .reserve-stat-card,
    .reserve-mobile-card,
    .reserve-live-card,
    .reserve-pill-card {
        padding: 18px;
    }
    .reserve-meta-label {
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
    }
    .reserve-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }
    .reserve-section-head--hero {
        margin-bottom: 24px;
    }
    .reserve-section-title {
        margin: 0;
        font-size: clamp(24px, 3vw, 30px);
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
    }
    .reserve-section-copy,
    .reserve-card-copy,
    .reserve-subcopy,
    .reserve-table-subtext,
    .reserve-mobile-meta span {
        color: #64748b;
    }
    .reserve-section-copy {
        margin: 8px 0 0;
        font-size: 15px;
        line-height: 1.7;
    }
    .reserve-summary-card,
    .reserve-stat-card,
    .reserve-mobile-top,
    .reserve-mobile-meta,
    .reserve-live-card {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }
    .reserve-summary-card__value,
    .reserve-stat-value,
    .reserve-card__value,
    .reserve-live-card__value,
    .reserve-table-title,
    .reserve-mobile-meta strong,
    .reserve-selector__stat strong,
    .reserve-pill-card strong {
        color: #0f172a;
        font-weight: 800;
        word-break: break-word;
    }
    .reserve-summary-card__value,
    .reserve-stat-value {
        margin: 0;
        line-height: 1.08;
        font-size: clamp(22px, 2.4vw, 32px);
    }
    .reserve-card__value,
    .reserve-live-card__value {
        font-size: clamp(20px, 2vw, 26px);
        line-height: 1.12;
    }
    .reserve-summary-card__icon,
    .reserve-icon {
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #ffffff;
        background: linear-gradient(135deg, #f0a83a, #6f33cc);
        box-shadow: 0 14px 26px rgba(111, 51, 204, 0.2);
    }
    .reserve-summary-card__icon.is-alt,
    .reserve-icon.is-alt {
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
    }
    .reserve-icon.is-income {
        background: linear-gradient(135deg, #10b981, #0f766e);
    }
    .reserve-balance-grid,
    .reserve-glance-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .reserve-glance-grid {
        gap: 16px;
    }
    .reserve-action-grid {
        margin: 22px 0;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .reserve-action-btn,
    .reserve-selector__actions .btn-main,
    .reserve-selector__actions .btn-border,
    .reserve-live-card .btn-main {
        width: 100%;
        min-height: 52px;
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
    .reserve-selector {
        display: grid;
        gap: 18px;
    }
    .reserve-selector__row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .reserve-selector__summary {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
    .reserve-selector__glance {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .reserve-selector__field {
        display: grid;
        gap: 8px;
    }
    .reserve-selector__field label,
    .reserve-selector__stat span,
    .reserve-pill-card span {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .reserve-selector__select {
        width: 100%;
        min-height: 52px;
        padding: 0 14px;
        border-radius: 16px;
        border: 1px solid rgba(15, 23, 42, 0.12);
        background: rgba(255, 255, 255, 0.88);
        color: #0f172a;
        font-weight: 700;
        appearance: auto;
        background-image: none !important;
        box-shadow: none;
        color-scheme: light;
    }
    .reserve-selector__select:focus {
        outline: none;
        border-color: rgba(var(--primary-color-rgb), 0.45);
        box-shadow: 0 0 0 4px rgba(var(--primary-color-rgb), 0.12);
    }
    .reserve-selector__stat,
    .reserve-selector__note,
    .reserve-pill-card {
        padding: 14px 16px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.62);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }
    .reserve-selector__stat,
    .reserve-pill-card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        min-height: 108px;
    }
    .reserve-selector__stat strong,
    .reserve-pill-card strong {
        font-size: clamp(18px, 1.8vw, 26px);
        line-height: 1.22;
        overflow-wrap: anywhere;
    }
    .reserve-selector__note {
        color: #334155;
        font-size: 15px;
        line-height: 1.75;
        overflow-wrap: anywhere;
    }
    .reserve-selector__actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .reserve-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 7px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .reserve-status-badge.is-available {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
    }
    .reserve-status-badge.is-progress {
        background: rgba(59, 130, 246, 0.12);
        color: #1d4ed8;
    }
    .reserve-status-badge.is-blocked {
        background: rgba(148, 163, 184, 0.16);
        color: #475569;
    }
    .reserve-history-wrap {
        padding: 0;
        overflow: hidden;
    }
    .reserve-table {
        margin-bottom: 0;
    }
    .reserve-table thead th {
        padding: 16px 18px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(255, 255, 255, 0.3);
    }
    .reserve-table tbody td {
        padding: 18px;
        vertical-align: middle;
        border-color: rgba(15, 23, 42, 0.08);
    }
    .reserve-amount {
        font-size: 16px;
        font-weight: 800;
    }
    .reserve-amount.is-credit {
        color: #059669;
    }
    .reserve-amount.is-debit {
        color: #dc2626;
    }
    .reserve-history-mobile,
    .reserve-sales-mobile {
        display: none;
        gap: 14px;
    }
    .reserve-mobile-meta {
        padding-top: 10px;
        margin-top: 10px;
        border-top: 1px solid rgba(15, 23, 42, 0.08);
        font-size: 14px;
    }
    .reserve-empty {
        text-align: center;
        color: #64748b;
        border-style: dashed;
    }
    .reserve-loader {
        position: fixed;
        inset: 0;
        z-index: 3000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(7, 8, 18, 0.88);
        backdrop-filter: blur(8px);
    }
    .reserve-loader.is-visible {
        display: flex;
    }
    .reserve-loader__card {
        width: min(320px, calc(100vw - 32px));
        padding: 28px 24px;
        border-radius: 24px;
        text-align: center;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 24px 50px rgba(15, 23, 42, 0.18);
    }
    .reserve-loader__logo {
        width: 76px;
        height: 76px;
        object-fit: contain;
        margin-bottom: 18px;
        animation: reserve-loader-pulse 1.2s ease-in-out infinite;
    }
    .reserve-loader__title {
        font-size: 22px;
        font-weight: 800;
        color: #101828;
        margin-bottom: 8px;
    }
    .reserve-loader__copy {
        color: #617086;
        margin-bottom: 18px;
    }
    .reserve-loader__bar {
        position: relative;
        overflow: hidden;
        height: 8px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.08);
    }
    .reserve-loader__bar::after {
        content: "";
        position: absolute;
        inset: 0;
        width: 40%;
        border-radius: inherit;
        background: linear-gradient(135deg, #f0a83a, #6f33cc);
        animation: reserve-loader-slide 1s linear infinite;
    }
    .reserve-modal {
        position: fixed;
        inset: 0;
        z-index: 2900;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(7, 8, 18, 0.82);
        backdrop-filter: blur(8px);
    }
    .reserve-modal.is-visible {
        display: flex;
    }
    .reserve-modal__dialog {
        position: relative;
        width: min(860px, calc(100vw - 32px));
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        padding: 24px;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 24px 50px rgba(15, 23, 42, 0.18);
    }
    .reserve-modal__close {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 42px;
        height: 42px;
        border: 0;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.06);
        border: 1px solid rgba(15, 23, 42, 0.08);
        color: #0f172a;
        font-size: 24px;
        line-height: 1;
    }
    .reserve-modal__head {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        padding-right: 40px;
    }
    .reserve-modal__icon {
        width: 68px;
        height: 68px;
        border-radius: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(240, 168, 58, 0.18), rgba(111, 51, 204, 0.24));
        border: 1px solid rgba(240, 168, 58, 0.22);
        box-shadow: 0 16px 28px rgba(111, 51, 204, 0.18);
    }
    .reserve-modal__icon img {
        width: 42px;
        height: 42px;
        object-fit: contain;
    }
    .reserve-modal__title {
        margin: 0 0 4px;
        color: #0f172a;
        font-size: 28px;
        font-weight: 800;
    }
    .reserve-modal__copy {
        margin: 0;
        color: #64748b;
        line-height: 1.7;
    }
    .reserve-modal__grid {
        display: grid;
        grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
        gap: 18px;
        margin-bottom: 18px;
    }
    .reserve-modal__panel,
    .reserve-modal__nft,
    .reserve-modal__selected {
        padding: 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.58);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }
    .reserve-modal__panel h5 {
        margin-bottom: 12px;
        color: #0f172a;
        font-size: 18px;
        font-weight: 800;
    }
    .reserve-modal__meta {
        display: grid;
        gap: 8px;
        color: #334155;
    }
    .reserve-modal__nft-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    .reserve-modal__nft {
        display: block;
        height: 100%;
        padding: 12px;
    }
    .reserve-modal__nft img {
        width: 100%;
        max-height: 150px;
        object-fit: cover;
        border-radius: 14px;
        margin-top: 10px;
    }
    .reserve-modal__selected {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px;
        margin-bottom: 16px;
    }
    .reserve-modal__selected img {
        width: 92px;
        height: 92px;
        object-fit: cover;
        border-radius: 16px;
        flex: 0 0 92px;
    }
    .reserve-modal__actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 18px;
    }
    .reserve-modal__actions .btn-main,
    .reserve-modal__actions .btn-border {
        min-width: 180px;
        text-align: center;
    }
    body.reserve-modal-open {
        overflow: hidden;
    }
    .dark-scheme .reserve-shell {
        background:
            radial-gradient(circle at top left, rgba(var(--secondary-color-rgb), 0.18), transparent 34%),
            linear-gradient(180deg, rgba(10, 14, 22, 0.8), rgba(10, 14, 22, 0) 360px);
    }
    .dark-scheme .reserve-panel,
    .dark-scheme .reserve-card,
    .dark-scheme .reserve-summary-card,
    .dark-scheme .reserve-stat-card,
    .dark-scheme .reserve-history-wrap,
    .dark-scheme .reserve-mobile-card,
    .dark-scheme .reserve-empty,
    .dark-scheme .reserve-live-card,
    .dark-scheme .reserve-pill-card,
    .dark-scheme .reserve-selector__stat,
    .dark-scheme .reserve-selector__note,
    .dark-scheme .reserve-modal__dialog,
    .dark-scheme .reserve-modal__panel,
    .dark-scheme .reserve-modal__nft,
    .dark-scheme .reserve-modal__selected {
        background: rgba(10, 14, 22, 0.86);
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 26px 54px rgba(0, 0, 0, 0.34);
    }
    .dark-scheme .reserve-meta-label,
    .dark-scheme .reserve-section-copy,
    .dark-scheme .reserve-card-copy,
    .dark-scheme .reserve-subcopy,
    .dark-scheme .reserve-table-subtext,
    .dark-scheme .reserve-mobile-meta span,
    .dark-scheme .reserve-selector__field label,
    .dark-scheme .reserve-selector__stat span,
    .dark-scheme .reserve-pill-card span,
    .dark-scheme .reserve-modal__copy,
    .dark-scheme .reserve-table thead th {
        color: #94a3b8;
    }
    .dark-scheme .reserve-section-title,
    .dark-scheme .reserve-summary-card__value,
    .dark-scheme .reserve-stat-value,
    .dark-scheme .reserve-card__value,
    .dark-scheme .reserve-live-card__value,
    .dark-scheme .reserve-table-title,
    .dark-scheme .reserve-mobile-meta strong,
    .dark-scheme .reserve-selector__select,
    .dark-scheme .reserve-selector__stat strong,
    .dark-scheme .reserve-pill-card strong,
    .dark-scheme .reserve-selector__note,
    .dark-scheme .reserve-modal__title,
    .dark-scheme .reserve-modal__panel h5,
    .dark-scheme .reserve-modal__meta,
    .dark-scheme .reserve-modal__close {
        color: #f8fafc;
    }
    .dark-scheme .reserve-selector__select {
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.06);
        color-scheme: dark;
    }
    .dark-scheme .reserve-selector__select option {
        background: #1a1327;
        color: #f8fafc;
    }
    .dark-scheme .reserve-table thead th {
        background: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .reserve-table tbody td,
    .dark-scheme .reserve-mobile-meta {
        border-color: rgba(255, 255, 255, 0.08);
    }
    .dark-scheme .reserve-status-badge.is-available {
        color: #86efac;
        background: rgba(16, 185, 129, 0.16);
    }
    .dark-scheme .reserve-status-badge.is-progress {
        color: #93c5fd;
        background: rgba(59, 130, 246, 0.18);
    }
    .dark-scheme .reserve-status-badge.is-blocked {
        color: #cbd5e1;
        background: rgba(148, 163, 184, 0.18);
    }
    .dark-scheme .reserve-loader__card {
        background: #11131f;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 24px 50px rgba(0, 0, 0, 0.35);
    }
    .dark-scheme .reserve-loader__title {
        color: #ffffff;
    }
    .dark-scheme .reserve-loader__copy {
        color: #aeb7c4;
    }
    .dark-scheme .reserve-loader__bar {
        background: rgba(255, 255, 255, 0.08);
    }
    @keyframes reserve-loader-pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.08); }
    }
    @keyframes reserve-loader-slide {
        0% { transform: translateX(-120%); }
        100% { transform: translateX(260%); }
    }
    @media (max-width: 1399.98px) {
        .reserve-overview,
        .reserve-history-grid {
            grid-template-columns: 1fr;
        }
        .reserve-selector__summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 1199.98px) {
        .reserve-panel,
        .reserve-empty {
            padding: 24px;
            border-radius: 24px;
        }
        .reserve-summary-grid,
        .reserve-glance-grid,
        .reserve-selector__glance {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .reserve-balance-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 991.98px) {
        .reserve-overview,
        .reserve-history-grid {
            grid-template-columns: 1fr;
        }
        .reserve-panel,
        .reserve-empty {
            padding: 24px;
            border-radius: 24px;
        }
        .reserve-selector__summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 767.98px) {
        .reserve-shell {
            padding-top: 16px;
            padding-bottom: 106px;
        }
        .reserve-stack {
            gap: 18px;
        }
        .reserve-balance-grid,
        .reserve-glance-grid,
        .reserve-action-grid,
        .reserve-summary-grid,
        .reserve-selector__row,
        .reserve-selector__summary,
        .reserve-selector__glance {
            grid-template-columns: 1fr;
        }
        .reserve-section-head,
        .reserve-summary-card,
        .reserve-stat-card,
        .reserve-mobile-top,
        .reserve-mobile-meta,
        .reserve-live-card {
            flex-direction: column;
        }
        .reserve-history-wrap {
            display: none;
        }
        .reserve-history-mobile,
        .reserve-sales-mobile {
            display: grid;
        }
        .reserve-selector__actions .btn-main,
        .reserve-selector__actions .btn-border,
        .reserve-modal__actions .btn-main,
        .reserve-modal__actions .btn-border,
        .reserve-live-card .btn-main {
            width: 100%;
            min-width: 0;
        }
        .reserve-modal__grid,
        .reserve-modal__nft-grid {
            grid-template-columns: 1fr;
        }
        .reserve-selector__stat,
        .reserve-pill-card {
            min-height: 0;
        }
    }
    @media (max-width: 575.98px) {
        .reserve-panel,
        .reserve-empty {
            padding: 20px;
            border-radius: 22px;
        }
        .reserve-modal__selected {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
