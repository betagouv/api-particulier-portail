import { Controller } from 'stimulus';

export default class extends Controller {
  static targets = ['source', 'button'];
  sourceTarget: any;
  buttonTarget: any;

  connect() {
    if (document.queryCommandSupported('copy')) {
      this.buttonTarget.classList.remove('hidden');
    }
  }

  copy() {
    navigator.clipboard.writeText(this.sourceTarget.innerHTML);
    document.execCommand('copy');
    this.buttonTarget.innerHTML = 'Jeton copié !';
  }
}
