export const STATUS_CONFIG = {
    // Yellow / Warning
    INITIATED: {
        class: 'status-warning',
        label: 'Initiated'
    },
    PENDING: {
        class: 'status-warning',
        label: 'Pending'
    },
    AUTHORIZE: {
        class: 'status-warning',
        label: 'Authorize'
    },

    // Green / Success
    CAPTURE: {
        class: 'status-success',
        label: 'Capture'
    },
    SETTLEMENT: {
        class: 'status-success',
        label: 'Settlement'
    },

    // Red / Danger
    DENY: {
        class: 'status-danger',
        label: 'Deny'
    },
    CANCEL: {
        class: 'status-danger',
        label: 'Cancel'
    },
    EXPIRE: {
        class: 'status-danger',
        label: 'Expire'
    },
    FAILURE: {
        class: 'status-danger',
        label: 'Failure'
    },

    // Info / Neutral (Biru / Gray)
    REFUND: {
        class: 'status-info',
        label: 'Refund'
    },
    PARTIAL_REFUND: {
        class: 'status-info',
        label: 'Partial Refund'
    },
    CHARGEBACK: {
        class: 'status-info',
        label: 'Chargeback'
    },
    PARTIAL_CHARGEBACK: {
        class: 'status-info',
        label: 'Partial Chargeback'
    }
};

export const DEFAULT_STATUS = {
    class: 'status-default',
    label: '-'
};

/**
 * Helper untuk mendapatkan config status berdasarkan nama status
 * @param {string} status
 */
export const getOrderStatusConfig = (status) => {
    if (!status) return DEFAULT_STATUS;
    const key = String(status).toUpperCase();
    return STATUS_CONFIG[key] || { class: 'status-default', label: status };
};