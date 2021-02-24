import '@gouvfr/all/dist/js/all.js';
import 'alpinejs';

const INITIAL_COPY_BUTTON_TEXT = 'Copier mon jeton';
const TRANSITION_COPY_BUTTON_TEXT = 'Jeton copié !';

const initTokenLine = (token) => {
  return {
    token,
    buttonText: INITIAL_COPY_BUTTON_TEXT,
    async copyTokenToClipboard() {
      const { state } = await navigator.permissions.query({
        name: 'clipboard-write' as PermissionName,
      });
      if (state === 'denied') {
        return;
      }
      navigator.clipboard.writeText(this.token);
      this.buttonText = TRANSITION_COPY_BUTTON_TEXT;
      setTimeout(() => {
        this.buttonText = INITIAL_COPY_BUTTON_TEXT;
      }, 3000);
    },
  };
};

// @ts-ignore
global.initTokenLine = initTokenLine;
