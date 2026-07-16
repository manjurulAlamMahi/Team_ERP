<style>
    .issue-card {
        border-radius: 10px;
        transition: box-shadow 0.2s;
        position: relative;
    }
    .issue-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.10) !important; }

    /* type colours */
    .issue-card-critical  { background: #fff5f5; border-color: #dc3545 !important; }
    .issue-card-urgent    { background: #fff8f8; border-color: #e07b80 !important; }
    .issue-card-high      { background: #f0f5ff; border-color: #0d6efd !important; }
    .issue-card-normal    { background: #f0fff4; border-color: #198754 !important; }

    .issue-card-critical .issue-title  { color: #dc3545; }
    .issue-card-urgent   .issue-title  { color: #c0434a; }
    .issue-card-high     .issue-title  { color: #0d6efd; }
    .issue-card-normal   .issue-title  { color: #198754; }

    /* Row card strip (completed list) */
    .issue-type-strip {
        width: 5px;
        flex-shrink: 0;
        border-radius: 9px 0 0 9px;
    }
    /* Grid card strip (pending list) */
    .issue-type-strip-top {
        height: 5px;
        border-radius: 9px 9px 0 0;
    }
    .strip-critical { background: #dc3545; }
    .strip-urgent   { background: #e07b80; }
    .strip-high     { background: #0d6efd; }
    .strip-normal   { background: #198754; }

    /* Done checkbox — more prominent */
    .issue-checkbox {
        width: 22px;
        height: 22px;
        cursor: pointer;
        border: 2px solid #adb5bd;
        border-radius: 6px;
    }
    .issue-checkbox:checked {
        background-color: #198754;
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25,135,84,.25);
    }
    .issue-checkbox:not(:disabled):hover {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25,135,84,.15);
    }
    .issue-checkbox:disabled { opacity: 0.35; cursor: not-allowed; }

    .issue-serial {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        font-size: 12px;
        font-weight: 600;
    }

    /* Comment highlight — draws the eye without being obnoxious */
    .issue-card.has-comments {
        box-shadow: 0 0 0 2px rgba(255, 193, 7, .4) !important;
    }
    .comment-badge {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffe69c;
        cursor: pointer;
        font-weight: 600;
        white-space: nowrap;
        animation: commentPulse 2.2s ease-in-out infinite;
    }
    .comment-badge:hover { background: #ffe69c; }
    @keyframes commentPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, .55); }
        50%      { box-shadow: 0 0 0 4px rgba(255, 193, 7, 0); }
    }

    /* Grid card (pending, 4-up) */
    .issue-grid-card { min-height: 100%; }
    .issue-remarks-clamp {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
