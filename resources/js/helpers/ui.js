window.showToast = function(message, type = 'success', onFinish = null) {
    let toastContainer = document.getElementById('toast-container');

    const bgColor = type === 'success' ? '#4CAF50' : '#dc3545';
    const toast = document.createElement('div');

    toast.innerText = message;
    toast.style.cssText = `
        background-color: ${bgColor};
        color: white;
        padding: 12px 20px;
        margin-bottom: 10px;
        border-radius: 4px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        font-weight: 500;
        min-width: 200px;
        text-align: center;
        opacity: 1;
        transition: opacity 0.3s ease-in-out;
    `;

    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            toast.remove();
            if (typeof onFinish === 'function') onFinish();
        }, 300);
    }, 1500);
};

window.showLoading = function() {
    const el = document.getElementById('global-loading-overlay');
    if (el) el.style.display = 'flex';
};

window.hideLoading = function() {
    const el = document.getElementById('global-loading-overlay');
    if (el) el.style.display = 'none';
};

window.showConfirm = function (message, onAccept, onCancel = null) {
    const modal = new bootstrap.Modal(document.getElementById('global-confirm-modal'));
    const messageElement = document.getElementById('global-confirm-message');
    const acceptButton = document.getElementById('global-confirm-accept');
    const cancelButton = document.getElementById('global-confirm-cancel');

    messageElement.textContent = message;

    acceptButton.replaceWith(acceptButton.cloneNode(true));
    cancelButton.replaceWith(cancelButton.cloneNode(true));

    const newAcceptButton = document.getElementById('global-confirm-accept');
    const newCancelButton = document.getElementById('global-confirm-cancel');

    newAcceptButton.addEventListener('click', () => {
        modal.hide();
        if (typeof onAccept === 'function') onAccept();
    });

    newCancelButton.addEventListener('click', () => {
        modal.hide();
        if (typeof onCancel === 'function') onCancel();
    });

    modal.show();
};


window.modalHelper = {
    open(id) {
        const modalEl = document.getElementById(id);
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();
        }
    },

    close(id) {
        const modalEl = document.getElementById(id);
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
    }
};