import { defineStore } from 'pinia';

export const useDialogStore = defineStore('dialog', {
    state: () => ({
        isVisible: false,
        type: 'info', // 'info', 'warning', 'success', 'confirm'
        title: '',
        message: '',
        resolvePromise: null,
        rejectPromise: null, // Used for confirm dialogs
    }),
    actions: {
        _show(options) {
            this.isVisible = true;
            this.type = options.type || 'info';
            this.title = options.title || '';
            this.message = options.message || '';
        },
        hide() {
            this.isVisible = false;
            this.title = '';
            this.message = '';
            this.resolvePromise = null;
            this.rejectPromise = null;
        },
        info(options) {
            this._show({ ...options, type: 'info' });
        },
        warning(options) {
            this._show({ ...options, type: 'warning' });
        },
        success(options) {
            this._show({ ...options, type: 'success' });
        },
        confirm(options) {
            return new Promise((resolve, reject) => {
                this._show({ ...options, type: 'confirm' });
                this.resolvePromise = resolve;
                this.rejectPromise = reject;
            });
        },
        confirmAccept() {
            if (this.resolvePromise) {
                this.resolvePromise(true);
            }
            this.hide();
        },
        confirmCancel() {
            if (this.resolvePromise) {
                this.resolvePromise(false);
            }
            this.hide();
        },
    },
});
